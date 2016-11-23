<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{

  protected $casts = [
    'success' => 'array',
    'fails' => 'array',
    'iter' => 'array',
    'results' => 'array'
  ];


  public function computeStat()
  {
    // total size
    // total queries
    //
  }
}
