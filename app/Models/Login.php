<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{

    protected $fillable = ['addr', 'user_id'];

    // this belongs to the User Model
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
