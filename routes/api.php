<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

app()->router->get('zoap/{key}/server', [
    'as' => 'zoap.server.wsdl',
    'uses' => '\App\Http\Controllers\ZoapController@server'
]);

app()->router->post('zoap/{key}/server', [
    'as' => 'zoap.server',
    'uses' => '\App\Http\Controllers\ZoapController@server'
]);
