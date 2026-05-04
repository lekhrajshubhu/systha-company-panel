<template>
  <div class="context-page">
    <div class="context-wrap pa-8">
      <div class="d-flex flex-column align-center text-center mb-4">
        <img :src="logoSrc" alt="Logo" class="logo" />
        <h1 class="text-h5 mt-4 mb-1">Select a Company</h1>
        <p class="text-body-2 text-medium-emphasis mb-0">
          Choose which company you want to manage.
        </p>
      </div>

      <v-alert v-if="errorMessage" type="error" variant="tonal" density="compact" class="mb-4">
        {{ errorMessage }}
      </v-alert>

      <v-list v-if="contexts.length" lines="two" class="context-list" density="comfortable">
        <v-list-item
          v-for="ctx in contexts"
          :key="ctx.key"
          class="context-item"
          :disabled="isSelecting"
          @click="selectContext(ctx)"
        >
          <template #prepend>
            <v-avatar size="40" rounded="lg" color="surface-variant">
              <v-img v-if="ctx.company?.logo" :src="ctx.company.logo" alt="Logo" cover />
              <span v-else class="text-caption font-weight-medium">
                {{ getInitials(ctx.company?.name ?? 'Company') }}
              </span>
            </v-avatar>
          </template>

          <v-list-item-title class="font-weight-medium">
            {{ ctx.company?.name || 'Company' }}
          </v-list-item-title>
          <v-list-item-subtitle>
            Role: {{ (ctx.roles && ctx.roles[0]) || '—' }}
          </v-list-item-subtitle>
        </v-list-item>
      </v-list>

      <div v-else class="text-body-2 text-medium-emphasis text-center">
        No contexts available.
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import logoSrc from '@/assets/logo-mark.svg'
import {
  AUTH_ACTIVE_CONTEXT_KEY,
  AUTH_CONTEXTS_KEY,
  AUTH_PENDING_CONTEXT_SELECTION_KEY,
  selectCompanyContext,
  saveActiveContext,
  saveAuthToken,
  type LoginContext,
} from '@/services/auth.api'
import { TENANTPANEL_ACCOUNT_KEY } from '@/services/companyAuth'

const router = useRouter()
const errorMessage = ref('')
const rawContexts = ref<LoginContext[]>([])
const isSelecting = ref(false)

const contexts = computed(() => rawContexts.value.filter((c) => c?.type === 'company' && c?.company?.code))

onMounted(() => {
  // If there's already an active context, this page should not be reachable.
  const pendingSelection = localStorage.getItem(AUTH_PENDING_CONTEXT_SELECTION_KEY) === 'true'
  const storedActiveContext = localStorage.getItem(AUTH_ACTIVE_CONTEXT_KEY)
  if (!pendingSelection && storedActiveContext) {
    try {
      const parsed = JSON.parse(storedActiveContext)
      if (parsed && typeof parsed === 'object') {
        router.replace({ name: 'company.dashboard' })
        return
      }
    } catch {
      // ignore
    }
  }

  const stored = localStorage.getItem(AUTH_CONTEXTS_KEY)
  if (!stored) {
    sessionStorage.setItem('company_auth_error', 'No contexts found. Please sign in again.')
    router.replace({ name: 'company.login' })
    return
  }
  try {
    const parsed = JSON.parse(stored)
    if (!Array.isArray(parsed) || parsed.length === 0) {
      sessionStorage.setItem('company_auth_error', 'No contexts found. Please sign in again.')
      router.replace({ name: 'company.login' })
      return
    }
    rawContexts.value = parsed as LoginContext[]
  } catch {
    sessionStorage.setItem('company_auth_error', 'Unable to read contexts. Please sign in again.')
    router.replace({ name: 'company.login' })
  }
})

const getInitials = (name: string): string => {
  const parts = name.split(' ').filter(Boolean)
  if (parts.length === 0) return 'CO'
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase()
  return (parts[0][0] + parts[1][0]).toUpperCase()
}

const selectContext = async (ctx: LoginContext) => {
  errorMessage.value = ''
  if (!ctx?.company?.code) {
    errorMessage.value = 'Invalid context selection.'
    return
  }

  if (isSelecting.value) return
  isSelecting.value = true

  try {
    const payload = await selectCompanyContext({ context_key: ctx.key, key: ctx.key })

    const token = payload?.token
    if (token) saveAuthToken(token)

    const active = payload?.active_context
    if (active) {
      saveActiveContext(active)
    } else {
      // Fallback: keep a minimal active context client-side so route guards pass,
      // but prefer server-provided active_context when available.
      saveActiveContext(ctx as any)
    }

    // Refresh cached contexts if API returns them.
    if (Array.isArray(payload?.contexts)) {
      localStorage.setItem(AUTH_CONTEXTS_KEY, JSON.stringify(payload.contexts))
      window.dispatchEvent(new Event('auth-changed'))
    }

    // Update shell "account" so app services can read company code.
    const storedUser = localStorage.getItem(TENANTPANEL_ACCOUNT_KEY)
    let parsedUser: any = null
    try {
      parsedUser = storedUser ? JSON.parse(storedUser) : null
    } catch {
      parsedUser = null
    }

    const company = active?.company ?? ctx.company
    const role =
      active?.roles?.[0]?.label ??
      active?.roles?.[0]?.value ??
      (ctx.roles && (ctx.roles as any)[0]) ??
      parsedUser?.role

    const account = {
      id: parsedUser?.id ?? payload?.client?.id ?? '',
      name: parsedUser?.name ?? 'Company User',
      email: parsedUser?.email ?? '',
      role,
      company: company?.code ? { name: company?.name ?? '', code: company.code } : undefined,
    }
    localStorage.setItem(TENANTPANEL_ACCOUNT_KEY, JSON.stringify(account))
    localStorage.removeItem(AUTH_PENDING_CONTEXT_SELECTION_KEY)

    await router.push({ name: 'company.dashboard' })
  } catch (e) {
    errorMessage.value = e instanceof Error ? e.message : 'Unable to select context. Please try again.'
  } finally {
    isSelecting.value = false
  }
}
</script>

<style scoped lang="scss">
@use '../../styles/variables' as *;

.context-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background:
    radial-gradient(circle at 20% 20%, rgba(15, 118, 110, 0.12), transparent 40%),
    radial-gradient(circle at 80% 70%, rgba(14, 116, 144, 0.08), transparent 42%);
}

.context-wrap {
  width: 100%;
  max-width: 520px;
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

.context-list {
  border-radius: $radius-md;
}

.context-item {
  cursor: pointer;
  border-radius: $radius-md;
}
</style>
