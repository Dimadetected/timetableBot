<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = ['id'];
    
    protected $with = ['faculty'];
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function faculty()
    {
        return $this->belongsTo('App\Models\Faculty');
    }
    
    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }
}
