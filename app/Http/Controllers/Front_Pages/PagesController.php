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

        $new_order = $this->process_order($customer, $prods, $total);
        $this->get_offers_and_attach($new_order, $prods, $to_attach);

        $customer->revenue = $customer->revenue + $new_order->total;
        $customer->save();

        $msg = 'Yooo nice order: Total: ' .$new_order->total. ' €, Discount: ' .($total - $new_order->total). ' €';
        Session::flash('flash_success', $msg);
        return redirect()->back();

    }

    // quantidade de produtos de uma determinada categoria
    protected function get_prods_from_cat($prods, $id_cat) {
        $prods_from_cat = array_filter($prods, function($prod, $key) use($id_cat) {
            return $prod->category->id == $id_cat;
        }, ARRAY_FILTER_USE_BOTH);

        return $prods_from_cat;
    }

    protected function process_order($customer, $prods, $total) {
        $discount = 0;
        $fields = array();
        $fields['total'] = $total;

        if($customer->revenue > 1000) {
            $fields['total'] -= round($fields['total'] * 0.1, 2);
        }

        // filtrar produtos da categoria 1
        $prods_cat1 = $this->get_prods_from_cat($prods, 1);
        
        $qtd_cat1 = array_sum(array_column($prods_cat1, 'qtd')); // quantidade de produtos da categoria 1

        if($qtd_cat1 >= 2) {
            $cheapest_prod_val = collect($prods)->min('price');
            $fields['total'] -= round($cheapest_prod_val * 0.2, 2);
        }

        $fields['id_customer'] = $customer->id;
        $fields['date'] = time();
        $order = \App\Order::create($fields);

        return $order;
    }

    protected function get_offers_and_attach($order, $prods, $to_attach) {
        // filtrar produtos da categoria 2
        $prods_cat2 = $this->get_prods_from_cat($prods, 2);
        
        $qtd_cat2 = array_sum(array_column($prods_cat2, 'qtd')); // quantidade de produtos da categoria 1

        if($qtd_cat2 >= 5) {
            $prod_offer = $prods_cat2[array_rand($prods_cat2)]; // escolher aleatoriamnete produto da categoria 2 para oferta
            $to_attach[$prod_offer->id]['quantity'] += 1;
        }
        $order->products()->attach($to_attach);
    }

}