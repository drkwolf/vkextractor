<?php

namespace App\Jobs;

use App\Models\DataMining;
use App\Models\Group as UserGroup;
use App\Models\Stat as UserStat;
use App\Models\Data as UserData;
use App\Models\User;
use App\VK\ApiStandalone;
use App\VK\ApiWithNoToken;
use App\VK\AppTypes;
use App\VK\Auth\AuthCrawler;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Exception;

class VkOpenJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

  protected $user_id;

  /**
   * @var  App\VK\ApiStandalone;
   */
  protected $api;

  protected $api2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $user_id)
    {
      $this->user_id = $user_id;
      $auth = new AuthCrawler('messages,wall,friends,likes,photos,audio,video,pages,notes,groups,board,polls');
      $credential = ['email' => env('VK_EMAIL'), 'password' => env('VK_PASS')];
      $vkToken = $auth->getToken($credential);

      $user = new User();
      $user->valuesFromTokenResponse($vkToken);
      $this->api = new ApiStandalone($user);
      $this->api2 = new ApiWithNoToken(0);
    }

  /**
   * get hidden field with annonyme access
   * @param $id
   * @return bool
   */
    private function get_userInfo() {
      $params = [ 'user_ids' => $this->user_id ];
      $result = $this->api->users->get($params);
      $params['fields'] = '';
      $result2 = $this->api2->users->get($params);
      $result['hidden'] = (int)array_has($result2, 'hidden');
      return  $result;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      // create user
      $User = User::firstOrNew(['nt_id' => $this->user_id, 'app_type' => AppTypes::OPEN]);
      $DataM = new DataMining();

      $api = $this->api;

      Log::info('VK extracting user id' . $this->user_id, ['context' => 'VKOPENJOB']);

      $Data = new UserData();
//      $Group = new UserGroup();
      $Stat = new UserStat();
      // prevent from search existing groups
      $params = ['user_ids' => $this->user_id];
      $Data->user_info = $this->get_userInfo();

      $params = ['user_id' => $this->user_id];
      if (!array_has($Data->user_info, 'deactivated')) {
        $Data->subscriptions = $api->users->getAllSubscriptions($params);
        $Data->followers = $api->users->getAllFollowers($params);
        $Data->friends = $api->friends->get($params + ['fields' => ['deactivated']]);
        $Data->friends_recent = $api->friends->getRecent($params);
        $Data->friends_lists = $api->friends->getLists($params);
        //TOOD add friends.getList

        $params = ['source_uid' => $this->user_id];
        $Data->friends_mutual = $api->friends->getAllMutual($params, $Data->friends);

        // Wall
        $params = ['owner_id' => $this->user_id];
        $Data->wall = $api->wall->getAllWall($params);
        // need to be fixed
        $Data->posts_reposts = $api->wall->getAllRepostsFromWall($Data->wall);
        $Data->posts_likes = $api->wall->getAllLikesFromWall($Data->wall);
//        $Data->posts_comments = $api->wall->getAllCommentsFromWall($Data->wall);
//        $Data->posts_comments_likes = $api->wall->getLikesFromComments($Data->posts_comments, $owner_id = $this->user_id);

        // photo
        $Data->photos = $api->photos->getAllPhotos($params);
        $Data->photos_albums = $api->photos->getAlbums($params);
        $Data->photos_likes = $api->photos->getAllLikesFromPhoto($Data->photos_albums);
//        $Data->photos_comments = $api->photos->getAllCommentsFromAlbums($Data->photos_albums);
//        $Data->photos_comments_likes = $api->photos->getLikesFromComments($Data->photos_comments, $owner_id = $this->user_id);

        // Videos
        $Data->videos = $api->videos->getAllVideos($params);
        $Data->videos_likes = $api->videos->getLikesFromVideos($Data->videos);
//        $Data->videos_comments = $api->videos->getAllCommentsFromVideos($Data->videos);
//        $Data->videos_comments_likes = $api->videos->getLikesFromComments($Data->videos_comments, $owner_id = $this->user_id);

        // get wall comment likes
//        $existing_g = $Group->all('id')->pluck('id')->toArray();
//        $groups = $api->groups->getAllGroups($params);
//
//        $groups_members = $api->groups->getMembersFromGroups($groups, $existing_g);
//
//        // Boards
//        $topics = $api->boards->getTopicsFromGroup($groups);
//        $topics_comments = $api->boards->getCommentsFromTopics($topics);
//        // FIXME result are irrelevant with the count of likes in the comments
//        $Group->topics_comments_likes = $api->likes->getLikesFromComments($Group->topics_comments,
//          $owner_id=$this->user_id, $type='topic_comment');
//        $Group->insertGroups($groups, $groups_members, $topics, $topics_comments); // TODO implemt
        Log::info('VK extracting user id' . $this->user_id . ' End', ['context' => 'VKOPENJOB']);
      }

      // save stats
      // TODO calculate results

      try {
        $User->last_load = Carbon::now();
        $User->save();
        $User->data()->save($Data);
        $DataM->insert_user($User->data);
        $Stat->user_id = $User->id;
        $Stat->update($api->client->getStats());
      } catch (\Exception $e) {
        dd($e->getMessage());
      }
    }

    public function insert_hidden() {
      $ids = User::all('nt_id')->pluck('nt_id')->toArray();
      $infos = $this->api->users->getAll($ids);
      foreach($infos as $info) {
        $user = User::where('nt_id', $info['id'])->first();
        $data = $user->data()->first();
        if($data) {
          $user_info = get_object_vars($data->user_info);
          $data->user_info = $info + ['hidden' => $user_info['hidden']];
          $data->save();
        } else {
          dump($info['id'].' not found');
        }
      }
    }
}
