<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    //
    protected $fillable = ['text'];

    // Relationships
    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
