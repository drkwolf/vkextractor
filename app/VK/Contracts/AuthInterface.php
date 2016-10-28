<?php

namespace App\VK\Contracts;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;

interface AuthInterface {

  public function getToken($params);
  public function setClient(GuzzleClientInterface $client = null);
}
