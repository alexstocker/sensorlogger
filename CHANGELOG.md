# SensorLogger for owncloud

### 0.1.1 UNRELEASED
  * Reworked and cleaned up JS #11
  * Added #51 make dashboard widgets position persistant
  * Modified #85 Allow 0 value @Kixunil
  * Changed Dashboard request type to GET
  * Changed LogList request type to GET
  * Changed DeviceList request type to GET
  * Changed DeviceTypeList request type to GET
  * Changed DeviceGroupList request type to GET
  * Changed DataTypeList request type to GET
  
### 0.1.0 RELEASED
  * Fixed GetDeviceDataTypes API call
  * Renamed SensorDevices to Devices
  * Renamed SensorGroups to DeviceGroup
  * Added DeleteDeviceGroup, Added DeleteDeviceType. #73 #6 contributed by @issb-gh
  * Fix bug in registerDevice, added some more checks on api registerDevice params
  * Added Missing Initial Migration #71
  * Change Database Scheme set user_id and uuid unique by migration #71
  * Modified DeviceId to unique for each user #71
  * Added more data validation on createLog #69
  * Added some more data validation #69 @vitoller
  * Modified registerDevice set deviceType optional
  * Modified registerDevice set deviceGroup optional
  * Modified registerDevice set deviceParentGroup optional 
  * Modified Device Sidebar content by adding some Labels
  * Modified Device Sidebar Select boxes by making the ability to create on the fly more visible
  * Added Share/Edit Icons to Devices. Removed Delete Icon because of already existing wipeOut action in Sidebar. #19 #74
  * Updated Controllers, Templates und JS
  * Fix index error on registerDevice, api registerDevice params validation
  * Updated README
  * Added missing initial Migration replaces database.xml
  * Updated CHANGELOG by adding some issue mentions #71 #69
  * Added data valiation on createLog #69. Added Database Migration to add unique index on user_id and uuid
  * Modified registerDevice and Device Sidebar
  * Added some more data validation #69 @vitoller
  * Removed obsolete and undefined properties in LogExtended
  * Adding some more requirements
  
### 0.0.10 REVOKED

### 0.0.9 RELEASED
  * Fixed min-version property due semver

### 0.0.8 RELEASED
  * Syncronisation Release to close gap between SensorLogger vor Owncloud and Nextcloud

### 0.0.7 RELEASED
  * WipeOut Device #49
  * Fixed missing device.displayName in widget config dropdown
  * Updated CHANGELOG
  * Added new Dashboard widget 24h Max Values #40 suggested by @ei-ke
  * Added new Dashboard widget 24h Max Values
  * Add wipeout icon set #49
  * Adding ClassFinder, MaxValues24hWidget, Widget Interface

### 0.0.6 RELEASED
  * Added SensorLogs::deleteLogById and deleteLog controller action #37'
  * Added dashboard widgets column media styles
  * Added timeoffset calculation for realtime charts
  * Added RealTime Data widget
  * Started to rebuild script.js #11, added RealTime Chart widget
  * Updated insertExtendedLog and insertLog to use QueryBuilder
  * Added SensorLogs::deleteLogById and deleteLog controller action #37'
  * Added dashboard widgets column media styles
  * Create ISSUE_TEMPLATE.md
  * Add CONTRIBUTING.md
  * Added CODE_OF_CONDUCT.md
  * Updated README
  
### 0.0.5
  * Fixed missing Select2 on oc v10.0.3, added DataTypes::deleteDeviceDataTypesByDeviceId, SensorDevices::isDeletable
  * Added missing DataTypes::getDataTypesByUserId
  * Added suggest from @bargru #33
  * Added .gitignore and updated CHANGELOG
  * Added DeleteDevice action, added Chart zooming ability, added Error some error codes, updated test/curl examples
  * Added Issue reference #26
  * Hotfix
  * Minor changes
  * Version updated to 0.0.3
  * Updated README
  * Updated App image
  * Added navigationManager, extended API controller and main controller by sharing stuff (to be implemented)
  * Updated and extend getters/setters, fixed some error
  * Fixed version
  * Removed TODO tag
  * Added TODO
  * Added deleteWidget

### 0.0.4.1

  * Now checking for already available DataTypes to omit duplicates on device registration
  * Fixed Bug referring to Issue #29 reported by @user17476566786

### 0.0.4

  * dashboard widget (chart)
  * added extended registerDevice (example: ./tests/curl/register.php)
  * added extended createLog (example: ./tests/curl/post_extend.php)
  * added getDataTypes (example: ./tests/curl/getdatatypes.php)

### 0.0.2

 * dashboard widget (list and last)
 * chart view

-- Alexander Stocker
-- alexstocker
-- Ozzie Isaacs
