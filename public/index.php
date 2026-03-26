<?php

// $publicPath = getcwd();

// $uri = urldecode(
//     parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
// );

// // This file allows us to emulate Apache's "mod_rewrite" functionality from the
// // built-in PHP web server. This provides a convenient way to test a Laravel
// // application without having installed a "real" web server software here.
// if ($uri !== '/' && file_exists($publicPath.$uri)) {
//     return false;
// }

// $formattedDateTime = date('D M j H:i:s Y');

// $requestMethod = $_SERVER['REQUEST_METHOD'];
// $remoteAddress = $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'];

// file_put_contents('php://stdout', "[$formattedDateTime] $remoteAddress [$requestMethod] URI: $uri\n");

// require_once $publicPath.'/index.php';

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
