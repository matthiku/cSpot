<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ItemNote extends Model
{

    // fields that users can modify
    protected $fillable = ['text', 'user_id'];


    // relationship to items table
    public function item()
    {
        return $this->belongsTo('App/Models/Item');
    }


    // relationship to users table
    public function user()
    {
        return $this->belongsTo('App/Models/User');
    }

}
