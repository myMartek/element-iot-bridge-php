<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Mainova\ElementIoTBridge;

final class GetStreamTest extends TestCase {

    public function testResponseIsArrayOfJsonData() {
        $after = '2021-07-17T15:00:00.000000';
        $before = '2021-07-14T15:00:00.000000';

        $res = ElementIoTBridge::getStream("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings/stream?sort=inserted_at&sort_direction=desc&measured_after=$after&measured_before=$before&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res);
        $this->assertNotEmpty($res);
    }

    public function testResponseSingleElement() {
        $check = 123;

        $res = ElementIoTBridge::getStream("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings/stream?measured_after=2021-07-13T14:04:19.239895Z&measured_before=2021-07-15T08:31:48.000000Z&sort=inserted_at&sort_direction=asc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res);
        $this->assertCount(1, $res);
        $this->assertInstanceOf(stdClass::class, $res[0]);
        $this->assertEquals($check, $res[0]->data->test);
    }

    public function testResponseReturnAllData() {
        $num = 100;
        $data = [];

        for ($i = 0; $i < $num; $i++) { 
            $res = ElementIoTBridge::getStream("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings/stream?measured_after=2021-07-13T14:04:19.239895Z&measured_before=2021-07-15T08:31:48.000000Z&sort=inserted_at&sort_direction=asc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");
            
            $this->assertIsArray($res);
            $this->assertCount(1, $res);
            
            array_push($data, $res);
        }
        
        $this->assertCount($num, $data);
    }

    public function testResponseIsEmptyWrongUrl() {
        $res = ElementIoTBridge::getStream("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings/stream?sort=inserted_at&sort_direction=desc&auth=35b1b8c5519ffdfb46cbc51deadaa96d111");

        $this->assertIsArray($res);
        $this->assertEmpty($res);
    }
}