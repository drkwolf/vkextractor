<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 11/9/16
 * Time: 5:46 PM
 */

namespace app\Jobs;


use App\Models\User;

class ScanFriendsJobShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


  public function handle()
  {
    $users = User::where('friends_loaded', false)->get();

    foreach ($users as $user) {
      foreach($user->friends as $friend) {
        if(!User::whereNtId($friend)->exists()) {
          dispatch(new VkOpenJob($friend));
        }
      }
    }
  }
}
