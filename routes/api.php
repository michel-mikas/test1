<?php

use Illuminate\Http\Request;

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
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/users/{id?}', function (Request $request) {
	//App\User::all()
	if(!is_null($request->id)) {
		return response()->json(App\User::where('id', $request->id)->get(), 200, [], JSON_PRETTY_PRINT);
	}
    return response()->json(App\User::where('id', '<', 5)->get(), 200, [], JSON_PRETTY_PRINT);
});

Route::post('/post/test', function (Request $request) {
	//App\User::all()
	//print_r($request->all());
    return response()->json($request->info, 200, [], JSON_PRETTY_PRINT);
});
