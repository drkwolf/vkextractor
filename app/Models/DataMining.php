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


  /**
   * used to populate from data table
   */
  public function populate() {

    Data::chunk(200, function($userData) {
      $inserts = [];
      foreach($userData as $data) {
        $ret = $this->translate_data($data);
        if($ret) $inserts[] = $ret;  // get only active users
      }

      $this->insert($inserts);
    });

  }

  public function translate_data($data) {
    $attributes =  Schema::getColumnListing($this->getTable());
    $attributes =  array_where($attributes, function($value, $key) {
      return !in_array($value, ['created_at', 'updated_at', 'id', 'user_id', 'vk_id']);
    });

    $personals = [ 'political', 'langs', 'religion', 'smoking', 'alcohol', 'live_main', 'people_main', 'inspired_by', ];

    $insert = [];
    $user_info = (array)$data['user_info'];

    if(array_has($user_info, 'deactivated')) return; // skip deactivated

    foreach($attributes as $attribute) {
      $value =array_get($user_info, $attribute, null);
      $insert[$attribute] =  ($value !== null && $value !== "");
    }

    if(array_has($user_info, 'personal')) {
      $p = $user_info['personal'];
      foreach($personals as $key => $personal) {
      $insert[$personal] =  array_has($p, $personal);
      }
    }

    $insert['visibility'] = !array_get($user_info, 'hidden');
//    dump($user_info);
    $insert['photo_50'] = !preg_match('/images\/camera/', array_get($user_info, 'user_info.photo_50'));
    $insert['sex'] = array_get($user_info, 'sex');
    $insert['user_id'] = $data->user->id;
    $insert['vk_id'] = $data->user->nt_id;
    $insert['wall_comments'] = array_get($user_info, 'wall_comments');
    $insert['can_post']= array_get($user_info, 'can_post');;
    $insert['can_see_all_posts']= array_get($user_info, 'can_see_all_posts');;
    $insert['can_see_audio']= array_get($user_info, 'can_see_audio');;
    $insert['can_write_private_message']= array_get($user_info, 'can_write_private_message');;

    $insert['counts'] = [
      'friends'         => array_get($data, 'friends.count',0),
      'recent'          => sizeof($data['friends_recent']),
      'mutual'          => sizeof($data['friends_mutual']),
      'lists'           => array_get($data, 'friends_lists.count',0 ),
      'followers'       => array_get($data, 'followers.count',0),
      'subscriptions'   => array_get($data, 'subscriptions,count', 0),
      'wall'            => array_get($data, 'wall.count',0 ),
      'posts_likes'     => array_get($data, 'posts_likes.count',0),
      'photos'          => array_get($data, 'photos.count',0),
      'photos_likes'    => array_get($data, 'photos_likes.count',0),
      'videos'          => array_get($data, 'videos.count',0 ),
      'videos_likes'    => array_get($data, 'videos_likes.count', 0),
    ];
    $counters = (array)array_get($user_info, 'counters', []);
    $insert['counts'] = array_merge($counters, $insert['counts']);
    $insert['counts'] = json_encode($insert['counts']);
    unset($insert['id']);

    return $insert;
  }

  public function insert_user($data)
  {

//    $data = Data::where('id', $id)->first();
    $insert = $this->translate_data($data);
    if($insert) $this->insert($insert);
  }

  public function export($path=null)
  {
   $data = $this->all()->toArray();
   if($path==null) $path = storage_path('app/data/datam.json');

    file_put_contents($path,json_encode($data));

  }
}
