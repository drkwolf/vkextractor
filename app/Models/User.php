<?php

namespace App\Models;

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
        'name', 'email', 'password', 'nt_token', 'expires_in', 'nt_id', 'nt_pass', 'app_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'nt_pass'
    ];

    public function data()
    {
        return $this->hasOne('App\Models\Data');
    }

  public function valuesFromTokenResponse($values, $credentials = null) {
    $this->nt_id = $values['user_id'];
    $this->nt_token = $values['access_token'];
    $this->expires_in = $values['expires_in'];
    $this->nt_pass = array_get($credentials, 'password');
  }

}
