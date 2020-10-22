<?php
declare(strict_types=1);
use App\Service\Router;
use App\Service\Http\Session;
require_once '../vendor/autoload.php';

$session = new Session();

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

(new Router($session))->run();