<?php

namespace OCA\SensorLogger\Tests\Unit\Lib;

use OCA\SensorLogger\Devices;
use OCP\IDBConnection;
use PHPUnit\Framework\TestCase;

class DevicesTest extends TestCase
{
    public function testUpdateDeviceRejectsUnknownColumnWithoutExecutingQuery(): void
    {
        $db = $this->createMock(IDBConnection::class);
        $db->expects(self::never())->method('getQueryBuilder');
        $db->expects(self::never())->method('executeStatement');

        self::assertFalse(Devices::updateDevice(1, 'user_id = 1; DELETE FROM users', 'alice', $db));
    }

    public function testInsertDeviceReturnsMessageWhenDeviceIdIsMissing(): void
    {
        $db = $this->createMock(IDBConnection::class);
        $db->expects(self::never())->method('getQueryBuilder');
        $db->expects(self::never())->method('executeStatement');

        self::assertSame('Missing device ID', Devices::insertDevice('alice', [], $db));
    }
}
