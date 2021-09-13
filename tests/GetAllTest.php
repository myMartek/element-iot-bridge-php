<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Mainova\ElementIoTBridge;

final class GetAllTest extends TestCase
{
    public function testResponseIsArrayofJsonData() {
        $after = '2021-07-13T00:00:00.000000Z';
        $before = '2021-07-17T12:00:00.000000Z';

        $res = ElementIoTBridge::getAll("https://mainova.element-iot.com/api/v1/devices/6e0f0cf0-72a7-4f03-86f8-a736f280a74b/readings?limit=100&measured_after=$after&measured_before=$before&sort=inserted_at&sort_direction=desc&auth=35b1b8c5519ffdfb46cbc51deadaa96d");

        $this->assertIsArray($res);
    }
}