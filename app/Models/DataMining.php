<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DataMining extends Model
{
  protected $table = 'data_mining';

  protected $casts = [
    'counts' => 'array'
  ];

  public function user() {
    $this->hasOne('Models\User');
  }


  public function populate() {

//    $userData = Data::with('user')->get();
    $inser_counter = 0;
    $attributes =  Schema::getColumnListing($this->getTable());
    $attributes =  array_except($attributes, ['created_at', 'updated_at', 'id', 'user_id']);
    Data::chunk(200, function($userData) use($attributes){
      $inserts = [];
      foreach($userData as $data) {
        $insert = [];
        foreach($attributes as $attribute) {
          $insert[$attribute] = array_has($data['user_info'], $attribute);
        }
        $insert['photo_50'] = !preg_match('/images\/camera/', array_get($data, 'user_info.photo_50'));
        $insert['counts'] = json_encode([
          'friends' => array_get($data, 'friends.count',0),
          'recent' => sizeof($data['friends_recent']),
          'mutual' => sizeof($data['friends_mutual']),
          'lists' => array_get($data, 'friends_lists.count',0 ),
          'followers' => array_get($data,'followers.count',0),
          'subscriptions' => array_get($data, 'subscriptions,count', 0),
          'wall' => array_get($data, 'wall.count',0 ),
          'posts_likes' => array_get($data,'posts_likes.count',0),
          'photos' => !array_get($data, 'photos.count',0),
          'photos_likes' => array_get($data, 'photos_likes.count',0),
          'videos' => array_get($data, 'videos.count',0 ),
          'videos_likes' => array_get($data, 'videos_likes.count', 0),
        ]);
        dump($data['id']);
        $insert['user_id'] = $data->user->id;
        $insert['vk_id'] = $data->user->nt_id;
        dump($data['user']->nt_id);
        $insert['visibility'] = array_get($data, 'user_info.hidden', 0);
//      dd($data['user_info']);
        unset($insert['id']);
        $inserts[] = $insert;
      }
      $this->insert($inserts);
    });


//    if(!empty($inserts)) $this->insert($inserts);
//    $result = json_encode($inserts);
//    $root = storage_path('app/data/datamining.json');
//    File::put($root, $result);
  }
}
