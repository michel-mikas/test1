<?php

namespace App\Http\Controllers\Admin_Panel;

use Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;





class ProductsController extends AdminPanelController
{

    protected $_mainTable = 'products';
    protected $_mainModel = 'App\Product';
    protected $_param_title = 'Product';
    protected $_param_success = 'Products';

    public function __construct(Request $request) {
        $this->_request = $request;
        parent::__construct();
        $this->_page_params = $this->get_page_params();
    }

    protected function handle_row($item) {

        $values = array(
            $item->id,
            $item->description,
            $item->category->name,
            $item->price,
            $item->orders()->count()
        );

        $row = array();
        $table_columns = array_keys($this->columns());
        foreach ($values as $key => $value) {
            $row[$table_columns[$key]] = $value;
        }

        $trType = '';
        $buttons = array('view');
        if($item->trashed()) {
            $buttons[] = 'restore';
            $trType = 'danger';
        }
        else {
            $buttons[] = 'delete';
        }
        $buttons = $this->get_buttons($item, $buttons, 'description');
        return array('data' => $row, 'trType' => $trType, 'buttons' => $buttons);
    }

    private function columns() {
        $columns = array(
            'id' => 'ID',
            'description' => 'Description',
            'category' => 'Category',
            'price' => 'Price',
            'total_orders' => 'Total Orders',
            'actions' => 'Actions'
        );
        return $columns;
    }

    public function get_all() {
        $datepicker = Null; // no datepicker
        
        $builder = $this->_mainModel::withTrashed();
        $rows = $this->get_table_data($builder);

        if($this->_request->ajax()) {
            return $rows->make(true);
        }

        $myTables = array(
            array(
                'title' => 'Active Users',
                'box_class' => 'success',
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


    public function view_item() {

        $model = $this->_mainModel;
        $item = $model::findOrFail($this->_request->id);

        $data = array(
            'inspect' => array(
                'id' => $item->id,
                'product-id' => $item->{'product-id'},
                'description' => $item->description,
                'Category' => $item->category,
                'Price' => $item->price. ' €',
                'Orders with this product (' .$item->orders()->count(). ')' => '<span><button class="btn btn-default btn-xs hide_text"><i class="fa fa-plus"></i></button><span class="hidden">' .$this->get_items_str($item->orders, 'orders', 'id'). '</span></span>',
            ),
        );

        $buttons = array();

        $data['buttons'] = $this->get_buttons($item, $buttons);

        $this->_page_params['section_title'] = $this->_param_title. ': ' .$item->description. ' (' .$item->{'product-id'}. ')';
        $this->_page_params['page_title'] = 'Dashboard | '.$this->_param_title;
        $dataToView = array(
            'page_params' => $this->_page_params,
            'data' => $data
        );
        return view('admin_panel/sections/inspect')->with($dataToView);
    }



    // ADD PRODUCT 

    protected function get_form_inputs($item=null) {

        $categories = \App\Category::all();
        foreach ($categories as $opt) {
            $opt->opt_text = $opt->name;
            $opt->props = array(
                'value' => $opt->id,
            );
        }

        $inputs = array(
            '_token' => array(
                'type' => 'hidden',
                'props' => array(
                    'name' => '_token',
                    'type' => 'hidden',
                    'value' => csrf_token()
                ),
            ),
            'product-id' => array(
                'input-group-addon' => '<i class="fa fa-bullseye"></i>',
                'label' => 'Product-Id',
                'type' => 'text',
                'parent_class' => 'input-group',
                'props' => array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'name' => 'product-id',
                    'value' => $this->get_input_value($item, old('product-id'), 'product-id', ''),
                    'id' => 'product-id',
                    'required' => true,
                ),
            ),
            'category' => array(
                'input-group-addon' => '<i class="fa fa-home"></i>',
                'label' => 'Category',
                'type' => 'select',
                'parent_class' => 'input-group',
                'param' => 'name',
                'select_props' => array(
                    'class' => 'form-control',
                    'name' => 'id_category',
                    'id' => 'id_category',
                    'required' => 1
                ),
                'opt1' => $this->get_1_opt('\App\Category', 'id_category', 'category', $item),
                'opts' => $categories,
                'required' => true,
            ),
            'price' => array(
                'input-group-addon' => '<i class="fa fa-euro"></i>',
                'label' => 'Price',
                'type' => 'number',
                'parent_class' => 'input-group',
                'props' => array(
                    'type' => 'number',
                    'class' => 'form-control',
                    'name' => 'price',
                    'min' => '1',
                    'step' => '0.01',
                    'value' => $this->get_input_value($item, old('price'), 'price', ''),
                    'id' => 'price',
                    'required' => true,
                ),
            ),
            'description' => array(
                'input-group-addon' => '<i class="fa fa-font"></i>',
                'value' => $this->get_input_value($item, old('description'), 'description', ''),
                'label' => 'Description',
                'type' => 'textarea',
                'parent_class' => 'input-group',
                'props' => array(
                    'name' => 'description',
                    'id' => 'description',
                    'class' => 'form-control',
                    'rows' => '5'
                ),
            ),
        );
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

        $this->_page_params['section_title'] = 'Add ' .$this->_param_title;
        $this->_page_params['page_title'] = 'Dashboard | Add ' .$this->_param_title;
        $dataToView = array(
            'page_params' => $this->_page_params,
            'data' => $this->get_page_data($form_action),
        );
        return view('admin_panel/sections/form')->with($dataToView);
    }

    private function val_item($inputs, $action) {

        $rules = array(
            'product-id' => 'required|max:255',
            'id_category' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'description' => 'required',
        );

        return Validator::make($inputs, $rules);
    }

    public function post_add() {
        $inputs = $this->entities_data($this->_request->except('_token'));

        $validator = $this->val_item($inputs, 'add');
        if($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $new_item = $this->_mainModel::create($inputs);

        Session::flash('flash_success', $this->_param_title. ' added (' .$new_item->description. ', ' .$new_item->{'product-id'}. ')');
        return redirect()->back();
    }




    // DELETE AND RESTORE


    public function delete() {
        $model = $this->_mainModel;
        $item = $model::withTrashed()->findOrFail($this->_request->id);
        $item->delete();
        return redirect()->back();
    }

    public function restore() {
        $model = $this->_mainModel;
        $item = $model::withTrashed()->findOrFail($this->_request->id);
        $item->restore();
        return redirect()->back();
    }




}