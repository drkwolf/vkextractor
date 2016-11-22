<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestUserData extends TestCase
{
  use DatabaseMigrations;

    public function testLoadGroups ()
    {
      $groups = $this->getArray(1,'groups');
//      $members = $this->getArray(1,'groups_memebers');
      $topics = $this->getArray(1,'topics');
      $topics_comments = $this->getArray(1,'topic_comments');

      $groups = $groups['items'];
      $topics = $topics['items'];
      $topics_comments = $topics_comments['items'];

      $insert= [];
      $di = 0;
      $dj = 0;
      $de = 0;
      $attributes =  \Illuminate\Support\Facades\Schema::getColumnListing((new \App\Models\Group())->getTable());
      foreach($groups as $group) {
        $group = array_only($group, $attributes);
        $di++;
        $id = $group['id'];
//        $group_members = $members['items'][$id];
        $group_topics = [];
        $comments = [];
        if(array_has($topics, $id)) {
          $dj++;
          $group_topics = $topics[$id]['items'];
          foreach($group_topics as $gtopic) {
            $tid = $gtopic['id'];
            if(array_get($topics_comments, $tid, false)) {
              $de++;
              $comments[$tid] = $topics_comments[$tid];
            }
          }
        }
        $group['members'] = json_encode([]);
        $group['topics'] = json_encode($group_topics);
        $group['topics_comments'] = json_encode($comments);
        $insert[] = $group;
      }
      dump('groups: '.$di.' topics '.$di.' comments '.$de);
      \App\Models\Group::insert($insert);
    }
}
