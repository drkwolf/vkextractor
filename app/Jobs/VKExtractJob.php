<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VKExtractJob implements ShouldQueue
{
  protected $start = 1000;
  protected $end = 101100;
  protected $depth = 7;
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($start=1000, $end=101000, $depth=7)
    {
        $this->start = $start;
      $this->end = $end;
      $this->depth =$depth;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
  public function handle()
  {
    for($i=$this->start ; $i< $this->end; $i++) {
      if(!\App\Models\User::where('nt_id', $i)->exists()) {
        dump('int user_id: '.$i);
        $job = new \App\Jobs\VkOpenJob($i);
        $job->handle();
        $this->get_friends($i, $this->depth);
      }
    }

  }

  public function get_friends($id, $depth)
  {
    if ($depth == 0) return;
    dump('depth '.$depth);
    $user = \App\Models\User::where('nt_id', $id)->first();
    $friends = $user->data->friends;
    foreach ($friends['items'] as $friend) {
      $fid = $friend['id'];
      dump('friends_user_id: '.$fid);
      if(!\App\Models\User::where('nt_id', $fid)->exists()) {
        $this->get_user($fid);
        $this->get_friends($fid, $depth-1);
      }
    }
    $user->friends_loaded = true;
    $user->save();
  }

  public function get_user($id)
  {
    if(!\App\Models\User::where('nt_id', $id)->exists()) {
      $job = new \App\Jobs\VkOpenJob($id);
      $job->handle();
    }
  }
}
