<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/28/16
 * Time: 6:41 PM
 */

namespace App\VK\Api;


use App\VK\Client;

class Users extends Client
{

    /**
     * @param array $params
     * @link [https://vk.com/dev/users.get]
     * @link [https://vk.com/dev/fields]
     */
    public function get(Array $params = []) {
        $default = [
            'user_ids' => null,
            'fields' => ['sex', 'city', 'country', 'universities','verified', 'home_town', 'education', 'universities',
                            'schools', 'common_count', 'personal', 'blacklisted'],
            'name_case' => 'nom',
        ];

        $params =  $this->mergeParameters($default, $params);
        return $this->request('users.get', $params);
    }

}