<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    
    public function users_type()
    {
        return $this->belongsTo('App\Models\UsersType');
    }
    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }
    
 
    
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $guarded=['id'];
    
    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
