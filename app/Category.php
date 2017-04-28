<?php

namespace App;


use Illuminate\Database\Eloquent\Model;







class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = False;
    protected $table = 'categories';

    public function products() {
        return $this->hasMany(\App\Product::class, 'id_category');
    }
}
