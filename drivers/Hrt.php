<?php
namespace Drivers {


    class Hrt extends \BaseStore implements \IMusicStore
    {

        public $domain = 'http://hurtowniamuzyczna.pl/';
        protected $per_page = 50;
        protected $result_limit = 1;
        protected $name_suffix;

        protected $nameOutOfStock = "Zapytaj";

        public function getProducts($url)
        {
            $results = array();

            $this->set_url($url, true);

            if (isset($this->options->producer)) {

                $producers = $this->_getProducers();

                $search = array_search(strtolower($this->options->producer), array_map('strtolower', $producers)); //case insensitive search

                if (!$search && $this->options->producer)
                    throw new \InvalidArgumentException("Taki producent nie istnieje w tej kategorii");

                $this->set_url($url . '?producent_id=' . $search);
            }

            $pages = $this->_getPagesNum();

            for ($i = 1; $i <= $pages; $i++) {
                $this->_getCategoryProducts($url, $i, $results, null);
            }

            $this->emptyProductsDB();
            $this->createProductsUrls($results);
            $this->parseProductUrls();
        }

        private function _getPagesNum()
        {

            $nav = $this->html->find(".navCenter", 0);

            $pages = 1;
            if (is_object($nav))
                $pages = $nav->find("a", -1)->innertext;

            return $pages;
        }

        public function getProductPage(&$product)
        {
            $this->set_url($product->url);

            $description = $this->html->find("[itemprop='description']", 0);

            if (is_object($description))
                $product->setDescription($description->innertext);

            $product->producer = $this->_getProducer();

            $stock = $this->html->find("[itemprop='availability']", 0);

            if (is_object($stock) && strstr($stock->plaintext, 'Zapytaj o'))
                $product->available = false;


            $photos = $this->html->find("a.photo500");

            foreach ($photos as $photo) {

                $img = $photo->find("img", 0);
                if (is_object($img))
                    $product->photos[] = $this->domain . $img->src;

            }

        }

        private function _getCategoryProducts($url, $page = 1, &$results, $id_producer = null)
        {

            $this->scrapper->getWebsite($url . http_build_query(array('page' => $page, 'id_producer' => $id_producer)), 'GET', null, false);

            $lista = $this->html->find("ul.nowaListaCategory", 0);



            if (!is_object($lista))
                throw new \InvalidArgumentException("Niepoprawny  URL");

            foreach ($lista->find("li") as $list) {

                $ent = new \Product($this->domain . $list->find("a", 0)->href);
                $name = trim($list->find(".nLName > p > a", 0)->plaintext);

                $ent->id_producer = $id_producer;
                $this->getPrice($list, $ent);

                $ent->meta_tags = $name;
                $ent->name = $name;
                $ent->meta_title = \URL::title($name);

                $results[] = $ent;

            }

        }

        private function getPrice($html, &$product)
        {

            $base_price = $html->find(".nLPriceBrutto", 0)->plaintext;// cena zwykla albo po promocji
            $product->price = (int)$base_price;

            $price_old = $html->find(".nLPriceOld", 0); //stara cena przed promocja
            if (is_object($price_old)) //jest promo
            {
                $product->discount = $price_old->plaintext - $product->price;
                $product->price = $price_old->plaintext;

            }
        }

        private
        function  _getProducer()
        {

            $logo = $this->html->find(".photoLogo", 0);

            if (is_object($logo)) {

                preg_match('/producent(\s.+\s)Logo/', $logo->alt, $match);
            }

            return trim(isset($match[1]) ? html_entity_decode(html_entity_decode($match[1])) : null);
        }

        private
        function  _getProducers()
        {
            $filtr = $this->html->find("ul.filtrList", 0);

            $results = array();

            if (is_object($filtr)) {

                $producers = $filtr->find("select[name='producent_id']", 0);
                if (is_object($producers)) {
                    $options = $producers->find("option");

                    foreach ($options as $option) {
                        if (strlen($option->value))
                            $results[$option->value] = html_entity_decode(html_entity_decode($option->innertext));
                    }
                }

            }
            return $results;
        }

    }
}