<?php

namespace App;


use Illuminate\Database\Eloquent\Model;







class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = False;
    protected $table = 'customers';

    public function orders() {
        return $this->hasMany(\App\Order::class, 'id_customer');
    }
}
