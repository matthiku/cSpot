<?php

namespace App\Models;

//use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword;


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
    protected $fillable = ['first_name', 'last_name', 'email', 'password'];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];





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





    public function getFullName() 
    {
        return $this->first_name.' '.$this->last_name;
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
     * Set the password attribute.
     *
     * @param string $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
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
            if($role->name == $name) return true;
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
        if ( $this->id==1 && $role->id<4 )
        {
            return false;
        }
        return $this->roles()->detach($role);
    }


    /**
     * Get the user's Roles
     * TODO
     * public function getRoles()
     */

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

    
}