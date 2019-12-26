<?php

/**
 * scope ======================
 *   active
 *   archived
 * 
 * accessor ===================
 *   full_name
 * 
 * mutator ====================
 *   email
 * 
 * relation ===================
 *   posts
 *   role (does not defined yet)
 */

namespace Nojiri1098\Annotate;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeActive()
    {
        //
    }

    public function getFullNameAttribute()
    {
        //
    }

    public function setPhoneNumberAttribute()
    {
        //
    }

    public function posts()
    {
        return $this->hasMany(User::class);
    }

    public function scopeArchived()
    {
        //
    }
}
