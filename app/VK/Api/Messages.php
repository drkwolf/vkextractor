<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/26/16
 * Time: 1:54 PM
 */

namespace App\VK\API;


use App\VK\Client;

class Messages extends Client
{
    /**
     * @param offset Offset needed to return a specific subset of messages
     * @param count Number of messages to return (maximum value 200)
     * @param user_id ID of the user whose message history you want to return
     * @param peer_id
     * @param start_message_id Starting message ID from which to return history
     * @param rev Sort order: 1 — return messages in chronological order; 0 — return messages in reverse chronological order
     */
    public function getHistory(Array $params = []){
        $default = [
            'user_id' => '',
            'offset' => '',
            'count' => '',
           'peer_id' => '',
            'start_message_id' => '',
            'rev' => ''
        ];

        $params =  $this->mergeParameters($default, $params);

        return $this->request('messages.getHistory', $params);

    }

    /*
     *  Returns a list of the current user's incoming or outgoing private messages
     * @param out 1 — to return outgoing messages; 0 — to return incoming messages (default)
     * @param offset Offset needed to return a specific subset of messages
     * @param count Number of messages to return Max 200
     * @param time_offset Maximum time since a message was sent, in seconds. To return messages without a time limitation, set as 0
     * @param filters Filter to apply:  1 — unread only; 2 — not from the chat; 4 — messages from friends
     * @param preview_length Number of characters after which to truncate a previewed message. To preview the full message, specify 0
     * @param last_message_id ID of the message received before the message that will be returned last
     */

    public function get(Array $params = []) {
       $default =  [
           'out' => 0,
           'offset' => 0,
           'count' => null
           ,
           'time_offset' => '',
           'filters' => '',
           'preview_length' => '',
           'last_message_id'=> ''
       ];
        $params = $this->mergeParameters($default, $params);
        
        return $this->request('messages.get', $params);
    }


    public function getOutgoing(Array $params = []) {
        $params['out'] = 1;
        return $this->get($params);
    }

    public function getIncoming(Array $params = []) {
        return $this->get($params);
    }

    /**
     * <ul>
     * <li> offset - The offset necessary to sample a subset of the dialogues. integer </li>
     * <li> count - Number of conversations that you want to obtain. positive number, default 20, maximum 200 </li>
     * <li> startMessageId - Message ID from which you want to return a list of the dialogues (see below for details.).
     *      number positive
     * </li>
     * <li> previewLength - The number of characters for which you want to trim a message. Specify 0 if you do not want
     *      to trim a message. (Default message is not truncated). Note - the text is cut according to the exact
     *      number of characters may be different from the specified value. number positive
     * </li>
     * <li> unread - A value of 1 means that you want to return only the dialogues which have unread incoming messages.
     *      Default 0. flag can be set to 1 or 0, available since version 5.14
     * </li>
     * </ul>
     */

    public function getDialogs(Array $params = []) {
        $default = [
            'count' => 20, //default
            'offset' => 0,
            'preview_length' => null,
            'start_message_id' => 0,
            'unread' => 0,
            'important' => 0,
            'unanswered' => 0
        ];

        $params = $this->mergeParameters($default, $params);

        return $this->request('messages.getDialogs', $params);

    }

}