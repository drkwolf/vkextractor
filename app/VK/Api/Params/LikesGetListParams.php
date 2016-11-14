<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/13/16
 * Time: 4:24 PM
 */

namespace App\VK\Api\Params;


class LikesGetListParams extends Parameters
{
  const MAX_COUNT = 1000;

   protected $required = [
      'type' , // comment, photo, audio, video, note
      'owner_id', //required
      'item_id', // required
  ];

   protected $default= [
      'type' => 'post', // comment, photo, audio, video, note
      'owner_id' => null, //required
      'item_id' => null, // required
      'count' => 1, // MAX 1000
      'offset' => 0,
      'filter' => 'likes', // all, copies :  returns information only about users who told their friends about the object
      'friends_only' => 0, // 1 for friends only
      'extended' => 0, // want user id only
    ];


}
