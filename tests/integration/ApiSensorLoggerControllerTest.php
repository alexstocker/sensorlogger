<?php

namespace OCA\SensorLogger\Tests\Integration;

use OCA\SensorLogger\Controller\ApiSensorLoggerController;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\ILogger;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\Share\IManager;
use PHPUnit\Framework\TestCase;

class ApiSensorLoggerControllerTest extends TestCase
{
    private IDBConnection $db;
    private string $userId;
    private string $deviceId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->db = \OC::$server->getDatabaseConnection();
        $this->db->beginTransaction();
        $testId = bin2hex(random_bytes(8));
        $this->userId = 'sensorlogger_test_' . $testId;
        $this->deviceId = 'sensorlogger-test-' . $testId;
    }

    protected function tearDown(): void
    {
        if ($this->db->isTransactionActive()) {
            $this->db->rollBack();
        }

        parent::tearDown();
    }

    public function testRegistrationDataTypesAndLogCreation(): void
    {
        $registeredTypes = [
            [
                'type' => 'temperature',
                'description' => 'Temperature',
                'unit' => 'C',
            ],
            [
                'type' => 'humidity',
                'description' => 'Humidity',
                'unit' => '%',
            ],
        ];
        $registrationResponse = $this->createController([
            'deviceId' => $this->deviceId,
            'deviceName' => 'Integration test sensor',
            'deviceParentGroup' => 'Integration test parent',
            'deviceDataTypes' => $registeredTypes,
        ])->registerDevice();

        self::assertInstanceOf(JSONResponse::class, $registrationResponse);
        $registrationData = $registrationResponse->getData();
        self::assertTrue($registrationData['success'], json_encode($registrationData));
        self::assertCount(2, $registrationData['data']);

        $dataTypesResponse = $this->createController([
            'deviceId' => $this->deviceId,
        ])->getDeviceDataTypes();
        $dataTypes = $dataTypesResponse->getData();
        self::assertTrue($dataTypes['success']);
        self::assertCount(2, $dataTypes['data']);

        $simpleLogResponse = $this->createController([
            'deviceId' => $this->deviceId,
            'temperature' => 21.5,
            'humidity' => 48.2,
        ])->createLog();
        self::assertTrue($simpleLogResponse->getData()['success']);

        $extendedLog = [
            ['dataTypeId' => (int)$dataTypes['data'][0]['id'], 'value' => 21.5],
            ['dataTypeId' => (int)$dataTypes['data'][1]['id'], 'value' => 48.2],
        ];
        $logResponse = $this->createController([
            'deviceId' => $this->deviceId,
            'data' => $extendedLog,
        ])->createLog();
        $logData = $logResponse->getData();

        self::assertTrue($logData['success']);
        self::assertSame($extendedLog, $logData['data']);

        $query = $this->db->getQueryBuilder();
        $query->select('data')
            ->from('sensorlogger_logs')
            ->where('user_id = :userId')
            ->andWhere('device_uuid = :deviceId')
            ->setParameter('userId', $this->userId)
            ->setParameter('deviceId', $this->deviceId);
        $storedLogs = $this->db->executeQuery(
            $query->getSQL(),
            $query->getParameters(),
            $query->getParameterTypes()
        )->fetchAllAssociative();

        self::assertCount(2, $storedLogs);
        $storedLogData = array_map(
            static fn (array $row): array => json_decode($row['data'], true, 512, JSON_THROW_ON_ERROR),
            $storedLogs
        );
        self::assertContains($extendedLog, $storedLogData);
    }

    private function createController(array $params): ApiSensorLoggerController
    {
        $request = $this->createMock(IRequest::class);
        $request->method('getParams')->willReturn($params);

        $user = $this->createMock(IUser::class);
        $user->method('getUID')->willReturn($this->userId);
        $userSession = $this->createMock(IUserSession::class);
        $userSession->method('getUser')->willReturn($user);

        return new ApiSensorLoggerController(
            'sensorlogger',
            $request,
            $this->db,
            $this->createMock(IConfig::class),
            $this->createMock(IManager::class),
            $this->createMock(IGroupManager::class),
            $this->createMock(IUserManager::class),
            $this->createMock(IL10N::class),
            $userSession,
            $this->createMock(ILogger::class)
        );
    }
}
