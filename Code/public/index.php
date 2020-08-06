<?php

declare(strict_types=1);
require_once '../vendor/autoload.php';

use App\Service\Router;
use App\Service\Database;

var_dump(Database::getPdo());


$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new Router();
$router->run();