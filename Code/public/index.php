<?php
declare(strict_types=1);
session_start();
use App\Service\Router;

define('ROOT', str_replace('public\index.php', '', $_SERVER['SCRIPT_FILENAME']));
require_once ROOT.'vendor/autoload.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new Router();
$router->run();
