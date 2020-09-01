<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'type' => 'array'
    ];
    
    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }
  
}
