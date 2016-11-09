<?php

abstract class BaseStore
{
    public $html;
    protected $url;
    protected $domain;
    protected $scrapper;
    protected $result_limit = 0;
    protected $parse_limit = 0;
    public $dimensions;
    public $categories;

    protected $urlsDB;
    protected $productsDB;

    function __construct($urlsDB, $productsDb, $parse_limit)
    {
        $this->urlsDB = $urlsDB;
        $this->productsDB = $productsDb;
        $this->scrapper = new WebScrap();
        $this->parse_limit = $parse_limit;
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

    /**
     * @param Product[] $result
     */
    public function outputCSV()
    {

        $csv = new CsvWriter();

        $csv->setHeaders(array('Nazwa', 'Cena', 'Producent', 'Kategorie', 'Opis', 'Opis krótki', 'Opis meta', 'Tagi meta', 'Waga', 'Zdjęcia', 'Widoczny', 'Zrodlo', 'Gdy brak na stanie', 'Kod produktu', 'Wysokość', 'Głębokość', 'Szerokość', 'Url'));

        $i = 0;

        $result = $this->productsDB->select();

        foreach ($result as &$row) {

            $row = json_decode(json_encode($row)); //array to object , stdclass to product object


            if ($this->result_limit > 0 && $i >= $this->result_limit)
                break;

            if (isset($row->description))
                $row->description = ($row->description);
            //  $row->photos = implode(',', $row->photos);

            //generate product code form first 3 xhcar of producer name and product name, ignore case, producer is always on begining of string

//echo $row->meta_tags;

            $t = substr($row->producer, 0, 3);
            $t1 = str_ireplace($row->producer, '', $row->meta_tags);
             $row->code = strtoupper(\URL::title($t.$t1, ''));

            unset($row->available);
            unset($row->id_producer);

            $csv->insertLine((array)$row);

            $i++;
        }

    }


}