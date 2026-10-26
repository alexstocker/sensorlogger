<script setup>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { translate as t } from '@nextcloud/l10n'

const props = defineProps({
	deviceId: { type: [Number, String], required: true },
})

const latest = ref(null)
const dataTypes = ref([])
const error = ref('')
const refreshSeconds = ref(5)
const measurementEntries = computed(() => (latest.value?.data || []).filter((entry) => (
	entry && typeof entry === 'object' && entry.value !== undefined && entry.value !== null
)))
const hasTemperature = computed(() => latest.value?.temperature !== undefined && latest.value.temperature !== null)
const hasHumidity = computed(() => latest.value?.humidity !== undefined && latest.value.humidity !== null)
const hasScalarMeasurements = computed(() => hasTemperature.value || hasHumidity.value)
let refreshTimer = null
let isMounted = false

function normalizeLog(log) {
	if (!log || typeof log !== 'object') {
		return null
	}

	let data = log.data
	if (typeof data === 'string') {
		try {
			data = JSON.parse(data)
		} catch {
			data = []
		}
	}
	if (data == null) {
		data = []
	}
	if (!Array.isArray(data) && typeof data === 'object') {
		data = data.dataTypeId !== undefined && data.value !== undefined
			? [data]
			: Object.values(data)
	}
	if (!Array.isArray(data)) {
		data = []
	}

	return {
		...log,
		data: data.filter((entry) => entry && typeof entry === 'object' && entry.value !== undefined),
	}
}

async function load() {
	error.value = ''
	try {
		const response = await axios.get(generateUrl(`/apps/sensorlogger/lastLog/${props.deviceId}`), {
			params: { limit: 1 },
		})
		const logs = Array.isArray(response.data?.logs)
			? response.data.logs
			: Array.isArray(response.data) ? response.data : []
		latest.value = normalizeLog(logs[0])
		dataTypes.value = Array.isArray(response.data?.dataTypes) ? response.data.dataTypes : []
	} catch (exception) {
		error.value = exception.message || t('sensorlogger', 'Could not refresh latest measurement')
	}
}

function dataTypeFor(entry) {
	return dataTypes.value.find((type) => Number(type.id) === Number(entry.dataTypeId))
}

function schedule() {
	clearTimeout(refreshTimer)
	if (isMounted) {
		refreshTimer = setTimeout(async () => {
			await load()
			schedule()
		}, refreshSeconds.value * 1000)
	}
}

onMounted(async () => {
	isMounted = true
	await load()
	schedule()
})
watch(() => props.deviceId, async () => {
	await load()
	schedule()
})
watch(refreshSeconds, schedule)
onUnmounted(() => {
	isMounted = false
	clearTimeout(refreshTimer)
})
</script>

<template>
	<section class="sensor-latest">
		<label>
			{{ t('sensorlogger', 'Refresh interval') }}
			<select v-model.number="refreshSeconds">
				<option :value="1">1 {{ t('sensorlogger', 'second') }}</option>
				<option :value="5">5 {{ t('sensorlogger', 'seconds') }}</option>
				<option :value="10">10 {{ t('sensorlogger', 'seconds') }}</option>
				<option :value="30">30 {{ t('sensorlogger', 'seconds') }}</option>
				<option :value="60">60 {{ t('sensorlogger', 'seconds') }}</option>
			</select>
		</label>
		<p v-if="error" class="sensorlogger-error" role="alert">{{ error }}</p>
		<p v-else-if="!latest" class="sensorlogger-empty">{{ t('sensorlogger', 'No data available') }}</p>
		<template v-else>
			<p v-if="!measurementEntries.length && hasScalarMeasurements">
				<strong>
					<template v-if="hasTemperature">{{ latest.temperature }}°C</template>
					<template v-if="hasTemperature && hasHumidity"> · </template>
					<template v-if="hasHumidity">{{ latest.humidity }}%</template>
				</strong>
			</p>
			<p v-for="entry in measurementEntries" :key="entry.dataTypeId">
				<strong>{{ entry.value }} {{ entry.short || dataTypeFor(entry)?.short }}</strong>
				{{ entry.description || dataTypeFor(entry)?.description }}
			</p>
			<p v-if="!measurementEntries.length && !hasScalarMeasurements" class="sensorlogger-empty">
				{{ t('sensorlogger', 'No data available') }}
			</p>
			<time>{{ latest.createdAt }}</time>
		</template>
	</section>
</template>
