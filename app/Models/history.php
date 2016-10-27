<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class history extends Model
{
    
    protected $fillable = ['changes', ['reason']];



	// history of plan changes
    public function plans()
    {
        return $this->belongsTo('App\Models\Plan');
    }


}
