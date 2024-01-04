# SensorLogger

### 0.1.4nc UNRELEASED
  * Added [NOREF] NC25 Support
  * Added [NOREF] NC24 Support. Closing #106 #100 #92 #80 #101
  * Fixed [NOREF] Migration on dropping non exisiting sensorlogger_devices_unique_idx
  * [NOREF] Cleanup
  * Fixed #101 DB Queries. Make use of queryBuilder. 
  * Fixed #103 Migration Version000010Date20190206183552. Drop index before setting new. Thanx @stefan123t for the hint
  * Fixed #103 Devices::getDevices contributed by @stefan123t
  * Added #101 PHP 8 and #100 NC23 Support
  * #98 Modified App description

### 0.1.3nc RELEASED
  * updated to support Nextcloud 20

### 0.1.2nc RELEASED
  * #85 Fixed error on submitting 0 values
  * #89 Updated to support Nextcloud 17
  * #86 Fixed fatal on widgetTemplateName

### 0.1.1nc RELEASED
  * jqPlot cleanup
  * Updated Screenshots
  * Minor css style changes
  * Raised supported nc version to 16 #81 and supported PHP Version to 7.3 #80. Thanx to @e-alfred and @c99ipnerd
  * Changed Widget Header background color to primary
  * Set Sidebar container absolute  

### 0.1.0nc RELEASED
  * Modified DataScheme and registerDevice #71. Added more data validation stuff #69
  * Modified registerDevice by making some params optional and modified Device Sidbar select boxes
  * Renamed SensorDevices to Devices
  * Renamed SensorGroups to DeviceGroup
  * Added DeleteDeviceGroup, Added DeleteDeviceType. #73 #6 contributed by @issb-gh
  * Fix bug in registerDevice, added some more checks on api registerDevice params
  * Added Missing Initial Migration #71
  * Change Database Scheme set user_id and uuid unique by migration #71
  * Modified DeviceId to unique for each user #71
  * Added more data validation on createLog #69
  * Added Database Migrations replace database.xml #71
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

### 0.0.9nc RELEASED
  * Updated to support Nextcloud 15 #64

### 0.0.8nc RELEASED
  * Added 24hMaxValues widget, Widget Interface, wipeOut Device
  * Catch up to SensorLogger for Owncloud 0.0.7
  * Added DeviceWipeOut Iconset, maxValues24h widget Template
  
### 0.0.7nc RELEASED 
  * Updated to support Nextcloud 14 and fixed dashboard sidebar display problem #53

### 0.0.6nc RELEASED
  * Nextcloud 13 support
  * Added SensorLogs::deleteLogById and deleteLog controller action #37
  * Added dashboard widgets column media styles
  * Create ISSUE_TEMPLATE.md
  * Add CONTRIBUTING.md
  * Added CODE_OF_CONDUCT.md
  * Updated README
  
### 0.0.5.1nc RELEASED
  * Updated to support nextcloud 13 suggested by @e-alfred issue #42
  * Fixed Issue #27 initial reported by @gsantner
### 0.0.5nc RELEASED
  * Added DeleteDevice action, added Chart zooming ability, added Error some error codes, updated test/curl examples
  * Fixed Issue #27
  * Updated info description
  * Update to 0.0.4 for nextcloud
  * Updated version tag
  * Updated info.xml by repo and doc links
  * Updated Version to 0.0.3nc
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

  * Initial adaption for nextcloud
  * dashboard widget (chart)
  * added extended registerDevice (example: ./tests/curl/register.php)
  * added extended createLog (example: ./tests/curl/post_extend.php)
  * added getDataTypes (example: ./tests/curl/getdatatypes.php)

### 0.0.2

 * dashboard widget (list and last)
 * chart view
