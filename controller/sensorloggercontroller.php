<?php
/**
 * @author Alexander Stocker <alex@stocker.info>
 *
 * @copyright Copyright (c) 2017, Alexander Stocker
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\SensorLogger\Controller;

use OC\OCS\Exception;
use OC\Security\CSP\ContentSecurityPolicy;
use OCA\SensorLogger\App;
use OCA\SensorLogger\DataTypes;
use OCA\SensorLogger\Device;
use OCA\SensorLogger\DeviceTypes;
use OCA\SensorLogger\iWidget;
use OCA\SensorLogger\Devices;
use OCA\SensorLogger\DeviceGroups;
use OCA\SensorLogger\SensorLogs;
use OCA\SensorLogger\Widgets;
use OCP\App\IAppManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IL10N;
use OCP\INavigationManager;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUserSession;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class SensorLoggerController
 *
 * @package OCA\SensorLogger\Controller
 */
class SensorLoggerController extends Controller
{

    /**
     * @var
     */
    private $userId;

    /** @var IDBConnection */
    protected $connection;

    /**
     * @var IConfig
     */
    protected $config;

    /**
     * @var IURLGenerator
     */
    protected $urlGenerator;

    /**
     * @var INavigationManager
     */
    protected $navigationManager;

    /**
     * @var IL10N
     */
    protected $l10n;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var IUserSession
     */
    protected $userSession;

    /**
     * @var IAppManager
     */
    protected $appManager;

    protected $widgets;

    /**
     * SensorLoggerController constructor.
     *
     * @param $AppName
     * @param IRequest $request
     * @param IURLGenerator $urlGenerator
     * @param INavigationManager $navigationManager
     * @param IL10N $l10n
     * @param IDBConnection $connection
     * @param IConfig $config
     * @param EventDispatcherInterface $eventDispatcherInterface
     * @param IUserSession $userSession
     * @param IAppManager $appManager
     */
    public function __construct(
        $AppName,
        Widgets $widgets,
        IRequest $request,
        IURLGenerator $urlGenerator,
        INavigationManager $navigationManager,
        IL10N $l10n,
        IDBConnection $connection,
        IConfig $config,
        EventDispatcherInterface $eventDispatcherInterface,
        IUserSession $userSession,
        IAppManager $appManager
    ) {
        parent::__construct($AppName, $request);
        $this->connection = $connection;
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->navigationManager = $navigationManager;
        $this->l10n = $l10n;
        $this->eventDispatcher = $eventDispatcherInterface;
        $this->userSession = $userSession;
        $this->appManager = $appManager;
        $this->widgets = $widgets;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function index()
    {
        $templateName = 'main';
        $widgets = $this->getWidgets();

        $parameters = array(
            'part' => 'dashboard',
            'widgets' => $widgets,
            'navItems' => $this->getNavigationItems()
        );

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    protected function getNavigationItems()
    {
        App::getNavigationManager()->add(
            [
                'id' => 'showDashboard',
                'appName' => 'sensorlogger',
                'order' => 0,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.showDashboard'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Dashboard'),
            ]
        );

        App::getNavigationManager()->add(
            array(
                'id' => 'showList',
                'appName' => 'sensorlogger',
                'order' => 1,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.showList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('List'),
            )
        );

        App::getNavigationManager()->add(
            array(
                'id' => 'deviceList',
                'appName' => 'sensorlogger',
                'order' => 2,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.deviceList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Devices'),
            )
        );

        App::getNavigationManager()->add(
            array(
                'id' => 'deviceTypeList',
                'appName' => 'sensorlogger',
                'order' => 3,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.deviceTypeList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Device Types'),
            )
        );

        App::getNavigationManager()->add(
            array(
                'id' => 'deviceGroupList',
                'appName' => 'sensorlogger',
                'order' => 4,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.deviceGroupList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Device Groups'),
            )
        );

        App::getNavigationManager()->add(
            array(
                'id' => 'dataTypeList',
                'appName' => 'sensorlogger',
                'order' => 5,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.dataTypeList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Data Types'),
            )
        );

        if ($this->config->getAppValue('core', 'shareapi_enabled', 'yes') === 'yes') {
            App::getNavigationManager()->add(
                array(
                    'id' => 'sharingin',
                    'appName' => 'sensorlogger',
                    'order' => 10,
                    'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.sharingIn'),
                    'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                    'name' => $this->l10n->t('Shared with you'),
                )
            );

            App::getNavigationManager()->add(
                [
                    'id' => 'sharingout',
                    'appName' => 'sensorlogger',
                    'order' => 10,
                    'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.sharingOut'),
                    'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                    'name' => $this->l10n->t('Shared with others'),
                ]
            );


            if ($this->config->getAppValue('core', 'shareapi_allow_links', 'yes') === 'yes') {
                App::getNavigationManager()->add(
                    [
                        'id' => 'sharinglinks',
                        'appName' => 'sensorlogger',
                        'order' => 10,
                        'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.sharedLink'),
                        'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                        'name' => $this->l10n->t('Shared by link'),
                    ]
                );
            }
        }


        $navItems = App::getNavigationManager()->getAll();
        usort($navItems, function ($item1, $item2) {
            return $item1['order'] - $item2['order'];
        });

        return $navItems;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function sharingIn()
    {
        # TODO [GH18]implement sensorloggercontroller::sharingIn
        $templateName = 'main';  // will use templates/main.php
        $parameters = array(
            'part' => 'listSharedDevices',
            'devices' => [],
            'navItems' => $this->getNavigationItems());

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function sharingOut()
    {
        #TODO [GH19] implement sensorloggercontroller::sharingOut
        $templateName = 'main';  // will use templates/main.php
        $parameters = array(
            'part' => 'listSharedDevices',
            'devices' => [],
            'navItems' => $this->getNavigationItems());

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function sharedLink()
    {
        # TODO [GH20] imnplement sensorloggercontroller::sharedLink
        $templateName = 'main';  // will use templates/main.php
        $parameters = array(
            'part' => 'listSharedDevices',
            'devices' => [],
            'navItems' => $this->getNavigationItems());

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @NoAdminRequired
     * @return array
     */
    protected function getWidgets()
    {
        $devices = Devices::getDevices($this->userSession->getUser()->getUID(), $this->connection);
        $widgets = [];

        $availableWidgets = new Widgets();
        $widgetTypes = $availableWidgets->getWidgetTypes();
        foreach ($devices as $device) {
            //foreach(Widgets::WIDGET_TYPES as $key => $value) {
            foreach ($widgetTypes as $key => $value) {
                $widgetConfig = json_decode($this->getUserValue(
                    'widget-' . $key . '-' . $device->getId(),
                    $this->userSession->getUser()->getUID()
                ));

                if ($widgetConfig === null) {
                    continue;
                }

                $customWidget = $availableWidgets->buildUserWidget(
                    $this->userSession->getUser()->getUID(),
                    $device,
                    $widgetConfig,
                    $this->connection,
                    $this->config
                );

                //$buildWidget = Widgets::build($this->userSession->getUser()->getUID(), $device, $widgetConfig, $this->connection, $this->config);
                $widgets[] = $customWidget;
            }
        }
        return $widgets;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function showList()
    {
        $logs = SensorLogs::getLogs($this->userSession->getUser()->getUID(), $this->connection);
        $templateName = 'main';
        $parameters = array(
            'part' => 'list',
            'logs' => $logs,
            'navItems' => $this->getNavigationItems());

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function getWidgetTypes()
    {
        //$widgetTypes = Widgets::WIDGET_TYPES;
        $widgets = new Widgets();
        $widgetTypes = $widgets->getWidgetTypes();
        $devices = Devices::getDevices($this->userSession->getUser()->getUID(), $this->connection);
        return $this->returnJSON(array('widgetTypes' => $widgetTypes, 'devices' => $devices));
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     * @throws \OCP\PreConditionNotMetException
     */
    public function createWidget()
    {
        $array = $this->request->getParams();
        $widgetId = $this->request->getParam('device_id');
        $widgetType = $this->request->getParam('widget_type');
        $json = json_encode($array);
        if (!$widgetId || !$widgetType) {
            return $this->returnJSON(array('errors' => 'Could not create widget!'));
        }
        try {
            $this->setUserValue('widget-' . $widgetType . '-' . $widgetId, $this->userSession->getUser()->getUID(), $json);
            return $this->returnJSON(array('id' => 'widget-' . $widgetType . '-' . $widgetId));
        } catch (Exception $e) {
            return $this->returnJSON(array('errors' => 'Could not create widget!'));
        }
    }

    /**
     * @NoAdminRequired
     * @param $id
     * @return DataResponse
     */
    public function deleteWidget($id)
    {
        try {
            $this->config->deleteUserValue($this->userSession->getUser()->getUID(), $this->appName, $id);
            return $this->returnJSON(array('success' => true));
        } catch (Exception $e) {
            return $this->returnJSON(array('success' => false));
        }
    }

    /**
     * @NoAdminRequired
     * @param $id
     * @return DataResponse
     */
    public function deleteDataType($id)
    {
        $userId = $this->userSession->getUser()->getUID();
        if (DataTypes::isDeletable($userId, (int)$id, $this->connection)) {
            try {
                DataTypes::deleteDataType($userId, (int)$id, $this->connection);
                return $this->returnJSON(array('success' => true));
            } catch (Exception $e) {
            }
        }
        return $this->returnJSON(array('success' => false));
    }

    /**
     * @NoAdminRequired
     * @param $id
     * @return DataResponse
     */
    public function deleteDeviceGroup($id)
    {
        $userId = $this->userSession->getUser()->getUID();
        if (DeviceGroups::isDeletable($userId, (int)$id, $this->connection)) {
            try {
                DeviceGroups::deleteDeviceGroup($userId, (int)$id, $this->connection);
                return $this->returnJSON(array('success' => true));
            } catch (Exception $e) {
            }
        }
        return $this->returnJSON(array('success' => false));
    }

    /**
     * @NoAdminRequired
     * @param $id
     * @return DataResponse
     */
    public function deleteDeviceType($id)
    {
        $userId = $this->userSession->getUser()->getUID();
        if (DeviceTypes::isDeletable($userId, (int)$id, $this->connection)) {
            try {
                DeviceTypes::deleteDeviceType($userId, (int)$id, $this->connection);
                return $this->returnJSON(array('success' => true));
            } catch (Exception $e) {
            }
        }
        return $this->returnJSON(array('success' => false));
    }

    /**
     * @NoAdminRequired
     * @param $id
     * @return DataResponse
     */
    public function deleteDevice($id)
    {
        if (Devices::isDeletable($this->userSession->getUser()->getUID(), (int)$id, $this->connection)) {
            try {
                Devices::deleteDevice((int)$id, $this->connection);
                DataTypes::deleteDeviceDataTypesByDeviceId((int)$id, $this->connection);
                return $this->returnJSON(array('success' => true));
            } catch (Exception $e) {
            }
        }
        return $this->returnJSON(array('success' => false));
    }

    /**
     * @param Device $device
     * @return bool
     */
    protected function deleteLogsByDevice($device)
    {
        try {
            SensorLogs::deleteLogsByUuid($device->getUuid(), $this->connection);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function wipeOutDevice()
    {
        if ($this->request->getParam('device_id') && $this->userSession->getUser()->getUID()) {
            $id = $this->request->getParam('device_id');
            $device = Devices::getDevice($this->userSession->getUser()->getUID(), (int)$id, $this->connection);

            if (!$this->deleteLogsByDevice($device)) {
                return $this->returnJSON(['errors' => 'Could not delete Logs for Device #' . $id]);
            }

            if (Devices::isDeletable($this->userSession->getUser()->getUID(), (int)$id, $this->connection)) {
                try {
                    Devices::deleteDevice((int)$id, $this->connection);
                    DataTypes::deleteDeviceDataTypesByDeviceId((int)$id, $this->connection);
                    return $this->returnJSON(array('success' => true));
                } catch (Exception $e) {
                }
            }
        }
        return $this->returnJSON(array('success' => false));
    }

    /**
     * @NoAdminRequired
     * @param $id
     * @return DataResponse
     */
    public function deleteLog($id)
    {
        try {
            SensorLogs::deleteLogById($id, $this->connection);
            return $this->returnJSON(array('success' => true));
        } catch (Exception $e) {
        }
        return $this->returnJSON(array('success' => false));
    }

    /**
     * @NoAdminRequired
     * @param int $id
     * @return TemplateResponse
     */
    public function showDeviceData($id)
    {

        # TODO [GH26] Rework sensorloggercontroller::showDeviceData
        # and rework template too

        $templateName = 'part.list';  // will use templates/main.php
        $logs = $this->getDeviceData($id);
        $parameters = array('part' => 'list', 'logs' => $logs);

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters, 'blank');
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @NoAdminRequired
     * @param int $id
     * @return DataResponse
     */
    public function showDeviceDetails($id)
    {
        $deviceDetails = Devices::getDeviceDetails($this->userSession->getUser()->getUID(), $id, $this->connection);
        $groups = DeviceGroups::getDeviceGroups($this->userSession->getUser()->getUID(), $this->connection);
        $types = DeviceTypes::getDeviceTypes($this->userSession->getUser()->getUID(), $this->connection);

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = $this->returnJSON(array(
            'deviceDetails' => $deviceDetails,
            'groups' => $groups,
            'types' => $types
        ));

        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @NoAdminRequired
     * @param $id
     */
    public function updateDevice($id)
    {
        $field = $this->request->getParam('name');
        $value = $this->request->getParam('value');
        Devices::updateDevice($id, $field, $value, $this->connection);
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function createDeviceType()
    {
        $deviceTypeName = $this->request->getParam('device_type_name');
        $deviceTypeId = DeviceTypes::insertDeviceType($this->userSession->getUser()->getUID(), $deviceTypeName, $this->connection);
        if (is_int($deviceTypeId)) {
            return $this->returnJSON(['deviceTypeId' => $deviceTypeId]);
        }
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function createDeviceGroup()
    {
        $deviceGroupName = $this->request->getParam('device_group_name');
        $deviceGroupId = DeviceGroups::insertSensorGroup($this->userSession->getUser()->getUID(), $deviceGroupName, $this->connection);
        if (is_int($deviceGroupId)) {
            return $this->returnJSON(['deviceGroupId' => $deviceGroupId]);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function showDashboard()
    {
        $log = SensorLogs::getLastLog($this->userSession->getUser()->getUID(), $this->connection);
        $widgets = $this->getWidgets();
        $templateName = 'main';
        $parameters = [
            'part' => 'dashboard',
            'log' => $log,
            'widgets' => $widgets,
            'navItems' => $this->getNavigationItems()
        ];

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @param int $id
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function deviceChart($id)
    {
        $templateName = 'part.chart';
        $device = Devices::getDevice($this->userSession->getUser()->getUID(), $id, $this->connection);
        $parameters = array('part' => 'chart', 'device' => $device);
        return new TemplateResponse($this->appName, $templateName, $parameters, 'blank');
    }

    /**
     * @NoAdminRequired
     * @param int $id
     * @return string
     */
    public function chartData($id)
    {
        return $this->getChartData($id);
    }

    /**
     * @NoAdminRequired
     * @param int $id
     * @return string
     */
    public function chartDataLastLog($id)
    {
        return $this->getChartDataLastLog($id);
    }

    /**
     * @NoAdminRequired
     * @param integer $id
     * @param integer $param
     * @return DataResponse
     */
    public function maxLastLog($id, $param)
    {
        if (is_int($id) && (is_int($param) && $param !== 0)) {
            $device = Devices::getDevice(
                $this->userSession->getUser()->getUID(),
                $id,
                $this->connection
            );

            $widget = new Widgets\MaxValues24hWidget();
            $logs = $widget->widgetData($this->userSession->getUser()->getUID(), $device, $this->connection);

            $dataTypes = DataTypes::getDeviceDataTypesByDeviceId(
                $this->userSession->getUser()->getUID(),
                $device->getId(),
                $this->connection
            );

            if (is_array($dataTypes) && !empty($dataTypes)) {
                $logs = array('logs' => $logs, 'dataTypes' => $dataTypes);
            }
            return $this->returnJSON($logs);
        }
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function deviceList()
    {
        $devices = Devices::getDevices($this->userSession->getUser()->getUID(), $this->connection);
        $templateName = 'main';
        $parameters = [
            'part' => 'listDevices',
            'devices' => $devices,
            'navItems' => $this->getNavigationItems()
        ];

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function deviceTypeList()
    {
        $deviceTypes = DeviceTypes::getDeviceTypes($this->userSession->getUser()->getUID(), $this->connection);
        $templateName = 'main';
        $parameters = [
            'part' => 'listDeviceTypes',
            'deviceTypes' => $deviceTypes,
            'navItems' => $this->getNavigationItems()
        ];

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function deviceGroupList()
    {
        $deviceGroups = DeviceGroups::getDeviceGroups($this->userSession->getUser()->getUID(), $this->connection);
        $templateName = 'main';
        $parameters = [
            'part' => 'listDeviceGroups',
            'deviceGroups' => $deviceGroups,
            'navItems' => $this->getNavigationItems()
        ];

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function dataTypeList()
    {
        $dataTypes = DataTypes::getDataTypes($this->userSession->getUser()->getUID(), $this->connection);
        $templateName = 'main';
        $parameters = [
            'part' => 'listDataTypes',
            'dataTypes' => $dataTypes,
            'navItems' => $this->getNavigationItems()
        ];

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");

        $response = new TemplateResponse($this->appName, $templateName, $parameters);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * @param $key
     * @param $userId
     * @return string
     */
    public function getUserValue($key, $userId)
    {
        return $this->config->getUserValue($userId, $this->appName, $key);
    }

    /**
     * @param $key
     * @param $userId
     * @param $value
     * @throws \OCP\PreConditionNotMetException
     */
    protected function setUserValue($key, $userId, $value)
    {
        $this->config->setUserValue($userId, $this->appName, $key, $value);
    }

    /**
     * @param $array
     * @return DataResponse
     */
    public function returnJSON($array)
    {
        try {
            return new DataResponse($array);
        } catch (\Exception $ex) {
            return new DataResponse(array('msg' => 'not found!'), Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @param int $id
     * @return string
     */
    protected function getChartData($id)
    {
        $limit = $this->request->getParam('limit') ?: 1000;
        $offset = $this->request->getParam('offset') ?: 0;

        $device = Devices::getDevice(
            $this->userSession->getUser()->getUID(),
            $id,
            $this->connection
        );

        $logs = SensorLogs::getLogsByUuId(
            $this->userSession->getUser()->getUID(),
            $device->getUuid(),
            $this->connection,
            $limit,
            $offset
        );

        $dataTypes = DataTypes::getDeviceDataTypesByDeviceId(
            $this->userSession->getUser()->getUID(),
            $device->getId(),
            $this->connection
        );

        if (is_array($dataTypes) && !empty($dataTypes)) {
            $logs = array('logs' => $logs, 'dataTypes' => $dataTypes);
        }
        return $this->returnJSON($logs);
    }

    protected function getChartDataLastLog($id)
    {
        $device = Devices::getDevice(
            $this->userSession->getUser()->getUID(),
            $id,
            $this->connection
        );

        $logs = SensorLogs::getLogsByUuId(
            $this->userSession->getUser()->getUID(),
            $device->getUuid(),
            $this->connection,
            $this->request->getParam('limit') ?: 1,
            $this->request->getParam('offset') ?: 0
        );

        $dataTypes = DataTypes::getDeviceDataTypesByDeviceId(
            $this->userSession->getUser()->getUID(),
            $device->getId(),
            $this->connection
        );

        if (is_array($dataTypes) && !empty($dataTypes)) {
            $logs = array('logs' => $logs, 'dataTypes' => $dataTypes);
        }
        return $this->returnJSON($logs);
    }

    /**
     * @param int $id
     * @return array
     */
    protected function getDeviceData($id)
    {
        $device = Devices::getDevice($this->userSession->getUser()->getUID(), $id, $this->connection);
        $logs = SensorLogs::getLogsByUuId($this->userSession->getUser()->getUID(), $device->getUuid(), $this->connection);
        return $logs;
    }
}
