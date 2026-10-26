import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { copyFileSync, mkdirSync, unlinkSync } from 'node:fs'
import { resolve } from 'node:path'

const copyCssToAppStyles = {
	name: 'copy-css-to-app-styles',
	writeBundle(output, bundle) {
		for (const [fileName, asset] of Object.entries(bundle)) {
			if (asset.type === 'asset' && fileName.endsWith('.css')) {
				mkdirSync(resolve('css'), { recursive: true })
				copyFileSync(resolve(output.dir, fileName), resolve('css/sensorlogger.css'))
				unlinkSync(resolve(output.dir, fileName))
			}
		}
	},
}

export default defineConfig({
	plugins: [vue(), copyCssToAppStyles],
	build: {
		outDir: 'js',
		emptyOutDir: false,
		target: 'es2022',
		cssCodeSplit: false,
		rollupOptions: {
			input: 'src/main.js',
			output: {
				entryFileNames: 'sensorlogger.js',
				chunkFileNames: 'sensorlogger-[name].js',
				assetFileNames: 'sensorlogger-[name][extname]',
			},
		},
	},
})
