<script setup>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { translate as t } from '@nextcloud/l10n'

const props = defineProps({
	deviceId: { type: [Number, String], required: true },
})

const latest = ref(null)
const dataTypes = ref([])
const error = ref('')
const refreshSeconds = ref(5)
let refreshTimer = null
let isMounted = false

async function load() {
	error.value = ''
	try {
		const response = await axios.get(generateUrl(`/apps/sensorlogger/lastLog/${props.deviceId}`), {
			params: { limit: 1 },
		})
		if (response.data?.logs) {
			latest.value = response.data.logs[0] || null
			dataTypes.value = response.data.dataTypes || []
		} else {
			latest.value = response.data?.[0] || null
			dataTypes.value = []
		}
	} catch (exception) {
		error.value = t('sensorlogger', 'Could not refresh latest measurement')
	}
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
			<p v-if="!latest.data?.length"><strong>{{ latest.temperature }}°C · {{ latest.humidity }}%</strong></p>
			<p v-for="entry in latest.data || []" :key="entry.dataTypeId">
				<strong>{{ entry.value }} {{ dataTypes.find((type) => Number(type.id) === Number(entry.dataTypeId))?.short }}</strong>
				{{ dataTypes.find((type) => Number(type.id) === Number(entry.dataTypeId))?.description }}
			</p>
			<time>{{ latest.createdAt }}</time>
		</template>
	</section>
</template>
