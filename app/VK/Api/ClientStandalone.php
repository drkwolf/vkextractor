<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/26/16
 * Time: 1:55 PM
 */

namespace App\VK\Api;

use App\VK\Exceptions\AccessDeniedVkException;
use App\VK\Exceptions\AuthorizationFailedVkException;
use App\VK\Exceptions\CaptchaRequiredVkException;
use App\VK\Exceptions\InternalErrorVkException;
use App\VK\Exceptions\PermissionDeniedVkException;
use App\VK\Exceptions\TokenExpiredException;
use App\VK\Exceptions\TooManyRequestsVkException;
use App\VK\Exceptions\TooMuchSimilarVkException;
use App\VK\Exceptions\UnknownErrorVkException;
use App\VK\Exceptions\VkException;

use App\Models\User;

use Carbon\Carbon;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

/**
 * It's important to use this class throut job Queue
 * Class Client
 * @package App\VK
 * //TODO client must support 1. authusers (standalone, OAUTH), App (
 */
class ClientStandalone extends ClientAbstract
{

    /**
     * @var User
     */
    protected $user;

    protected $version;

    public function __construct(User $user,$version = null) {
        parent::__construct($version);

        $this->user = $user;
        $this->setToken($user->nt_token);
        $this->setUserId($user->nt_id);

        if ($this->isTokenExpired()) {
            throw new TokenExpiredException('Token has expired');
        }
    }


    /**
     * check of the Token has been expired
     * @return bool
     */
    protected function isTokenExpired() {
        $now = Carbon::now();
        $diff = $now->diffInSeconds($this->user->updated_at);
        // expires_in should be set
        if ($this->user->expires_in === null) {
         throw new Exception('Expires_in is not set');
        }
        return ((int)$this->user->expires_in < $diff);
    }
}
