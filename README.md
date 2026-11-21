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

Read more [SensorLogger WIKI](https://github.com/alexstocker/sensorlogger/wiki/)

![SensorLogger for nextcloud Dashboard](https://www.html5live.at/wp-content/uploads/2026/10/sensorlogger-nc35-dashboard.png)

![SensorLogger for nextcloud Widget Sidebar](https://www.html5live.at/wp-content/uploads/2026/10/sensorlogger-nc35-widget-sidebar.png)

![SensorLogger for nectcloud Device Details](https://www.html5live.at/wp-content/uploads/2026/10/ensorlogger-nc35-device-edit.png)
