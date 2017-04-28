<?php

namespace App\Http\Controllers\Admin_Panel;

use Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Hash;
use Validator;




class AdminsController extends AdminPanelController
{

    protected $_mainTable = 'admins';
    protected $_mainModel = 'App\Admin';
    protected $_param_title = 'Admin';
    protected $_param_success = 'admin';
    protected $_profile_uploads_folder = 'images/uploads/admins';

    public function __construct(Request $request) {
        $this->_request = $request;
        parent::__construct();
        $this->_page_params = $this->get_page_params();
    }

    // METHODS NO REDIRECT


    // all items datatable

    protected function handle_row($item) {

        $values = array(
            $item->name,
            $item->email,
            strftime('%d-%m-%Y', $item->updated),
            $item->permission_lvl,
        );

        $row = array();
        $table_columns = array_keys($this->columns());
        foreach ($values as $key => $value) {
            $row[$table_columns[$key]] = $value;
        }

        $trType = '';
        $buttons = array();
        if(Auth::guard($this->_guard)->user()->permission_lvl === 1) {
            $buttons = array('ban');
            if($item->trashed()) {
                $trType = 'danger';
                $buttons = array('restore');
            }
        }
        $buttons = $this->get_buttons($item, $buttons, 'name');
        return array('data' => $row, 'trType' => $trType, 'buttons' => $buttons);
    }

    private function columns() {
        $columns = array(
            'name' => 'Name',
            'email' => 'Email',
            'joined' => 'Joined',
            'permission_lvl' => 'Permission Level',
            'actions' => 'Actions'
        );
        return $columns;
    }

    public function get_all() {
        $datepicker = Null; // no datepicker

        $datepicker = array(
            'min_label' => 'From',
            'max_label' => 'To',
            'comlumnNameSQL' => 'updated',
            'columnIndex' => 2, // index from collumn to filter the data, if more tables have data the collumn must be at same place (index) in all tables
        );


        $builder = $this->_mainModel::withTrashed();
        $rows = $this->get_table_data($builder);

        if($this->_request->ajax()) {
            return $rows->make(true);
        }

        $myTables = array(
            array(
                'title' => 'All ' .str_plural($this->_param_title),
                'box_class' => '',
                'columns' => $this->columns(),
                'rows' => $rows,
            ),
        );

        $this->_page_params['section_title'] = 'All ' .$this->_request->type_all. ' ' .str_plural($this->_param_title);
        $this->_page_params['page_title'] = 'Dashboard | All ' .$this->_request->type_all. ' ' .str_plural($this->_param_title);
        $dataToView = array(
            'page_params' => $this->_page_params,
            'data' => array(
                'tables' => $myTables,
                'datepicker' => $datepicker
            )
        );

        if(count($rows) > 1000 && count($myTables) < 2) {
            $dataToView['datatable'] = array(
                'server_side' => True,
            );
            return view('admin_panel/sections/datatable_server')->with($dataToView);
        }
        else {
            $dataToView['datatable'] = array(
                'server_side' => False
            );
            return view('admin_panel/sections/datatable')->with($dataToView);
        }
    }









        //INSPECT/VIEW ITEM



    private function get_menus_list($items, $table, $param = Null) {

        $param = (isset($param)) ? $param : 'name';
        $items_str = '';

        foreach ($items as $item) {
            $faMenu = ($item->valid) ? 'fa-check' : 'fa-times';
            $items_str .= '<a class="menus_list" href="/' .Session::get('lang'). '/admin/' .$table. '/view/' .$item->id. '"><img class="image_inspect" src="' .$item->img_file. '" alt="">' .$item->name_en. ' <i class="fa ' .$faMenu. '"></i></a>';
        }
        return $items_str;
    }

    public function view_item() {
        Session::put('user', ['das'=>123, 'sadfsa'=>'hey']);
        dd(Session::get('user'));
        $model = $this->_mainModel;
        $item = $model::withTrashed()->findOrFail($this->_request->id);

        $data = array(
            'inspect' => array(
                'Name' => $item->name,
                'Profile Image' => '<img class="image_inspect" alt="image profile ' .$item->name. '" src="' .$this->_profile_uploads_folder. '/' .$item->profile_img. '">',
                'Districts' => $this->get_items_str($item->districts, 'districts'),
                'Counties' => $this->get_items_str($item->counties, 'counties'),
                'Kitchen Types' => $this->get_items_str($item->kitchen_types, 'kitchen_types', 'name_en'),
                'Menus' => '<br>' .$this->get_menus_list($item->menus, 'menus', 'file', $item),
            ),
        );

        if($item->trashed()) {
            $buttons = array('restore');
        }
        else if($item->pending == 1) {
            $buttons = array('ban');
        }
        else if($item->active == 0 && $item->pending == 0) {
            $buttons = array('validate', 'ban');
        }
        else {
            $buttons = array('ban');
        }

        $data['buttons'] = $this->get_buttons($item, $buttons);

        $this->_page_params['section_title'] = $this->_param_title. ': ' .$item->name;
        $this->_page_params['page_title'] = 'Dashboard | '.$this->_param_title;
        $dataToView = array(
            'page_params' => $this->_page_params,
            'data' => $data
        );
        return view('admin_panel/sections/inspect')->with($dataToView);
    }
















    // FORMS

    protected function get_form_inputs($item=null) {

        $permissions = collect(
            array(
                (object) array(
                    'opt_text' => '',
                    'props' => array(
                        'value' => ''
                    )
                ),
                (object) array(
                    'opt_text' => 'Total',
                    'props' => array(
                        'value' => 1
                    )
                ),
                (object) array(
                    'opt_text' => 'Partial',
                    'props' => array(
                        'value' => 2
                    )
                ),
            )
        );

        $inputs = array(
            '_token' => array(
                'type' => 'hidden',
                'props' => array(
                    'name' => '_token',
                    'type' => 'hidden',
                    'value' => csrf_token()
                ),
            ),
            'name' => array(
                'input-group-addon' => '<i class="fa fa-font"></i>',
                'label' => 'Name',
                'type' => 'name',
                'parent_class' => 'input-group',
                'props' => array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'name' => 'name',
                    'value' => $this->get_input_value($item, old('name'), 'name', ''),
                    'id' => 'name',
                    'required' => true,
                ),
            ),
            'email' => array(
                'input-group-addon' => '<i class="fa fa-at"></i>',
                'label' => 'Email (username)',
                'type' => 'email',
                'parent_class' => 'input-group',
                'props' => array(
                    'type' => 'email',
                    'class' => 'form-control',
                    'name' => 'email',
                    'value' => $this->get_input_value($item, old('email'), 'email', ''),
                    'id' => 'email',
                    'required' => true,
                ),
            ),
            'password1' => array(
                'input-group-addon' => '<i class="fa fa-font"></i>',
                'label' => 'Password',
                'type' => 'password',
                'parent_class' => 'input-group',
                'props' => array(
                    'type' => 'password',
                    'class' => 'form-control',
                    'min' => '0',
                    'name' => 'password1',
                    'value' => '',
                    'id' => 'password1',
                ),
            ),
            'password2' => array(
                'input-group-addon' => '<i class="fa fa-font"></i>',
                'label' => 'Repeat Password',
                'type' => 'text',
                'parent_class' => 'input-group',
                'props' => array(
                    'type' => 'password',
                    'class' => 'form-control',
                    'min' => '0',
                    'name' => 'password2',
                    'value' => '',
                    'id' => 'password2',
                ),
            ),
            'profile_pic' => array(
                'input-group-addon' => '<i class="fa fa-euro"></i>',
                'label' => 'Image',
                'type' => 'file',
                'parent_class' => 'input-group',
                'props' => array(
                    'type' => 'file',
                    'name' => 'profile_pic',
                    'id' => 'img'
                ),
            ),
        );
        if(is_null($item)) { // if adding
            $inputs['permission'] = array(
                'input-group-addon' => '<i class="fa fa-user"></i>',
                'label' => 'Permission Level',
                'type' => 'select',
                'parent_class' => 'input-group',
                'select_props' => array(
                    'class' => 'form-control',
                    'name' => 'permission_lvl',
                    'id' => 'permission_lvl',
                    'required' => 1
                ),
                'param' => 'name',
                'opts' => $permissions,
                'required' => true,
            );
        }
        return $inputs;
    }

    protected function get_page_data($form_action, $item=null) {

        $data = array(
            'form_params' => array(
                'action' => $form_action,
                'button_text' => 'Adicionar ' .$this->_param_title,
                'inputs' => $this->get_form_inputs($item)
            ),
        );
        if($item != null) {
            $data['form_params']['inputs']['id'] = array(
                'type' => 'hidden',
                'props' => array(
                    'name' => 'id',
                    'type' => 'hidden',
                    'value' => $item->id
                ),
            );
        }
        return $data;
    }

    public function add() {
        
        $form_action = Session::get('lang'). '/admin/' .$this->_mainTable. '/post_add';

        $this->_page_params['section_title'] = 'Adicionar ' .$this->_param_title;
        $this->_page_params['page_title'] = 'Dashboard | Adicionar ' .$this->_param_title;
        $dataToView = array(
            'page_params' => $this->_page_params,
            'data' => $this->get_page_data($form_action),
        );
        return view('admin_panel/sections/form')->with($dataToView);
    }

    /*public function edit() {
        
        $form_action = Session::get('lang'). '/admin/' .$this->_mainTable. '/post_edit';

        $this->_page_params['section_title'] = 'Adicionar ' .$this->_param_title;
        $this->_page_params['page_title'] = 'Dashboard | Adicionar ' .$this->_param_title;
        $dataToView = array(
            'page_params' => $this->_page_params,
            'data' => $this->get_page_data($form_action),
        );
        return view('admin_panel/sections/form')->with($dataToView);
    }*/

    public function profile_edit() {

        $item = Auth::guard($this->_guard)->user();

        $form_action = Session::get('lang'). '/admin/' .$this->_mainTable. '/post_edit';

        $this->_page_params['section_title'] = 'Editar perfil';
        $this->_page_params['page_title'] = 'Dashboard | Editar Perfil';
        $dataToView = array(
            'page_params' => $this->_page_params,
            'data' => $this->get_page_data($form_action, $item),
        );
        return view('admin_panel/sections/form')->with($dataToView);
    }












//  POSTS


    private function val_item($inputs, $action='add') {

        $rules = array(
            'name' => 'required',
            'email' => 'required|unique:' .$this->_mainTable. ',email',
            'password1' => 'required|min:8',
            'password2' => 'same:password1',
            'permission_lvl' => 'required|between:1,2',
            'profile_pic' => 'required|image|max:1024'
        );

        if($action == "edit") {
            $rules['email'] = 'required|unique:' .$this->_mainTable. ',email,'.$inputs['id'];
            $rules['profile_pic'] = 'image|max:1024';
            $rules['password1'] = 'min:8';
            unset($rules['permission_lvl']);
        }

        return Validator::make($inputs, $rules);
    }

    public function post_add() {
        
        $inputs = $this->_request->except('_token', 'password1', 'password2', 'profile_pic');
        $inputs = $this->entities_data($inputs);

        $validator = $this->val_item($this->_request->all(), 'add');
        if($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $inputs['password'] = Hash::make($this->_request->password1);
        $inputs['updated'] = time();
        $inputs['id_admin'] = Auth::guard($this->_guard)->user()->id;
        
        $inputs['profile_pic'] = $this->store_img();
        $item = $this->_mainModel::create($inputs);
        
        Session::flash('flash_success', 'Success on adding a ' .$this->_param_success. ' (' .$item->name. ')');

        return redirect()->back();
    }

    public function post_edit() {
        
        $item = Auth::guard($this->_guard)->user();

        $inputs = $this->_request->except('_token', 'password1', 'password2', 'profile_pic');
        $inputs = $this->entities_data($inputs);

        $validator = $this->val_item($this->_request->all(), 'edit');
        if($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $inputs['password'] = Hash::make($this->_request->password1);
        $inputs['updated'] = time();
        $inputs['profile_pic'] = $this->store_img($item);
        $item->update($inputs);
        
        Session::flash('flash_success', 'Success on editing your profile (' .$item->name. ')');

        return redirect()->back();
    }

    private function store_img($item = null) {
        $img = $this->_request->profile_pic;
        if(!is_null($item) && is_null($img)) {
            return $item->profile_pic;
        }
        if($img != null) {
            $fileName = md5(str_random(20)). '_' .time(). '.' .$img->clientExtension();
            if(!is_null($item)) {
                if(file_exists(public_path($this->_profile_uploads_folder. '/' .$item->profile_pic))) {
                    unlink(public_path($this->_profile_uploads_folder. '/' .$item->profile_pic));
                }
            }
            else {
                return $item->profile_pic;
            }
            $img->move(public_path($this->_profile_uploads_folder), $fileName);
            return $fileName;
        }
        return 'no_img';
    }
















    // METHODS WITH REDIRECT

    public function delete() {
        $model = $this->_mainModel;
        if(Auth::guard($this->_guard)->user()->permission_lvl !== 1) {
            Session::flash('flash_error', 'You don\'t have enought permissions to do this');
            return redirect()->back();
        }
        $item = $model::withTrashed()->findOrFail($this->_request->id);
        $item->delete();
        return redirect()->back();
    }

    public function restore() {
        $model = $this->_mainModel;
        $item = $model::withTrashed()->findOrFail($this->_request->id);
        if(Auth::guard($this->_guard)->user()->permission_lvl !== 1) {
            Session::flash('flash_error', 'You don\'t have enought permissions to do this');
            return redirect()->back();
        }
        $item->restore();

        return redirect()->back();
    }


    // FORMS

}