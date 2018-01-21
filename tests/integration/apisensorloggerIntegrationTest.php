<?php

namespace OCA\SensorLogger\Tests\Integration;

use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\App;
use PHPUnit_Framework_TestCase;

use OCA\SensorLogger\Controller;

class apisensorloggerIntegrationTest extends PHPUnit_Framework_TestCase
{

    private $controller;
    private $mapper;
    private $userId = 'dolly';

    public function setUp()
    {
        parent::setUp();
        $app = new App('sensorlogger');
        $container = $app->getContainer();

        // only replace the user id
        $container->registerService('UserId', function($c) {
            return $this->userId;
        });

        $this->controller = $container->query(
            'OCA\SensorLogger\Controller\apisensorloggerController'
        );

    }

    public function testCreateLog() {

    }

}