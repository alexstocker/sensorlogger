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

The app's new frontend targets Nextcloud 34 and newer. It is a bundled Vue
single-page app with a single server-rendered Nextcloud shell and authenticated
JSON data endpoint; it no longer relies on core-provided jQuery, Select2,
X-editable, or jqPlot. The current app release target is `0.2.0nc`. The
Nextcloud 34 baseline also raises the declared PHP minimum to 8.2 and MySQL
minimum to 8.0. See `frontend-architecture.md` for frontend development and
build details.

## Follow-up runtime errors

The local Nextcloud 34 API test also exposed a controller dependency issue:
`SensorLoggerApiController` requested `OCP\ILogger`, whose logging methods
were removed in Nextcloud 31, so the framework could not instantiate that
interface. The controller now injects `Psr\Log\LoggerInterface`; a local
authenticated request with an invalid, non-persisting payload reached the
controller and returned its JSON validation error. No sensor data was
submitted by that check.

The local page-route check also confirmed that the read-only navigation
routes, including `showList`, were registered as POST even though their
links are GET URLs. These routes and their AJAX callers now use GET. Once the
route was reached, `templates/part.list.php` exposed a PHP 8 `TypeError` by
calling `sizeof()` on the raw JSON string stored in each log's `data` column.
`Log::getData()` now decodes persisted JSON before returning extended log
entries, while legacy scalar-log JSON produces an empty extended-data list.

After changing the routes, a 412 response remained because Nextcloud 34's
`SecurityMiddleware` enforces CSRF checks on controller methods regardless
of HTTP verb unless the method is annotated `@NoCSRFRequired`. The app's
read-only GET methods were missing that annotation, so ordinary browser GET
requests without a request token were rejected. Added `@NoCSRFRequired` only
to read-only GET handlers; state-changing POST handlers remain CSRF-protected.
Local verification confirmed tokenless authenticated GETs succeed while a
POST to the widget-creation route without a token still returns 412.

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

### Frontend adaptation and navigation rework

Nextcloud 34 removed jQuery and jQuery UI from core after deprecating jQuery
for multiple releases. The previous SensorLogger code depended on global
jQuery, jqPlot, X-editable, and Select2. A short-lived compatibility patch
bundled jQuery, but this retained the legacy dependency graph and continued to
leave page navigation split between full-page and AJAX-fragment rendering.

The frontend is now a Vue single-page app, built with Vite and bundled with
the app. PHP renders one Nextcloud shell; Vue owns the app navigation, page
content, browser history, and sidebars. Read-only UI data is delivered by an
authenticated, allowlisted JSON endpoint. Firmware-facing API routes remain
separate. Views use the existing domain queries and serialize explicit
frontend data shapes. The dashboard charts are rendered as SVG, avoiding the
old jqPlot runtime. PHP remains responsible for authorization, persistence,
and write validation.

The app now declares Nextcloud 34 as its minimum and has no maximum version
cap. Its release target is `0.2.0nc` (the app metadata version is `0.2.0`).
Building the frontend requires Node.js and npm; generated JS/CSS are included
so an installed app does not need Node. Browser integration and visual
verification are still outstanding.

### Direct page routes returned fragments without the Nextcloud shell

The main `/apps/sensorlogger/` route renders `templates/main.php`, which
provides Nextcloud's app navigation/content/sidebar structure and loads the
app styles. Routes such as `/apps/sensorlogger/showList` instead returned a
`blank`-layout `part.list` template unconditionally. That worked only when
the JavaScript fetched the route and inserted the fragment into the existing
app shell; opening the route directly rendered a bare table without the
Nextcloud header, app navigation, or styles.

That interim fragment/full-page behavior has been removed. All app-view
routes now render the same shell and initial route state; Vue requests view
data from the JSON endpoint and updates URL/history centrally. This also
removes the previous device-list template parameter mismatch.

### Device-list sidebar interaction and layout

These reports identify the legacy dependency failures that prompted the
rewrite. The Vue sidebar loads device detail JSON, renders native inputs and
selects, and sends changes through the existing allowlisted device-update
endpoint. The endpoint uses a parameterized write scoped to the signed-in
user's device. Type/group assignments are checked against the user's own
options; clearing an assignment uses the schema's existing `0` sentinel.

The device-details handler was delegated to both each device row and its
share/menu action links. Since clicks bubble, a click on an action link could
run the same handler twice, toggling the sidebar open and then closed. The
sidebar also retained previous device details when switching rows, and its
body/footer sizing relied on absolute-position rules that did not provide a
usable scrolling content area after the markup refresh.

Device-row clicks and action-link clicks now open the details panel through a
single handler; clicks on chart/data links do not trigger it. The panel
toggles closed only when selecting the already-open device, replaces stale
details while loading another device, aborts stale requests, and displays a
visible error if details cannot load. The sidebar content now uses a
constrained flex layout so its body can scroll independently of the footer.

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

The app previously advertised Nextcloud 14–34 and PHP 7.4–8.5. The frontend
and compatibility work now targets Nextcloud 34 and newer; metadata declares
Nextcloud 34+, PHP 8.2–8.5, and MySQL 8.0+. The existing `.travis.yml` matrix
still tests PHP 5.6/7 against old ownCloud branches rather than Nextcloud 34
and PHP 8.

The integration test previously had `testAppRemove()` recursively call
itself instead of testing app removal. That broken test was removed so the
integration test runner cannot recurse indefinitely.

**Severity:** Low  
**Confidence:** 9/10  
**Status:** Metadata now reflects the Nextcloud 34+ target and its PHP/MySQL
minimums. The legacy CI matrix still does not establish Nextcloud 34/PHP 8
coverage.

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
- The API controller's PSR logger dependency was resolved on the local
  Nextcloud 34.0.5 instance. The malformed-request check verified controller
  dispatch and validation response only; it did not verify successful device
  registration or sensor-data persistence.
- The six read-only navigation pages returned HTTP 200 on local Nextcloud
  34.0.5 using an authenticated request token. No data-changing requests were
  sent.
- A focused entity check verified decoding of both scalar-log JSON and
  extended-log JSON.
- Read-only page GETs succeeded without a request token, and a state-changing
  POST without a token was still rejected with HTTP 412.
- The Vue bundle builds successfully, but interactive browser behavior and
  visual appearance have not been verified in a browser.
- The frontend no longer loads the previous jQuery, X-editable, Select2, or
  jqPlot scripts. The generated bundle and CSS are committed app assets.
- Read-only frontend data is served through the authenticated JSON route;
  write actions continue to use POST endpoints and CSRF protection.
- Browser checks of navigation/history, direct routes, device switching, and
  CRUD behavior remain outstanding.
- PHP syntax checks do not validate Nextcloud database API compatibility or
  connection-type behavior.

## References

- [Nextcloud 34 `IQueryBuilder` interface](https://github.com/nextcloud/server/blob/v34.0.5/lib/public/DB/QueryBuilder/IQueryBuilder.php)
- [Nextcloud 34 system requirements](https://docs.nextcloud.com/server/34/admin_manual/installation/system_requirements.html)
