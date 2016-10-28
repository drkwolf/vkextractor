<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/26/16
 * Time: 10:09 AM
 */

namespace App\VK\Api;


use App\VK\Client;

class Friends extends Client
{

    /**
     * @link [https://vk.com/dev/friends.get] [<friends.get>]
     * @param array $params
     * @return Array
     */
    public function get(Array $params = []) {
        $default = [
            'user_id' => $this->getUserId(),
            'count' => '',
            'order' => '', 'list_id' => '',
            'offset' => '', 'fields' => '',
            'name_case' => '', 'flatten' => FALSE,
        ];

        $params =  $this->mergeParameters($default, $params);
        return $this->request('friends.get', $params);

    }

    /*
     * Returns a list of identifiers of newly added friends of this user
     * @param int $count  default 100 max 10000
     * @return Array
     * @link [https://vk.com/dev/friends.getRecent] [<friends.getRecent>]
     */
    public function getRecents(int $count = 100) {
        return $this->request('friends.getRecent',['count' => $count]);
    }

    public function getAllRecents() {

    }

    /**
     * Returns a list of friends in common identifiers between a pair of users.
     * @param array $params
     * @return Array
     *  <ul>
     * <li> SourceUid - User ID whose friends intersect with a user ID with friends target_uid. If not specified,
     *      it is considered that source_uid ID is the current user. positive number a default user identifier of
     *      the current
     * </li>
     * <li> TargetUid - The user ID with which you want to look for friends in common. number positive </li>
     * <li> TargetUids - List of user IDs, with which you need to look for friends in common. list of positive
     *      numbers, separated by commas
     * </li>
     * <li> Order - The order in which you want to return a list of mutual friends. Valid values: random -
     *      returns friends randomly. line
     * </li>
     * <li> Count - The number of mutual friends that you want to return. (By default - all mutual friends)
     *      positive number
     * </li>
     * <li> Offset - The offset necessary to sample a subset of common friends. number positive </li>
     * </ul>
     * @link [https://vk.com/dev/friends.getMutual] [<friends.getMutual>]
     */
        public function getMutual(Array $params = []) {
            $default = [
                'source_id' => '' , 'target_uid' => '',
                'target_uids' => '', 'order' => '', 'count' => '',
                'offset' => ''
            ];

            $params =  $this->mergeParameters($default, $params);
            return $this->request('friends.getMitual',$params);
        }

}