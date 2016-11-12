<?php

require_once("defines.php");

$urlsDb = new \Fllat("urls", DB);
$productsDB = new \Fllat("products", DB);

register_shutdown_function('shutdown');

$post = $_POST;

if (empty($post))
    return;

#$parser = new Drivers\Rnr($urlsDb, $productsDB, $post['limit']);

try {

    $options = new Option();
    $options->parse_limit = Arr::Get($post, 'limit');
    $options->producer = Arr::get($post, 'producer');
    $options->name_suffix = Arr::get($post, 'name_suffix');

    $options->result_limit = Arr::get($post, 'record', null);

    $driver = Arr::get($post, 'driver', 'Hrt');
    $class = "Drivers\\" . $driver;

    $parser = new $class($urlsDb, $productsDB, $options);


    $action = \Arr::Get($post, 'action');

    if ($action == 'new') {
        $parser->getProducts($post['url']);
    } else
        $results = $parser->ParseProductUrls();


    print json_encode(array('remains' => count($urlsDb->select()), 'count' => count($productsDB->select()), 'result_limit' => $parser->result_limit));


} catch (Exception $ex) {

    print json_encode(array('error' => $ex->getMessage()));

}


function shutdown()
{
    $a = error_get_last();


}

