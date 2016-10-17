<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultItem extends Model
{
    //

    protected $fillable = [
        'type_id',
        'file_id',
        'seq_no',
        'forLeadersEyesOnly',
        'text',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];


    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }


    public function file()
    {
        return $this->belongsTo('App\Models\File');
    }

}
