<template>
  <div class="login-page">
    <div class="login-form-wrap pa-8">
      <div class="d-flex flex-column align-center text-center mb-4">
        <img :src="logoSrc" alt="Logo" class="logo" />
        <h1 class="text-h5 mt-4 mb-1">Welcome Back</h1>
        <p class="text-body-2 text-medium-emphasis mb-0">Sign in to your admin panel</p>
      </div>

      <v-form @submit.prevent="onSubmit">
        <v-text-field
          v-model="email"
          label="Email"
          type="email"
          variant="outlined"
          density="comfortable"
          prepend-inner-icon="mdi-email-outline"
          autocomplete="off"
          class="mb-2"
        />

        <v-text-field
          v-model="password"
          :type="showPassword ? 'text' : 'password'"
          label="Password"
          variant="outlined"
          density="comfortable"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
          @click:append-inner="showPassword = !showPassword"
          autocomplete="current-password"
          class="mb-4"
        />

        <v-alert
          v-if="errorMessage"
          type="error"
          variant="tonal"
          density="compact"
          class="mb-3"
        >
          {{ errorMessage }}
        </v-alert>

        <app-flat-button type="submit" :loading="isSubmitting" size="large" block>Sign In</app-flat-button>
      </v-form>
    </div>
  </div>
</template>

<script setup lang="ts">
import AppFlatButton from '@/components/AppFlatButton.vue'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import logoSrc from '@/assets/logo-mark.svg'
import {
  AUTH_ACTIVE_CONTEXT_KEY,
  AUTH_CONTEXTS_KEY,
  AUTH_PENDING_CONTEXT_SELECTION_KEY,
  loginCompany,
  saveActiveContext,
  saveAuthToken,
} from '@/services/auth.api'
import { clearAuthSession, TENANTPANEL_ACCOUNT_KEY } from '@/services/companyAuth'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const isSubmitting = ref(false)
const errorMessage = ref('')
const router = useRouter()

onMounted(() => {
  const authError = sessionStorage.getItem('company_auth_error')
  if (authError) {
    errorMessage.value = authError
    sessionStorage.removeItem('company_auth_error')
  }
})

const onSubmit = async () => {
  if (isSubmitting.value) return
  
  errorMessage.value = ''
  isSubmitting.value = true

  try {
    const payload = await loginCompany({
      email: email.value.trim(),
      password: password.value,
    })

    console.log({payload});
    const token = payload?.token
    if (token) saveAuthToken(token)

    const contexts = Array.isArray(payload?.contexts) ? payload.contexts : []

    if (contexts.length) {
      localStorage.setItem(AUTH_CONTEXTS_KEY, JSON.stringify(contexts))
      window.dispatchEvent(new Event('auth-changed'))
    }

    // Store basic account info for the app shell (company filled after context selection).
    if (payload?.client) {
      const name =
        `${payload.client?.first_name ?? ''} ${payload.client?.last_name ?? ''}`.trim() ||
        'Company User'
      localStorage.setItem(
        TENANTPANEL_ACCOUNT_KEY,
        JSON.stringify({
          id: payload.client?.id ?? '',
          name,
          email: payload.client?.email ?? email.value.trim(),
        }),
      )
    }

    const requiresContextSelection = Boolean(payload?.requires_context_selection)
    const activeContext = payload?.active_context ?? null

    if (!requiresContextSelection && activeContext) {
      saveActiveContext(payload.active_context)
      localStorage.removeItem(AUTH_PENDING_CONTEXT_SELECTION_KEY)

      const activeCompany = activeContext.company
      const client = payload.client
      const name = `${client?.first_name ?? ''} ${client?.last_name ?? ''}`.trim() || 'Company User'
      const rawRole = (activeContext as any)?.roles?.[0]
      const role = typeof rawRole === 'string' ? rawRole : rawRole?.label ?? rawRole?.value
      const account = {
        id: client?.id ?? '',
        name,
        email: client?.email ?? email.value.trim(),
        role,
        company: activeCompany?.code
          ? { name: activeCompany?.name ?? '', code: activeCompany.code }
          : undefined,
      }
      localStorage.setItem(TENANTPANEL_ACCOUNT_KEY, JSON.stringify(account))
      await router.push({ name: 'company.dashboard' })
      return
    }

    // If context selection is required and no active context is set, go to context selector.
    if (requiresContextSelection && !activeContext) {
      // Ensure we don't carry over a stale active context/account from a previous session.
      localStorage.removeItem(AUTH_ACTIVE_CONTEXT_KEY)
      try {
        const existingAccountStr = localStorage.getItem(TENANTPANEL_ACCOUNT_KEY)
        if (existingAccountStr) {
          const existingAccount = JSON.parse(existingAccountStr)
          if (existingAccount && typeof existingAccount === 'object') {
            delete existingAccount.company
            localStorage.setItem(TENANTPANEL_ACCOUNT_KEY, JSON.stringify(existingAccount))
          }
        }
      } catch {
        // ignore
      }
      if (contexts.length) {
        localStorage.setItem(AUTH_CONTEXTS_KEY, JSON.stringify(contexts))
      }
      localStorage.setItem(AUTH_PENDING_CONTEXT_SELECTION_KEY, 'true')
      window.dispatchEvent(new Event('auth-changed'))
      await router.push({ name: 'company.context-select' })
      return
    }

    clearAuthSession()
    localStorage.removeItem(AUTH_ACTIVE_CONTEXT_KEY)
    localStorage.removeItem(AUTH_CONTEXTS_KEY)
    localStorage.removeItem(AUTH_PENDING_CONTEXT_SELECTION_KEY)
    throw new Error('Unable to determine login context. Please contact support.')
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Unable to connect to server.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped lang="scss">
@use '../../styles/variables' as *;

.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background:
    radial-gradient(circle at 20% 20%, rgba(15, 118, 110, 0.12), transparent 40%),
    radial-gradient(circle at 80% 70%, rgba(14, 116, 144, 0.08), transparent 42%);
}

.login-form-wrap {
  width: 100%;
  max-width: 440px;
  border: 1px solid rgba(15, 23, 42, 0.1);
  border-radius: $radius-md;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(2px);
}

.logo {
  width: 64px;
  height: 64px;
  display: block;
}
</style>
