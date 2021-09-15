<?php

namespace Mainova;
use GuzzleHttp\Client;

class ElementIoTBridge {
  private static function replaceUrlParameters($url = '', $newParams = []) {
    if($url){
      $urlArray = parse_url($url);
      $queryString = $urlArray['query'];

      parse_str($queryString, $queryParams);

      $queryParams = array_merge($queryParams, $newParams);

      $urlArray['query'] = http_build_query($queryParams);

      if(!empty($urlArray)){
        $url = $urlArray['scheme'].'://'.$urlArray['host'].$urlArray['path'].'?'.$urlArray['query'];
      }
    }

    return $url;
  }

  /**
  * Send a request to Element-IoT with optional HTTP Data body
  *
  * @static   
  * @param    {String} url API URL of Element-IoT
  * @param    {Object} data Optional POST Data
  * @param    {String} method HTTP method to use for the request
  * @param    {Array} options Guzzle Client Options used for the request
  * @return   {Object} URL return data or null
  */
  public static function request(String $url, $data = null, String $method = "GET", Array $options = []) {
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
  * 
  * @static
  * @param    {String} url API URL of Element-IoT
  * @return   {Array} of data points
  */
  
  public static function getAll(String $url) {
    $result = [];
    $res = null;
    $tryagain = false;
    $retrieveafter = '';
    $client = new Client(['verify' => false]);

    do {
      $res = null;
      $tryagain = false;

      $params = [
        'limit' => 100
      ];

      if ($retrieveafter != '') {
        $params['retrieve_after'] = $retrieveafter;
      }

      $url = ElementIoTBridge::replaceUrlParameters($url, $params);

      try {
        $response = $client->request('GET', $url);

        $res = json_decode($response->getBody()->getContents(), false);

        $retrieveafter = $res->retrieve_after_id;
        $result = array_merge($result, $res->body);
      } catch (\GuzzleHttp\Exception\ClientException $e) {
        if ($e->getCode() == 429 && $e->hasResponse()) {
          $response = $e->getResponse();
          
          if ($response->hasHeader('x-ratelimit-reset')) {
            usleep(intval($response->getHeader('x-ratelimit-reset')[0]) + 1);

            $tryagain = true;
          }
        }
      } catch (\Exception $e) {}
    } while ($tryagain || ($res && $res->body && count($res->body) == 100));

    return $result;
  }

  /**
  * Get all data points using Element-IoTs Streaming API
  * 
  * @static
  * @param    {String} url Stream API URL of Element-IoT
  * @return   {Array} of data points
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
