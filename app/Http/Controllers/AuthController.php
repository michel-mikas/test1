<?php

namespace App\Http\Controllers;

use Hash;
use Session;
use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    private $_guard = Null;
    private $_guards = array('admin' => 'admin/dashboard', 'user' => 'user/test_page', 'chef' => 'chef/test_chef_page');

    public function __construct(Request $request) {
        if(count($request->segments()) > 0) {
            $this->_guard = $request->segments()[1];
        }
    }

    public function login_auth(Request $request) {
        if(isset($this->_guards[$this->_guard])) {
            $credentials = $request->except('_token', 'remember_me');
            if(!Auth::guard($this->_guard)->attempt($credentials, $request->has('remember_me'))) {
                Session::flash('flash_error', 'Something went wrong with your login ' .$this->_guard);
                return redirect(url(Session::get('lang'). '/' .$this->_guard. '/login'));
            }
        }
        return redirect(url(Session::get('lang'). '/' .$this->_guards[$this->_guard]));
    }

    public function logout() {
        Auth::guard($this->_guard)->logout();
        return redirect('/' .$this->_guard);
    }
}