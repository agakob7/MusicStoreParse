<?php

define('__ROOT__', dirname(__FILE__) . '/');
define('__DRIVERS__', __ROOT__ . '/drivers/');
define('__VENDOR__', __ROOT__ . '/vendor/');

spl_autoload_register(function ($class_name) {

    if (file_exists(__ROOT__ . $class_name . '.php'))
        include __ROOT__ . $class_name . '.php';
    if (file_exists(__DRIVERS__ . $class_name . '.php'))
        include __DRIVERS__ . $class_name . '.php';
    if (file_exists(__VENDOR__ . $class_name . '.php'))
        include __VENDOR__ . $class_name . '.php';

});


if (isset($_POST['url'])) {

    $parser = new Drivers\Rnr();
    try {
        $file = $parser->getProducts($_POST['url'], $_POST['category'], $_POST['weight'], $_POST['name_suffix'], array('producer' => $_POST['producer']));
$nic = 0;
        download($file);

    } catch (InvalidArgumentException $ex) {
        print $ex->getMessage();
    }

}

function download($file)
{
    $fp = fopen($file, 'rb');
    header('Content-Encoding: UTF-8');
    header('Content-Description: File Transfer');
    header("Content-type: application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header("Content-Length: " . filesize($file));
    print "\xEF\xBB\xBF"; // UTF-8 BOM

    fpassthru($fp);
    exit;
}

?>

<html lang="pl-pl">
<head>

    <meta charset="utf-8"/>
    <title></title>
    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" type="text/css" rel=stylesheet>

</head>
<body>
<div class="container">
    <form method="post" class='form form-horizontal'>
        <div class="form-group">
            <label class="control-label ">Url</label>
            <input type="url" name="url" class="form-control" required/>
            <span>Adres url kategorii w sklepie, np. http://www.rnr.pl/instrumenty-klawiszowe/keyboardy </span>
        </div>
        <div class="form-group">
            <label class="control-label ">Nazwa producenta:</label>
            <input type="text" name="producer" class="form-control"/>
            <span>Nazwa producenta, którego produkty mają zostać pobrane</span>
        </div>

        <div class="form-group">
            <label class="control-label ">Waga:</label>
            <input type="number" name="weight" class="form-control"/>
            <span>Waga produktu (tylko liczby)</span>
        </div>

        <div class="form-group">
            <label class="control-label ">Dołącz do nazwy</label>
            <input type="text" name="name_suffix" class="form-control"/>
        </div>

        <div class="form-group">
            <label class="control-label ">Nazwa kategorii docelowej</label>
            <input type="text" name="category" class="form-control" required/>
            <span>Jeśli nie istnieje zostanie dodana</span>
        </div>

        <div class="form-group"><input type="submit" value="Wyślij" class="btn btn-primary"/></div>

    </form>
</div>
</body>
