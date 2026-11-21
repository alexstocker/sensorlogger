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

    public function testInsertDeviceTypeUsesOwnCloudQueryBuilder(): void
    {
        $queryBuilder = $this->getMockBuilder(\OC\DB\QueryBuilder\QueryBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'insert',
                'values',
                'createNamedParameter',
                'getSQL',
                'getParameters',
                'getParameterTypes',
            ])
            ->getMock();
        $queryBuilder->expects(self::once())
            ->method('insert')
            ->with('sensorlogger_device_types')
            ->willReturnSelf();
        $queryBuilder->expects(self::exactly(2))
            ->method('createNamedParameter')
            ->withConsecutive(['alice'], ['Outdoor Sensor'])
            ->willReturnOnConsecutiveCalls(':userId', ':name');
        $queryBuilder->expects(self::once())
            ->method('values')
            ->with([
                'user_id' => ':userId',
                'device_type_name' => ':name',
            ])
            ->willReturnSelf();
        $queryBuilder->method('getSQL')
            ->willReturn('INSERT INTO sensorlogger_device_types VALUES (:userId, :name)');
        $queryBuilder->method('getParameters')
            ->willReturn(['userId' => 'alice', 'name' => 'Outdoor Sensor']);
        $queryBuilder->method('getParameterTypes')
            ->willReturn(['userId' => \PDO::PARAM_STR, 'name' => \PDO::PARAM_STR]);

        $db = $this->getMockBuilder(\OC\DB\Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getQueryBuilder', 'executeStatement', 'lastInsertId'])
            ->getMock();
        $db->expects(self::once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilder);
        $db->expects(self::once())
            ->method('executeStatement')
            ->with(
                'INSERT INTO sensorlogger_device_types VALUES (:userId, :name)',
                ['userId' => 'alice', 'name' => 'Outdoor Sensor'],
                ['userId' => \PDO::PARAM_STR, 'name' => \PDO::PARAM_STR]
            )
            ->willReturn(1);
        $db->expects(self::once())
            ->method('lastInsertId')
            ->willReturn(23);

        $controller = $this->createController([], [], $db);
        $insertDeviceType = new \ReflectionMethod(ApiSensorLoggerController::class, 'insertDeviceType');
        $insertDeviceType->setAccessible(true);

        self::assertSame(23, $insertDeviceType->invoke($controller, 'Outdoor Sensor'));
    }

    private function createController(
        array $params,
        array $methods = [],
        ?IDBConnection $db = null
    ): ApiSensorLoggerController {
        $request = $this->createMock(IRequest::class);
        $request->method('getParams')->willReturn($params);

        $user = $this->createMock(\OCP\IUser::class);
        $user->method('getUID')->willReturn('alice');
        $userSession = $this->createMock(IUserSession::class);
        $userSession->method('getUser')->willReturn($user);

        $controller = $this->getMockBuilder(ApiSensorLoggerController::class)
            ->setConstructorArgs([
                'sensorlogger',
                $request,
                $db ?? $this->createMock(IDBConnection::class),
                $this->createMock(IConfig::class),
                $this->createMock(IManager::class),
                $this->createMock(IGroupManager::class),
                $this->createMock(IUserManager::class),
                $this->createMock(IL10N::class),
                $userSession,
                $this->createMock(ILogger::class),
            ]);

        $controller->onlyMethods($methods);

        return $controller->getMock();
    }
}
