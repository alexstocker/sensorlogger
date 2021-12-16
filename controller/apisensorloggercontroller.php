<?php

namespace OCA\SensorLogger\Controller;

use Guzzle\Plugin\ErrorResponse\Exception\ErrorResponseException;
use OC\OCS\Exception;
use OC\OCS\Result;
use OC\Share\Share;
use OCA\SensorLogger\DataType;
use OCA\SensorLogger\DataTypes;
use OCA\SensorLogger\Error;
use OCA\SensorLogger\Devices;
use OCP\API;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
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
use OCP\Share\Exceptions\ShareNotFound;
use OCP\Share\IManager;
use OCP\Share\IShare;

/**
 * Class ApiSensorLoggerController
 *
 * @package OCA\SensorLogger\Controller
 */
class ApiSensorLoggerController extends ApiController
{
    private $db;

    /**
     * @var IUserSession
     */
    private $userSession;

    /** @var IManager */
    private $shareManager;
    /** @var IGroupManager */
    private $groupManager;
    /** @var IUserManager */
    private $userManager;
    /** @var IUser */
    private $currentUser;
    /** @var IL10N */
    private $l;

    private $logger;

    protected $errors = [];

    protected $config;

    protected $response;

    public function __construct(
        $AppName,
        IRequest $request,
        IDBConnection $db,
        IConfig $config,
        IManager $shareManager,
        IGroupManager $groupManager,
        IUserManager $userManager,
        IL10N $l10n,
        IUserSession $userSession,
        ILogger $logger
    ) {
        parent::__construct(
            $AppName,
            $request,
            'PUT, POST, GET, DELETE, PATCH',
            'Authorization, Content-Type, Accept',
            1728000
        );
        $this->db = $db;
        $this->userSession = $userSession;
        $this->config = $config;
        $this->shareManager = $shareManager;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->l = $l10n;
        $this->logger = $logger;
    }

    public function log($message)
    {
        $this->logger->error($message, ['app' => $this->appName]);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @CORS
     * @return Error|JSONResponse
     * @throws \Exception
     */
    public function createLog()
    {
        $params = $this->request->getParams();
        if (!isset($params['deviceId']) || $params['deviceId'] === null || empty($params['deviceId'])) {
            $this->errors[] = 'deviceId required';
        }

        if (!isset($params['data'])) {
            if (!isset($params['temperature']) && !isset($params['humidity'])) {
                $this->errors[] = 'Neither temperature nor humidity data found';
            }
        }

        if (isset($params['data'])) {
            if (empty($params['data'])) {
                $this->errors[] = 'No Sensor data found';
            } else {
                foreach ($params['data'] as $param) {
                    if (is_array($param)) {
                        if (!array_key_exists('dataTypeId', $param)
                            || !array_key_exists('value', $param)) {
                            $this->errors[] = 'Data Array needs to contain dataTypeId AND value';
                        } else {
                            if (!(int)$param['dataTypeId']) {
                                $this->errors[] = 'dataTypeId needs to be an integer';
                            }
                            if (!is_numeric($param['value'])) {
                                $this->errors[] = 'value needs to be an integer or float';
                            }
                        }
                    } else {
                        $this->errors[] = 'Malformed data found';
                    }
                }
            }
        }

        if (!empty($this->errors)) {
            return $this->requestResponse(false, Error::MISSING_PARAM, implode(',', $this->errors));
        }

        if (isset($params['data']) && empty($this->errors)) {
            if ($this->checkRegisteredDataTypes($params)
                && empty($this->errors)) {
                $this->insertExtendedLog($params);
            }
        }

        if (!isset($params['data']) && empty($this->errors)) {
            $this->insertLog($params);
        }
        if (!empty($this->errors)) {
            return $this->requestResponse(false, Error::NOT_FOUND, implode(',', $this->errors));
        }
        return $this->requestResponse(
            true,
            Http::STATUS_OK,
            'Sensor Log successfully stored',
            $params['data']
        );
    }

    /**
     * @param $array
     * @return bool
     */
    protected function insertExtendedLog($array)
    {
        if (!$this->checkRegisteredDevice($array)) {
            $this->errors[] = 'Device #'.$array['deviceId'].' not registered';
            return false;
        }
        if (!isset($array['date']) || empty($array['date'])) {
            $array['date'] = date('Y-m-d H:i:s');
        }

        $deviceId = $array['deviceId'];
        $dataJson = json_encode($array['data']);

        $query = $this->db->getQueryBuilder();
        $query->insert('sensorlogger_logs')
            ->values([
                'created_at' => $query->createNamedParameter($array['date']),
                'user_id' => $query->createNamedParameter($this->userSession->getUser()->getUID()),
                'device_uuid' => $query->createNamedParameter($deviceId),
                'data' => $query->createNamedParameter($dataJson)
            ])
            ->execute();
    }

    /**
     * @param array $array
     * @return bool
     */
    protected function insertLog($array)
    {
        $registered = $this->checkRegisteredDevice($array);
        if (isset($array['deviceId'])) {
            if (!$registered) {
                $registered = $this->insertDevice($array);
            }
        }

        if (!isset($array['date']) || empty($array['date'])) {
            $array['date'] = date('Y-m-d H:i:s');
        }

        if ($registered || !isset($array['deviceId'])) {
            $deviceId = $array['deviceId'] ?: null;

            $dataJson = json_encode($array);

            $query = $this->db->getQueryBuilder();
            $query->insert('sensorlogger_logs')
                ->values([
                    'temperature' => $query->createNamedParameter($array['temperature']),
                    'humidity' => $query->createNamedParameter($array['humidity']),
                    'created_at' => $query->createNamedParameter($array['date']),
                    'user_id' => $query->createNamedParameter($this->userSession->getUser()->getUID()),
                    'device_uuid' => $query->createNamedParameter($deviceId),
                    'data' => $query->createNamedParameter($dataJson)
                ])
                ->execute();
        }
        return true;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @CORS
     * @return JSONResponse|Error
     * @throws \Exception
     */
    public function registerDevice()
    {
        if (!$this->checkRegisteredDevice($this->request->getParams()) &&
            $this->checkRequestParams($this->request->getParams()) &&
            empty($this->errors)) {
            $params = $this->request->getParams();
            $lastInsertId = $this->insertDevice($this->request->getParams());
            if (is_int($lastInsertId)) {

                /** Insert option param deviceType */
                if (isset($params['deviceType'])) {
                    $deviceTypeId = $this->insertDeviceType($params['deviceType']);
                    if (is_int($deviceTypeId)) {
                        try {
                            Devices::updateDevice($lastInsertId, 'type_id', (string)$deviceTypeId, $this->db);
                        } catch (\Exception $e) {
                            $this->errors[] = $e->getMessage();
                        }
                    }
                }

                /** Insert option param deviceGroup */
                if (isset($params['deviceGroup'])) {
                    $deviceGroupId = $this->insertDeviceGroup($params['deviceGroup']);
                    if (is_int($deviceGroupId)) {
                        try {
                            Devices::updateDevice($lastInsertId, 'group_id', $deviceGroupId, $this->db);
                        } catch (\Exception $e) {
                            $this->errors[] = $e->getMessage();
                        }
                    }
                }

                /** Insert option param deviceParentGroup */
                if ($params['deviceParentGroup']) {
                    $deviceGroupParentId = $this->insertDeviceGroup($params['deviceParentGroup']);
                    if (is_int($deviceGroupParentId)) {
                        try {
                            Devices::updateDevice($lastInsertId, 'group_parent_id', $deviceGroupParentId, $this->db);
                        } catch (\Exception $e) {
                            $this->errors[] = $e->getMessage();
                        }
                    }
                }

                foreach ($params['deviceDataTypes'] as $key => $array) {
                    $availableDataTypes = DataTypes::getDataTypesByUserId($this->userSession->getUser()->getUID(), $this->db);
                    /** @var DataType $availableDataType */
                    foreach ($availableDataTypes as $availableDataType) {
                        if ($availableDataType->getShort() === $array['unit'] && $availableDataType->getType() === $array['type']) {
                            $dataTypeId = $availableDataType->getId();
                            if (is_int($dataTypeId)) {
                                $this->insertDeviceDataTypes($lastInsertId, $dataTypeId);
                            }
                            continue 2;
                        }
                    }
                    $dataTypeId = $this->insertDataTypes($array);
                    if (is_int($dataTypeId)) {
                        $this->insertDeviceDataTypes($lastInsertId, $dataTypeId);
                    }
                }
                $deviceDataTypes = DataTypes::getDeviceDataTypesByDeviceId($this->userSession->getUser()->getUID(), $lastInsertId, $this->db);
                return $this->requestResponse(
                    true,
                    Http::STATUS_OK,
                    'Device successfully registered',
                    $deviceDataTypes
                );
            }
        } elseif (!empty($this->errors)) {
            return $this->requestResponse(false, Error::MISSING_PARAM, implode(',', $this->errors));
        } else {
            return $this->requestResponse(false, Error::DEVICE_EXISTS, 'Device already exists!');
        }

        return $this->requestResponse(false, Error::UNKNOWN, 'RegisterDevice failed due UNKNOWN reason. Sorry.');
    }

    /**
     * @param bool $success
     * @param int|null $code
     * @param string|null $message
     * @param array|null $data
     * @return Error|JSONResponse
     * @throws \Exception
     */
    protected function requestResponse($success, $code = null, $message = null, $data = [])
    {
        if (!$success) {
            if ($code === null) {
                $code = Error::UNKNOWN;
            }
            $response = new JSONResponse();
            $array = [
                'success' => false,
                'error' => ['code' => $code,
                    'message' => $message
                ]
            ];
        } else {
            $response = new JSONResponse();
            $array = [
                'success' => true,
                'message' => $message,
                'data' => $data
            ];
        }
        $response->setData($array)->render();
        return $response;
    }

    /**
     * @param $deviceType
     * @return bool|int
     */
    protected function insertDeviceType($deviceType)
    {
        $sql = 'INSERT INTO `*PREFIX*sensorlogger_device_types` (`user_id`,`device_type_name`) VALUES(?,?)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $this->userSession->getUser()->getUID());
        $stmt->bindParam(2, $deviceType);
        if ($stmt->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    /**
     * @param string $string
     * @return int
     */
    protected function insertDeviceGroup($string)
    {
        $sql = 'INSERT INTO `*PREFIX*sensorlogger_device_groups` (`user_id`,`device_group_name`) VALUES(?,?)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $this->userSession->getUser()->getUID());
        $stmt->bindParam(2, $string);
        if ($stmt->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    /**
     * @param array $array
     * @return int
     */
    protected function insertDataTypes($array)
    {
        $sql = 'INSERT INTO `*PREFIX*sensorlogger_data_types` (`user_id`,`description`,`type`,`short`) VALUES(?,?,?,?)';
        $stmt = $this->db->prepare($sql);

        if (isset($array['description'])) {
            $description = $array['description'] ?: '';
        } else {
            $description = '';
        }

        if (isset($array['type'])) {
            $type = $array['type'] ?: '';
        } else {
            $type = '';
        }

        if (isset($array['unit'])) {
            $unit = $array['unit'] ?: '';
        } else {
            $unit = '';
        }

        $stmt->bindParam(1, $this->userSession->getUser()->getUID());
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $type);
        $stmt->bindParam(4, $unit);
        if ($stmt->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    /**
     * @param $params
     * @return bool|JSONResponse
     */
    protected function checkRequestParams($params)
    {
        if (isset($params['deviceType']) && empty($params['deviceType'])) {
            $this->errors[] = 'Param deviceType is set but empty';
        }
        if (isset($params['deviceGroup']) && empty($params['deviceGroup'])) {
            $this->errors[] = 'Param deviceGroup is set but empty';
        }
        if (isset($params['deviceParentGroup']) && empty($params['deviceParentGroup'])) {
            $this->errors[] = 'Param deviceParentGroup is set but empty';
        }
        if (!isset($params['deviceId']) || empty($params['deviceId'])) {
            $this->errors[] = 'Param deviceId missing';
        }
        if (!isset($params['deviceDataTypes']) || empty($params['deviceDataTypes'])) {
            $this->errors[] = 'Param deviceDataTypes missing!';
        }
        if (!empty($params['deviceDataTypes'])) {
            foreach ($params['deviceDataTypes'] as $deviceDataType) {
                if (!array_key_exists('type', $deviceDataType)) {
                    $this->errors[] = 'DeviceDataType Param type missing!';
                }
                if (!array_key_exists('description', $deviceDataType)) {
                    $this->errors[] = 'DeviceDataType Param description missing!';
                }
                if (!array_key_exists('unit', $deviceDataType)) {
                    $this->errors[] = 'DeviceDataType Param unit missing!';
                }
            }
        }
        if (empty($this->errors)) {
            return true;
        }
        return false;
    }

    /**
     * @param int $deviceId
     * @param int $dataTypeId
     */
    protected function insertDeviceDataTypes($deviceId, $dataTypeId)
    {
        $sql = 'INSERT INTO `*PREFIX*sensorlogger_device_data_types` (`user_id`,`device_id`,`data_type_id`) VALUES(?,?,?)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $this->userSession->getUser()->getUID());
        $stmt->bindParam(2, $deviceId);
        $stmt->bindParam(3, $dataTypeId);
        $stmt->execute();
    }

    /**
     * @param $params
     * @return bool;
     */
    protected function checkRegisteredDevice($params)
    {
        if (isset($params['deviceId'])) {
            $device = Devices::getDeviceByUuid(
                $this->userSession->getUser()->getUID(),
                $params['deviceId'],
                $this->db
            );

            if ($device) {
                return true;
            }
        }
        return false;
    }

    protected function checkRegisteredDataTypes($params)
    {
        if (!isset($params['data'])) {
            return false;
        }
        $dataArray = $params['data'];
        $userDataTypes = DataTypes::getDataTypes($this->userSession->getUser()->getUID(), $this->db);

        foreach ($dataArray as $dataKey => $data) {
            foreach ($userDataTypes as $userDataType) {
                if ((int)$userDataType['id'] === (int)$data['dataTypeId']) {
                    unset($dataArray[$dataKey]);
                }
            }
        }

        if (!empty($dataArray)) {
            foreach ($dataArray as $errData) {
                $this->errors[] = 'dataTypeId #' . $errData['dataTypeId'] .
                    ' for User ' . $this->userSession->getUser()->getUID() . ' unknown';
            }
            return false;
        }
        return true;
    }

    /**
     * @param array $array
     * @return int|string
     */
    protected function insertDevice($array)
    {
        $sql = 'INSERT INTO `*PREFIX*sensorlogger_devices` (`uuid`,`name`,`type_id`,`user_id`) VALUES(?,?,?,?)';
        $stmt = $this->db->prepare($sql);

        if (isset($array['deviceId'])) {
            if (!isset($array['deviceName'])) {
                $array['deviceName'] = 'Default device';
            }

            if (!isset($array['deviceTypeId'])) {
                $array['deviceTypeId'] = 0;
            }

            $stmt->bindParam(1, $array['deviceId']);
            $stmt->bindParam(2, $array['deviceName']);
            $stmt->bindParam(3, $array['deviceTypeId']);
            $stmt->bindParam(4, $this->userSession->getUser()->getUID());

            if ($stmt->execute()) {
                return (int)$this->db->lastInsertId();
            }
        } else {
            return 'Missing device ID';
        }
        return false;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @CORS
     * @return Error|JSONResponse
     * @throws \Exception
     */
    public function getDeviceDataTypes()
    {
        $params = $this->request->getParams();
        if (!isset($params['deviceId'])) {
            $this->errors[] = 'deviceId required';
            return $this->requestResponse(false, Error::MISSING_PARAM, implode(',', $this->errors));
        }

        $device = Devices::getDeviceByUuid($this->userSession->getUser()->getUID(), $params['deviceId'], $this->db);

        if ($device === null || $device === false) {
            $this->errors[] = 'No Device Found';
            return $this->requestResponse(false, Error::NOT_FOUND, implode(',', $this->errors));
        }

        $deviceDataTypes = DataTypes::getDeviceDataTypesByDeviceId($this->userSession->getUser()->getUID(), $device->getId(), $this->db);
        if ($deviceDataTypes === null || $deviceDataTypes === false) {
            $this->errors[] = 'No DataTypes for Device #'.$params['deviceId'].' found';
            return $this->requestResponse(false, Error::NOT_FOUND, implode(',', $this->errors));
        }

        return $this->requestResponse(
            true,
            Http::STATUS_OK,
            'DataTypes for Device #'.$params['deviceId'],
            $deviceDataTypes
        );
    }

    /**
     * Get all shared devices
     */
    public function getAllShares()
    {
        # TODO [GH12] Add apisensorloggercontroller::getallshares
    }

    /**
     * Share a device
     */
    public function createShare()
    {
        # TODO [GH13] Add apisensorloggercontroller::createShare

		$share = $this->shareManager->newShare();
		if (!$this->shareManager->shareApiEnabled()) {
			return new Result(null, 404, $this->l->t('Share API is disabled'));
		}
		$name = $this->request->getParam('name', null);

    }

    /**
     * Get a shared device by id
     * @param $id
     * @return Result
     */
    public function getShare($id)
    {
        # TODO [GH14] Add apisensorloggercontroller::getShare
        if (!$this->shareManager->shareApiEnabled()) {
            return new Result(null, 404, $this->l->t('Share API is disabled'));
        }

        try {
            $share = $this->getShareById($id);
        } catch (ShareNotFound $e) {
            return new Result(null, 404, $this->l->t('Wrong share ID, share doesn\'t exist'));
        }

        if ($this->canAccessShare($share)) {
            try {
            } catch (Exception $e) {
            }
        }

        return new Result(null, 404, $this->l->t('Wrong share ID, share doesn\'t exist'));
    }

    /**
     * @param $id
     * @return null|IShare
     * @throws ShareNotFound
     */
    private function getShareById($id)
    {
        # TODO [GH15] Add apisensorloggercontroller::getShareById

        $share = null;

        try {
            $share = $this->shareManager->getShareById('ocinternal:' . $id);
        } catch (ShareNotFound $e) {
            if (!$this->shareManager->outgoingServer2ServerSharesAllowed()) {
                throw new ShareNotFound();
            }
            $share = $this->shareManager->getShareById('ocFederatedSharing:' . $id);
        }

        return $share;
    }

    /**
     * Update shared device
     */
    public function updateShare()
    {
        # TODO [GH16] Add apisensorloggercontroller::updateShare
    }

    /**
     * Delete a shared device
     */
    public function deleteShare()
    {
        # TODO [GH17] Add apisensorloggercontroller::deleteshare
    }

    /**
     * @param IShare $share
     * @return bool
     */
    protected function canAccessShare(IShare $share)
    {
        // A file with permissions 0 can't be accessed by us. So Don't show it
        if ($share->getPermissions() === 0) {
            return false;
        }

        // Owner of the file and the sharer of the file can always get share
        if ($share->getShareOwner() === $this->currentUser->getUID() ||
            $share->getSharedBy() === $this->currentUser->getUID()
        ) {
            return true;
        }

        // If the share is shared with you (or a group you are a member of)
        if ($share->getShareType() === Share::SHARE_TYPE_USER &&
            $share->getSharedWith() === $this->currentUser->getUID()) {
            return true;
        }

        if ($share->getShareType() === Share::SHARE_TYPE_GROUP) {
            $sharedWith = $this->groupManager->get($share->getSharedWith());
            if (!is_null($sharedWith) && $sharedWith->inGroup($this->currentUser)) {
                return true;
            }
        }

        return false;
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
            return new DataResponse(array('msg' => 'not found!'), API::RESPOND_NOT_FOUND);
        }
    }
}
