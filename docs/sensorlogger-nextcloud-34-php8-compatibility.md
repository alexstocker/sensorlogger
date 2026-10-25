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

### Frontend adaptation

The server-rendered SensorLogger layout has been refreshed for current
Nextcloud use without introducing a frontend framework or build pipeline.
The main template now uses semantic navigation, main-content, and sidebar
regions; removes unused Angular-style attributes and placeholder sidebar
tabs; and exposes sidebar visibility to assistive technology. Management
tables now have valid header rows and accessible labels, the device columns
match their data, and the dashboard uses a responsive grid, Nextcloud theme
colors, visible keyboard focus, and reduced-motion support.

This is a compatibility-focused refresh, not a Vue rewrite. The repository
has no frontend package manifest or build tooling, and a rewrite would affect
the app's broad declared Nextcloud 14–34 support. Existing jQuery, jqPlot,
inline editing, and Select2-dependent controls remain; the missing Select2
asset described above is still an unresolved functional concern.

### Direct page routes returned fragments without the Nextcloud shell

The main `/apps/sensorlogger/` route renders `templates/main.php`, which
provides Nextcloud's app navigation/content/sidebar structure and loads the
app styles. Routes such as `/apps/sensorlogger/showList` instead returned a
`blank`-layout `part.list` template unconditionally. That worked only when
the JavaScript fetched the route and inserted the fragment into the existing
app shell; opening the route directly rendered a bare table without the
Nextcloud header, app navigation, or styles.

Page responses now distinguish normal browser requests from AJAX requests
using `X-Requested-With`: AJAX navigation still receives the same fragment,
while direct page requests render the `main` template with the requested
content and active navigation item. This behavior is shared by the related
dashboard, device, and management-list routes. Standalone chart pages now
initialize their chart after the full layout loads.

The first direct device-list response then exposed a mismatched template
parameter: the controller selected `part.list` even though the response data
and AJAX template were for `part.listDevices`. The main layout consequently
included the log table template without its required `logs` data. The
controller now selects `part.listDevices` for full-page rendering as well.

### Device-list sidebar interaction and layout

The browser console reported `$ is not defined` in `js/app.js` and
`jQuery is not defined` in `js/script.js`. SensorLogger relies on jQuery
throughout its legacy frontend and plugins, but Nextcloud 34 does not provide
the old global jQuery dependency. The app now bundles the jQuery 3.7.1
distribution already present in the local Nextcloud 34 installation,
preserving its upstream license header, and loads it before app scripts and
jQuery plugins. This avoids a runtime dependency on the separately installed
Serverinfo app.

After jQuery loaded, the sidebar code failed on `$(...).tooltip is not a
function`. Nextcloud 34 does not provide the old jQuery tooltip extension, and
the bundled Poshytip plugin does not add that API. Removed these unsupported
calls and now copy tooltip text to standard `title` attributes for browser
native tooltips.

The next sidebar request returned the device details JSON successfully, but
the panel remained at “Loading device details”. The response handler tried to
initialize X-editable fields using the Select2 editor type, which is not
bundled with SensorLogger. That JavaScript exception occurred before the
device title and details were inserted. Device details now render with native
inputs and selects, and device-field updates use an explicit allowlist and a
parameterized update scoped to the signed-in user's device. Type/group
assignments are checked against the signed-in user's own options; clearing an
assignment uses the schema's existing `0` sentinel. Device type/group creation
controls remain available and assign the new value to the device.

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
- The frontend markup and styles were refreshed, but interactive browser
  behavior and visual appearance were not verified in a browser.
- Direct page routes now render the complete app shell while same-origin
  AJAX navigation continues to return fragments; live browser verification
  remains outstanding.
- Device sidebar event handling and layout have been corrected; interactive
  browser verification remains outstanding.
- The missing global jQuery dependency is bundled with SensorLogger and loaded
  before its jQuery-dependent scripts; browser-console verification remains
  outstanding.
- Unsupported jQuery tooltip calls are removed; device actions use native
  browser tooltips. Browser verification remains outstanding.
- Device details now use native controls rather than the missing Select2
  editor. The update endpoint accepts only the intended device fields and
  checks device ownership; browser interaction remains unverified.
- Dashboard navigation was exercised as part of that check; broader
  interactive/browser behavior remains unverified.
- PHP syntax checks do not validate Nextcloud database API compatibility or
  connection-type behavior.

## References

- [Nextcloud 34 `IQueryBuilder` interface](https://github.com/nextcloud/server/blob/v34.0.5/lib/public/DB/QueryBuilder/IQueryBuilder.php)
- [Nextcloud 34 system requirements](https://docs.nextcloud.com/server/34/admin_manual/installation/system_requirements.html)
