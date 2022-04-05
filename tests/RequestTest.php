<?php declare(strict_types=1);

require_once __DIR__ . '/../src/ElementIoTBridge.php';

use PHPUnit\Framework\TestCase;
use Mainova\ElementIoTBridge;

final class RequestTest extends TestCase {

  public function testResponseIsArrayofJsonData() {
    $data = array(
      "type" => 'Animal',
      "props" => array (
        "breed" => 'dog',
        "friends" => ['Humans', 'Other dogs']
      )
    );

    $data = json_decode(json_encode($data));

    $res = ElementIoTBridge::request("http://echo.mainova.digital", $data);

    $this->assertEquals($data, $res);
  }

  public function testResponseIsEmptyWrongUrl() {
    $data = array(
      "type" => 'Animal',
      "props" => array (
        "breed" => 'dog',
        "friends" => ['Humans', 'Other dogs']
      )
    );

    $res = ElementIoTBridge::request("http://wrongandnotexisting.mainova.digital", $data);

    $this->assertEquals(NULL, $res);
  }

  public function testResponseIs200ElementOnServer() {
    $rand = random_int(0, 10000);
    $data = array(
      "type" => 'create_packet',
      "opts" => array(
        "payload" => json_encode(array("test" => $rand)),
        "encoding" => 'json',

        "packet_type" => 'up',
        "meta" => array(
          "frame_port" => 1
        )
      )
    );

    $res = ElementIoTBridge::request("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/actions?auth=35b1b8c5519ffdfb46cbc51deadaa96d", $data);

    $this->assertEquals(200, $res->status);

    $res = ElementIoTBridge::request("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/packets?auth=35b1b8c5519ffdfb46cbc51deadaa96d&limit=1");

    $this->assertIsArray($res->body);
    $this->assertEquals($rand ,$res->body[0]->payload->test);
  
    # 100 Calls
    for ($i = 0; $i < 100; $i++) { 
      $rand = random_int(0, 10000);
      
      $data = array( "type" => 'create_packet',
        "opts" => array(
          "payload" => json_encode(array("test" => $rand)),
          "encoding" => 'json',
          "packet_type" => 'up',
          "meta" => array(
            "frame_port" => 1
          )
        )
      );

      $res = ElementIoTBridge::request("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/actions?auth=35b1b8c5519ffdfb46cbc51deadaa96d", $data);

      $this->assertEquals(200, $res->status);
    
      $res = ElementIoTBridge::request("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/packets?auth=35b1b8c5519ffdfb46cbc51deadaa96d&limit=1");
    
      $this->assertIsArray($res->body);
      $this->assertEquals($rand, $res->body[0]->payload->test);
    }
  }

  public function testResponseIsNullWrongUrl() {
    $rand = random_int(0, 10000);
    $data =array ( "type" => 'create_packet',
      "opts" => array(
        "payload" => array ("test", $rand),
        "encoding" => 'json',
        "packet_type" => 'up',
        "meta" => array(
          "frame_port" => 1
        )
      )
    );

    $res = ElementIoTBridge::request("https://mainova.element-iot.com/api/v1/devic/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/actions?auth=35b1b8c5519ffdfb46cbc51deadaa96d", $data);

    $this->assertNull($res);
  }

  public function testResponseIsTimeoutAndTryAgain() {
    $res = ElementIoTBridge::request("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings?measured_after=2021-07-13T14:04:19.239895Z&measured_before=2021-07-15T08:31:48.000000Z&sort=inserted_at&sort_direction=asc&auth=35b1b8c5519ffdfb46cbc51deadaa96d", null, 'GET', ['timeout' => 0.001]);

    $this->assertNull($res);

    $res = ElementIoTBridge::request("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings?measured_after=2021-07-13T14:04:19.239895Z&measured_before=2021-07-15T08:31:48.000000Z&sort=inserted_at&sort_direction=asc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

    $this->assertEquals(123, $res->body[0]->data->test);
  }
}