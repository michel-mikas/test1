<?php

namespace App\Http\Controllers\Admin_Panel;

use Session;
use Validator;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;





class LandingController extends AdminPanelController
{
    
    public function __construct(Request $request) {
        $this->_request = $request;
        parent::__construct();
        $this->_page_params = $this->get_page_params();
    }

    public function home() {

        $side_bar = $this->get_sidebar();
        $page_params = $this->get_page_params();

        $page_params['section_title'] = 'Dashboard';
        $page_params['page_title'] = 'My Dashboard';

        $dataToView = array(
            'page_params' => $page_params,
        );

        return view('admin_panel/sections/home')->with($dataToView);
    }
}