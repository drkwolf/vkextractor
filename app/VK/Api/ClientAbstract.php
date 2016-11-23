<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/29/16
 * Time: 5:19 PM
 */

namespace app\VK\Api;

use App\VK\Exceptions\AccessDeniedVkException;
use App\VK\Exceptions\AuthorizationFailedVkException;
use App\VK\Exceptions\CaptchaRequiredVkException;
use App\VK\Exceptions\InternalErrorVkException;
use App\VK\Exceptions\PermissionDeniedVkException;
use App\VK\Exceptions\RequiredParameterException;
use App\VK\Exceptions\TooManyRequestsVkException;
use App\VK\Exceptions\TooMuchSimilarVkException;
use App\VK\Exceptions\UnknownErrorVkException;
use App\VK\Exceptions\UserDeletedOrBannedException;
use App\VK\Exceptions\VkException;

use Carbon\Carbon;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as HttpClient;
use App\VK\Contracts\ClientInterface;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;


abstract class ClientAbstract implements ClientInterface
{
  /**
   * requests by second
   */


  /**
   * @var int request counter
   */
  protected static $counter = 1;
  protected static $fails = 0;


  protected $http;
  protected $version;
  protected $app_type;

  protected $user_id;
  protected $nt_token;

  protected $queryLog;
  protected $failureLog;

  protected $stats = [
    'success' => [],
    'fails' => [],
    'iter' => [],
  ];


  public function __construct($version = null) {
    $this->version = $version? $version : static::API_VERSION;
    $this->http = new HttpClient([
      'base_uri'    => static::API_URI,
      'timeout'     => static::API_TIMEOUT,
      'connect_timeout' => static::CONNECTION_TIMEOUT,
      'http_errors' => false,
      'headers'     => [
        'User-Agent' => 'unifr.ch/vkProject',
        'Accept'     => 'application/json',
      ],
    ]);
    // Query stats logger
    $this->queryLog  = new Logger('queries');
    $handler = new RotatingFileHandler(storage_path('logs/queries.log'), 0, Logger::INFO );
    $this->queryLog->pushHandler($handler);

    // channel loger for failure
    $this->failureLog = new Logger('failure');
    $handler = new RotatingFileHandler(storage_path('logs/failure.log'), 0, Logger::INFO );
    $this->failureLog->pushHandler($handler);
  }

  public function getUserId()
  {
    return $this->user_id;
  }

  /**
   * @param mixed $user_id
   */
  public function setUserId(int $user_id)
  {
    $this->user_id = $user_id;
  }

  public function setToken(String $token)
  {
    $this->nt_token = $token;
  }

  public function getToken()
  {
    if(!isset($this->nt_token)) {
      throw new RequiredParameterException("Parameter access_token is not set");
    }
    return $this->nt_token;
  }

  /**
   * send request to vk api and make sure to don't send more than 3 request/sercond
   *
   * @param String $method vk method
   * @param array $default
   * @param array $params
   * @return array vk resonse
   * @throws VkException
   */
  public function request(String $method, Array $default, Array $params = []) {
    $params = $this->buildParameters($default, $params);

    //prevent TOmany Request Excetion
    if (++static::$counter > static::REQ_BY_S) {
      usleep(static::WAIT_AFTER_REQ); // .7 second
      static::$counter = 1;
    }

    try {
      $time_start = microtime(true);
      $data = $this->http->post($method, $params);
      $length = $data->getheader('content-length');
      $total_time = microtime(true) - $time_start;
      $this->queryLog->info($method, [
        'time' => $total_time, 'counter' => static::$counter,
        'params' => $params,
        'length' => $length
      ]);
      $response = $this->getResponseData($data);
    } catch(TooManyRequestsVkException $e ) {
      $time_start = microtime(true);
      usleep(static::WAIT_AFTER_REQ_ERROR); // .5 second
      $data = $this->http->post($method, $params); //send it again
      $length = $data->getheader('content-length');
      $total_time = microtime(true) - $time_start;
      $response = $this->getResponseData($data);
      $this->queryLog->info($method, [
        'time' => $total_time, 'counter' => static::$counter,
        'params' => $params,
        'length' => $length
      ]);
      $this->failureLog->error('TooMany Queries/s ', ['method' => $method, 'counter' => static::$counter, 'params' => $params]);

      $this->stats['fails'][] = [
        'user_id' => $this->getUserId(),
        'date' => Carbon::now(),
        'exception' => 'TooManyRequests',
        'method' => $method,
        'time' => $total_time,
        'counter' => static::$counter,
      ];
    } catch(AccessDeniedVkException $e) {
      $response = [ 'count' => 0, 'access_denied' => true, 'items' => []];
    } catch(InternalErrorVkException $e) {
      $this->failureLog->error('internal Error', ['method' => $method, 'counter' => static::$fails, 'params' => $params]);
      $this->stats['fails'][] = [
        'user_id' => $this->getUserId(),
        'date' => Carbon::now(),
        'exception' => 'InternalError',
        'method' => $method,
        'time' => $total_time,
        'counter' => static::$counter,
      ];
      if (static::$fails > 3) {
        throw $e;
      } else {
        sleep(5*static::$fails); // increase sleep after each fails
        static::$fails++;
        return $this->request($method, $default, $params);
      }
    } catch (TooMuchSimilarVkException $e) {
      $this->failureLog->error('Too much similar: resend', ['method' => $method, 'counter' => static::$fails, 'params' => $params]);
      $this->stats['fails'][] = [
        'user_id' => $this->getUserId(),
        'date' => Carbon::now(),
        'exception' => 'TooMuchSimilar',
        'method' => $method,
        'time' => $total_time,
        'counter' => static::$counter,
      ];
      $this->request('users.get', [], []); // fake request to reste
      if (static::$fails > 5) {
        throw $e;
      } else {
        sleep(5*static::$fails); // increase sleep after each fails
        static::$fails++;
        return $this->request($method, $default, $params);
      }


      return $this->request($method, $default, $params); // resend
    } catch(VkException $e) {
      $this->failureLog->error('Exception ', ['method' => $method, 'exception' => $e,'counter' => static::$counter,  'params' => $params]);
      throw $e;
    } catch(GuzzleException $e) {
      // TODO serialise obeset then resume after connection comes
      throw $e;
    }

    $this->stats['success'] =[
      'user_id' => $this->getUserId(),
      'date' => Carbon::now(),
      'method' =>$method,
      'time' => $total_time,
      'counter' => static::$counter,
      'length' => $length
    ];

    static::$fails = 0; // fails
    return $response;
  }

  /**
   * @param array $default
   * @param array $params
   * @return array http parameters
   */
  protected function buildParameters(Array $default, Array $params)
  {
    $default['v'] = $this->version;

    $filtered = array_only($params, array_keys($default));
    $collect = collect($default);
    $collect = $collect->merge($filtered);

    // remove empty keys
    $params = $collect->reject(function ($value, $key) {
      return $value === null;
    });

    return [RequestOptions::FORM_PARAMS => $params->all()];
  }


  /**
   * get all user's data (by sending multiple requests)
   *
   * @param callable $callback
   * @param int $max_count max items by request
   * @param array $params VK method's parameters
   * @return array|mixed
   */
  public function getAll(callable $callback, int $max_count, Array $params = [] ) {
    $params['offset'] = isset($params['offset'])? $params['offset']: 0;
    $params['count'] = $max_count;

    $msg = call_user_func_array($callback, [$params]); //send first request
    $items = array_get($msg, 'items', $msg);
    $count = array_get($msg, 'count', sizeof($items));

    $max_iter = $count/$max_count-1; // +1 done
    $time_start = microtime(true);
    for($i=0; $i<$max_iter; $i++) { //some awfull bugs by the api (likes.get item_id=2655 owner_id=5)
      $params['offset'] += $max_count;
      $msg = call_user_func_array($callback, [$params]);
      $items = array_merge($items, $msg['items']);
//      dump($count, $count - sizeof($items) , $params['offset'],'-----');
    }
    $time_total = microtime(true)- $time_start;
    $stats = ['context' => $callback, 'count' => $max_count,
      'iter' => $max_iter, 'time' => $time_total
    ];
    Log::debug('getAll', $stats );
    $this->stats['iter'][] = $stats;

    // count and items size dosen't match allows
    return ['count' => sizeof($items), 'items' => $items];
  }

  public function getStats()
  {
    return $this->stats;
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
      0  => VkException::class,
      1  => UnknownErrorVkException::class,
      5  => AuthorizationFailedVkException::class,
      6  => TooManyRequestsVkException::class,
      7  => PermissionDeniedVkException::class,
      9  => TooMuchSimilarVkException::class,
      10 => InternalErrorVkException::class,
      14 => CaptchaRequiredVkException::class,
      15 => AccessDeniedVkException::class,
      18 => UserDeletedOrBannedException::class,
      212 => AccessDeniedVkException::class,
      // TOOD add other Exeception 18W
    ];

    $exception = isset($map[$code]) ? $map[$code] : $map[0];

    return new $exception($message, $code);
  }
}
