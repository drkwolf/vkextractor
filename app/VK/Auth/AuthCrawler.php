<?php

namespace  App\VK\Auth;

use Goutte\Client as HttpClient;
use App\VK\Exceptions\AuthorizationFailedVkException;
use Exception;

use App\VK\Auth\AbstractAuth;

/**
 * Authenticate user using Crawler client
 * required email/phone and password
 */
class AuthCrawler extends AbstractAuth {

    protected $crawler;

    public function __construct($scope = '')
    {
        parent::__construct($scope);
        $this->crawler = new HttpClient();
        $this->crawler->setClient($this->http);
    }


    public function getUrl($params = []) {
        $default = collect([
            'client_id' => $this->clientId,
            'scope' => $this->scope,
            'redirect_uri' => static::BLANK_REDIRECT,
            'response_type' => 'token',
            'display' => 'page'
        ]);
        $params = $default->merge($params);
        return AbstractAuth::AUTHORIZE_URI.http_build_query($params->all());
    }

    public function getToken($params) {
        $url = $this->getUrl();

        if(!array_has($params, ['email', 'password'])) {
            throw new Exception('Expecting array with email and pass keys');
        }
        // vk use pass as parameters.
        $params['pass'] = $params['password'];
        unset($params['password']);

        try {
            $form       = $this->crawler->request('GET', $url)
                ->selectButton('Log in')
                ->form($params); // fill vk login form
            $response   = $this->crawler->submit($form);

        } catch(Exception $e) {
            throw new Exception('http request to vk faild', 0,$e);
        }
        $uri = parse_url($response->getUri());
        if(array_has($uri, 'fragment')) {
            parse_str($uri['fragment'], $tokens); // token are in the fragment
        } else {
            throw new AuthorizationFailedVkException('login for '.$params['email'].' faild');
        }

        return $tokens;
    }

}
