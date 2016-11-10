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


$driver = Arr::get($post, 'driver', 'Hrt');

$class = "Drivers\\" . $driver;
$parser = new $class($urlsDb, $productsDB, $options);

$filename = date("d_m_Y") . '-' . rand(1000, 9999) . '.csv';

#header('Content-Encoding: UTF-8');


try {
   header('Content-Description: File Transfer');
    header("Content-type: application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . basename($filename));
    header('Content-Transfer-Encoding: binary');
    $parser->outPutCsv();
    exit();

} catch (Exception $ex) {
    header_remove();
}
