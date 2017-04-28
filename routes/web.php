<?php
use App\Http\libs\Route_Structure as Structure;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', 'Front_Pages\PagesController@home')->middleware(['lang']);

Route::group(['middleware' => ['lang'], 'prefix' => '{lang}'], function () {
	
	Route::get('/reset_all', 'ResetAll@run');

	$structure = new Structure;

	$routes = $structure->get_front_pages_routes();
	foreach ($routes as $route) {
		$method = $route['request_method'];
		Route::$method($route['route'], $route['controller']. '@' .$route['method']);
	}

    Route::group(['middleware' => ['guest'], 'prefix' => 'admin'], function () use ($structure) {

    	$routes = $structure->get_admin_routes();
		foreach ($routes as $route) {
			$method = $route['request_method'];
			Route::$method($route['route'], $route['controller']. '@' .$route['method']);
		}

	});

});

