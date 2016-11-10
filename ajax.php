<?php

require_once("defines.php");

$urlsDb = new \Fllat("urls", DB);
$productsDB = new \Fllat("products", DB);

$post = $_POST;

if (empty($post))
    return;

#$parser = new Drivers\Rnr($urlsDb, $productsDB, $post['limit']);

try {

    $options = new Option();
    $options->parse_limit = Arr::Get($post, 'limit');
    $options->producer = Arr::get($post, 'producer');
    $options->name_suffix = Arr::get($post, 'name_suffix');

    $driver = Arr::get($post, 'driver', 'Hrt');
    $class = "Drivers\\" . $driver;

    $parser = new $class($urlsDb, $productsDB, $options);

    switch (\Arr::Get($post, 'action')) {

        case "retry":
            $results = $parser->retry();
            break;
        case "new":
            $results = $parser->getProducts($post['url']);
            break;
    }

    print json_encode(array('remains' => count($urlsDb->select())));

} catch (Exception $ex) {

    print json_encode(array('error' => $ex->getMessage()));

}
?>