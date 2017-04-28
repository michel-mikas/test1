<?php

namespace App\Http\Controllers\Front_Pages;

use Hash;
use Session;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function test_page() {
    	//Auth::guard('user')->logout();
    	$user_model = 'App\User';
    	$headers = [];
    	$code = 200;
    	//$allUsers = $user_model::all();
        $allUsers = $user_model::withTrashed()->get();
        $countUsers = $user_model::withTrashed()->count();
        echo $countUsers;
    	foreach ($allUsers as $key => $value) {
    		if($value->id == Auth::guard('user')->user()->id) {
    			$allUsers[$key]->loggedin = true;
    			break;
    		}
    	}
    	//dd(Auth::guard('user')->user(), Auth::guard('user')->user()->getOriginal());
    	//Auth::guard('user')->user()->getOriginal();
    	return response()->json($allUsers, $code, $headers, JSON_PRETTY_PRINT);
    }

}