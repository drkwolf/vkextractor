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
  protected $nodes_by_depty = 10;
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

      $this->resetProgress();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
  public function handle()
  {
    for($i=$this->start ; $i< $this->end; $i++) {
      $this->get_user($i);
      $this->get_friends($i, $this->depth);
      $this->iter++;
      $this->resetProgress();
    }
  }

  public function get_friends($id, $depth)
  {
    if ($depth == 0) return;
    $user = User::where('nt_id', $id)->first();
    $friends = $user->data->friends;
    $totFriends = sizeof($friends['items']);
    $this->setProgress($depth, $totFriends );
    $by_depth = $this->nodes_by_depty;
    foreach ($friends['items'] as $key => $friend) {
      $dt = Carbon::now();
      $fid = $friend['id'];
      dump('iter:'.$this->iter.' size:'.$key.'/'.sizeof($friends['items']).' id: '.$fid.' t: '.$dt->toTimeString());
      $this->dispProgress($depth);
      $this->get_user($fid);
      if($by_depth--) break;
    }
    $user->friends_loaded = true;
    $this->get_foaf($friends, $depth-1);
    $user->save();
  }

  public function get_foaf($friends, $depth) {
    foreach ($friends['items'] as $friend) {
      $fid = $friend['id'];
      if ($depth) $this->setProgress($depth+1, 0, 1);
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

  protected  function setProgress($depth, $totFriends=0, $size =0)
  {
    $this->progress[$depth]['tot'] += $totFriends;
    $this->progress[$depth]['size'] += $size;
  }

  protected function dispProgress($depth) {
    $this->progress[$depth]['current'] += 1;

    $head = range(1, $this->depth);
    $head[$depth-1] .='*';
    foreach($head as $i) echo '| '.str_pad($i, 10);
    echo "|\n";
    foreach($this->progress as $progress) {
    echo '| '.str_pad($progress['current'].'/'.$progress['tot'], 10);
    }
    echo "|\n";
    foreach($this->progress as $progress) {
      echo '| '.str_pad($progress['size'], 10);
    }
    echo "|\n";

  }

  protected  function resetProgress() {
    foreach(range(1,$this->depth) as $i) $this->progress[$i] = ['current'=> 0, 'tot' => 0, 'size' => 0];
  }
}
