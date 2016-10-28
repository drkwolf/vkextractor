<?php

namespace App\Http\Controllers\Social\VK;


use App\User;
use App\VK\Api;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index() {
        $user = Auth::user();
        $api = new Api($user);
        $messages =  $api->messages->getHistory(['count' => 1]);
        return view('social.vk.index');
    }
}
