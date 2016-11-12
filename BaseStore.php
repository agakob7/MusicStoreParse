<?php

abstract class BaseStore implements IMusicStore
{
    /** @var SimpleHtmlDom * */
    public $html;
    protected $url;
    public $domain;
    protected $scrapper;
    public $result_limit = 0;
    public $options;
    /** @var Fllat * */
    protected $uDB;
    /** @var Fllat * */
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

        //load website content
        $content = $this->scrapper->getWebsite($this->url);

        $this->html->load($content);

    }

    protected function emptyUrlsDB()
    {
        $this->uDB->rw(array());
    }

    /**
     *remove products from db
     */
    protected function emptyProductsDB()
    {
        $this->pDB->rw(array());
    }

    /**
     * push products array to db
     */
    protected function createProductsUrls($products = array())
    {
        $this->uDB->rw($products); //Rewrites all content the database with the provided data.
    }

    /**
     * continue parse products from db
     */
    public function ParseProductUrls()
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
     * Get products from db, apply filter from post
     * @param Product[] $result
     */
    public function getResults($limit = 0, $offset = 0)
    {
        $i = 0;

        $products = $this->pDB->select();

        foreach ($products as &$product) {

            if ($this->result_limit > 0 && $i >= $this->result_limit)
                break;

            /** @var Product $product */
            $product = \Object::recast(new \Product(''), json_decode(json_encode($product))); //array to object , stdclass to product object

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

            $results[] = $product;

            $i++;
        }

        if ($limit > 0)
            return array_slice($results, $offset, $limit);

        return $results;

    }


}