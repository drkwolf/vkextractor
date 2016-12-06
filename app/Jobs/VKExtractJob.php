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
  use InteractsWithQueue, Queueable, SerializesModels;

  protected $start = 1000;
  protected $end = 101100;
  protected $depth = 7;
  protected $progress = [];
  protected $iter = 1;
  protected $command;
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

      foreach(range(1,$depth+1) as $i) $this->progress[$i] = ['current'=> 0, 'tot' => 0];
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
      $this->iter++;
    }
  }

  public function get_friends($id, $depth)
  {
    if ($depth == 0) return;
    dump('depth '.$depth);
    $user = User::where('nt_id', $id)->first();
    $friends = $user->data->friends;
    $totFriends = sizeof($friends['items']);
    foreach ($friends['items'] as $key => $friend) {
      $dt = Carbon::now();
      $fid = $friend['id'];
      dump('iter: '.$this->iter.' depth: '.$depth.' size:'.$key.'/'.sizeof($friends['items']).' id: '.$fid.' t: '.$dt->toTimeString());
      $this->dispProgress($depth, $key, $totFriends,$dt->toTimeString() );
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
        $this->get_user($fid);
      }
      $this->get_friends($fid, $depth);
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

  protected function dispProgress($depth, $current, $totFriends, $time) {
    $this->progress[$depth] = ['current' => $current, 'tot' => $totFriends];
    $head = range(1, $this->depth+1);
    foreach($head as $i) echo '| '.str_pad($i, 10).'|';
    echo "\n";
    foreach($this->progress as $progress) {
    echo '| '.str_pad($progress['current'].'/'.$progress['tot'], 10).'|';
    }
    echo "\n";
  }
}
