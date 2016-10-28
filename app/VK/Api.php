<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/26/16
 * Time: 7:06 PM
 */

namespace App\VK;


use App\User;
use App\VK\Api\Friends;
use App\VK\Api\Messages;
use App\VK\Api\Params\MessagesGetDiablogsParams;
use App\VK\Api\Params\MessagesGetParams;

class Api
{
    public $friends;
    public $messages;


    public function __construct(User $user) {
        $this->friends = new Friends($user);
        $this->messages = new Messages($user);
    }


    public function getMessagesFrequency() {

    }

    /**
     * return all user messages
     */
    public function getAllMsgs() {
        $in = $this->getMessages(false);
        $out = $this->getMessages(true);
        dd($in->merge($out)->sortby('id'));
    }


    /**
     * return incomme our outgoing messages
     * @param $out 0 for incoming and 1 for outgoing
     * @return \Illuminate\Support\Collection
     */
    public function getMessages(Boolean $out) {
        $params = ['count' => MessagesGetParams::MAX_COUNT, 'offset' => 0, 'out' => (int)$out];
        $msg = $this->messages->get($params);
        $items = collect($msg['items']);

        while($msg['count'] - $items->count() > 0) {
            $params['offset'] += MessagesGetParams::MAX_COUNT;
            $msg = $this->messages->get($params);
            $items->merge($msg['items']);
        }

        return $items;
    }


    public function getAllDialogs() {
        $params = ['count' => MessagesGetDiablogsParams::MAX_COUNT, 'offset' => 0];
        $msg = $this->messages->getDialogs($params);
        $items = collect($msg['items']);

        $a = 0;
        while($msg['count'] - $items->count() > 0) {
            $params['offset'] += MessagesGetDiablogsParams::MAX_COUNT;
            $msg = $this->messages->getDialogs($params);
            dd($msg['items'],$items->all(),  $items->merge($msg['items']), $items->count());
            $items->merge($msg['items']);
            dd($msg['items']);
            print($msg['count'].' '.$items->count() );

            if($a++ > 5 ) dd('merde');
        }

        return $items;
    }

}