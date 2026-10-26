import { createApp } from 'vue'
import App from './App.vue'

const root = document.getElementById('sensorlogger-app')

if (root && root.dataset.sensorloggerMounted !== 'true') {
	const navigation = JSON.parse(root.dataset.navigation || '[]')
	const app = createApp(App, {
		navigation,
		initialView: root.dataset.initialView || 'dashboard',
		initialId: root.dataset.initialId || '',
		activeNavigation: root.dataset.activeNavigation || 'showDashboard',
	})
	app.mount(root)
	root.dataset.sensorloggerMounted = 'true'
}
