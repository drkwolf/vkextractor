<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Group extends Model
{
    //
  protected $casts = [
    'members' => 'array',
    'topics' => 'array',
    'topics_comments' => 'array',
    'topics_comments_likes' => 'array',
  ];

  /**
   * adapt and insert output from requests with one single query
   * @param $groups
   * @param $members
   * @param $topics
   * @param array $topics_comments
   */
  public function insertGroups($groups, $members, $topics, $topics_comments=[])
  {
    $groups = $groups['items'];
    $members = $members['items'];
    $topics = $topics['items'];
    $topics_comments = $topics_comments['items'];

    $insert= [];

    $attributes =  Schema::getColumnListing($this->getTable());
    foreach($groups as $group) {
      $group = array_only($group, $attributes);
      $id = $group['id'];
      $group_members = $members[$id];
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
      $group['members'] = $group_members;
      $group['topics'] = $group_topics;
      $group['topics_comments'] = $comments;
      $insert[] = $group;
    }

    $this->insert($insert);
  }
}
