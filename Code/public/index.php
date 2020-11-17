<?php
declare(strict_types=1);
use App\Service\Router;
require_once '../vendor/autoload.php';
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
(new Router())->run();