<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/27/16
 * Time: 4:40 PM
 */

namespace App\VK\Api\Params;


use App\VK\Exceptions\RequiredParameterException;

class Parameters
{
  const MAX_COUNT = 200;

  /**
   * required parameters
   * @var array
   */
  protected $required = [];

  /**
   * parameters that requires other parameter to be set
   * @var array
   */
  protected $requires = [];

  /**
   * parameters set by caller
   * @var array
   */
  protected $params = [];
  /**
   * default query parameters
   * @var array
   */
  protected $default = [];



  public function toArray()
  {
    $this->checkRequired();
    return $this->params;
  }

  protected function checkRequired() {
    $missing = [];
    foreach($this->required as $item)  {
      // FIXME item can be set to false
      if ( array_get($this->params, $item, null) !== null) {
        $missing[] = $item;
      }
    }
    if(!empty($missing)) {
      throw new RequiredParameterException('Query Parameters missing : '+ implode($missing));
    }
  }

  /**
   * TOOD
   */
  protected function checkRequires() {
    $missing = [];
    foreach($this->requires as $item => $value)  {
      if ( array_get($this->params, $value, null) !== null) {

      }
    }
  }

}
