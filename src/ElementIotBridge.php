<?php

namespace Mainova;
use GuzzleHttp\Client;

class ElementIoTBridge {
  public static function request(String $url, Array $data = null, String $method = 'GET', Array $options = []) {
    $client = new Client($options);

    if ($data != Null) {
      $method = 'POST';
    }

    $a = 0;

    do {
      try {
        $response = $client->request($method, $url, ['json' => $data]);

        return json_decode($response->getBody()->getContents(), false);
      } catch (\GuzzleHttp\Exception\ConnectException $e) {
        $a++;

        usleep(100);

        continue;
      } catch (\GuzzleHttp\Exception\ClientException $e) {
        if ($e->getCode() == 429 && $e->hasResponse()) {
          $response = $e->getResponse();
          
          if ($response->hasHeader('x-ratelimit-reset')) {
            usleep(intval($response->getHeader('x-ratelimit-reset')[0]) + 1);

            return ElementIoTBridge::request($url, $data, $method);
          }
        } else {
          break;
        }
      } catch (\Exception $e) {
        break;
      }
    } while ($a <= 5);

    return null;
  }

  public static function getAll(String $url) {
    return [];
  }

  public static function getStream(String $url) {
    return [];
  }
}
