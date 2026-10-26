# SensorLogger frontend architecture

## Release and compatibility

The frontend rework targets SensorLogger `0.2.0nc` on the `0.2-nc-devel`
branch and Nextcloud 34 or newer. The Nextcloud app shell is rendered by
`templates/main.php`; Vue owns navigation, page content, browser history, and
the app sidebar after mounting.

The JavaScript runtime and Nextcloud Vue components are bundled with the app.
SensorLogger does not depend on core-provided jQuery or implicit global
frontend libraries. The `@nextcloud/axios` and `@nextcloud/router` packages
provide authenticated requests and Nextcloud-aware URLs.

Dashboard widgets can be reordered by dragging them or using the move buttons.
Their heights are adjustable per widget, and their widths can span available
dashboard columns by dragging either bottom corner. The grid adapts to the
viewport width with a maximum of three columns. Each user's order, heights,
and widths are saved in Nextcloud user configuration.

## Development build

Install Node.js 20.19 or newer (or 22.12 or newer) and npm, then run these
commands from the app directory:

```sh
npm ci
npm run build
```

Vite compiles `src/main.js` and its Vue components into `js/sensorlogger.js`.
The build copies the component CSS bundle to `css/sensorlogger.css`; both
generated assets are shipped with the app so app installation does not need
Node.js. `npm run dev` rebuilds the bundle when source files change.

## Navigation and view data

The PHP controller returns the same app shell for each UI route, with initial
view and navigation state embedded as data attributes. Vue handles menu
navigation and browser back/forward events, updating the URL and active
navigation item together. Device log and chart routes retain direct-loadable
URLs.

`frontendData/{view}` is a read-only, authenticated JSON route. Its view
parameter is allowlisted and the device log view checks device ownership.
Device detail and chart data use the existing read endpoints. Mutations stay
on POST routes; the frontend uses `@nextcloud/axios` so Nextcloud's request
token is included. Sensor-client API endpoints and persisted measurement
formats are unchanged.

## Validation

The production bundle can be checked with `npm run build`. Backend changes
should also pass PHP syntax checks and `git diff --check`. Before release,
exercise the app in Nextcloud 34+ for direct routes, navigation/history,
device detail edits, dashboard widgets, charts, and list deletion actions.
