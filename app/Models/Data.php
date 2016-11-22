<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 */
class Data extends Model
{

  protected $fillable = ['friends', 'friends_recent', 'messages', 'user_info'];

  protected $hidden = ['id', 'user_id'];

   protected $casts = [
    'friends' => 'array',
//    'user_info' => 'array',
     'friends_recent' => 'array',
     'friends_mutual' => 'array',
    'messages' => 'array',
  ];
  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }

//  public function getFriendsAttribute($value) {
//    return json_decode($value);
//  }
//  public function setFriendsAttribute($value)
//  {
//    $this->attributes['friends'] = json_encode($value);
//  }
//
//  public function setFriendsRecentAttribute($value) {
//    $this->attributes['friends_recent'] = json_encode($value);
//  }
//  public function getFriendsRecentAttribute($value) {
//    return json_decode($value);
//  }
//
//  public function setMessagesAttribute($value) {
//    $this->attributes['messages'] = json_encode($value);
//  }
//  public function getMessagesAttribute($value) {
//    return json_decode($value);
//  }

   public function setUserInfoAttribute($value) {
     if(isset($value[0]))
       $this->attributes['user_info'] = json_encode($value[0]);
     else
       $this->attributes['user_info'] = json_encode($value);
  }

  public function getUserInfoAttribute($value) {
    return json_decode($value);
  }

}
