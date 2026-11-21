<?php

namespace OCA\SensorLogger\Tests\Unit\Controller;

use OCA\SensorLogger\Controller\ApiSensorLoggerController;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\ILogger;
use OCP\IRequest;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\Share\IManager;
use PHPUnit\Framework\TestCase;

class ApiSensorLoggerControllerTest extends TestCase
{
    public function testCreateLogRejectsMissingRequiredParameters(): void
    {
        $controller = $this->createController([]);

        $response = $controller->createLog();
        $data = $response->getData();

        self::assertInstanceOf(JSONResponse::class, $response);
        self::assertFalse($data['success']);
        self::assertSame(9001, $data['error']['code']);
        self::assertSame(
            'deviceId required,Neither temperature nor humidity data found',
            $data['error']['message']
        );
    }

    public function testCreateLogStoresExtendedSensorData(): void
    {
        $params = [
            'deviceId' => 'device-1',
            'data' => [
                ['dataTypeId' => 5, 'value' => 21.5],
            ],
        ];
        $controller = $this->createController($params, ['checkRegisteredDataTypes', 'insertExtendedLog']);
        $controller->expects(self::once())
            ->method('checkRegisteredDataTypes')
            ->with($params)
            ->willReturn(true);
        $controller->expects(self::once())
            ->method('insertExtendedLog')
            ->with($params);

        $response = $controller->createLog();
        $data = $response->getData();

        self::assertTrue($data['success']);
        self::assertSame($params['data'], $data['data']);
    }

    public function testCreateLogStoresTemperatureWithoutExtendedData(): void
    {
        $params = [
            'deviceId' => 'device-1',
            'temperature' => 21.5,
        ];
        $controller = $this->createController($params, ['insertLog']);
        $controller->expects(self::once())
            ->method('insertLog')
            ->with($params);

        $response = $controller->createLog();
        $data = $response->getData();

        self::assertTrue($data['success']);
        self::assertSame([], $data['data']);
    }

    private function createController(array $params, array $methods = []): ApiSensorLoggerController
    {
        $request = $this->createMock(IRequest::class);
        $request->method('getParams')->willReturn($params);

        $controller = $this->getMockBuilder(ApiSensorLoggerController::class)
            ->setConstructorArgs([
                'sensorlogger',
                $request,
                $this->createMock(IDBConnection::class),
                $this->createMock(IConfig::class),
                $this->createMock(IManager::class),
                $this->createMock(IGroupManager::class),
                $this->createMock(IUserManager::class),
                $this->createMock(IL10N::class),
                $this->createMock(IUserSession::class),
                $this->createMock(ILogger::class),
            ]);

        if ($methods !== []) {
            $controller->onlyMethods($methods);
        }

        return $controller->getMock();
    }
}
