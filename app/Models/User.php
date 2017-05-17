<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use App\Models\Plan;
use Auth;

use Cmgmyr\Messenger\Traits\Messagable;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

//use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Auth\Passwords\CanResetPassword;
//use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


class User extends Authenticatable //implements Authenticatable //, Contract, CanResetPasswordContract
{

    use Notifiable, Messagable; // Authenticatable; //, CanResetPassword;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'notify_by_email',
        'startPage',
        'last_login',
        'last_access',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $dates = [
        'last_login', 'created_at', 'updated_at', 'last_access'
    ];



    /**
     * Relationship to Logins table
     */
    public function logins()
    {
        return $this->hasMany('App\Models\Login');
    }

    /**
     * Relationship to Social table
     */
    public function social()
    {
        return $this->hasMany('App\Models\Social');
    }

    /**
     * Relationship to the Plan model
     *
     * Each plan has a leader and a teacher.
     * A user can "own" many plans as leader or teacher!
     */
    public function plans_as_leader()
    {
        return $this->hasMany('App\Models\Plan', 'leader_id');
    }
    public function plans_as_teacher()
    {
        return $this->hasMany('App\Models\Plan', 'teacher_id');
    }

    /**
     * Relationship with the Item Notes
     */
    public function itemNotes()
    {
        return $this->hasMany('App\Models\ItemNote');
    }

    /**
     * Relationship with the History records
     */
    public function histories()
    {
        return $this->hasMany('App\Models\History');
    }

    public function notes()
    {
        return $this->hasMany('App\Models\Note');
    }

    /**
     * Many-to-many relationship with instruments table
     */
    public function instruments()
    {
        return $this->belongsToMany('App\Models\Instrument')->withTimestamps();
    }




    public function hasInstrument($name)
    {
        foreach($this->instruments as $instrument)
        {
            if ( strtolower($instrument->name) == strtolower($name) ) return true;
        }

        return false;
    }

    public function assignInstrument($instrument)
    {
        // Don't assign the same instrument again...
        if ($this->hasInstrument($instrument->name)) { return; }
        // link to the new instrument
        return $this->instruments()->attach($instrument);
    }

    public function removeInstrument($instrument)
    {
        if ( $this->id==1 && $instrument->id<4 )
        {
            return false;
        }
        return $this->instruments()->detach($instrument);
    }






    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }



    public function getLastLoginIpAttribute()
    {
        // get all logins from this user
        $logins = $this->logins;
        if ($logins->count())
            return $logins->last()->addr; // return only the newest
        return 'n/a';
    }



    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->token = str_random(30);
        });
    }



    /**
     * With field first_name also fill the field 'name' for backwards-compatibility!
     *
     * @param string $name
     */
    public function setFirstNameAttribute($first_name)
    {
        $this->attributes['first_name'] = $first_name;
        $this->attributes['name'] = $first_name;
    }






    /**
     * Confirm the user.
     *
     * @return void
     */
    public function confirmEmail()
    {
        $this->verified = true;
        $this->token = null;
        $this->save();
    }





    // see https://tuts.codingo.me/laravel-social-and-email-authentication

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role')->withTimestamps();
    }


    public function hasRole($name)
    {
        foreach($this->roles as $role)
        {
            if ( strtolower($role->name) == strtolower($name) ) return true;
        }

        return false;
    }


    public function assignRole($role)
    {
        // Don't assign the same role again...
        if ($this->hasRole($role->name)) { return; }
        // link to the new role
        return $this->roles()->attach($role);
    }


    public function removeRole($role)
    {
        // do not allow a user to dismember from higher rights himself, unless another admin does it
        if ( $this->id==Auth::user()->id && $role->id<4 )
        {
            return false;
        }
        return $this->roles()->detach($role);
    }



    /**
     * Check if a user has rights to modify a plan
     * either with Author or higher role
     * or as leader or teacher of a plan
     */
    public function ownsPlan($plan_id)
    {
        if ( Auth::user()->isAuthor() ) return true;

        // find the Plan
        $plan = Plan::find($plan_id);
        if ( $this->id==$plan->leader_id || $this->id==$plan->teacher_id ) {
            return true;
        }
        return false;
    }




    /**
     * Define various access rights levels
     * (highest to lowest)
     */
    public function isAdmin()
    {
        return $this->hasRole('administrator') ;
    }
    public function isEditor()
    {
        return $this->hasRole('administrator') || $this->hasRole('editor') ;
    }
    public function isAuthor()
    {
        return $this->hasRole('administrator') || $this->hasRole('editor') || $this->hasRole('author');
    }
    public function isLeader()
    {
        return $this->hasRole('administrator') || $this->hasRole('editor') || $this->hasRole('author') || $this->hasRole('leader');
    }
    public function isMusician()
    {
        return $this->hasRole('administrator') || $this->hasRole('editor') || $this->hasRole('author') || $this->hasRole('leader') || $this->hasRole('musician');
    }
    public function isUser()
    {
        return $this->hasRole('administrator') || $this->hasRole('editor') || $this->hasRole('author') || $this->hasRole('leader') || $this->hasRole('musician') || $this->hasRole('user');
    }

    // we need this in order to not show certain content that is only of interest for musicians
    public function hasMusician()
    {
        return $this->hasRole('musician') || $this->hasRole('lead musician');
    }

}
