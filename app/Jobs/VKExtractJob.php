<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
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
      dump('progess:'.$i/$this->end.'% int user_id: '.$i);
      $this->get_user($i);
      $this->get_friends($i, $this->depth);
    }
  }

  public function get_friends($id, $depth)
  {
    if ($depth == 0) return;
    dump('depth '.$depth);
    $user = User::where('nt_id', $id)->first();
    $friends = $user->data->friends;
    foreach ($friends['items'] as $key => $friend) {
      $dt = Carbon::now();
      $fid = $friend['id'];
      dump('size:'.$key.'/'.sizeof($friends['items']).' id: '.$fid.' t: '.$dt->toTimeString());
      $this->get_user($fid);
    }
    $user->friends_loaded = true;
    $this->get_foaf($friends, $depth-1);
    $user->save();
  }

  public function get_foaf($friends, $depth) {
    foreach ($friends['items'] as $friend) {
      $fid = $friend['id'];
      dump('size:'.sizeof($friends).' id: '.$fid);
      if(!User::where('nt_id', $fid)->exists()) {
        $this->get_friends($fid, $depth);
      }
    }
  }

  public function get_user($id)
  {
    if(!User::where('nt_id', $id)->exists()) {
      $job = new \App\Jobs\VkOpenJob($id);
      $job->handle();
    } else {
      dump('user_id '.$id.' exists');
    }
  }
}
