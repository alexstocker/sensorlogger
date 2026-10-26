<script setup>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { translate as t } from '@nextcloud/l10n'

const props = defineProps({
	deviceId: { type: [Number, String], required: true },
	live: { type: Boolean, default: false },
})

const loading = ref(false)
const error = ref('')
const payload = ref(null)
const metric = ref('temperature')
const refreshSeconds = ref(5)
let refreshTimer = null
let isMounted = false

const logs = computed(() => {
	if (Array.isArray(payload.value?.logs)) {
		return [...payload.value.logs].reverse()
	}
	return Array.isArray(payload.value) ? [...payload.value].reverse() : []
})

const dataTypes = computed(() => payload.value?.dataTypes || [])
const metrics = computed(() => [
	{ value: 'temperature', label: t('sensorlogger', 'Temperature (°C)') },
	{ value: 'humidity', label: t('sensorlogger', 'Humidity (%)') },
	...dataTypes.value.map((item) => ({
		value: `data-${item.id}`,
		label: item.description || item.short || item.type,
	})),
])

const points = computed(() => {
	const values = logs.value.map((log) => {
		if (metric.value.startsWith('data-')) {
			const dataTypeId = Number(metric.value.slice(5))
			return (log.data || []).find((entry) => Number(entry.dataTypeId) === dataTypeId)?.value
		}
		return log[metric.value]
	}).map(Number).filter(Number.isFinite)

	if (!values.length) {
		return ''
	}

	const min = Math.min(...values)
	const max = Math.max(...values)
	const spread = max - min || 1
	return values.map((value, index) => {
		const x = values.length === 1 ? 400 : 30 + (index * 740 / (values.length - 1))
		const y = 280 - ((value - min) * 250 / spread)
		return `${x},${y}`
	}).join(' ')
})
const firstTimestamp = computed(() => logs.value[0]?.createdAt || '')
const lastTimestamp = computed(() => logs.value[logs.value.length - 1]?.createdAt || '')
const selectedMetricLabel = computed(() => metrics.value.find((item) => item.value === metric.value)?.label || '')

async function load() {
	loading.value = true
	error.value = ''
	try {
		const response = await axios.get(generateUrl(`/apps/sensorlogger/chartData/${props.deviceId}`), {
			params: { limit: 1000 },
		})
		payload.value = response.data
	} catch (exception) {
		error.value = t('sensorlogger', 'Could not load chart data')
	} finally {
		loading.value = false
	}
}

async function loadLatest() {
	error.value = ''
	try {
		const response = await axios.get(generateUrl(`/apps/sensorlogger/lastLog/${props.deviceId}`), {
			params: { limit: 1 },
		})
		const latestLogs = Array.isArray(response.data?.logs)
			? response.data.logs
			: Array.isArray(response.data) ? response.data : []
		if (!latestLogs.length) {
			return
		}

		const existingLogs = logs.value
		const knownIds = new Set(existingLogs.map((log) => log.id))
		const newLogs = latestLogs.filter((log) => !knownIds.has(log.id))
		if (newLogs.length) {
			payload.value = {
				logs: [...newLogs, ...existingLogs].slice(0, 1000),
				dataTypes: response.data?.dataTypes || dataTypes.value,
			}
		}
	} catch (exception) {
		error.value = t('sensorlogger', 'Could not refresh chart data')
	}
}

function restartRefresh() {
	clearTimeout(refreshTimer)
	if (props.live && isMounted) {
		refreshTimer = setTimeout(async () => {
			await loadLatest()
			restartRefresh()
		}, refreshSeconds.value * 1000)
	}
}

onMounted(async () => {
	isMounted = true
	await load()
	restartRefresh()
})
watch(() => props.deviceId, async () => {
	await load()
	restartRefresh()
})
watch(refreshSeconds, restartRefresh)
onUnmounted(() => {
	isMounted = false
	clearTimeout(refreshTimer)
})
</script>

<template>
	<section class="sensor-chart" :aria-busy="loading">
		<label v-if="live">
			{{ t('sensorlogger', 'Refresh interval') }}
			<select v-model.number="refreshSeconds">
				<option :value="1">1 {{ t('sensorlogger', 'second') }}</option>
				<option :value="5">5 {{ t('sensorlogger', 'seconds') }}</option>
				<option :value="10">10 {{ t('sensorlogger', 'seconds') }}</option>
				<option :value="30">30 {{ t('sensorlogger', 'seconds') }}</option>
				<option :value="60">60 {{ t('sensorlogger', 'seconds') }}</option>
			</select>
		</label>
		<label>
			{{ t('sensorlogger', 'Measurement') }}
			<select v-model="metric">
				<option v-for="item in metrics" :key="item.value" :value="item.value">
					{{ item.label }}
				</option>
			</select>
		</label>
		<p v-if="loading" role="status">{{ t('sensorlogger', 'Loading chart data…') }}</p>
		<p v-else-if="error" class="sensorlogger-error" role="alert">{{ error }}</p>
		<p v-else-if="!points" class="sensorlogger-empty">{{ t('sensorlogger', 'No chart data available') }}</p>
		<svg v-else viewBox="0 0 800 320" role="img" :aria-label="t('sensorlogger', 'Sensor measurements over time')">
			<line x1="30" y1="280" x2="770" y2="280" class="chart-axis" />
			<polyline :points="points" class="chart-line" />
			<text x="30" y="24" class="chart-label">{{ selectedMetricLabel }}</text>
			<text x="30" y="308" class="chart-label">{{ firstTimestamp }}</text>
			<text x="770" y="308" class="chart-label" text-anchor="end">{{ lastTimestamp }}</text>
		</svg>
	</section>
</template>
