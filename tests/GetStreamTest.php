<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Mainova\ElementIoTBridge;

final class GetStreamTest extends TestCase {

    public function testResponseIsArrayofJsonData() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-17T12:00:00.000000Z';

        $res = ElementIoTBridge::getStream("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings/stream?sort=inserted_at&sort_direction=desc&measured_after=$after&measured_before=$before&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res);
        $this->assertNotEmpty($res);
    }

    public function testResponseSingleElement() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-17T12:00:00.000000Z';
        $check = 123;

        $res = ElementIoTBridge::getStream("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings/stream?sort=inserted_at&sort_direction=desc&measured_after=$after&measured_before=$before&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res);
        $this->assertCount(1, $res);
        $this->assertIsObject($res[1]);
        $this->assertEquals($check, $res[1]);
    }

    public function testResponseReturnAllData() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-17T12:00:00.000000Z';

        $num = 100;
        $data = [];

        for ($i=0; $i < $num; $i++) { 
            
            $res = ElementIoTBridge::getStream("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/reading?limit=1&measured_after=$after&measured_before=$before&sort=inserted_at&sort_direction=desc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");
            $this->assertIsArray($res);
            $this->assertCount(1, $res);
            
            array_push($data, $res);
        }
        
        $this->assertCount($num, $data);
    }

    public function testResponseIsEmptyWrongURL() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-17T12:00:00.000000Z';

        $res = ElementIoTBridge::getStream("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings/stream?sort=inserted_at&sort_direction=desc&measured_after=$after&measured_before=$before&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res);
        $this->assertEmpty($res);
    }
}