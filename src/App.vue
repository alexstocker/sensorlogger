<script setup>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import NcButton from '@nextcloud/vue/components/NcButton'
import NcLoadingIcon from '@nextcloud/vue/components/NcLoadingIcon'
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { translate as t } from '@nextcloud/l10n'
import SensorChart from './components/SensorChart.vue'
import SensorLatest from './components/SensorLatest.vue'

const props = defineProps({
	navigation: { type: Array, default: () => [] },
	initialView: { type: String, default: 'dashboard' },
	initialId: { type: String, default: '' },
	activeNavigation: { type: String, default: 'showDashboard' },
})

const navToView = {
	showDashboard: 'dashboard',
	showList: 'logs',
	deviceList: 'devices',
	deviceTypeList: 'deviceTypes',
	deviceGroupList: 'deviceGroups',
	dataTypeList: 'dataTypes',
	sharingin: 'sharingIn',
	sharingout: 'sharingOut',
	sharinglinks: 'sharedLink',
}
const viewToNav = Object.fromEntries(Object.entries(navToView).map(([nav, view]) => [view, nav]))
const titleByView = {
	dashboard: t('sensorlogger', 'Dashboard'),
	logs: t('sensorlogger', 'Sensor logs'),
	devices: t('sensorlogger', 'Devices'),
	deviceTypes: t('sensorlogger', 'Device types'),
	deviceGroups: t('sensorlogger', 'Device groups'),
	dataTypes: t('sensorlogger', 'Data types'),
	deviceLogs: t('sensorlogger', 'Device logs'),
	deviceChart: t('sensorlogger', 'Device chart'),
	sharingIn: t('sensorlogger', 'Shared with you'),
	sharingOut: t('sensorlogger', 'Shared with others'),
	sharedLink: t('sensorlogger', 'Shared by link'),
}

const view = ref(props.initialView)
const itemId = ref(props.initialId)
const activeNav = ref(props.activeNavigation)
const result = ref({})
const loading = ref(false)
const error = ref('')
const sidebar = ref('')
const selectedDevice = ref(null)
const deviceDetails = ref(null)
const groups = ref([])
const types = ref([])
const creating = ref({ group: '', parent: '', type: '' })
const widgetType = ref('')
const widgetDevice = ref('')
const dashboardLayout = ref({ order: [], heights: {}, spans: {} })
const draggedWidgetId = ref('')
const resizingWidget = ref(null)
let dashboardLayoutSaveQueue = Promise.resolve()
let currentPageRequest = 0

const title = computed(() => titleByView[view.value] || t('sensorlogger', 'SensorLogger'))
const dashboardWidgets = computed(() => {
	const widgets = result.value.widgets || []
	const positions = new Map(dashboardLayout.value.order.map((id, index) => [id, index]))
	return [...widgets].sort((left, right) => {
		const leftPosition = positions.get(left.id)
		const rightPosition = positions.get(right.id)
		return (leftPosition ?? Number.MAX_SAFE_INTEGER) - (rightPosition ?? Number.MAX_SAFE_INTEGER)
	})
})
const managementRows = computed(() => ({
	deviceTypes: result.value.deviceTypes || [],
	deviceGroups: result.value.deviceGroups || [],
	dataTypes: result.value.dataTypes || [],
}[view.value] || []))
const managementColumns = computed(() => ({
	deviceTypes: [
		{ key: 'id', label: '#' },
		{ key: 'device_type_name', label: t('sensorlogger', 'Type name') },
	],
	deviceGroups: [
		{ key: 'id', label: '#' },
		{ key: 'device_group_name', label: t('sensorlogger', 'Device group') },
	],
	dataTypes: [
		{ key: 'id', label: '#' },
		{ key: 'description', label: t('sensorlogger', 'Description') },
		{ key: 'type', label: t('sensorlogger', 'Data type') },
		{ key: 'short', label: t('sensorlogger', 'Unit') },
	],
}[view.value] || []))

function appUrl(path) {
	return generateUrl(`/apps/sensorlogger/${path}`)
}

function menuUrl(navId) {
	return props.navigation.find((item) => item.id === navId)?.href || '#'
}

function routeFor(viewName, id = '') {
	if (viewName === 'deviceLogs') {
		return appUrl(`showDeviceData/${id}`)
	}
	if (viewName === 'deviceChart') {
		return appUrl(`deviceChart/${id}`)
	}
	return menuUrl(viewToNav[viewName] || 'showDashboard')
}

function pageForLocation() {
	const path = window.location.pathname
	for (const item of props.navigation) {
		const target = new URL(item.href, window.location.origin).pathname
		if (target === path) {
			return { view: navToView[item.id] || 'dashboard', id: '', nav: item.id }
		}
	}
	const dataMatch = path.match(/\/showDeviceData\/(\d+)\/?$/)
	if (dataMatch) {
		return { view: 'deviceLogs', id: dataMatch[1], nav: 'deviceList' }
	}
	const chartMatch = path.match(/\/deviceChart\/(\d+)\/?$/)
	if (chartMatch) {
		return { view: 'deviceChart', id: chartMatch[1], nav: 'deviceList' }
	}
	return { view: props.initialView, id: props.initialId, nav: props.activeNavigation }
}

async function fetchPageData(viewName, id) {
	if (viewName === 'deviceChart') {
		const [devices, chart] = await Promise.all([
			axios.get(appUrl('frontendData/devices')),
			axios.get(appUrl(`chartData/${id}`), { params: { limit: 1000 } }),
		])
		const device = (devices.data.devices || []).find((item) => String(item.id) === String(id))
		if (!device) {
			throw new Error(t('sensorlogger', 'Device not found'))
		}
		return { device, chart: chart.data }
	}
	const params = viewName === 'deviceLogs' ? { deviceId: id } : {}
	const response = await axios.get(appUrl(`frontendData/${viewName}`), { params })
	return response.data
}

async function loadPage(viewName, id = '') {
	const requestId = ++currentPageRequest
	view.value = viewName
	itemId.value = id
	activeNav.value = viewToNav[viewName] || 'deviceList'
	loading.value = true
	error.value = ''
	result.value = {}
	closeSidebar()
	try {
		const pageData = await fetchPageData(viewName, id)
		if (requestId === currentPageRequest) {
			if (viewName === 'dashboard') {
				dashboardLayout.value = {
					order: pageData.layout?.order || [],
					heights: pageData.layout?.heights || {},
					spans: pageData.layout?.spans || {},
				}
				for (const widget of pageData.widgets || []) {
					widget.height = dashboardLayout.value.heights[widget.id] || 300
					widget.span = dashboardLayout.value.spans[widget.id] || 1
				}
			}
			result.value = pageData
		}
	} catch (exception) {
		if (requestId === currentPageRequest) {
			error.value = exception.response?.data?.error || exception.message || t('sensorlogger', 'Could not load this view')
		}
	} finally {
		if (requestId === currentPageRequest) {
			loading.value = false
		}
	}
}

function navigate(viewName, href = routeFor(viewName), id = '') {
	window.history.pushState({ view: viewName, id, nav: viewToNav[viewName] || 'deviceList' }, '', href)
	loadPage(viewName, id)
}

function onNavigation(event, item) {
	const targetView = navToView[item.id]
	if (!targetView) {
		return
	}
	event.preventDefault()
	navigate(targetView, item.href)
}

function onPopState(event) {
	const state = event.state || pageForLocation()
	loadPage(state.view, state.id || '')
	activeNav.value = state.nav || viewToNav[state.view] || 'deviceList'
}

function openDevice(device) {
	selectedDevice.value = device
	deviceDetails.value = null
	groups.value = []
	types.value = []
	sidebar.value = 'device'
	axios.get(appUrl(`showDeviceDetails/${device.id}`)).then(({ data }) => {
		if (String(selectedDevice.value?.id) !== String(device.id)) {
			return
		}
		deviceDetails.value = data.deviceDetails
		groups.value = data.groups || []
		types.value = data.types || []
	}).catch(() => {
		error.value = t('sensorlogger', 'Could not load device details')
		closeSidebar()
	})
}

function closeSidebar() {
	sidebar.value = ''
	selectedDevice.value = null
	deviceDetails.value = null
}

async function updateDevice(field, value, event = null) {
	try {
		const { data } = await axios.post(appUrl(`updateDevice/${selectedDevice.value.id}`), {
			name: field,
			value,
		})
		if (!data.success) {
			throw new Error(t('sensorlogger', 'Could not update device'))
		}
		if (field === 'name') {
			deviceDetails.value.name = value
			selectedDevice.value.name = value
			result.value.devices?.forEach((device) => {
				if (String(device.id) === String(selectedDevice.value.id)) {
					device.name = value
				}
			})
		} else {
			const property = field === 'type_id'
				? 'type'
				: field === 'group_id' ? 'group' : 'groupParent'
			const id = Number(value)
			deviceDetails.value[property] = id
			selectedDevice.value[property] = id
			const options = field === 'type_id' ? types.value : groups.value
			const option = options.find((entry) => Number(entry.id) === id)
			const displayField = field === 'type_id'
				? 'deviceTypeName'
				: field === 'group_id' ? 'deviceGroupName' : 'deviceGroupParentName'
			deviceDetails.value[displayField] = option
				? option[field === 'type_id' ? 'device_type_name' : 'device_group_name']
				: ''
			if (field !== 'group_parent_id') {
				result.value.devices?.forEach((device) => {
					if (String(device.id) === String(selectedDevice.value.id)) {
						device[field === 'type_id' ? 'deviceTypeName' : 'deviceGroupName'] = deviceDetails.value[displayField]
					}
				})
			}
		}
		return true
	} catch (exception) {
		if (event?.target) {
			event.target.value = field === 'name'
				? deviceDetails.value?.name || ''
				: String(deviceDetails.value?.[field === 'type_id' ? 'type' : field === 'group_id' ? 'group' : 'groupParent'] || 0)
		}
		error.value = exception.message || t('sensorlogger', 'Could not update device')
		return false
	}
}

async function createDeviceOption(kind, name) {
	const trimmed = name.trim()
	if (!trimmed) {
		return
	}
	const isType = kind === 'type'
	const endpoint = isType ? 'createDeviceType' : 'createDeviceGroup'
	const payload = isType
		? { device_type_name: trimmed }
		: { device_group_name: trimmed }
	try {
		const { data } = await axios.post(appUrl(endpoint), payload)
		const id = data.deviceTypeId || data.deviceGroupId
		if (!id) {
			throw new Error(t('sensorlogger', 'Could not create option'))
		}
		const field = isType ? 'type_id' : kind === 'parent' ? 'group_parent_id' : 'group_id'
		if (isType) {
			if (!types.value.some((type) => Number(type.id) === Number(id))) {
				types.value.push({ id, device_type_name: trimmed })
			}
		} else {
			if (!groups.value.some((group) => Number(group.id) === Number(id))) {
				groups.value.push({ id, device_group_name: trimmed })
			}
		}
		if (!await updateDevice(field, id)) {
			return
		}
		creating.value[kind] = ''
	} catch (exception) {
		error.value = exception.message || t('sensorlogger', 'Could not create option')
	}
}

async function postAction(endpoint, payload = {}) {
	try {
		const { data } = await axios.post(appUrl(endpoint), payload)
		if (!data.success) {
			throw new Error(data.errors || t('sensorlogger', 'The action could not be completed'))
		}
		return true
	} catch (exception) {
		error.value = exception.message || t('sensorlogger', 'The action could not be completed')
		return false
	}
}

async function removeItem(row) {
	const routes = {
		logs: `deleteLog/${row.id}`,
		devices: `deleteDevice/${row.id}`,
		deviceTypes: `deleteDeviceType/${row.id}`,
		deviceGroups: `deleteDeviceGroup/${row.id}`,
		dataTypes: `deleteDataType/${row.id}`,
	}
	const endpoint = routes[view.value]
	if (!endpoint || !window.confirm(t('sensorlogger', 'Are you sure you want to delete this item?'))) {
		return
	}
	if (await postAction(endpoint)) {
		await loadPage(view.value, itemId.value)
	}
}

async function deleteWidget(widget) {
	const id = `widget-${widget.type}-${widget.deviceId}`
	if (await postAction(`deleteWidget/${encodeURIComponent(id)}`)) {
		await loadPage('dashboard')
		const currentIds = new Set((result.value.widgets || []).map((entry) => entry.id))
		dashboardLayout.value.order = dashboardLayout.value.order.filter((widgetId) => currentIds.has(widgetId))
		for (const widgetId of Object.keys(dashboardLayout.value.heights)) {
			if (!currentIds.has(widgetId)) {
				delete dashboardLayout.value.heights[widgetId]
			}
		}
		for (const widgetId of Object.keys(dashboardLayout.value.spans)) {
			if (!currentIds.has(widgetId)) {
				delete dashboardLayout.value.spans[widgetId]
			}
		}
		await persistDashboardLayout()
	}
}

async function persistDashboardLayout() {
	const layout = {
		order: dashboardWidgets.value.map((widget) => widget.id),
		heights: Object.fromEntries(dashboardWidgets.value.map((widget) => [widget.id, widget.height])),
		spans: Object.fromEntries(dashboardWidgets.value.map((widget) => [widget.id, widget.span])),
	}
	const save = async () => {
		try {
			const { data } = await axios.post(appUrl('saveDashboardLayout'), {
				layout: JSON.stringify(layout),
			})
			if (!data.success) {
				throw new Error(data.errors || t('sensorlogger', 'Could not save dashboard layout'))
			}
			dashboardLayout.value = layout
		} catch (exception) {
			error.value = exception.response?.data?.error || exception.message || t('sensorlogger', 'Could not save dashboard layout')
		}
	}
	dashboardLayoutSaveQueue = dashboardLayoutSaveQueue.then(save, save)
	await dashboardLayoutSaveQueue
}

function moveWidget(draggedId, targetId, insertAfter = false) {
	if (!draggedId || !targetId || draggedId === targetId) {
		return
	}
	const order = dashboardWidgets.value.map((widget) => widget.id)
	const fromIndex = order.indexOf(draggedId)
	const targetIndex = order.indexOf(targetId)
	if (fromIndex < 0 || targetIndex < 0) {
		return
	}
	const [movedId] = order.splice(fromIndex, 1)
	const insertionIndex = order.indexOf(targetId) + (insertAfter ? 1 : 0)
	order.splice(insertionIndex, 0, movedId)
	dashboardLayout.value.order = order
	persistDashboardLayout()
}

function startWidgetDrag(event, widget) {
	draggedWidgetId.value = widget.id
	event.dataTransfer.effectAllowed = 'move'
	event.dataTransfer.setData('text/plain', widget.id)
}

function dropWidget(event, targetWidget) {
	const draggedId = event.dataTransfer.getData('text/plain') || draggedWidgetId.value
	const bounds = event.currentTarget.getBoundingClientRect()
	moveWidget(draggedId, targetWidget.id, event.clientY >= bounds.top + bounds.height / 2)
	draggedWidgetId.value = ''
}

function getDashboardColumns() {
	const grid = document.querySelector('.sensorlogger-dashboard')
	if (!grid) {
		return { count: 1, width: 280 }
	}
	const styles = window.getComputedStyle(grid)
	const columns = styles.gridTemplateColumns.split(' ').length
	const gap = Number.parseFloat(styles.columnGap) || 0
	return {
		count: columns,
		width: (grid.clientWidth - gap * (columns - 1)) / columns,
	}
}

function startWidgetResize(event, widget) {
	const { count, width } = getDashboardColumns()
	resizingWidget.value = {
		id: widget.id,
		pointerId: event.pointerId,
		startX: event.clientX,
		startY: event.clientY,
		startHeight: widget.height,
		startSpan: Math.min(widget.span, count),
		columnWidth: width,
		maxSpan: Math.min(3, count),
	}
	event.currentTarget.setPointerCapture(event.pointerId)
}

function resizeWidget(event, widget) {
	if (resizingWidget.value?.id !== widget.id || resizingWidget.value.pointerId !== event.pointerId) {
		return
	}
	const isLeftHandle = event.currentTarget.classList.contains('resize-left')
	const horizontalDelta = (event.clientX - resizingWidget.value.startX) * (isLeftHandle ? -1 : 1)
	const spanDelta = Math.round(horizontalDelta / resizingWidget.value.columnWidth)
	widget.height = Math.max(180, Math.min(800, resizingWidget.value.startHeight + event.clientY - resizingWidget.value.startY))
	if (resizingWidget.value.maxSpan > 1
		&& Math.abs(horizontalDelta) >= resizingWidget.value.columnWidth / 2) {
		widget.span = Math.max(1, Math.min(resizingWidget.value.maxSpan, resizingWidget.value.startSpan + spanDelta))
	}
}

function endWidgetResize(event, widget) {
	if (resizingWidget.value?.id !== widget.id || resizingWidget.value.pointerId !== event.pointerId) {
		return
	}
	resizingWidget.value = null
	persistDashboardLayout()
}

async function saveWidget() {
	if (!widgetType.value || !widgetDevice.value) {
		return
	}
	const success = await postAction('saveWidget', {
		widget_type: widgetType.value,
		device_id: widgetDevice.value,
	})
	if (success) {
		closeSidebar()
		await loadPage('dashboard')
	}
}

async function wipeDevice() {
	if (!selectedDevice.value || !window.confirm(t('sensorlogger', 'Delete this device and all its logs?'))) {
		return
	}
	if (await postAction('wipeOutDevice', { device_id: selectedDevice.value.id })) {
		closeSidebar()
		await loadPage('devices')
	}
}

onMounted(() => {
	window.addEventListener('popstate', onPopState)
	loadPage(props.initialView, props.initialId)
})
onUnmounted(() => window.removeEventListener('popstate', onPopState))
</script>

<template>
	<div class="sensorlogger-shell">
		<nav id="app-navigation" :aria-label="t('sensorlogger', 'SensorLogger navigation')">
			<div id="controls">
				<NcButton v-if="view === 'dashboard'" type="primary" @click="sidebar = 'widget'">
					{{ t('sensorlogger', 'New widget') }}
				</NcButton>
			</div>
			<ul id="navigation-list">
				<li v-for="item in navigation" :key="item.id" :class="{ active: activeNav === item.id }">
					<a :href="item.href" :class="{ active: activeNav === item.id }" @click="onNavigation($event, item)">
						{{ item.name }}
					</a>
				</li>
			</ul>
		</nav>

		<div id="app-content">
			<main id="app-content-wrapper" class="sensorlogger-content">
				<div v-if="error" class="sensorlogger-error" role="alert">
					{{ error }}
					<NcButton @click="error = ''">{{ t('sensorlogger', 'Dismiss') }}</NcButton>
				</div>
				<div v-if="loading" class="sensorlogger-loading" role="status">
					<NcLoadingIcon :size="24" />
					{{ t('sensorlogger', 'Loading…') }}
				</div>

				<section v-else-if="view === 'dashboard'" class="sensorlogger-dashboard">
					<div v-if="!result.widgets?.length" class="sensorlogger-empty">
						<h2>{{ t('sensorlogger', 'No dashboard widgets configured') }}</h2>
						<p>{{ t('sensorlogger', 'Add a widget to see your sensor data here.') }}</p>
					</div>
					<article
						v-for="widget in dashboardWidgets"
						:key="widget.id"
						class="sensorlogger-card"
						:class="[`widget-span-${widget.span}`, { 'is-dragged': draggedWidgetId === widget.id }]"
						:style="{ height: `${widget.height}px` }"
						@dragover.prevent.stop
						@drop.prevent.stop="dropWidget($event, widget)"
					>
						<header class="sensorlogger-card-header">
							<div>
								<span
									class="sensorlogger-widget-drag-handle"
									draggable="true"
									:title="t('sensorlogger', 'Drag to reorder')"
									:aria-label="t('sensorlogger', 'Drag to reorder')"
									@dragstart.stop="startWidgetDrag($event, widget)"
									@dragend="draggedWidgetId = ''"
								>Move</span>
								<h2>{{ widget.name }} <small>{{ widget.displayName }}</small></h2>
							</div>
							<div class="sensorlogger-widget-controls">
								<NcButton :disabled="dashboardWidgets.indexOf(widget) === 0" :aria-label="t('sensorlogger', 'Move widget earlier')" @click="moveWidget(widget.id, dashboardWidgets[Math.max(0, dashboardWidgets.indexOf(widget) - 1)]?.id)">{{ t('sensorlogger', 'Move earlier') }}</NcButton>
								<NcButton :disabled="dashboardWidgets.indexOf(widget) === dashboardWidgets.length - 1" :aria-label="t('sensorlogger', 'Move widget later')" @click="moveWidget(widget.id, dashboardWidgets[Math.min(dashboardWidgets.length - 1, dashboardWidgets.indexOf(widget) + 1)]?.id)">{{ t('sensorlogger', 'Move later') }}</NcButton>
								<NcButton @click="deleteWidget(widget)">{{ t('sensorlogger', 'Remove') }}</NcButton>
							</div>
						</header>
						<SensorChart v-if="['chart', 'realTimeChart'].includes(widget.type)" :device-id="widget.deviceId" :live="widget.type === 'realTimeChart'" />
						<template v-else-if="widget.type === 'list'">
							<p v-if="!widget.log?.length" class="sensorlogger-empty">{{ t('sensorlogger', 'No data available') }}</p>
							<ul v-else class="sensorlogger-measurements">
								<li v-for="log in widget.log" :key="log.id">
									<time>{{ log.createdAt }}</time>
									<span v-if="!log.data.length">{{ log.temperature }}°C · {{ log.humidity }}%</span>
									<span v-for="entry in log.data" :key="entry.dataTypeId">{{ entry.value }} {{ entry.short }}</span>
								</li>
							</ul>
						</template>
						<SensorLatest v-else-if="widget.type === 'realTimeLast'" :device-id="widget.deviceId" />
						<template v-else-if="widget.log">
							<p v-if="!widget.log.data?.length">{{ widget.log.temperature }}°C · {{ widget.log.humidity }}%</p>
							<p v-for="entry in widget.log.data" :key="entry.dataTypeId">
								<strong>{{ entry.value }} {{ entry.short }}</strong> {{ entry.description }}
							</p>
							<time>{{ widget.log.createdAt }}</time>
						</template>
						<p v-else class="sensorlogger-empty">{{ t('sensorlogger', 'No data available') }}</p>
						<span
							class="sensorlogger-widget-resize resize-left"
							:title="t('sensorlogger', 'Drag to resize widget height')"
							@pointerdown.stop.prevent="startWidgetResize($event, widget)"
							@pointermove="resizeWidget($event, widget)"
							@pointerup="endWidgetResize($event, widget)"
							@pointercancel="endWidgetResize($event, widget)"
						></span>
						<span
							class="sensorlogger-widget-resize resize-right"
							:title="t('sensorlogger', 'Drag to resize widget height')"
							@pointerdown.stop.prevent="startWidgetResize($event, widget)"
							@pointermove="resizeWidget($event, widget)"
							@pointerup="endWidgetResize($event, widget)"
							@pointercancel="endWidgetResize($event, widget)"
						></span>
					</article>
				</section>

				<section v-else-if="view === 'devices'" class="sensorlogger-table-wrap">
					<table class="sensorlogger-table">
						<thead><tr><th>{{ t('sensorlogger', 'UUID') }}</th><th>{{ t('sensorlogger', 'Name') }}</th><th>{{ t('sensorlogger', 'Type') }}</th><th>{{ t('sensorlogger', 'Group') }}</th><th>{{ t('sensorlogger', 'Actions') }}</th></tr></thead>
						<tbody>
							<tr v-for="device in result.devices" :key="device.id">
								<td>{{ device.uuid }}</td><td>{{ device.name }}</td><td>{{ device.deviceTypeName }}</td><td>{{ device.deviceGroupName }}</td>
								<td class="sensorlogger-actions">
									<NcButton @click="openDevice(device)">{{ t('sensorlogger', 'Details') }}</NcButton>
									<NcButton @click="navigate('deviceChart', routeFor('deviceChart', device.id), device.id)">{{ t('sensorlogger', 'Chart') }}</NcButton>
									<NcButton @click="navigate('deviceLogs', routeFor('deviceLogs', device.id), device.id)">{{ t('sensorlogger', 'Data') }}</NcButton>
								</td>
							</tr>
						</tbody>
					</table>
					<p v-if="!result.devices?.length" class="sensorlogger-empty">{{ t('sensorlogger', 'No devices registered') }}</p>
				</section>

				<section v-else-if="view === 'logs' || view === 'deviceLogs'" class="sensorlogger-table-wrap">
					<table class="sensorlogger-table">
						<thead><tr><th>#</th><th>{{ t('sensorlogger', 'UUID') }}</th><th>{{ t('sensorlogger', 'Temperature') }}</th><th>{{ t('sensorlogger', 'Humidity') }}</th><th>{{ t('sensorlogger', 'Additional data') }}</th><th>{{ t('sensorlogger', 'Created') }}</th><th>{{ t('sensorlogger', 'Actions') }}</th></tr></thead>
						<tbody>
							<tr v-for="log in result.logs" :key="log.id">
								<td>{{ log.id }}</td><td>{{ log.deviceUuid }}</td><td>{{ log.temperature }}</td><td>{{ log.humidity }}</td>
								<td><span v-for="entry in log.data" :key="entry.dataTypeId">{{ entry.value }} {{ entry.short }} </span></td>
								<td>{{ log.createdAt }}</td><td><NcButton @click="removeItem(log)">{{ t('sensorlogger', 'Delete') }}</NcButton></td>
							</tr>
						</tbody>
					</table>
					<p v-if="!result.logs?.length" class="sensorlogger-empty">{{ t('sensorlogger', 'No sensor logs found') }}</p>
				</section>

				<section v-else-if="view === 'deviceChart'" class="sensorlogger-card">
					<h2>{{ result.device?.name }} <small>{{ result.device?.uuid }}</small></h2>
					<SensorChart :device-id="itemId" />
				</section>

				<section v-else-if="managementColumns.length" class="sensorlogger-table-wrap">
					<table class="sensorlogger-table">
						<thead><tr><th v-for="column in managementColumns" :key="column.key">{{ column.label }}</th><th>{{ t('sensorlogger', 'Actions') }}</th></tr></thead>
						<tbody>
							<tr v-for="row in managementRows" :key="row.id">
								<td v-for="column in managementColumns" :key="column.key">{{ row[column.key] }}</td>
								<td><NcButton @click="removeItem(row)">{{ t('sensorlogger', 'Delete') }}</NcButton></td>
							</tr>
						</tbody>
					</table>
					<p v-if="!managementRows.length" class="sensorlogger-empty">{{ t('sensorlogger', 'No entries found') }}</p>
				</section>

				<section v-else class="sensorlogger-empty">
					<h2>{{ t('sensorlogger', 'Sharing') }}</h2>
					<p>{{ t('sensorlogger', 'No shared devices are available.') }}</p>
				</section>
			</main>
		</div>

		<aside id="app-sidebar" :aria-hidden="sidebar ? 'false' : 'true'" :class="{ 'is-open': sidebar }">
			<div class="sensorlogger-sidebar">
				<header class="sensorlogger-sidebar-header">
					<h2>{{ sidebar === 'device' ? (deviceDetails?.name || t('sensorlogger', 'Device details')) : t('sensorlogger', 'New dashboard widget') }}</h2>
					<NcButton @click="closeSidebar">{{ t('sensorlogger', 'Close') }}</NcButton>
				</header>
				<div v-if="sidebar === 'device'" class="sensorlogger-sidebar-body">
					<p v-if="!deviceDetails" role="status">{{ t('sensorlogger', 'Loading device details…') }}</p>
					<template v-else>
						<label>{{ t('sensorlogger', 'Device name') }}<input :value="deviceDetails.name" @change="updateDevice('name', $event.target.value, $event)"></label>
						<label>{{ t('sensorlogger', 'Device unique ID') }}<input :value="deviceDetails.uuid" readonly></label>
						<label>{{ t('sensorlogger', 'Device group') }}
							<select :value="deviceDetails.group || 0" @change="updateDevice('group_id', $event.target.value, $event)">
								<option value="0">{{ t('sensorlogger', 'None') }}</option><option v-for="group in groups" :key="group.id" :value="group.id">{{ group.device_group_name }}</option>
							</select>
						</label>
						<label>{{ t('sensorlogger', 'Parent group') }}
							<select :value="deviceDetails.groupParent || 0" @change="updateDevice('group_parent_id', $event.target.value, $event)">
								<option value="0">{{ t('sensorlogger', 'None') }}</option><option v-for="group in groups" :key="group.id" :value="group.id">{{ group.device_group_name }}</option>
							</select>
						</label>
						<label>{{ t('sensorlogger', 'Device type') }}
							<select :value="deviceDetails.type || 0" @change="updateDevice('type_id', $event.target.value, $event)">
								<option value="0">{{ t('sensorlogger', 'None') }}</option><option v-for="type in types" :key="type.id" :value="type.id">{{ type.device_type_name }}</option>
							</select>
						</label>
						<label>{{ t('sensorlogger', 'Create group') }}<input v-model="creating.group"></label>
						<NcButton @click="createDeviceOption('group', creating.group)">{{ t('sensorlogger', 'Create group') }}</NcButton>
						<label>{{ t('sensorlogger', 'Create parent group') }}<input v-model="creating.parent"></label>
						<NcButton @click="createDeviceOption('parent', creating.parent)">{{ t('sensorlogger', 'Create parent group') }}</NcButton>
						<label>{{ t('sensorlogger', 'Create device type') }}<input v-model="creating.type"></label>
						<NcButton @click="createDeviceOption('type', creating.type)">{{ t('sensorlogger', 'Create device type') }}</NcButton>
						<NcButton type="error" @click="wipeDevice">{{ t('sensorlogger', 'Delete device and logs') }}</NcButton>
					</template>
				</div>
				<div v-else-if="sidebar === 'widget'" class="sensorlogger-sidebar-body">
					<label>{{ t('sensorlogger', 'Widget type') }}
						<select v-model="widgetType">
							<option value="">{{ t('sensorlogger', 'Select a widget') }}</option>
							<option v-for="(entry, key) in result.widgetTypes" :key="key" :value="key">{{ entry.displayName }}</option>
						</select>
					</label>
					<label>{{ t('sensorlogger', 'Device') }}
						<select v-model="widgetDevice">
							<option value="">{{ t('sensorlogger', 'Select a device') }}</option>
							<option v-for="device in result.devices" :key="device.id" :value="device.id">{{ device.name }}</option>
						</select>
					</label>
					<NcButton type="primary" @click="saveWidget">{{ t('sensorlogger', 'Save widget') }}</NcButton>
				</div>
			</div>
		</aside>
	</div>
</template>
