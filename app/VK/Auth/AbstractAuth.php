<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/25/16
 * Time: 3:18 PM
 */

namespace App\VK\Auth;

use App\VK\Contracts\AuthInterface;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Client as HttpClient;

abstract class AbstractAuth implements AuthInterface
{
    const  BASE_URI = 'https://oauth.vk.com/';
    const BLANK_REDIRECT = 'https://oauth.vk.com/blank.hmtl';
    const AUTHORIZE_URI = 'https://oauth.vk.com/authorize?';
    const TOKEN_URI = 'access_token?client_id=%s&client_secret=%s&redirect_uri=%s&code=%s';
    const USER_AGENT = 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0';
    const TIMEOUT = 30;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @var ClientInterface
     */
    protected $http;

    /**
     * @param $scope String should be sperated by coma
     * @details
     * List of Available Settings of \href{https://vk.com/dev/permissions}{Access Permissions}:
     * \itemize{
     *   \item \strong{friends} Access to friends.
     *   \item \strong{photos} Access to photos.
     *   \item \strong{audio} Access to audios.
     *   \item \strong{video} Access to videos.
     *   \item \strong{docs} Access to documents.
     *   \item \strong{notes} Access to user notes.
     *   \item \strong{pages} Access to wiki pages.
     *   \item \strong{status} Access to user status.
     *   \item \strong{wall} Access to standard and advanced methods for the wall.
     *   \item \strong{groups} Access to user groups.
     *   \item \strong{messages} Access to advanced methods for messaging.
     *   \item \strong{notifications} Access to notifications about answers to the user.
     * }
     */
    public function __construct($scope = '')
    {


        $this->clientId = env('VKONTAKTE_KEY');
        $this->clientSecret = env('VKONTAKTE_SECRET');
        $this->redirectUri = env('VKONTAKTE_REDIRECT_URI');
        $this->scope = $scope;
        $this->setClient();
    }

    public function getUrl($params = []) {
        $default = [
            'redirect_uri' => static::BLANK_REDIRECT,
            'client_id' => $this->clientId,
            'scope' => $this->scope,
            'response_type' => 'token',
            'display' => 'page'
        ];

        return parent::BASE_URL.'?'.http_build_query($params);
    }

    public function setClient(GuzzleClientInterface $client = null) {
        $this->http = $client? $client : $this->http = new HttpClient([
            'base_uri'    => static::BASE_URI,
            'timeout'     => static::TIMEOUT,
            'http_errors' => false,
            'headers'     => [
                'User-Agent' => static::USER_AGENT,
                'Accept'     => 'application/json',
            ],
        ]);
    }

}