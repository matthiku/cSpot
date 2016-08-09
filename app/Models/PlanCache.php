<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanCache extends Model
{

    // mass assignement protection
    protected $fillable = ['key', 'value'];

    public function plan() {
        return $this->belongsTo('App\Models\Plan');
    }
}
