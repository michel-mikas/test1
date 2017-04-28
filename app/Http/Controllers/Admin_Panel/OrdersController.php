<?php

namespace App\Http\Controllers\Admin_Panel;

use Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;






class OrdersController extends AdminPanelController
{

    protected $_mainTable = 'orders';
    protected $_mainModel = 'App\Order';
    protected $_param_title = 'Order';
    protected $_param_success = 'Orders';

    public function __construct(Request $request) {
        $this->_request = $request;
        parent::__construct();
        $this->_page_params = $this->get_page_params();
    }

    protected function handle_row($item) {

        $values = array(
            $item->id,
            $item->customer->name,
            $item->total,
            strftime('%d-%m-%Y', $item->date),
        );

        $row = array();
        $table_columns = array_keys($this->columns());
        foreach ($values as $key => $value) {
            $row[$table_columns[$key]] = $value;
        }

        $trType = '';
        $buttons = array('view');
        $buttons = $this->get_buttons($item, $buttons, 'name');
        return array('data' => $row, 'trType' => $trType, 'buttons' => $buttons);
    }

    private function columns() {
        $columns = array(
            'name' => 'Name',
            'custumer' => 'Customer',
            'total' => 'Total (€)',
            'date' => 'Date',
            'actions' => 'Actions'
        );
        return $columns;
    }

    public function get_all() {
        $datepicker = Null; // no datepicker

        $datepicker = array(
            'min_label' => 'From',
            'max_label' => 'To',
            'comlumnNameSQL' => 'joined',
            'columnIndex' => 2, // index from collumn to filter the data, if more tables have data the collumn must be at same place (index) in all tables
        );
        $rows = $this->get_table_data();

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

    protected function get_prods_str($items, $table, $param = Null) {
        $param = (isset($param)) ? $param : 'name';
        $items_str = '';

        foreach ($items as $item) {
            $items_str .= '<a href="' .$this->_baseUrl. '/admin/' .$table. '/view/' .$item->id. '">' .$item->$param. ' (' .$item->pivot->quantity. ')</a>, ';
        }
        $items_str = rtrim($items_str, ', ');
        return $items_str;
    }


    public function view_item() {

        $model = $this->_mainModel;
        $item = $model::findOrFail($this->_request->id);

        $data = array(
            'inspect' => array(
                'id' => $item->id,
                'Total' => $item->total. ' €',
                'Date' => strftime('%d-%m-%Y  %H:%M', $item->date),
                'Customer' => '<a href="/' .Session::get('lang'). '/admin/customers/view/' .$item->customer->id. '">' .$item->customer->name. '</a>',
                'Products (qtd)' => '<span><button class="btn btn-default btn-xs hide_text"><i class="fa fa-plus"></i></button><span class="hidden">' .$this->get_prods_str($item->products, 'products', 'description'). '</span></span>',
            ),
        );

        $buttons = array();

        $data['buttons'] = $this->get_buttons($item, $buttons);

        $this->_page_params['section_title'] = $this->_param_title. ': ' .$item->id;
        $this->_page_params['page_title'] = 'Dashboard | '.$this->_param_title;
        $dataToView = array(
            'page_params' => $this->_page_params,
            'data' => $data
        );
        return view('admin_panel/sections/inspect')->with($dataToView);
    }

}