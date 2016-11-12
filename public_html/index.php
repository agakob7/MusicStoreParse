<?php
require_once("defines.php");

$urlsDb = new \Fllat("urls", DB);
$productsDB = new \Fllat("products", DB);

$notProcessUrlsCnt = count($urlsDb->select());

$products = count($productsDB->select());
$post = $_POST;


$drivers = getDrivers();

function getDrivers()
{
    $res = array();
    $classes = array_diff(scandir(DRIVERS), array('..', '.'));

    foreach ($classes as $class) {
        $name = str_replace('.php', '', $class);

        $class = "Drivers\\" . $name;
        $parser = new $class(null, null, null);
        $res[$name] = $parser->domain;
    }
    return $res;
}

?>
<html lang="pl-pl">
<head>

    <meta charset="utf-8"/>
    <title></title>
    <link href="/assets/bootstrap.min.css" type="text/css" rel="stylesheet"/>
    <link href="/assets/style.css" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="/assets/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="/assets/script.js"></script>

</head>
<body>
<div class="container">
    <div class="col-md-5">
        <form method="post" class='form form-horizontal' id="getdata">

            <div class="form-group">
                <label class="control-label">Strona</label>

                <select name="driver" class="form-control" required>
                    <option value="">wybierz</option>
                    <?php foreach ($drivers as $key => $driver) : ?>
                        <option value="<?= $key ?>"><?= $driver ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="control-label ">Url</label>
                <input type="url" name="url" class="form-control" value="<?= Arr::get($post, 'url') ?>" required/>
                <small class="help-block">Adres url kategorii w sklepie, np.
                    http://www.rnr.pl/instrumenty-klawiszowe/keyboardy
                </small>
            </div>


            <div class="form-group">
                <label class="control-label">Nazwa producenta:</label>
                <input type="text" name="producer" class="form-control"
                       value="<?= Arr::get($post, 'producer') ?>"/>
                <small class="help-block">Nazwa producenta, którego produkty mają zostać pobrane</small>
            </div>


            <div class="form-group">
                <label class="control-label">Limit</label>

                <input type="number" name="limit" class="form-control" size="4" required
                       value="20"/>
                <small class="help-block">Ile produktów pobrać jednocześnie, 0 = bez limitu</small>
            </div>


            <div class="form-group">

                <input type="submit" name='new' value="Nowe zapytanie" class="btn btn-primary"/>

                <input type="submit"
                       name='retry' <?php if (!$notProcessUrlsCnt): ?> style="display: none" <?php endif ?>
                       value="Pobierz brakujące produkty:
                        <?= $notProcessUrlsCnt ?>"
                       class="btn btn-danger "/>

                <button class="hidden btn btn-warning" id="loading">
                    <span class="glyphicon glyphicon-refresh spinning"></span> Pobieram...
                </button>

            </div>


        </form>
    </div>
    <div class="col-md-6 col-md-offset-1">
        <form method="post" class='form form-horizontal'>

            <div class="form-group">
                <label class="control-label">Dołącz do nazwy</label>
                <input type="text" name="name_suffix" class="form-control"
                       value="<?= Arr::get($post, 'name_suffix') ?>"/>
                <small class="help-block">Tekst, który zostanie dołączony za nazwą produktu</small>
            </div>

            <div class="form-group">
                <label class="control-label">Nazwa kategorii docelowej</label>
                <input type="text" name="category" class="form-control" required
                       value="<?= Arr::get($post, 'category') ?>"/>

            </div>

            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Waga:</label>
                        <input type="number" name="weight" class="form-control"
                               value="<?= Arr::get($post, 'weight') ?>"/>
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    <div class="form-group">
                        <label class="control-label">Szerokość:</label>
                        <input type="number" name="width" class="form-control" "<?= Arr::get($post, 'width') ?>" />
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    <div class="form-group">
                        <label class="control-label">Wysokość:</label>
                        <input type="number" name="height" class="form-control" <?= Arr::get($post, 'height') ?>" />
                    </div>
                </div>

                <div class="col-md-2 col-md-offset-1">
                    <label class="control-label">Głębokość:</label>

                    <div class="form-group">
                        <input type="number" name="depth" class="form-control" "<?= Arr::get($post, 'depth') ?>" />
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label class="control-label">Limit rekordów</label>
                <input type="number" name="record" class="form-control" value="0"/>
                <small class="help-block">Ile rekordów w jednym pliku csv. 0 = bez limitu</small>
            </div>
            <div class="form-group">
                <a href="#" onclick="showFiles();return false; " id="csv"
                   class="btn btn-success"  <?php if ($notProcessUrlsCnt): ?> style="display:none" <?php endif ?>>
                Generuj CSV</a>
            </div>
            <div class="form-group">
                <p id="products_cnt" <?php if ($notProcessUrlsCnt): ?> style="display:none" <?php endif ?>>
                    Produkty: <?= $products ?>  </p>

                <p id="files_list">


                </p>
            </div>
        </form>
    </div>
</body>
