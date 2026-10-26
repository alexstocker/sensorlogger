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
use OCA\SensorLogger\DataTypes;
use OCA\SensorLogger\Device;
use OCA\SensorLogger\DeviceTypes;
use OCA\SensorLogger\iWidget;
use OCA\SensorLogger\Devices;
use OCA\SensorLogger\DeviceGroups;
use OCA\SensorLogger\SensorLogs;
use OCA\SensorLogger\Widgets;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IL10N;
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
     * @var IL10N
     */
    protected $l10n;

    /**
     * @var IUserSession
     */
    protected $userSession;

    protected $widgets;

    /**
     * SensorLoggerController constructor.
     *
     * @param $AppName
     * @param IRequest $request
     * @param IURLGenerator $urlGenerator
     * @param IL10N $l10n
     * @param IDBConnection $connection
     * @param IConfig $config
     * @param IUserSession $userSession
     */
    public function __construct(
        $AppName,
        Widgets $widgets,
        IRequest $request,
        IURLGenerator $urlGenerator,
        IL10N $l10n,
        IDBConnection $connection,
        IConfig $config,
        IUserSession $userSession
    ) {
        parent::__construct($AppName, $request);
        $this->connection = $connection;
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->l10n = $l10n;
        $this->userSession = $userSession;
        $this->widgets = $widgets;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function index()
    {
        return $this->getAppPageResponse('dashboard', 'showDashboard');
    }

    protected function getNavigationItems()
    {
        $navItems = [];

        $navItems[] = [
                'id' => 'showDashboard',
                'app' => 'sensorlogger',
                'order' => 0,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.showDashboard'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Dashboard'),
            ];

        $navItems[] = [
                'id' => 'showList',
                'app' => 'sensorlogger',
                'order' => 1,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.showList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('List'),
            ];

        $navItems[] = [
                'id' => 'deviceList',
                'app' => 'sensorlogger',
                'order' => 2,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.deviceList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Devices'),
            ];

        $navItems[] = [
                'id' => 'deviceTypeList',
                'app' => 'sensorlogger',
                'order' => 3,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.deviceTypeList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Device Types'),
            ];

        $navItems[] = [
                'id' => 'deviceGroupList',
                'app' => 'sensorlogger',
                'order' => 4,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.deviceGroupList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Device Groups'),
            ];

        $navItems[] = [
                'id' => 'dataTypeList',
                'app' => 'sensorlogger',
                'order' => 5,
                'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.dataTypeList'),
                'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                'name' => $this->l10n->t('Data Types'),
            ];

        if ($this->config->getAppValue('core', 'shareapi_enabled', 'yes') === 'yes') {
            $navItems[] = [
                    'id' => 'sharingin',
                    'app' => 'sensorlogger',
                    'order' => 10,
                    'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.sharingIn'),
                    'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                    'name' => $this->l10n->t('Shared with you'),
                ];

            $navItems[] = [
                    'id' => 'sharingout',
                    'app' => 'sensorlogger',
                    'order' => 10,
                    'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.sharingOut'),
                    'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                    'name' => $this->l10n->t('Shared with others'),
                ];


            if ($this->config->getAppValue('core', 'shareapi_allow_links', 'yes') === 'yes') {
                $navItems[] = [
                        'id' => 'sharinglinks',
                        'app' => 'sensorlogger',
                        'order' => 10,
                        'href' => $this->urlGenerator->linkToRoute('sensorlogger.sensorlogger.sharedLink'),
                        'icon' => $this->urlGenerator->imagePath('sensorlogger', 'app.svg'),
                        'name' => $this->l10n->t('Shared by link'),
                    ];
            }
        }

        return $navItems;
    }

    private function getAppPageResponse($initialView, $activeNavigation, $id = null)
    {
        $response = new TemplateResponse($this->appName, 'main', [
            'navItems' => $this->getNavigationItems(),
            'initialView' => $initialView,
            'activeNavigation' => $activeNavigation,
            'initialId' => $id,
        ]);
        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");
        $response->setContentSecurityPolicy($policy);
        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function sharingIn()
    {
        return $this->getAppPageResponse('sharingIn', 'sharingin');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function sharingOut()
    {
        return $this->getAppPageResponse('sharingOut', 'sharingout');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function sharedLink()
    {
        return $this->getAppPageResponse('sharedLink', 'sharinglinks');
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
        return $this->getAppPageResponse('logs', 'showList');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param string $view
     * @return DataResponse
     */
    public function frontendData($view)
    {
        $userId = $this->userSession->getUser()->getUID();
        $deviceId = (int)$this->request->getParam('deviceId', 0);

        switch ($view) {
            case 'dashboard':
                $widgets = array_map([$this, 'serializeWidgetForUi'], array_filter($this->getWidgets()));
                return $this->returnJSON([
                    'widgets' => array_values($widgets),
                    'layout' => json_decode($this->getUserValue(
                        'dashboard-layout',
                        $userId
                    ), true) ?: ['order' => [], 'heights' => [], 'spans' => []],
                    'widgetTypes' => (new Widgets())->getWidgetTypes(),
                    'devices' => Devices::getDevices($userId, $this->connection),
                ]);
            case 'logs':
                return $this->returnJSON([
                    'logs' => array_map([$this, 'serializeLogForUi'], SensorLogs::getLogs($userId, $this->connection)),
                ]);
            case 'devices':
                return $this->returnJSON(['devices' => Devices::getDevices($userId, $this->connection)]);
            case 'deviceTypes':
                return $this->returnJSON(['deviceTypes' => DeviceTypes::getDeviceTypes($userId, $this->connection)]);
            case 'deviceGroups':
                return $this->returnJSON(['deviceGroups' => DeviceGroups::getDeviceGroups($userId, $this->connection)]);
            case 'dataTypes':
                return $this->returnJSON(['dataTypes' => DataTypes::getDataTypes($userId, $this->connection)]);
            case 'deviceLogs':
                if ($deviceId < 1 || !Devices::getDevice($userId, $deviceId, $this->connection)) {
                    return new DataResponse(['error' => 'Device not found'], Http::STATUS_NOT_FOUND);
                }
                return $this->returnJSON([
                    'logs' => array_map([$this, 'serializeLogForUi'], $this->getDeviceData($deviceId)),
                ]);
            case 'sharingIn':
            case 'sharingOut':
            case 'sharedLink':
                return $this->returnJSON(['devices' => []]);
            default:
                return new DataResponse(['error' => 'Unknown view'], Http::STATUS_NOT_FOUND);
        }
    }

    private function serializeWidgetForUi($widget)
    {
        $log = $widget->getLog();
        if (is_array($log)) {
            $log = array_map([$this, 'serializeLogForUi'], $log);
        } elseif ($log instanceof \OCA\SensorLogger\Log) {
            $log = $this->serializeLogForUi($log);
        } else {
            $log = null;
        }

        return [
            'id' => $widget->getType() . '-' . $widget->getDeviceId(),
            'deviceId' => $widget->getDeviceId(),
            'type' => $widget->getType(),
            'name' => $widget->getName(),
            'displayName' => $widget->getDisplayName(),
            'log' => $log,
        ];
    }

    private function serializeLogForUi($log)
    {
        $data = [];
        foreach ($log->getData() as $entry) {
            $dataType = DataTypes::getDataTypeById(
                $this->userSession->getUser()->getUID(),
                $entry->getDataTypeId(),
                $this->connection
            );
            $data[] = [
                'dataTypeId' => $entry->getDataTypeId(),
                'value' => $entry->getValue(),
                'type' => $dataType ? $dataType->getType() : '',
                'short' => $dataType ? $dataType->getShort() : '',
                'description' => $dataType ? $dataType->getDescription() : '',
            ];
        }

        return [
            'id' => $log->getId(),
            'deviceUuid' => $log->getDeviceUuid(),
            'temperature' => $log->getTemperature(),
            'humidity' => $log->getHumidity(),
            'createdAt' => $log->getCreatedAt(),
            'data' => $data,
        ];
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
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
            return $this->returnJSON(array('success' => false, 'errors' => 'Could not create widget!'));
        }
        try {
            $this->setUserValue('widget-' . $widgetType . '-' . $widgetId, $this->userSession->getUser()->getUID(), $json);
            return $this->returnJSON(array(
                'success' => true,
                'id' => 'widget-' . $widgetType . '-' . $widgetId,
            ));
        } catch (Exception $e) {
            return $this->returnJSON(array('success' => false, 'errors' => 'Could not create widget!'));
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
     * @return DataResponse
     */
    public function saveDashboardLayout()
    {
        $layoutJson = $this->request->getParam('layout', '');
        $layout = is_string($layoutJson) ? json_decode($layoutJson, true) : null;
        if (!is_array($layout)
            || !is_array($layout['order'] ?? [])
            || !is_array($layout['heights'] ?? [])
            || !is_array($layout['spans'] ?? [])) {
            return $this->returnJSON(['success' => false, 'errors' => 'Invalid dashboard layout']);
        }

        $userId = $this->userSession->getUser()->getUID();
        $availableWidgets = array_map(
            static function ($widget) {
                return $widget->getType() . '-' . $widget->getDeviceId();
            },
            array_filter($this->getWidgets())
        );
        $order = [];
        foreach ($layout['order'] ?? [] as $widgetId) {
            if (is_string($widgetId) && in_array($widgetId, $availableWidgets, true) && !in_array($widgetId, $order, true)) {
                $order[] = $widgetId;
            }
        }

        $heights = [];
        foreach ($layout['heights'] ?? [] as $widgetId => $height) {
            if (in_array($widgetId, $availableWidgets, true) && is_numeric($height)) {
                $heights[$widgetId] = max(180, min(800, (int)$height));
            }
        }

        $spans = [];
        foreach ($layout['spans'] ?? [] as $widgetId => $span) {
            if (in_array($widgetId, $availableWidgets, true) && is_numeric($span)) {
                $spans[$widgetId] = max(1, min(3, (int)$span));
            }
        }

        $this->setUserValue(
            'dashboard-layout',
            $userId,
            json_encode(['order' => $order, 'heights' => $heights, 'spans' => $spans])
        );
        return $this->returnJSON(['success' => true]);
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
        $userId = $this->userSession->getUser()->getUID();
        if (Devices::getDevice($userId, (int)$id, $this->connection)
            && Devices::isDeletable($userId, (int)$id, $this->connection)) {
            try {
                Devices::deleteDevice((int)$id, $userId, $this->connection);
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
            SensorLogs::deleteLogsByUuid($device->getUuid(), $this->userSession->getUser()->getUID(), $this->connection);
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
            if (!$device) {
                return $this->returnJSON(['success' => false]);
            }

            if (!$this->deleteLogsByDevice($device)) {
                return $this->returnJSON(['errors' => 'Could not delete Logs for Device #' . $id]);
            }

            if (Devices::isDeletable($this->userSession->getUser()->getUID(), (int)$id, $this->connection)) {
                try {
                    Devices::deleteDevice((int)$id, $this->userSession->getUser()->getUID(), $this->connection);
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
        $deleted = SensorLogs::deleteLogById(
            (int)$id,
            $this->userSession->getUser()->getUID(),
            $this->connection
        );
        if ($deleted) {
            return $this->returnJSON(['success' => true]);
        }
        return $this->returnJSON(['success' => false]);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param int $id
     * @return TemplateResponse
     */
    public function showDeviceData($id)
    {
        return $this->getAppPageResponse('deviceLogs', 'deviceList', (int)$id);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param int $id
     * @return DataResponse
     */
    public function showDeviceDetails($id)
    {
        $deviceDetails = Devices::getDeviceDetails($this->userSession->getUser()->getUID(), $id, $this->connection);
        if (!$deviceDetails) {
            return new DataResponse(['error' => 'Device not found'], Http::STATUS_NOT_FOUND);
        }
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
     * @param int $id
     * @return DataResponse
     */
    public function updateDevice($id)
    {
        $field = $this->request->getParam('name');
        $value = $this->request->getParam('value');
        $editableFields = ['name', 'type_id', 'group_id', 'group_parent_id'];
        if (!in_array($field, $editableFields, true)) {
            return $this->returnJSON(['success' => false]);
        }

        $userId = $this->userSession->getUser()->getUID();
        if ($field === 'name') {
            if (!is_string($value) || trim($value) === '') {
                return $this->returnJSON(['success' => false]);
            }
            $value = trim($value);
        } elseif ($value === '' || $value === null) {
            $value = 0;
        } else {
            if (!is_string($value) && !is_int($value)) {
                return $this->returnJSON(['success' => false]);
            }
            $value = filter_var($value, FILTER_VALIDATE_INT);
            if ($value === false || $value < 0) {
                return $this->returnJSON(['success' => false]);
            }

            if ($value > 0) {
                $optionExists = $field === 'type_id'
                    ? DeviceTypes::getDeviceType($userId, $value, $this->connection)
                    : DeviceGroups::getDeviceGroupById($userId, $value, $this->connection);
                if (!$optionExists) {
                    return $this->returnJSON(['success' => false]);
                }
            }
        }

        $updated = Devices::updateDevice($id, $field, $value, $userId, $this->connection);
        return $this->returnJSON(['success' => $updated]);
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
        return $this->getAppPageResponse('dashboard', 'showDashboard');
    }

    /**
     * @param int $id
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function deviceChart($id)
    {
        return $this->getAppPageResponse('deviceChart', 'deviceList', (int)$id);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param int $id
     * @return string
     */
    public function chartData($id)
    {
        return $this->getChartData($id);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param int $id
     * @return string
     */
    public function chartDataLastLog($id)
    {
        return $this->getChartDataLastLog($id);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param integer $id
     * @param integer $param
     */
    public function maxLastLog($id, $param)
    {
        if (is_int($id) && (is_int($param) && $param !== 0)) {
            $device = Devices::getDevice(
                $this->userSession->getUser()->getUID(),
                $id,
                $this->connection
            );
            if (!$device) {
                return new DataResponse(['error' => 'Device not found'], Http::STATUS_NOT_FOUND);
            }

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
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function deviceList()
    {
        return $this->getAppPageResponse('devices', 'deviceList');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function deviceTypeList()
    {
        return $this->getAppPageResponse('deviceTypes', 'deviceTypeList');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function deviceGroupList()
    {
        return $this->getAppPageResponse('deviceGroups', 'deviceGroupList');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return TemplateResponse
     */
    public function dataTypeList()
    {
        return $this->getAppPageResponse('dataTypes', 'dataTypeList');
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
        if (!$device) {
            return new DataResponse(['error' => 'Device not found'], Http::STATUS_NOT_FOUND);
        }

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
        if (!$device) {
            return [];
        }
        $logs = SensorLogs::getLogsByUuId($this->userSession->getUser()->getUID(), $device->getUuid(), $this->connection);
        return $logs;
    }
}
