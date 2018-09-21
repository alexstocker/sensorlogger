# SensorLogger
### (0.0.7) UNRELEASED
  * WipeOut Device #49
  * Fixed missing device.displayName in widget config dropdown
  * Updated CHANGELOG
  * Added new Dashboard widget 24h Max Values #40 suggested by @ei-ke
  * Added new Dashboard widget 24h Max Values
  * Add wipeout icon set #49
  * Adding ClassFinder, MaxValues24hWidget, Widget Interface

### (0.0.6) RELEASED
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
