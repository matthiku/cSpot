<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    
    protected $fillable = ['changes', 'reason', 'user_id'];



	// history of plan changes
    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }




	// history of plan changes
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


}
