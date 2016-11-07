<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 */
class Data extends Model
{

  protected $fillable = ['friends', 'friends_recent', 'messages', 'user_info'];

  protected $hidden = ['id', 'user_id'];

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }

  public function getFriendsAttribute($value) {
    return json_decode($value);
  }
  public function getFriendsRecentAttribute($value) {
    return json_decode($value);
  }


  public function getMessagesAttribute($value) {
    return json_decode($value);
  }

  public function getUserInfoAttribute($value) {
    $data = json_decode($value);
    return $data[0] ; // FIXME HACK
  }
}
