<?php

namespace Mainova;
use GuzzleHttp\Client;

class ElementIoTBridge {
  public static function request(String $url, Array $data = null, String $method = 'GET') {
    $client = new \GuzzleHttp\Client();
    $response = $client->request($method, $url);

    return $response->getBody();
  }

  public static function getAll(String $url) {
    return [];
  }

  public static function getStream(String $url) {
    return [];
  }
}
