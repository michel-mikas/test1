<?php

namespace App\Http\Controllers\Admin_Panel;
use Hash;
use Session;
use Datatables;
use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;




class AdminPanelController extends Controller
{

    protected $_baseUrl = Null;
    protected $_baseFolderViews = 'admin_panel'; // Change on views, the extend field for each one
    protected $_guard = 'admin';

    public function __construct() {
        $this->_baseUrl = url('/');
        if(!is_null($this->_request->lang)) {
            $this->_baseUrl = $this->_baseUrl. '/' .$this->_request->lang;
        }
    }

    protected function get_page_params() {
        $page_params = array(
            'side_bar' => $this->get_sidebar(),
        );
        return $page_params;
    }

    protected function get_models() {
        return array(
            'Chef' => 'App\Chef',
        );
    }






    // HELPERS

    protected function entities_data($inputs, $trim_data = false) {
        foreach ($inputs as $key => $value) {
            if($trim_data) {
                $inputs[$key] = trim(htmlentities($value));
                continue;
            }
            $inputs[$key] = htmlentities($value);
        }
        return $inputs;
    }

    protected function get_input_value($item=null, $old=null, $param='name', $default='') {
        if($old) {
            return $old;
        }
        else if($item != null) {
            return $item->$param;
        }
        else {
            return $default;
        }
    }

    protected function get_1_opt($model, $old, $param, $item=null, $withTrashed=false, $default='<option></option>') { // select type input
        if(old($old)) {
            if($withTrashed) {
                $opt1 = $model::withTrashed()->find(old($old));
            }
            else {
                $opt1 = $model::find(old($old));
            }
        }
        else if($item != null) {
            $opt1 = $item->$param;
        }
        else {
            return $default;
        }
        return ($opt1 != null) ? '<option value="' .$opt1->id. '">' .$opt1->name. '</option>' : $default;
    }






    // SIDE BAR

    // main menu
    protected function get_sidebar() {

        $url = $this->_request->url();

        $hrefs = array(
            'customers' => array(
                'main' => $this->_baseUrl. '/admin/customers/all',
            ),
            'orders' => array(
                'main' => $this->_baseUrl. '/admin/orders/all',
            ),
            'products' => array(
                'main' => '#',
                'all' => $this->_baseUrl. '/admin/products/all',
                'add' => $this->_baseUrl. '/admin/products/add',
            ),
        );

        $links = array(
            'Products' => array(
                'href' => $hrefs['products']['main'],
                'fa_icon' => 'fa fa-cubes',
                'is_active' => str_contains($url, '/products/'),
                'dropdown' => array(
                    'Add' => array(
                        'is_active' => ($url == $hrefs['products']['add']),
                        'href' => $hrefs['products']['add'],
                        'fa_icon' => 'fa fa-plus',
                    ),
                    'All' => array(
                        'is_active' => ($url == $hrefs['products']['all']),
                        'href' => $hrefs['products']['all'],
                        'fa_icon' => 'fa fa-list',
                        'label' => array(
                            'data' => \App\Product::all()->count(),
                            'class' => 'label-primary'
                        ),
                    ),
                ),
            ),
            'Customers' => array(
                'href' => $hrefs['customers']['main'],
                'fa_icon' => 'fa fa-user',
                'is_active' => str_contains($url, '/customers/'),
                'label' => array(
                    'data' => \App\Customer::all()->count(),
                    'class' => 'label-primary'
                ),
            ),
            'Orders' => array(
                'href' => $hrefs['orders']['main'],
                'fa_icon' => 'fa fa-cc-visa',
                'is_active' => str_contains($url, '/orders/'),
                'label' => array(
                    'data' => \App\Order::all()->count(),
                    'class' => 'label-primary'
                ),
            ),
            /*'Administrators' => array(
                'href' => $hrefs['admins']['main'],
                'fa_icon' => 'fa fa-cutlery',
                'is_active' => str_contains($url, '/admins/'),
                'dropdown' => array(
                    'Add' => array(
                        'is_active' => ($url == $hrefs['admins']['add']),
                        'href' => $hrefs['admins']['add'],
                        'fa_icon' => 'fa fa-plus',
                    ),
                    'All' => array(
                        'is_active' => ($url == $hrefs['admins']['all']),
                        'href' => $hrefs['admins']['all'],
                        'fa_icon' => 'fa fa-list',
                    ),
                ),
            ),*/
        );
        return $links;
    }





















    // DATABTABLES

    protected function get_table_data($builder=null) {
        if($this->_request->ajax()) { // if datatables server side
            // if dates are chosen
            if ($this->_request->has('inputMinDate') && $this->_request->has('inputMaxDate')) {
                $items = $this->get_items_date_interval($builder);
                $rows = $this->get_rows($items);
                return $this->ajax_get_all($rows);
            }
            // when table loads for the first time
            $items = $this->get_items($builder);
            $rows = $this->get_rows($items);
            return $this->ajax_get_all($rows);
        }
        $items = $this->get_items($builder);
        $rows = $this->get_rows($items);
        return $rows;
    }

    protected function get_items($builder = Null) {

        $model = $this->_mainModel; // main model from the caller controller

        if($builder == Null) {
            return $model::all();
        }
        return $builder->get();
    }

    protected function get_rows($allItems) {
        $rows = array();
        $buttons = array();

        foreach ($allItems as $key => $item) {

            $row = $this->handle_row($item); // method on the caller controller

            if($this->_request->ajax()) {
                $rows[$key] = $row['data'];
                $rows[$key]['DT_RowClass'] = $row['trType'];
                $buttons[$key] = $row['buttons'];
            }
            else {
                $rows[$key]['trType'] = $row['trType'];
                $rows[$key]['values'] = $row['data'];
                $rows[$key]['values']['actions'] = $row['buttons'];
            }
            
        }
        
        if($this->_request->ajax()) {
            $rowsData = array('rows' => $rows, 'actions' => $buttons);
            return $rowsData;
        }
        return $rows;
    }

    protected function get_items_date_interval($builder=null) {

        $model = $this->_mainModel;

        $columnName = $this->_request->columnName;
        $timeStampSecondsMin = $this->_request->inputMinDate;
        $timeStampSecondsMax = $this->_request->inputMaxDate;

        if($builder == Null) {
            $builder = $model::where($columnName, '>=', $timeStampSecondsMin)->where($columnName, '<=', $timeStampSecondsMax);
            $allItems = $builder->get();
        }
        else {
            $allItems = $builder->where($columnName, '>=', $timeStampSecondsMin)->where($columnName, '<=', $timeStampSecondsMax)->get();
        }
        return $allItems;
    }

    protected function ajax_get_all($rows) {
        $allRows = collect($rows['rows']);
        $datatable = Datatables::of($allRows);
        $datatable->addColumn('actions', function ($row) use ($rows) {
            $buttons = '';
            // Thanks to the row $rowIndex we can now get the correspondent actions of the row
            // NOTE YOU CAN PASS THE ITEM ID INSTEAD OF $rowIndex along with the other data collumns of the table , SEE test-multi_gult , to see the old implementation.... handle_row method have one of the collumn values must be the item id

            // ATENTION, BE SURE THER'S NO DUPLICATE ROWS (same array two or more times) IN $ROWS, because this line below

            $rowIndex = array_search($row, $rows['rows']);
            foreach ($rows['actions'][$rowIndex] as $action) {
                if(isset($action['modal'])) {
                    $buttons .= '<a data-question="' .$action['modal']['question']. '" data-name="' .$action['modal']['name']. '" href="' .url($action['href']). '" title="' .$action['title']. '" data-advice="' .$action['modal']['advice_phrase']. '" data-textclass="' .$action['modal']['class_phrase']. '" data-btnconfirm="' .$action['modal']['btn_confirm']. '" class="' .$action['linkClass']. ' hasModal"><span class="' .$action['spanClass']. '"></span></a>';
                }
                else {
                    $buttons .= '<a title="' .$action['title']. '" href="' .$action['href']. '" class="' .$action['linkClass']. '"><span class="' .$action['spanClass']. '"></span></a>';
                }
            }
            return $buttons;
        });
        return $datatable;
    }














    // VIEW ITEM

    protected function get_items_str($items, $table, $param = Null) {
        $param = (isset($param)) ? $param : 'name';
        $items_str = '';

        foreach ($items as $item) {
            $items_str .= '<a href="' .$this->_baseUrl. '/admin/' .$table. '/view/' .$item->id. '">' .$item->$param. '</a>, ';
        }
        $items_str = rtrim($items_str, ', ');
        return $items_str;
    }







    // ACTIONS

    // just add actions at your will
    protected function get_buttons($item, $actions, $param_name = Null) {
        $param_name = ($param_name == Null) ? 'name' : $param_name;
        $allButtons = array(
            'check' => array(
                'action' => 'Check',
                'title' => 'Check details',
                'linkClass' => 'actionButton btn btn-default btn-sm',
                'spanClass' => 'fa fa-eye',
            ),
            'view' => array(
                'action' => 'View',
                'title' => 'View details',
                'linkClass' => 'actionButton btn btn-default btn-sm',
                'spanClass' => 'fa fa-eye',
                'href' => $this->_baseUrl. '/admin/' .$this->_mainTable. '/view/' .$item->id,
            ),
            'restore' => array(
                'action' => 'Restore',
                'title' => 'Restore',
                'linkClass' => 'actionButton btn btn-warning btn-sm',
                'spanClass' => 'fa fa-wrench',
                'href' => $this->_baseUrl. '/admin/' .$this->_mainTable. '/restore/' .$item->id,
            ),
            'ban' => array(
                'action' => 'Ban',
                'title' => 'Ban',
                'linkClass' => 'actionButton btn btn-danger btn-sm',
                'spanClass' => 'fa fa-ban',
                'href' => $this->_baseUrl. '/admin/' .$this->_mainTable. '/delete/' .$item->id,
                'modal' => array(
                    'question' => 'Ban ' .$this->_param_title. ':',
                    'name' => $item->$param_name. '?',
                    'advice_phrase' => '',
                    'btn_confirm' => 'Ban',
                    'class_phrase' => '',
                ),
   
            ),
            'cancel' => array(
                'action' => 'Cancel',
                'title' => 'Cancel',
                'linkClass' => 'actionButton btn btn-danger btn-sm',
                'spanClass' => 'fa fa-times',
            ),
            'delete' => array(
                'action' => 'Delete',
                'title' => 'Delete',
                'linkClass' => 'actionButton btn btn-danger btn-sm',
                'spanClass' => 'fa fa-trash-o',
                'href' => $this->_baseUrl. '/admin/' .$this->_mainTable. '/delete/' .$item->id,
                'modal' => array(
                    'question' => 'Delete ' .$this->_param_title. ':',
                    'name' => $item->$param_name. '?',
                    'advice_phrase' => '',
                    'btn_confirm' => 'Delete',
                    'class_phrase' => '',
                ),
            ),
            'edit' => array(
                'action' => 'Edit',
                'title' => 'Edit',
                'linkClass' => 'actionButton btn btn-default btn-sm',
                'spanClass' => 'fa fa-pencil-square-o',
                'href' => $this->_baseUrl. '/admin/' .$this->_mainTable. '/edit/' .$item->id,
            ),
            'validate' => array(
                'action' => 'Validate',
                'title' => 'Validate',
                'linkClass' => 'actionButton btn btn-success btn-sm',
                'spanClass' => 'fa fa-check',
                'href' => $this->_baseUrl. '/admin/' .$this->_mainTable. '/validate/' .$item->id,
            ),
        );

        $buttons = array();
        foreach ($actions as $action) {
            $buttons[$action] = $allButtons[$action];
        }

        return $buttons;
    }

}
