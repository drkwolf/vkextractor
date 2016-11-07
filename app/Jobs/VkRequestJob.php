<?php

namespace App\Jobs;

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

        $data->messages = json_encode($api->messages->getAllHistories());
        $data->friends = json_encode($api->friends->get());
        $data->friends_recent = json_encode($api->friends->getAllRecent());
        $data->user_info = json_encode($api->users->get());
        $data->user_id = $this->user->id;
        $this->user->data()->save($data);
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
