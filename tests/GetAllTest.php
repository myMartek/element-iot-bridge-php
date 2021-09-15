<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Mainova\ElementIoTBridge;

final class GetAllTest extends TestCase {
    public function testResponseIsArrayofJsonData() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-17T12:00:00.000000Z';

        $res = ElementIoTBridge::getAll("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings?limit=100&measured_after=$after&measured_before=$before&sort=inserted_at&sort_direction=desc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res->body);
    }

    public function testResponseWithoutLimitIsArrayofJsonData() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-16T00:00:00.000000Z';

        $res = ElementIoTBridge::getAll("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings?measured_after=$after&measured_before=$before&sort=inserted_at&sort_direction=desc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res->body);
    }

    public function testResponseWithLimit1() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-16T00:00:00.000000Z';

        $res = ElementIoTBridge::getAll("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings?limit=1&measured_after=$after&measured_before=$before&sort=inserted_at&sort_direction=desc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res->body);
    }

    public function testResponseIsEmptyWrongAuth() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-16T00:00:00.000000Z';

        $res = ElementIoTBridge::getAll("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings?limit=1&measured_after=$after&measured_before=$before&sort=inserted_at&sort_direction=desc&auth=35b1b8c5519ffdfb46cbc51deadaa96d111");

        $this->assertIsArray($res);
        $this->assertEmpty($res);
    }

    public function testResponseIsEmptyWrongUrl() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-16T00:00:00.000000Z';

        $res = ElementIoTBridge::getAll("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/reading?limit=1&measured_after=$after&measured_before=$before&sort=inserted_at&sort_direction=desc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res);
        $this->assertEmpty($res);
    }

    public function testResponseReturnAllData() {
        $num = 100;
        $data = [];

        for ($i=0; $i < $num; $i++) { 
            
            $res = ElementIoTBridge::getAll("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings?measured_after=2021-07-13T14:04:19.239895Z&measured_before=2021-07-15T08:31:48.000000Z&sort=inserted_at&sort_direction=asc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");
            $this->assertIsArray($res->body);
            $this->assertCount(1, $res->body);
            
            array_push($data, $res);
        }
        
        $this->assertCount($num, $data);
    }
}