<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/26/16
 * Time: 1:55 PM
 */

namespace App\VK;


use App\User;
use App\VK\Exceptions\TokenExpiredException;
use App\VK\Exceptions\TooManyRequestsVkException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

/**
 * It's important to use this class throut job Queue
 * Class Client
 * @package App\VK
 */
class Client
{
    const API_URI = 'https://api.vk.com/method/';
    const API_VERSION = '5.59';
    const API_TIMEOUT = 30.0;

    /**
     * VK has limitation of 3 requests by seconds
     * @var int counter for the number of requests
     */
    public static $counter = 0;

    /**
     * @var User
     */
    protected $user;

    protected $http;

    protected $version;

    protected $friends;
    protected $messages;
    protected $httpClient;




    public function __construct(User $user, $version = null) {
        $this->user = $user;

        if ($this->isTokenExpired()) {
            throw new TokenExpiredException('Token has expired');
        }
        $this->version = $version? $version : static::API_VERSION;
        $this->http = new HttpClient([
            'base_uri'    => static::API_URI,
            'timeout'     => static::API_TIMEOUT,
            'http_errors' => false,
            'headers'     => [
                'User-Agent' => 'unifr.ch/vkProject',
                'Accept'     => 'application/json',
            ],
        ]);
    }

    /**
     * send request to vk api and make sure to don't send more than 3 request/sercond
     *
     * @param String $method vk method
     * @param array $params
     * @return array vk resonse
     */
    protected function request(String $method, Array $params) {
        $params = $this->buildParameters($params);

        //prevent TOmany Request Excetion
        if (++static::$counter > 2) {
            usleep(500000); // .5 second
            static::$counter = 0;
        }

        try {
            $response = $this->http->post($method, $params);
            return $this->getResponseData($response);
        } catch(TooManyRequestsVkException $e ) {
            sleep(1); // .5 second
            $response = $this->http->post($method, $params); //send it again
            return $this->getResponseData($response);
        }
    }

    /**
    * @param array $params
    * @return array
    */
    private function buildParameters($params) {
        $params['v'] = $this->version;
        $params['access_token'] = $this->getToken();

        return [RequestOptions::FORM_PARAMS => $params];
    }


    protected function getToken() {
        return $this->user->vk_token;
    }

    protected function getUserId() {
        return $this->user->vk_id;
    }

    /**
     * check of the Token has been expired
     * @return bool
     */
    protected function isTokenExpired() {
        $now = \Carbon\Carbon::now();
        $diff = $now->diffInSeconds($this->user->updated_at);
        return ((int)$this->user->expires_in < $diff);
    }


    /**
     * @param ResponseInterface $response
     * @return array
     * @throws VkException
     */
    protected function getResponseData(ResponseInterface $response)
    {
        $data = json_decode((string)$response->getBody(), true);

        $this->checkErrors($data);

        return $data['response'];
    }

    /**
     * @param $data
     * @throws VkException
     */
    protected function checkErrors($data)
    {
        if (isset($data['error'])) {
            throw self::toException($data['error']);
        }

        if (isset($data['execute_errors'][0])) {
            throw self::toException($data['execute_errors'][0]);
        }
    }

    /**
     * @param array $error
     * @return VkException
     */
    public static function toException($error)
    {
        $message = isset($error['error_msg']) ? $error['error_msg'] : '';
        $code = isset($error['error_code']) ? $error['error_code'] : 0;

        $map = [
            0  => Exceptions\VkException::class,
            1  => Exceptions\UnknownErrorVkException::class,
            5  => Exceptions\AuthorizationFailedVkException::class,
            6  => Exceptions\TooManyRequestsVkException::class,
            7  => Exceptions\PermissionDeniedVkException::class,
            9  => Exceptions\TooMuchSimilarVkException::class,
            10 => Exceptions\InternalErrorVkException::class,
            14 => Exceptions\CaptchaRequiredVkException::class,
            15 => Exceptions\AccessDeniedVkException::class,
        ];

        $exception = isset($map[$code]) ? $map[$code] : $map[0];

        return new $exception($message, $code);
    }

    /**
     * @param Array $collect
     * @param array $params
     * @deprecated
     */
    protected function mergeParameters(Array $default, Array $params) {
        $filtred = array_only($params, array_keys($default));
        $collect = collect($default);
        $params = $collect->merge($filtred);

        return $params->all();
    }


}