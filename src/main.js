import { createApp } from 'vue'
import App from './App.vue'

const root = document.getElementById('sensorlogger-app')

if (root) {
	const navigation = JSON.parse(root.dataset.navigation || '[]')
	createApp(App, {
		navigation,
		initialView: root.dataset.initialView || 'dashboard',
		initialId: root.dataset.initialId || '',
		activeNavigation: root.dataset.activeNavigation || 'showDashboard',
	}).mount(root)
}
