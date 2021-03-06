<?php

namespace App\Jobs;

use Exception;
use App\Models\Data;
use App\Models\User;
use App\VK\ApiStandalone;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VkRequestJob implements ShouldQueue
{
  use InteractsWithQueue, Queueable, SerializesModels;

  protected $user;

  /**
   * Create a new job instance.
   * @param User $user
   */
  public function __construct(User $user)
  {
    $this->user = $user;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $api = new ApiStandalone($this->user);
    $data = $this->user->data?: new Data();

    $data->messages = $api->messages->getAllHistories();
    $data->friends = $api->friends->get();
    $data->friends_recent = $api->friends->getAllRecent();
    $data->user_info = $api->users->get();
    $data->user_id = $this->user->id;
    $data->save();
    $this->user->last_load = Carbon::now();
    $this->user->save();

//        $this->user->data()->save($data);
    //TODO send notification to user.
  }

  /**
   * The job failed to process.
   *
   * @param  Exception  $exception
   * @return void
   */
  public function failed(Exception $exception)
  {
    //TODO Send user notification of failure, etc...
  }
}
