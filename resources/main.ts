import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from '@/App.vue'
import { vuetify } from '@/plugins/vuetify'
import { vuetifyProTipTap } from '@/plugins/tiptap'
import { startTokenAutoRefresh } from '@/services/companyAuth'
import 'vuetify/styles'
import 'vuetify-pro-tiptap/styles/editor.css'
import '@/styles/main.scss'
import { router } from './router'

const app = createApp(App)

app.use(createPinia())
app.use(vuetify)
app.use(router)
app.use(vuetifyProTipTap)
;(app.config as any).unwrapInjectedRef = true
app.mount('#app')

startTokenAutoRefresh()
