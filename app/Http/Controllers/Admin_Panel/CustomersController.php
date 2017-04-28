<?php

namespace App\Http\Controllers\Admin_Panel;

use Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;






class CustomersController extends AdminPanelController
{

    protected $_mainTable = 'customers';
    protected $_mainModel = 'App\Customer';
    protected $_param_title = 'Customer';
    protected $_param_success = 'Customer';

    public function __construct(Request $request) {
        $this->_request = $request;
        parent::__construct();
        $this->_page_params = $this->get_page_params();
    }

    protected function handle_row($item) {

        $values = array(
            $item->name,
            $item->revenue,
            $item->orders()->count(),
            $item->orders()->sum('total'),
            strftime('%d-%m-%Y', $item->since),
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
            'revenue' => 'Revenue (€)',
            'num_of_orders' => 'Num of Orders',
            'total' => 'Total Spent (€)',
            'since' => 'Since',
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
            'columnIndex' => 4, // index from collumn to filter the data, if more tables have data the collumn must be at same place (index) in all tables
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


    public function view_item() {

        $model = $this->_mainModel;
        $item = $model::findOrFail($this->_request->id);

        $data = array(
            'inspect' => array(
                'Name' => $item->name,
                'Since' => strftime('%d-%m-%Y  %H:%M', $item->joined),
                'Revenue' => $item->revenue. ' €',
                'Orders made (' .$item->orders()->count(). ')' => '<span><button class="btn btn-default btn-xs hide_text"><i class="fa fa-plus"></i></button><span class="hidden">' .$this->get_items_str($item->orders, 'orders', 'id'). '</span></span>',
            ),
        );

        $buttons = array();

        $data['buttons'] = $this->get_buttons($item, $buttons);

        $this->_page_params['section_title'] = $this->_param_title. ': ' .$item->name;
        $this->_page_params['page_title'] = 'Dashboard | '.$this->_param_title;
        $dataToView = array(
            'page_params' => $this->_page_params,
            'data' => $data
        );
        return view('admin_panel/sections/inspect')->with($dataToView);
    }

}