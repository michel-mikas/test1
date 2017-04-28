<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;







class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = False;
    protected $table = 'users';
    protected $guarded = [];
    protected $dates = ['deleted_at'];

    public function updated_by() {
        return $this->belongsTo('App\Admin', 'id_admin');
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $hidden = [
        'password', 'remember_token',
    ];
}
