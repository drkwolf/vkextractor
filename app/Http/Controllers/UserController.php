<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class UserController extends Controller
{

    public function getData() {
        $user = \Auth::user();
        $data = $user->data()->first();
        $res = [];
        foreach($data->toArray() as $item => $value) {
            $res[$item] = json_decode($value);
        }
        $res['user_info'] = $res['user_info'][0]; // TODO hack!
        return response()->json(['response' => $res ]);
    }

    public function getUpdate() {
      $user = \Auth::user();
      $this->dispatch(new VkRequestJob($user));
    }

}
