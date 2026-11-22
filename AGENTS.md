# Agent instructions

These instructions apply to the SensorLogger app in this repository.

## Repository scope

- In the ownCloud Docker development checkout, `sensorlogger` points to the nested Git checkout at `vendor/alexstocker/sensorlogger`. Make app changes in that checkout, not in unrelated sibling apps or the ownCloud core.
- The recommended contributor setup is the surrounding owncloud-docker-development environment. Use its Docker-based ownCloud runtime for onboarding and validation rather than asking new contributors to assemble a separate ownCloud stack; setup instructions are in the development repository's README.
- Check both the development repository and app checkout status before editing. Preserve existing user changes; do not revert unrelated work.
- Keep changes focused on the request. Update `CHANGELOG.md` for user-visible fixes and behavior changes under `0.1.2 UNRELEASED`.

## Implementation

- Preserve the app's ownCloud namespace (`OCA\SensorLogger`) and existing conventions unless a requested change requires otherwise.
- PSR-12 is mandatory for all new and modified PHP code. Do not reformat unrelated legacy files solely to satisfy the standard.
- Maintain compatibility with the versions declared in `appinfo/info.xml`: ownCloud 10–11 and PHP 8.0+. Do not assume the IDE's indexed core source matches the runtime; verify behavior against the core version used by the target environment.
- Database access must follow the DBAL version supported by the target ownCloud runtime. Prefer the app's query-builder patterns and parameter binding; do not concatenate user input into SQL.
- Keep controller tests isolated with mocks and avoid requiring a live database for unit tests. Use integration tests only for behavior that genuinely requires ownCloud services or persistence.
- Do not add dependencies or generated files without a clear need. The Composer lockfile is ignored by this app checkout.

## Verification

- Run PHP syntax checks on changed PHP files with `php -l`.
- Run PHPCS with PSR-12 on changed PHP files: `vendor/bin/phpcs -p <changed-paths> --standard=PSR12`.
- Run the app's PHPUnit suite when dependencies and an ownCloud bootstrap are available: `phpunit -c phpunit.xml`.
- Check PHP 8.3 compatibility for changed app files with:

  ```sh
  vendor/bin/phpcs -p <changed-paths> --standard=PHPCompatibility --runtime-set testVersion 8.3
  ```

- Review `git -C sensorlogger diff --check` and the final diff. Report checks that could not be run and why; do not claim a suite passed unless it was executed.
