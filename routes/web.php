<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Symfony\Component\Finder\Finder;

$router->get('/', function () use ($router) {
    return 'Hello EBANX!';
    // return $router->app->version();
});

//ROTAS API
$require = function () use ($router) {
    $files = Finder::create()
                ->in(app()->path() . '/Http/Routes')
                ->name('*.php');

    foreach ($files as $file) {
        require $file->getRealPath();
    }
};

$require();