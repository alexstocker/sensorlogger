# SensorLogger: Nextcloud 34 and PHP 8 compatibility findings

## Summary

The reported dashboard error is caused by using a removed/unavailable
`QueryBuilder::execute()` method. Nextcloud 34's public
`OCP\DB\QueryBuilder\IQueryBuilder` API provides `executeQuery()` for SELECT
queries and `executeStatement()` for INSERT, UPDATE, and DELETE queries.
Changing only the first failing call would expose the same incompatibility in
other SensorLogger paths.

The compatibility fixes described below have since been applied. A live
Nextcloud 34 integration test is still needed to verify runtime behavior.

## Follow-up runtime errors

After the QueryBuilder and widget connection fixes, two subsequent dashboard
requests uncovered additional compatibility failures:

1. **Navigation setup:** Nextcloud 34 logged that `appinfo/app.php` is no
   longer supported. The request then failed in `lib/App.php:20` because
   `OC\Server::getAppManager()` does not exist. This failure occurred while
   `SensorLoggerController::getNavigationItems()` prepared the dashboard
   navigation. The legacy entrypoint was empty apart from comments; it and
   the obsolete `lib/App.php` helper have been removed. The controller now
   constructs its own menu items rather than manipulating Nextcloud's global
   navigation manager.
2. **Template resources:** With the controller completing, rendering
   `templates/main.php:25` failed on undefined `vendor_script()`. The error
   page also logged that `vendor/select2/select2.css` could not be resolved
   because there is no `vendor` app/resource in the SensorLogger checkout.
   Both Select2 vendor helper calls were removed. The underlying Select2
   plugin is not bundled, so editor controls using Select2 remain a separate
   functional concern.

## Findings

### High: QueryBuilder calls use `execute()`

The request to `/apps/sensorlogger/` reaches
`SensorLoggerController::getWidgets()` and then
`Devices::getDevices()`. The SELECT query at `lib/Devices.php:44`
calls `$query->execute()`, matching the reported Nextcloud 34 exception.

The initial investigation found 31 active QueryBuilder `->execute()` calls in
these files:

- `lib/Devices.php`
- `lib/DataTypes.php`
- `lib/DeviceGroups.php`
- `lib/DeviceTypes.php`
- `lib/SensorLogs.php`
- `lib/Widgets/MaxValues24hWidget.php`
- `lib/Controller/SensorLoggerApiController.php`

The app now uses a compatibility helper that calls `executeQuery()` for
SELECT and `executeStatement()` for INSERT/UPDATE/DELETE on newer Nextcloud
versions, while falling back to `execute()` on older supported versions. This
preserves the app's declared Nextcloud 14 minimum while accommodating the
Nextcloud 34 API.

**Severity:** High  
**Confidence:** 10/10  
**Status:** Fixed across the listed files.

### Medium: Max-values widget expects the wrong connection type

`lib/Widgets/iWidget.php:15` declares the widget connection
parameter as `OC\DB\Connection`; `lib/Widgets/MaxValues24hWidget.php:41`
uses that same internal type. The controller's connection is typed as
`OCP\IDBConnection`, and Nextcloud 34 passes an `OC\DB\ConnectionAdapter`.
That adapter does not extend `OC\DB\Connection`, so invoking the widget could
raise a PHP `TypeError`. Relevant calls include `lib/Widgets.php:96` and
`lib/Controller/SensorLoggerController.php:722`.

**Severity:** Medium  
**Confidence:** 9/10  
**Status:** Fixed by typing the widget interface and implementation against
the public `OCP\IDBConnection` interface.

### High: Legacy bootstrap and internal server API usage

The `appinfo/app.php` entrypoint triggers a Nextcloud 34 deprecation warning.
It contained no active registration code, so it was removed rather than
replaced with an unnecessary bootstrap implementation. The subsequent fatal
error came from `lib/App.php:20`, which attempted to construct an internal
`OC\NavigationManager` using the removed `OC\Server::getAppManager()` method.

The controller no longer creates an internal navigation manager or modifies
Nextcloud's global navigation state. It now builds the app's own sub-navigation
list directly, avoiding injected manager API differences and preventing
unrelated core/app entries from appearing in SensorLogger's menu. The
obsolete `lib/App.php` helper was removed.

**Severity:** High  
**Confidence:** 10/10  
**Status:** Fixed. No `appinfo/app.php`, `lib/App.php`, or static
`App::getNavigationManager()` references remain, and SensorLogger's local
navigation no longer depends on internal or public global navigation manager
methods.

### High: Template calls unavailable vendor resource helpers

The dashboard template called `vendor_script()` and `vendor_style()` for
Select2. Those functions are not defined by Nextcloud 34, and the app does
not contain the requested `vendor/select2/select2` resources. The calls have
been removed so rendering the main template no longer fatals or asks
Nextcloud to resolve a nonexistent `vendor` app.

The app's legacy inline Select2 editor integration is not bundled with the
app; editing controls that depend on that plugin may still need a compatible
Select2 asset or a native-select rewrite.

**Severity:** High  
**Confidence:** 10/10  
**Status:** Removed the invalid template calls. Select2-dependent edit
behavior remains unverified.

### Low: Declared compatibility exceeds demonstrated test coverage

The current `appinfo/info.xml` advertises Nextcloud 14–34 and
PHP 7.4–8.5. Nextcloud 34's published system requirements list PHP 8.2–8.5
and MySQL 8.0 as the MySQL option, while the app metadata still declares MySQL
5.7. The existing `.travis.yml` matrix tests PHP 5.6/7 against old ownCloud
branches rather than Nextcloud 34 and PHP 8.

The integration test previously had `testAppRemove()` recursively call
itself instead of testing app removal. That broken test was removed so the
integration test runner cannot recurse indefinitely.

**Severity:** Low  
**Confidence:** 9/10  
**Status:** The app metadata and legacy CI matrix were not changed. The
database minimum applies across the app's full declared Nextcloud support
range, so raising it based only on Nextcloud 34's requirements could
misrepresent compatibility with older supported releases. The CI matrix still
does not establish Nextcloud 34/PHP 8 coverage.

## Validation and limitations

- PHP 8.5.4 syntax-checked all 45 PHP files remaining after the legacy
  `appinfo/app.php` and `lib/App.php` files were removed.
- A smoke test confirmed the compatibility helper dispatches to the modern
  methods when present and falls back to `execute()` otherwise.
- Verified the legacy bootstrap entrypoint and obsolete internal server API
  references are removed.
- Verified the main template no longer calls `vendor_script()` or
  `vendor_style()`.
- PHPUnit is not installed in the environment; the app Makefile test target
  downloads PHPUnit if missing, so it was not run.
- No live Nextcloud 34 instance was available for runtime verification.
- The configured `nextcloud-dev.loc` test URL does not resolve from this
  environment, so the updated dashboard could not be exercised here.
- PHP syntax checks do not validate Nextcloud database API compatibility or
  connection-type behavior.

## References

- [Nextcloud 34 `IQueryBuilder` interface](https://github.com/nextcloud/server/blob/v34.0.5/lib/public/DB/QueryBuilder/IQueryBuilder.php)
- [Nextcloud 34 system requirements](https://docs.nextcloud.com/server/34/admin_manual/installation/system_requirements.html)
