# Development guidelines

## Supported environment

SensorLogger is an ownCloud app. Its app metadata declares support for ownCloud 10–11 and PHP 8.0 or later. Validate changes against the actual ownCloud runtime where possible, especially for framework and database APIs; IDE-indexed core sources may not match the deployed core checkout.

## Recommended onboarding and development environment

Use the [owncloud-docker-development environment](https://github.com/alexstocker/owncloud-docker-development) to get started. It provides a Docker-based ownCloud installation with the SensorLogger checkout bind-mounted into the server, so contributors can work on the app and see changes in a running ownCloud instance without first assembling the full platform manually. This keeps onboarding steps focused and reduces the learning curve for ownCloud-specific development.

Follow that repository's README for prerequisites, initial setup, and container commands. In this development checkout, the top-level `sensorlogger` path points to the app checkout under `vendor/alexstocker/sensorlogger`; make SensorLogger changes in that app checkout and use the Docker environment to validate runtime behavior.

## Project layout

- `controller/`: web and OCS/API request handling.
- `lib/`: entities, database operations, widgets, and app services.
- `appinfo/`: app metadata, routes, and registration.
- `templates/`, `css/`, `js/`, and `l10n/`: user interface and translations.
- `tests/unit/`: isolated unit tests.
- `tests/integration/`: tests requiring ownCloud's app container or services.
- `scripts/`: CLI demos for registering a device and sending simulated API readings.

## PHP and database changes

- PSR-12 is mandatory for all new and modified PHP code. Run `vendor/bin/phpcs -p <changed-paths> --standard=PSR12` and resolve reported violations before merging. Avoid unrelated formatting changes to legacy files.
- Preserve the `OCA\SensorLogger` namespace and project conventions that do not conflict with PSR-12.
- Keep controller validation and response behavior explicit. Cover both successful and invalid-input paths when changing an endpoint.
- Use parameterized query-builder operations for database input. For DBAL 3 named parameters, bind keys without the leading colon.
- Entity constructors must honor ownCloud's `Entity` / `EntityInterface` constructor contract. Use setters or factory methods to populate entities rather than introducing incompatible constructor signatures.
- When changing serialized or API data, preserve the established response shape unless the change intentionally updates that contract.

## Tests and checks

Install the app's development dependencies with Composer, then run unit tests using `phpunit -c phpunit.xml`. The unit-test bootstrap loads ownCloud core when it can find `lib/base.php`; set `OWNCLOUD_ROOT` to the ownCloud checkout path when it cannot infer the correct root.

Run the PHP 8.3 compatibility scan with:

```sh
vendor/bin/phpcs -p lib/ controller/ --standard=PHPCompatibility --runtime-set testVersion 8.3
```

For focused changes, lint changed files with `php -l`, run PSR-12 checks on all changed PHP files, and execute the relevant unit tests. Run integration tests only in a configured ownCloud environment with its test database and services available.

## Change hygiene

- Add a concise entry under `0.1.2 UNRELEASED` in `CHANGELOG.md` for user-visible fixes or behavior changes.
- Keep test fixtures and request examples consistent with API changes.
- Do not commit credentials, runtime data, dependency caches, or generated artifacts.
- Before finishing, inspect the diff and run `git diff --check`; retain unrelated existing worktree changes.
