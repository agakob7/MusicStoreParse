<?php

abstract class BaseStore implements IMusicStore
{
    /** @var SimpleHtmlDom **/
    public $html;
    protected $url;
    public $domain;
    protected $scrapper;
    protected $result_limit = 0;
    public $options;
    /** @var Fllat **/
    protected $uDB;
    /** @var Fllat **/
    protected $pDB;
    protected $nameOutOfStock = "aa";

    function __construct($urlsDB, $productsDb, $options)
    {
        //,  $categories, $dimensions = array(), $parse_limit
        $this->uDB = $urlsDB;
        $this->pDB = $productsDb;
        $this->scrapper = new WebScrap();
        $this->options = $options;
    }

    /**
     * @param string $url
     * @throws \Exception
     */
    protected function set_url($url)
    {

        if (is_object($this->html))
            $this->html->clear();

        if (!strstr($url, 'http'))
            $url = $this->domain . $url;

        $this->url = $url;

        $this->html = new SimpleHtmlDom();

        $content = $this->scrapper->getWebsite($this->url);

        $this->html->load($content);

    }

    protected function emptyProductsDB()
    {
        $this->pDB->rw(array());
    }

    public function Retry()
    {
        $this->parseProductUrls(null);
    }

    protected function createProductsUrls($products = array())
    {
        $this->uDB->rw($products); //Rewrites all content the database with the provided data.

    }

    protected function parseProductUrls()
    {

        $products = $this->uDB->select(array());

        $i = 1;

        foreach ($products as $product) {

            if ($i > $this->options->parse_limit)
                break;

            $product = \Object::recast(new \Product(''), json_decode(json_encode($product))); //array to object , stdclass to product object
            $this->getProductPage($product);

            $product->photos = implode(',', $product->photos);

            if (!$this->pDB->exists("url", $product->url))
                $this->pDB->add($product);

            $find = $this->uDB->where(array(), "url", $product->url);

            if ($find)
                $this->uDB->rm(key($find));

            $i++;
        }


    }


    /**
     * @param Product[] $result
     */
    public
    function outputCSV()
    {

        $csv = new CsvWriter();

        $csv->setHeaders(array('Nazwa', 'Cena', 'Producent', 'Kategorie', 'Opis', 'Opis krótki', 'Opis meta', 'Tagi meta', 'Waga', 'Zdjęcia', 'Widoczny', 'Zrodlo', 'Gdy brak na stanie', 'Kod produktu', 'Wysokość', 'Głębokość', 'Szerokość', 'Zniżka', 'Url'));

        $i = 0;

        $result = $this->pDB->select();

        foreach ($result as &$row) {


            /** @var Product $product */
            $product = \Object::recast(new \Product(''), json_decode(json_encode($row))); //array to object , stdclass to product object

            if ($this->result_limit > 0 && $i >= $this->result_limit)
                break;

            $product->prepareCode();

            $product->setName($this->options->name_suffix, $this->nameOutOfStock);
            $product->setShortDescription();

            $product->weight = $this->options->weight;
            $product->width = $this->options->width;
            $product->height = $this->options->height;
            $product->depth = $this->options->depth;
            $product->categories = $this->options->categories;


            unset($product->available);
            unset($product->id_producer);
            unset($product->data);

            $csv->insertLine((array)$product);

            $i++;
        }

    }


}