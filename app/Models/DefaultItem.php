<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultItem extends Model
{
    //

    protected $fillable = ['type_id', 'seq_no', 'text'];



    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

}
