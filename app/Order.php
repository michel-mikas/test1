<?php

namespace App;


use Illuminate\Database\Eloquent\Model;







class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = False;
    protected $table = 'orders';
    protected $guarded = [];

    public function customer() {
        return $this->belongsTo(\App\Customer::class, 'id_customer');
    }

    public function products() {
        return $this->belongsToMany(\App\Product::class, 'products_orders', 'id_order', 'id_product')->withPivot('quantity');
    }
}
