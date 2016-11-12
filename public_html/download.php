<?php
require_once("defines.php");

$post = array();
$urlsDb = new \Fllat("urls", DB);
$productsDB = new \Fllat("products", DB);

$options = new Option();

$post = $_GET;

$options->weight = Arr::get($post, 'weight');
$options->height = Arr::get($post, 'height');
$options->width = Arr::get($post, 'width');
$options->depth = Arr::get($post, 'depth');
$options->categories = Arr::get($post, 'category');
$options->name_suffix = Arr::get($post, 'name_suffix');
$options->result_limit = Arr::get($post, 'record', 0);
$options->result_offset = Arr::get($post, 'offset', 0);

$driver = Arr::get($post, 'driver', 'Hrt');

$class = "Drivers\\" . $driver;
$parser = new $class($urlsDb, $productsDB, $options);

#header('Content-Encoding: UTF-8');



$file = array($driver, date("d_m_Y_H:i_s"), $options->result_offset);

$filename = implode('-', $file) . '.csv';


try {

    header('Content-Description: File Transfer');
    header("Content-type: application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . basename($filename));
    header('Content-Transfer-Encoding: binary');
    $csv = new CsvWriter();

    $results = $parser->getResults($options->result_limit, $options->result_offset);

    $csv->setHeaders(array('Nazwa', 'Cena', 'Producent', 'Kategorie', 'Opis', 'Opis krótki', 'Opis meta', 'Tagi meta', 'Waga', 'Zdjęcia', 'Widoczny', 'Zrodlo', 'Gdy brak na stanie', 'Kod produktu', 'Wysokość', 'Głębokość', 'Szerokość', 'Zniżka', 'Url'));
    $i = 0;

    foreach ($results as &$row) {
        $csv->insertLine((array)$row);
    }

    exit();

} catch (Exception $ex) {
    header_remove();
}

