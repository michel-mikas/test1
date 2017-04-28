<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;






class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = False;
    protected $table = 'products';
    protected $guarded = [];

    use SoftDeletes;

    public function orders() {
        return $this->belongsToMany(\App\Order::class, 'products_orders', 'id_product', 'id_order')->withPivot('quantity');
    }

    public function category() {
        return $this->belongsTo(\App\Category::class, 'id_category');
    }
}
