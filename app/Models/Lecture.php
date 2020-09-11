<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    protected $guarded = ['id'];

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }
}
