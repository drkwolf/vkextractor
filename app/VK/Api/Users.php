<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/28/16
 * Time: 6:41 PM
 */

namespace App\VK\Api;


use App\VK\Api\ClientAbstract;

class Users
{
    protected $client;

    public function __construct(ClientAbstract $client) {
        $this->client = $client;
    }

    /**
     * @param array $params
     * @link [https://vk.com/dev/users.get]
     * @link [https://vk.com/dev/fields]
     */
    public function get(Array $params = []) {
        $default = [
            'user_ids' => null,
            'fields' => ['sex', 'city', 'photo_50', 'country', 'universities','verified', 'home_town', 'education', 'universities',
                            'schools', 'common_count', 'personal', 'blacklisted'],
            'name_case' => 'nom',
        ];

        return $this->client->request('users.get', $default, $params);
    }

}