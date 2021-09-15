<?php

namespace Mainova;
use GuzzleHttp\Client;

class ElementIoTBridge {

  /**
 * Send a request to Element-IoT with optional HTTP Data body
 * @param {String} url API URL of Element-IoT
 * @param {Object} data Optional POST Data
 * @param {String} method HTTP method to use for the request
 * @param {Array} options Guzzle Client Options used for the request
 * @returns {Object} URL return data or null
 */
  public static function request(String $url, $data = null, String $method = 'GET', Array $options = []) {
    $options = array_merge(['verify' => false], $options);
    
    $client = new Client($options);

    if ($data != Null) {
      $method = 'POST';

      if ($data instanceof \stdClass) {
        $data = json_decode(json_encode($data), true);
      }
    }

    $a = 0;

    do {
      try {
        $response = $client->request($method, $url, ['json' => $data]);

        return json_decode($response->getBody()->getContents());
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

  /**
 * Get all data points iterating over all pages
 * @param {String} url API URL of Element-IoT
 * @returns {Array} of data points
 */
  public static function getAll(String $url) {
    $result = [];
    $res = Null;
    $tryagain = false;
    $retrieveafter = '';
    $client = new Client(['verify' => false]);

    do {
      try {
        if ($retrieveafter != '') {
          $url = $retrieveafter;
        }

        $response = $client->request('GET', $url);

        return json_decode($response->getBody()->getContents(), false);
      } catch (\GuzzleHttp\Exception\ClientException $e) {
        if ($e->getCode() == 429 && $e->hasResponse()) {
          $response = $e->getResponse();
          
          if ($response->hasHeader('x-ratelimit-reset')) {
            usleep(intval($response->getHeader('x-ratelimit-reset')[0]) + 1);
          } else {
            break;
          }
        } else {
          break;
        }
      } catch (\Exception $e) {
        break;
      }

      $res = $client->request('GET', $url);

      $retrieveafter = $res->data->retrieve_after_id;
      array_push($result, $res);
    } while (count($res->data->body) == 100 || $tryagain);

    return $result;
  }

  /**
 * Get all data points using Element-IoTs Streaming API
 * @param {String} url Stream API URL of Element-IoT
 * @returns {Array} of data points
 */
  public static function getStream(String $url) {
    $result = [];
    $client = new Client(['verify' => false]);

    try {
      $res = $client->request('GET', $url);

      $result = explode('\n', $res->getBody()->getContents());

      $result = array_map(function ($element) {
        return json_decode($element);
      }, $result);
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      if ($e->getCode() == 429 && $e->hasResponse()) {
        $response = $e->getResponse();
        
        if ($response->hasHeader('x-ratelimit-reset')) {
          usleep(intval($response->getHeader('x-ratelimit-reset')[0]) + 1);

          return ElementIoTBridge::getStream($url);
        }
      }
    } catch (\Exception $e) {}

    return $result;
  }
}
