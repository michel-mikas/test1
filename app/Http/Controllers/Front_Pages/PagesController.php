<?php

namespace App\Http\Controllers\Front_Pages;

use Hash;
use Session;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{

    public function home(Request $request) {
    	$customers = \App\Customer::all();
        $dataToView = array(
            'customers' => $customers,
        );
        return view('welcome')->with($dataToView);
    }

    public function login_form(Request $request) {
        $guard = $request->segments()[1];
        $dataToView = array(
            'loginTitle' => 'Login as ' .$guard,
            'form_action' => Session::get('lang'). '/' .$guard. '/login/auth',
        );
        return view('login')->with($dataToView);
    }

    public function make_order(Request $request) {
        $customer = \App\Customer::findOrfail($request->id);
        $products = \App\Product::all();
        $dataToView = array(
            'customer' => $customer,
            'products' => $products
        );
        return view('make_order')->with($dataToView);
    }

    public function post_order(Request $request) {
        $qts = $request->quantities;

        /* verificando do lado servidor */

        $this->validate($request, [
            'id_customer' => 'exists:customers,id',
        ]);

        $prod_model = \App\Product::class;
        $qts = array_filter($qts, function($v, $k) use($prod_model) {
            if(is_null($prod_model::find($k)) || !is_numeric($v)) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);

        // tudo ok, adicionar à db

        if(count($qts) < 1)  {
            Session::flash('flash_error', 'Something went wrong with your order');
            return redirect()->back();   
        }


        $total = 0;
        $to_attach = array();
        $prods = array();
        foreach ($qts as $id_prod => $qtd) {
            $prod = $prod_model::find($id_prod);
            $prod->qtd = $qtd;
            $prods[] = $prod;
            $total += round($prod->price * $qtd, 2);
            $to_attach[$id_prod] = array('quantity' => $qtd);
        }

        $customer = \App\Customer::find($request->id_customer);
        $customer->revenue = $customer->revenue + $total;
        $customer->save();

        $new_order = $this->process_order($customer, $prods, $total);
        $new_order->products()->attach($to_attach);
        $offers = $this->get_offer($new_order);
        $msg = 'Yooo nice order: Total: ' .$new_order->total. ' €, Discount: ' .($total - $new_order->total). ' €';
        Session::flash('flash_success', $msg);
        return redirect()->back();

    }

    protected function process_order($customer, $prods, $total) {
        $discount = 0;
        $fields = array();
        $fields['total'] = $total;

        if($customer->revenue > 1000) {
            $fields['total'] -= round($fields['total'] * 0.1, 2);
        }

        // filtrar produtos da categoria 2
        $prods_cat2 = array_where($prods, function ($prod, $key) {
            return $prod->category == 2;
        });
        
        $qtd_cat2 = array_sum(array_column($prods_cat2, 'qtd')); // quantidade de produtos da categoria 2

        if($qtd_cat2 >= 2) {
            $cheapest_prod_val = collect($prods)->min('price');
            $fields['total'] -= round($cheapest_prod_val * 0.4, 2);
        }

        $fields['id_customer'] = $customer->id;
        $fields['date'] = time();
        $order = \App\Order::create($fields);
        return $order;
    }

    protected function get_offer($order) {
        
    }

}