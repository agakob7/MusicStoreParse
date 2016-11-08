<?php

abstract class BaseStore
{
    public $html;
    protected $url;
    protected $domain;
    protected $scrapper;
    protected $limit = 2;

    function __construct()
    {
        $this->scrapper = new WebScrap();
    }

    /**
     * @param string $url
     * @throws \Exception
     */
    function set_url($url)
    {

        if (is_object($this->html))
            $this->html->clear();

        if (!strstr($url, "http"))
            $url = $this->domain . $url;

        $this->url = $url;

        $this->html = new simple_html_dom();

        $content = $this->scrapper->getWebsite($this->url);

        $this->html->load($content);

    }

    /**
     * @param Product[] $result
     */
    function saveCSV($result)
    {
        $filename = date("d_m_Y") . '-' . rand(1000, 9999);

        $file = 'csv/' . $filename . '.csv';

        $fp = fopen($file, 'w');

        $delimeter = ';';

        #fputs($fp, "\xEF\xBB\xBF"); //important

        fputcsv($fp, array('Nazwa', 'Cena', 'Producent', 'Kategorie', 'Opis', 'Opis krótki', 'Opis meta',  'Tagi meta', 'Waga', 'Zdjęcia', 'Widoczny', 'Zrodlo', 'Url'), $delimeter);

        $i = 0;

        foreach ($result as &$row) {

            if ($this->limit > 0 && $i > $this->limit)
                break;

            $row->photos = implode(',', $row->photos);

            unset($row->available);

            fputcsv($fp,(array)$row, $delimeter);

            $i++;
        }
        fclose($fp);

        return $file;

    }


}

