<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    // mass assignment protection
	protected $fillable = [
		'date',
		'leader_id',
		'teacher_id',
		'type_id',
		'info',
		'state',
		'changer',
	];
	protected $hidden = [
		'created_at', 'updated_at'
	];
	protected $dates = [
		'date', 'created_at', 'updated_at'
	];
	// protected $dateFormat = 'U';


	// the leader_id points to the id on the users table
	public function leader() 
	{
		return $this->belongsTo('App\Models\User', 'leader_id');
	}

	// the teacher_id points to the id on the users table
	public function teacher() 
	{
		return $this->belongsTo('App\Models\User', 'teacher_id');
	}

	// the type_id points to the id on the types table (the type of service of this plan)
	public function type() 
	{
		return $this->belongsTo('App\Models\Type');
	}


    public function items()
    {
        return $this->hasMany('App\Models\Item');
    }


}
