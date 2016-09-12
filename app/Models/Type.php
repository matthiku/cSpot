<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    //

    protected $fillable = [
    	'name',
    	'start',
    	'end',
    	'repeat',
    	'leader_id',
    	'resource_id'
	];



    public function plans()
    {
        return $this->hasMany('App\Models\Plan', 'type_id');
    }


    public function default_leader()
    {
    	return $this->belongsTo('App\Models\User', 'leader_id');
    }


    public function default_resource()
    {
    	return $this->belongsTo('App\Models\Resource', 'resource_id');
    }

}
