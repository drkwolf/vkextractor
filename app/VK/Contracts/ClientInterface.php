<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/29/16
 * Time: 5:08 PM
 */

namespace app\VK\Contracts;


interface ClientInterface
{
    const API_URI = 'https://api.vk.com/method/';
    const API_VERSION = '5.59';
    const API_TIMEOUT = 30.0;

    const REQ_BY_S = 3;
    const WAIT_AFTER_REQ = 500000; // .5s
    const WAIT_AFTER_REQ_ERROR = 1000000; // 1s


    /**
     * token  need by some requests
     * @param $params
     * @return String Token
     */
    public function getToken();
    public function setToken(String $token);
    public function getUserId();
    public function setUserId(int $user_id);

}