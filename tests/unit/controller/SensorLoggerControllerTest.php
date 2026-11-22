<?php

namespace OCA\SensorLogger\Tests\Unit\Controller;

use OCA\SensorLogger\Controller\SensorLoggerController;
use OCA\SensorLogger\Widgets;
use OCP\App\IAppManager;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IL10N;
use OCP\INavigationManager;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserSession;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SensorLoggerControllerTest extends TestCase
{
    public function testCreateWidgetStoresConfigurationForCurrentUser(): void
    {
        $params = [
            'device_id' => 42,
            'widget_type' => 'list',
            'position' => ['x' => 1, 'y' => 2],
        ];
        $request = $this->createRequest($params);
        $config = $this->createMock(IConfig::class);
        $config->expects(self::once())
            ->method('setUserValue')
            ->with('alice', 'sensorlogger', 'widget-list-42', json_encode($params));

        $controller = $this->createController($request, $config);

        $response = $controller->createWidget();

        self::assertInstanceOf(DataResponse::class, $response);
        self::assertSame(['id' => 'widget-list-42'], $response->getData());
    }

    public function testCreateWidgetRequiresDeviceAndWidgetType(): void
    {
        $controller = $this->createController($this->createRequest(['device_id' => 42]));

        $response = $controller->createWidget();

        self::assertSame(['errors' => 'Could not create widget!'], $response->getData());
    }

    private function createController(IRequest $request, ?IConfig $config = null): SensorLoggerController
    {
        $user = $this->createMock(IUser::class);
        $user->method('getUID')->willReturn('alice');

        $userSession = $this->createMock(IUserSession::class);
        $userSession->method('getUser')->willReturn($user);

        return new SensorLoggerController(
            'sensorlogger',
            $this->createMock(Widgets::class),
            $request,
            $this->createMock(IURLGenerator::class),
            $this->createMock(INavigationManager::class),
            $this->createMock(IL10N::class),
            $this->createMock(IDBConnection::class),
            $config ?? $this->createMock(IConfig::class),
            $this->createMock(EventDispatcherInterface::class),
            $userSession,
            $this->createMock(IAppManager::class)
        );
    }

    private function createRequest(array $params): IRequest
    {
        $request = $this->createMock(IRequest::class);
        $request->method('getParams')->willReturn($params);
        $request->method('getParam')->willReturnCallback(
            static function (string $key) use ($params) {
                return $params[$key] ?? null;
            }
        );

        return $request;
    }
}
