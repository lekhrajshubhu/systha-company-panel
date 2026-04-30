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
          autocomplete="email"
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
import { AUTH_ACTIVE_CONTEXT_KEY, AUTH_CONTEXTS_KEY, loginCompany, saveActiveContext, saveAuthToken } from '@/services/auth.api'

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

    const token = payload?.token;

    console.log({token});
    if (token) saveAuthToken(token);

    if (Array.isArray(payload?.contexts) && payload.contexts.length) {
      localStorage.setItem(AUTH_CONTEXTS_KEY, JSON.stringify(payload.contexts));
    }



    // Persist active context returned by the server.
    try {
      saveActiveContext(payload.active_context);
    } catch {
      // ignore
    }
    try {
      localStorage.setItem(AUTH_ACTIVE_CONTEXT_KEY, JSON.stringify(payload.active_context));
    } catch {
      // ignore
    }
    await router.push({ name: 'company.dashboard' })
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
