<?php

define('ROOT', dirname(__FILE__) . '/');
define('DRIVERS', ROOT . 'drivers/');
define('VENDOR', ROOT . 'vendor/');
define('HELPERS', ROOT . 'helpers/');
define('DB', ROOT . 'db');
require_once(VENDOR . "/fllat/fllat.php");
require_once("AutoLoader.php");