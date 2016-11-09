<?php

define('__ROOT__', dirname(__FILE__) . '/');
define('__DRIVERS__', __ROOT__ . 'Drivers/');
define('__VENDOR__', __ROOT__ . 'Vendor/');
require_once("/Vendor/fllat/fllat.php");

require_once("AutoLoader.php");

$urlsDb = new \Fllat("urls", __ROOT__ . "db");
$productsDB = new \Fllat("products", __ROOT__ . "db");

$post = $_POST;

if (empty($post))
    return;

$parser = new Drivers\Rnr($urlsDb, $productsDB, $post['limit']);


try {


    switch (\Arr::Get($post, 'action')) {

        case "retry":
            $results = $parser->retry();
            break;
        case "new":
            $dimensions  = array(
                'weight' =>Arr::get($_POST,'weight'),
                'height' => Arr::get($_POST,'height'),
                'width' => Arr::get($_POST,'width'),
                'depth' => Arr::get($_POST,'depth')
            );

            $results = $parser->getProducts($_POST['url'], $_POST['category'], $dimensions, $_POST['name_suffix'], array('producer' => $_POST['producer']));

            break;
    }
    print json_encode(array('remains' => count($urlsDb->select())));

} catch (Exception $ex) {

    print json_encode(array('error' => $ex->getMessage()));

}
?>