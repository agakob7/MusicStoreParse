<?php
namespace Drivers {


    class Rnr extends \BaseStore
    {

        public $domain = 'http://rnr.pl/';
        protected $per_page = 12;

        protected $nameOutOfStock = "Niedostępny";

        public function getProducts($url, $filters = array())
        {

            $this->set_url($url, true);

            $query = $this->_getInitCategoryQry($this->html);

            //     $this->_getPages();

            $http_query = preg_replace('/%5B\d+/', '%5B', http_build_query($query));

            $results = array('pages' => 0, 'total' => 0, 'products' => array(), 'filters' => array('producers' => array()));

            $this->_getCategoryProducts(1, $results, $http_query, $url, array('filters', 'total', 'products'));

            // $this->_filterPost($query['src'], 'producers', null);

            $producers = $results['filters']['producers'];

            if (\Arr::get($filters, 'producer')) {

                ///search producer_id by entered name
                $id_producer = $this->_getProducer(\Arr::get($filters, 'producer'), $producers, 'name', 'id');

                if ($id_producer == null)
                    throw new \InvalidArgumentException("Taki producent nie istnieje w tej kategorii");

                $results = array();

                $this->_filterPost($query['src'], 'producers', $id_producer);
                $this->_getCategoryProducts(1, $results, $http_query, $url, array('products', 'total')); //new content after apling filters

            }

            //parse next pages
            for ($i = 2; $i <= $results['pages']; $i++) {
                $this->_getCategoryProducts($i, $results, $http_query, $url, array('products'));
            }


            foreach ($results['products'] as &$product) {
                $product->producer = $this->_getProducer($product->id_producer, $producers, 'id', 'name');
            }

            $this->emptyProductsDB();
            $this->createProductsUrls($results['products']);
            $this->parseProductUrls();

        }

        function _getPages()
        {
            $this->scrapper->getWebsite("http://www.rnr.pl/offer/render/properties", 'POST', http_build_query(array(
                'display' => 'grid',
                'limit' => $this->per_page,
                'order' => '+short_description.name'
            )), false);
        }


        /**
         * @param \Produprivateduct
         */
        public function getProductPage(&$product)
        {

            $this->set_url($product->url);

            $photos = $this->html->find("a.foto");

            $description = $this->html->find("#description", 0);

            if (is_object($description))
                $product->setDescription($description->innertext);

            //get 3 chars from producer name, remove producer name from product name and join

            foreach ($photos as $photo) {
                $prop = "data-type";
                if ($photo->$prop != 'youtube')//ignore yt movies
                    $product->photos[] = $photo->href;
            }

        }

        private
        function _getInitCategoryQry($html)
        {
            $offers = $html->find(".offer-render", 0);

            if (!is_object($offers))
                throw new \InvalidArgumentException("Niepoprawny  URL");

            $data_products = 'data-products';
            $data_src = 'data-src';
            $data_properties = 'data-properties';
            $data_filters = 'data-filters';
            $data_paginator = 'data-paginator';
            $data_compare = 'data-compare';

            $post = [
                'products' => json_decode($offers->$data_products, false),
                'id' => $offers->id,
                'src' => $offers->$data_src,
                'properties' => $offers->$data_properties,
                'filters' => $offers->$data_filters,
                'paginator' => 1,
                'compare' => 1,

            ];

            return $post;

        }

        //availability, 53,11
        private
        function _filterPost($src, $type = 'null', $values = null)
        {
            $this->scrapper->getWebsite("http://www.rnr.pl/offer/render/filter/set", 'POST', http_build_query(array(
                'src' => $src,
                'type' => $type,
                'values' => json_encode($values)
            )), false);

        }

        private
        function _getCategoryProducts($page = 1, &$results, $http_query, $referrer, $fields = array('products', 'filters', 'total'))
        {

            $json = $this->scrapper->getWebsite("http://www.rnr.pl/offer/render?page=" . $page, 'POST', $http_query, $referrer, false);

            $json_decoded = json_decode($json, true);

            if (in_array('total', $fields)) {
                $results['total'] = $json_decoded['paginator']['total'];
                $results['pages'] = $json_decoded['paginator']['last_page'];

            }
            if (in_array('filters', $fields))
                $results['filters'] = $json_decoded['filters'];

            if (in_array('products', $fields)) {

                foreach ($json_decoded['products'] as $row) {

                    $ent = new \Product($this->domain . $row['url']['modurl_path']);
                    $ent->available = $row['availability_id'] == 9 ? false : true;
                    $name = trim($row['short_description']['name']);

                    $ent->meta_tags = $name;
                    $ent->meta_title = \URL::title($name);
                    $ent->price = $row['price']['price_basic'];
                    $ent->id_producer = $row['producer_id'];

                    $results['products'][] = $ent;
                }
            }
        }

        private
        function _getProducer($search, $haystack, $key = 'id', $result = 'name')
        {
            foreach ($haystack as $s) {
                if (strtolower($s[$key]) == strtolower($search)) // ignore case when comparing
                    return $s[$result];
            }
            return null;
        }

    }
}