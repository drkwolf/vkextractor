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
//    'user_info' => 'array',
    'friends' => 'array',
     'friends_recent' => 'array',
     'friends_mutual' => 'array',
     'friends_lists' => 'array',
     'followers' => 'array',
     'subscriptions' => 'array',
     'wall' => 'array',

     'posts' => 'array',
     'posts_reposts' => 'array',
     'posts_likes' => 'array',
     'posts_comments' => 'array',
     'posts_comments_likes' => 'array',

     'photos' => 'array',
     'photos_albums' => 'array',
     'photos_likes' => 'array',
     'photos_comments' => 'array',
     'photos_comments_likes' => 'array',

     'videos' => 'array',
     'videos_likes' => 'array',
     'videos_comments' => 'array',
     'videos_comments_likes' => 'array',

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
