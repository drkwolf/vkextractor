<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DataMining extends Model
{
  protected $table = 'data_mining';

  public function user() {
    $this->hasOne('Models\User');
  }


  public function populate() {
    $attributes =  Schema::getColumnListing($this->getTable());
    $attributes =  array_except($attributes, ['created_at', 'updated_at', 'id', 'user_id']);

    $userData = Data::with('user')->get();
    $inserts = [];
    foreach($userData as $data) {
      $insert = [];
      dd($data['user']->id);
      foreach($attributes as $attribute) {
        $insert['user_id'] = $data['user']->nt_id;
        $insert[$attribute] = array_has($data['user_info'], $attribute);
        $insert['counts'] = [
          'friends' => $data['friends']['count'],
          'recent' => sizeof($data['friends_recent']),
          'mutual' => sizeof($data['friends_mutual']),
          'lists' => array_get($data, 'friends_lists.count',0 ),
          'followers' => array_get($data,'followers.count',0),
          'subscriptions' => array_get($data, 'subscriptions,count', 0),
          'wall' => array_get($data, 'wall.count',0 ),
          'posts' => array_get($data, 'posts.count', 0),
          'posts_likes' => array_get($data,'posts_likes.count',0),
          'photos' => array_get($data, 'photos.count',0),
          'photos_likes' => array_get($data, 'photos_likes.count',0),
          'videos' => array_get($data, 'videos.count',0 ),
          'videos_likes' => array_get($data, 'videos_likes.count', 0),
        ];
        unset($insert['id']);
      }
      $inserts[] = $insert;
    }

    $root = storage_path('app/data/datamining.json');
    $result = json_encode($inserts);
    File::put($root, $result);
    return $inserts;

  }
}
