<?php 

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Social extends Model {


    protected $table = 'social_logins';

    protected $fillable = ['provider', 'social_id'];


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
