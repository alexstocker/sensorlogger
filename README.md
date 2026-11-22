# SensorLogger

### Latest Stable Release
- owncloud [0.1.1](https://github.com/alexstocker/sensorlogger/releases/tag/0.1.1)
- nextcloud [0.2.0nc](https://github.com/alexstocker/sensorlogger/releases/tag/0.2.0nc)

### Latest Stable Branches
- owncloud [0.1-stable](https://github.com/alexstocker/sensorlogger/tree/0.1-stable)
- nextcloud [0.2-nc-stable](https://github.com/alexstocker/sensorlogger/tree/0.2-nc-stable)

### Current Development Branches
- owncloud [0.1-devel](https://github.com/alexstocker/sensorlogger/tree/0.1-devel)
- nextcloud [0.2-nc-devel](https://github.com/alexstocker/sensorlogger/tree/0.2-nc-devel)

### Upcoming Versions
- owncloud 0.1.2
- nextcloud 0.2.1

### Development Environments
* Checkout [Nextcloud Docker Development for SensorLogger](https://github.com/sensorlogger/nextcloud-docker-development)
* Checkout [Owncloud Docker Development for SensorLogger](https://github.com/sensorlogger/owncloud-docker-development)

### Development and Unit Tests

For the easiest ownCloud development setup, use [owncloud-docker-development](https://github.com/alexstocker/owncloud-docker-development). It provides a Docker-based ownCloud instance with SensorLogger mounted into the running server, avoiding the need to assemble an ownCloud stack separately. Follow that repository's README to install dependencies and start the containers.

From the development checkout, run the SensorLogger unit tests inside the ownCloud container:

```sh
docker exec -u www-data -w /var/www/owncloud/apps-external/sensorlogger \
  owncloud_server env OWNCLOUD_ROOT=/var/www/owncloud \
  vendor/bin/phpunit -c phpunit.xml
```

PHPUnit should report `OK` and the number of tests and assertions. To generate a Clover coverage report as well, run PHPUnit with coverage mode enabled:

```sh
docker exec -u root -w /var/www/owncloud/apps-external/sensorlogger \
  -e OWNCLOUD_ROOT=/var/www/owncloud -e XDEBUG_MODE=coverage \
  owncloud_server vendor/bin/phpunit -c phpunit.xml \
  --coverage-filter lib --coverage-filter controller \
  --coverage-clover tests/unit-coverage.xml --coverage-text
```

The coverage report is written to `sensorlogger/tests/unit-coverage.xml`. Coverage requires Xdebug in the ownCloud container; the text summary is also printed in the terminal.

Run the API integration suite against the ownCloud database with:

```sh
docker exec -u www-data -w /var/www/owncloud/apps-external/sensorlogger \
  owncloud_server env OWNCLOUD_ROOT=/var/www/owncloud \
  vendor/bin/phpunit -c phpunit.integration.xml
```

The API integration test registers a temporary device, checks its data types, and stores an extended log. Database changes are rolled back after each test; no credentials or manual HTTP requests are needed.

### API Demo Scripts

The `scripts/` directory contains interactive examples for registering a demo device, listing its data types, and sending simulated sensor readings. These scripts send real requests to ownCloud and store sensor data. In the recommended owncloud-docker-development checkout, add or update these settings in the root `.env` (existing `.env` files are not overwritten when defaults change):

```sh
SENSORLOGGER_URL=http://localhost:8080
SENSORLOGGER_USERNAME=admin
SENSORLOGGER_APP_PASSWORD=your-ownCloud-app-password
SENSORLOGGER_DEVICE_ID=
```

Use an app password created in ownCloud's Security settings, not `ADMIN_PASSWORD`. From the development checkout, run:

```sh
php scripts/sensorlogger-demo.php register
php scripts/sensorlogger-demo.php types
php scripts/sensorlogger-demo.php extended
php scripts/sensorlogger-demo.php basic
```

The script reads SensorLogger settings from the development `.env`, while explicitly exported shell variables take precedence. After registration, save the printed ID as `SENSORLOGGER_DEVICE_ID` in `.env` before running `types` or `extended`. If not using the development checkout, export `SENSORLOGGER_URL`, `SENSORLOGGER_USERNAME`, and `SENSORLOGGER_APP_PASSWORD` in the shell. The `basic` command can run without registering a device first. To continuously send readings, use `scripts/sensorlogger-demo.sh basic [interval-seconds]` or `scripts/sensorlogger-demo.sh extended [interval-seconds]`; stop with Ctrl-C. The extended mode requires a registered device ID. Both scripts default to `http://localhost:8080` and require PHP's cURL extension.

Read more [SensorLogger WIKI](https://github.com/alexstocker/sensorlogger/wiki/)

![SensorLogger for nextcloud Dashboard](https://www.html5live.at/wp-content/uploads/2026/10/sensorlogger-nc35-dashboard.png)

![SensorLogger for nextcloud Widget Sidebar](https://www.html5live.at/wp-content/uploads/2026/10/sensorlogger-nc35-widget-sidebar.png)

![SensorLogger for nectcloud Device Details](https://www.html5live.at/wp-content/uploads/2026/10/ensorlogger-nc35-device-edit.png)
