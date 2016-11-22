<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
  protected $casts = [
    'members' => 'array',
    'topics' => 'array',
    'topics_comments' => 'array',
    'topics_comments_likes' => 'array',
  ];

  public function insertGroup($groups, $members, $topics, $topics_comments=[])
  {
    $groups = $groups['items'];
    $members = $members['items'];
    $topics = $topics['items'];
    $topics_comments = $topics_comments['items'];

    $insert= [];
      foreach($groups as $group) {
        $id = $group['id'];
//        $group_members = $members[$id];
        $group_topics = [];
        $comments = [];
        if(array_has($topics, $id)) {
          $group_topics = $topics[$id]['items'];
          foreach($group_topics as $gtopic) {
            $tid = $gtopic['id'];
            if(array_get($topics_comments, $tid, false)) {
              $comments[$tid] = $topics_comments[$tid];
            }
          }
        }
//        $group['members'] = $group_members;
        $group['topics'] = $group_topics;
        $group['topics_comments'] = $comments;
        $insert[] = $group;
      }

      $this->insert($insert[]);
    }


  public function getTopicsAttribute($value) {
    return json_decode($value);
  }
  public function setTopicsAttribute($value)
  {
    $this->attributes['topics'] = json_encode($value);
  }
  public function getTopicsCommentsAttribute($value) {
    return json_decode($value);
  }
  public function setTopicsCommentsAttribute($value)
  {
    $this->attributes['topics_comments'] = json_encode($value);
  }
  public function getTopicsCommentsLikesAttribute($value) {
    return json_decode($value);
  }
  public function setTopicsCommentsLikesAttribute($value)
  {
    $this->attributes['topics_comments_likes'] = json_encode($value);
  }
  public function getMembersAttribute($value) {
    return json_decode($value);
  }
  public function setMembersAttribute($value)
  {
    $this->attributes['members'] = json_encode($value);
  }

}
