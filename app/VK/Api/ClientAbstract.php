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
use League\Flysystem\Exception;
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
      $response = [ 'count' => 0, 'method' => $method, 'access_denied' => true, 'items' => $params];
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
      if (static::$fails > 5) {
        throw $e;
      } else {
        sleep(5*static::$fails); // increase sleep after each fails
        for($i=0; $i<5*static::$fails; $i++)
          $this->request('users.get', [], []); // fake request to reste

        static::$fails++;
        return $this->request($method, $default, $params);
      }
    } catch(VkException $e) {
      $this->failureLog->error('Exception ', ['method' => $method, 'exception' => $e,'counter' => static::$counter,  'params' => $params]);
      throw $e;
    } catch(GuzzleException $e) {
      // TODO serialise obeset then resume after connection comes
      $this->failureLog->error($e);
      $this->failureLog->error('param '.$params);
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

  public function getAll2($method, $max_count, $params, $max_exec=25) {
    $params['offset'] = isset($params['offset'])? $params['offset']: 0;
    $params['count'] = $max_count;


    try {
      $msg = $this->request($method, $params); //send first request
    } catch (UserDeletedOrBannedException $e) {
      return [ 'deactivated' => true, 'count' => 0, 'items' => []];
    }

    $items = array_get($msg, 'items', $msg);
    $count =  array_get($msg, 'count', sizeof($items));

    $time_start = microtime(true);
//    dump('count: '.$count.', max_iter: '.$max_iter.', execute: '.$execute_count);
    for($offset=$max_count+$params['offset']; $offset<=$count; $offset+=$max_count*$max_exec) { //some awfull bugs by the api (likes.get item_id=2655 owner_id=5)
      $execute = '';
      $remain = ($count-$offset);
      ($max_count*$max_exec >= $remain)? $j_max = ceil($remain/$max_count):$j_max=$max_exec;
      for($j=0; $j<$j_max; $j++) {
        $params['offset'] += $max_count;
        $execute .= 'API.'.$method.'('.json_encode($params, JSON_HEX_QUOT).'),';
      }
//      dump('j_max: '.$j_max.' offset: '.$offset.' offset2:'. $params['offset'].' count: '.$count);
      $resp = $this->request('execute', ['code' => 'return ['.$execute.'];']);

      foreach($resp as $res) {
        $items = array_merge($items, $res['items']);
      }

    }
    $time_total = microtime(true)- $time_start;
    $stats = ['context' => $method, 'count' => $max_count,'time' => $time_total ];
    Log::debug('getAll', $stats );
    $this->stats['iter'][] = $stats;
//    dump($time_total, sizeof($items));

    // count and items size dosen't match allows
    return ['count' => sizeof($items), 'items' => $items];

  }

  /**
   * send execute command for all parameters (25 query/request), check the result for content
   * that exceed count and request the remaining content. this way we limit the number of request
   * for example comments for user 1 : 81 comment exceed max_count while 224 not
   * @param $method
   * @param $max_count
   * @param $params
   * @param int $max_exec
   * @return array
   */
  public function getAll3($method, $max_count, $params, $max_exec=25) {
    $count =  sizeof($params);
    $items = [];

    $time_start = microtime(true);
//    dump('count: '.$count.', max_iter: '.$max_iter.', execute: '.$execute_count);

    for($i=0; $i< $count; $i+=$max_exec) {
      $execute = '';
      $remain = ($count-$i);
      $j_max = ($remain < $max_exec)? $remain:$max_exec;
//      dump('offset: '.$i.' j_max: '.($i+$j_max).' remain: '.$remain.' count: '.$count);
      for($j=0; $j<$j_max; $j++) {
        $param = $params[$i+$j];
        $param['count'] = $max_count;
        $id = array_pull($param, 'id');
        $execute .= '"'.$id.'":API.'.$method.'('.json_encode($param, JSON_HEX_QUOT).'),';
      }

      try {
        $items = $items + $this->request('execute', ['code' => 'return {'.$execute.'};']);
      } catch (UserDeletedOrBannedException $e) {
        $items2 = [];
        for($j=0; $j<$j_max; $j++) {
          $param = $params[$i+$j];
          $param['count'] = $max_count;
          $id = array_pull($param, 'id');
          try {
            $items2[$id] = $this->request($method, $param);
          } catch (UserDeletedOrBannedException $e) {
            Log::info('user banned id : '.$id);
          }
        }
        $items = $items + $items2;
      }

    }

    $id =0;
    foreach ($items as $key => $item) {
      if(array_get($item, 'count', 0) > $max_count) {
        $param = $params[$id];
        $param['offset'] = $max_count;
        $result = $this->getAll2($method,$max_count,$param);
        $items[$key]['items'] = $item['items'] + $result['items'];
//        dump($item['count'].' '.sizeof($items[$key]['items']).' '. sizeof($result['items']));
      }
      $id++;
    }

    if(array_has($items, 'items')) {
      return $items;
    } else {
      return ['count' => sizeof($items), 'items' => $items];
    }
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
