<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    //
    protected $fillable = ['text', 'read_by_leader'];

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
