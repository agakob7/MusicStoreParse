<?php
require_once("defines.php");


$urlsDb = new \Fllat("urls", DB);
$productsDB = new \Fllat("products", DB);

$notProcessUrlsCnt = count($urlsDb->select());
#print_r($urlsDb->select());

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
    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" type="text/css" rel=stylesheet>
    <script type="text/javascript"
            src="http://kontrolowani.pl/wp-content/themes/kontrolowani/libs/jquery-1.11.3.min.js"></script>
    <style type="text/css">
        .glyphicon.spinning {
            animation: spin 1s infinite linear;
            -webkit-animation: spin2 1s infinite linear;
        }

        @keyframes spin {
            from {
                transform: scale(1) rotate(0deg);
            }
            to {
                transform: scale(1) rotate(360deg);
            }
        }

        @-webkit-keyframes spin2 {
            from {
                -webkit-transform: rotate(0deg);
            }
            to {
                -webkit-transform: rotate(360deg);
            }
        }

    </style>

    <script type="text/javascript">


        function postData(action) {

            $("#csv").addClass("hidden");

            $("#retry").addClass("hidden");

            $.ajaxSetup({
                beforeSend: function () {
                    $("#loading").removeClass("hidden");
                },
                complete: function () {
                    $("#loading").addClass("hidden");
                }
            });

            var frm = $("form").serializeArray();

            frm.push({name: "action", value: action});

            $.post("ajax.php", frm, function (data) {
                if (data.error) {
                    alert(data.error);
                } else {
                    if (data.remains == 0) {
                        $("#retry").addClass("hidden");
                        $("#csv").removeClass("hidden");

                    }
                    else {
                        $("#remains").text(data.remains);
                        $("#retry").removeClass("hidden");
                        $("#csv").addClass("hidden");
                    }
                }

            }, "json");
            return true;
        }
        function getCsv() {
            $("#csv").attr("href", 'download.php?' + $.param($("form").serializeArray()));
        }

    </script>
</head>
<body>
<div class="container">
    <form method="post" class='form form-horizontal' onsubmit="javascript:postData('new'); return false;"/>

    <div class="form-group">
        <label class="control-label ">Strona</label>

        <select name="driver"  class="form-control" required>
            <option value="">wybierz</option>
            <?php foreach ($drivers as $key => $driver) : ?>
            <option value="<?= $key ?>"><?= $driver ?></option>
            <?php endforeach; ?>
        </select>
    </div>


    <div class="form-group">
        <label class="control-label ">Url</label>
        <input type="url" name="url" class="form-control" value="<?= Arr::get($post, 'url') ?>" required/>
        <span>Adres url kategorii w sklepie, np. http://www.rnr.pl/instrumenty-klawiszowe/keyboardy </span>
    </div>

    <div class="form-group">
        <label class="control-label ">Nazwa producenta:</label>
        <input type="text" name="producer" class="form-control" value="<?= Arr::get($post, 'producer') ?>"/>
        <span>Nazwa producenta, którego produkty mają zostać pobrane</span>
    </div>


    <div class="form-group">
        <label class="control-label ">Dołącz do nazwy</label>
        <input type="text" name="name_suffix" class="form-control" value="<?= Arr::get($post, 'name_suffix') ?>"/>
        <span>Tekst który zostanie dołączony do nazwy produktu</span>
    </div>

    <div class="form-group">
        <label class="control-label ">Nazwa kategorii docelowej</label>
        <input type="text" name="category" class="form-control" required
               value="<?= Arr::get($post, 'category') ?>"/>

    </div>


    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label ">Waga:</label>
                <input type="number" name="weight" class="form-control"
                       value="<?= Arr::get($post, 'weight') ?>"/>
            </div>
        </div>
        <div class="col-md-2 col-md-offset-1">
            <div class="form-group"><label class="control-label ">Szerokość:</label>
                <input type="number" name="width" class="form-control" "<?= Arr::get($post, 'width') ?>" />
            </div>
        </div>
        <div class="col-md-2 col-md-offset-1">
            <div class="form-group">
                <label class="control-label ">Wysokość:</label>
                <input type="number" name="height" class="form-control" <?= Arr::get($post, 'height') ?>" />
            </div>
        </div>

        <div class="col-md-2 col-md-offset-1">
            <label class="control-label ">Głębokość:</label>

            <div class="form-group">
                <input type="number" name="depth" class="form-control" "<?= Arr::get($post, 'depth') ?>" />
            </div>
        </div>

    </div>
    <div class="form-group">
        <label class="control-label ">Limit</label>
        <input type="number" name="limit" class="form-control" required
               value="20"/>
        <span>Ile produktów pobrać jednocześnie</span>
    </div>
    <div class="form-group">


        <input type="submit" value="Nowe zapytanie" class="btn btn-primary"/>

        <a href="#" onclick="return postData('retry');" id="retry"
           class="btn btn-danger <?php if (!$notProcessUrlsCnt): ?> hidden <?php endif ?>">
            Pobierz brakujące produkty: <span id="remains"><?= $notProcessUrlsCnt ?></span> </a>

        <a href="#" onclick="return getCsv();" target="_blank" id="csv"
           class="btn btn-success <?php if ($notProcessUrlsCnt): ?> hidden <?php endif ?>">
            Pobierz CSV</a>
        <button class="hidden btn btn-warning" id="loading">
            <span class="glyphicon glyphicon-refresh spinning"></span> Pobieram...
        </button>
    </div>


    </form>
</div>
</body>
