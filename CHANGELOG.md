# SensorLogger

#### 05.02.2019 (JB)
  * php-Seite DeviceTypes.php:
	* SQL-Statements prepared SQL Statements geaendert (setParameters), um SQL-Injektionen zu verhindern
	
  * php-Seite SensorGroups.php:
	* Methoden deleteDeviceGroupByX[Id/Name] implementiert
	* SQL-Statements von QueryBuilder auf prepared SQL Statements geaendert, um SQL-Injektionen zu verhindern

#### 04.02.2019 (JB)
  * php-Seite apisensorloggercontroller.php:
	* return value function checkRegisteredDevice changed (now true/false again)
	* code at the caller positions adjusted accordingly
	
#### 28.01.2019 (JB)
  * php-Seite apisensorloggercontroller.php:
	* registerDevice: Code-Optimierung (returnvalue !== null, params)
	* Aenderung der InsertX-Strategie: bisher insert into -> getId, jetzt select -> getId oder insert into getId
	* checkRegisteredDevice: Code-Optimierung (select id, return value: id)
	* insertExtendedLog: Bugfix (return false bei Fehler)
	* insertDevice: Bugfix (return value nur int, -1 bei Fehler, ansonsten id)
	* methode getDeviceTypes eingefuegt
	* alle Abfragen mit is_int durch is_numeric ausgetauscht
	
  * php-Seite DeviceTypes.php:
	* methoden getDeviceTypeById, getDeviceTypeByName und getDeviceTypesByDeviceId hinzugefuegt
	* Aenderung der Methode insertDeviceType (Pruefung auf Existenz)
	
  * php-Seite routes.php:
	* array-Element getDeviceTypes eingefuegt

  * php-Seite part.listDeviceTypes.php:
	* <?php p($deviceType['uuid']); ?> entfernt
	
  * neue PHP-Seite Devices eingefuehrt (Alternative zu SensorDevices):
	* Methoden liefern Id als Integer oder Array als DB-Fields
  
### 24.01.2019 (JB)
  * Limit 1000 -> 100: getChartData in getChartData (sensorloggercontroller.php)
  * UUID -> Name: Device->GetUuid() -> Device->GetName() in <h3> (part.chart.php)
  

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
