<?php

define('__ROOT__', dirname(__FILE__) . '/');
define('__DRIVERS__', __ROOT__ . 'Drivers/');
define('__VENDOR__', __ROOT__ . 'Vendor/');
require_once("/Vendor/fllat/fllat.php");

require_once("AutoLoader.php");

$post = array();
$urlsDb = new \Fllat("urls", __ROOT__ . "db");
$productsDB = new \Fllat("products", __ROOT__ . "db");

$parser = new Drivers\Rnr($urlsDb, $productsDB, 20);

$filename = date("d_m_Y") . '-' . rand(1000, 9999) . '.csv';

#header('Content-Encoding: UTF-8');
header('Content-Description: File Transfer');
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=' . basename($filename));
header('Content-Transfer-Encoding: binary');


$parser->outPutCsv();
exit();
