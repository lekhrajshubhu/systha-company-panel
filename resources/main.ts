import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from '@/App.vue'
import { vuetify } from '@/plugins/vuetify'
import { vuetifyProTipTap } from '@/plugins/tiptap'
import { router } from '@/router'
import { startTokenAutoRefresh } from '@/services/companyAuth'
import 'vuetify/styles'
import 'vuetify-pro-tiptap/styles/editor.css'
import '@/styles/main.scss'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(vuetify)
app.use(vuetifyProTipTap)
;(app.config as any).unwrapInjectedRef = true
app.mount('#app')

startTokenAutoRefresh()
