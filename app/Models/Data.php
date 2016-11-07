<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 */
class Data extends Model
{

    protected $fillable = ['friends', 'friends_recent', 'messages', 'user_info'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
