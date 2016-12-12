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
        'resource_id',
        'weekday',
    	'subtitle',
        'generic',
	];



    public function plans()
    {
        return $this->hasMany('App\Models\Plan', 'type_id');
    }


    public function defaultItems()
    {
        return $this->hasMany('App\Models\DefaultItem', 'type_id');
    }


    public function default_leader()
    {
    	return $this->belongsTo('App\Models\User', 'leader_id');
    }


    public function default_resource()
    {
    	return $this->belongsTo('App\Models\Resource', 'resource_id');
    }


    public function getWeekdayNameAttribute()
    {
        $value = $this->weekday;
        $weekdays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        if ( is_numeric($value) && $value >= 0 && $value < 7)
            return $weekdays[$value];

        return '-';
    }

}
