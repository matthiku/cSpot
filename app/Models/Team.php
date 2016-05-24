<?php

# (C) 2016 Matthias Kuhs, Ireland

# Team is actually a list of team members belonging to a plan and having a certain role in that plan

namespace App\Models;

use App\Models\User;
use App\Models\Role;
use App\Models\Plan;

use Illuminate\Database\Eloquent\Model;



class Team extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_team';


    /**
     *   team members of a plan are being requested 
     *   by the leader and confirmed by the member
     */
    protected $fillable = [
        'available',
        'requested',
        'confirmed',
        'user_id',
        'role_id',
        'comment',
    ];



    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }


    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }


}


