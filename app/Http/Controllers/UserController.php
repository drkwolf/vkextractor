<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserController extends Controller
{

  public function getData() {
    $user = \Auth::user();
    if (!$user->last_load) {
      $data =  [];
    } else {
      $data = $user->data()->first();
    }
    return response()->json(['response' => ['last_load' => $user->last_load, 'data' => $data ] ]);
  }

  public function getUpdate() {
    $user = \Auth::user();
    $this->dispatch(new VkRequestJob($user));
  }

  public function UserNotification() {

  }

}
