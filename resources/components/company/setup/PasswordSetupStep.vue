<template>
  <div>
    <div class="d-flex align-center mb-2">
      <v-icon class="mr-2" color="primary">mdi-lock-reset</v-icon>
      <div class="text-subtitle-1 font-weight-medium">Change password</div>
    </div>
    <div class="text-body-2 text-medium-emphasis mb-4">Set a new password for this account.</div>

    <v-text-field
      v-model="passwordForm.password"
      label="New Password"
      :type="showNewPassword ? 'text' : 'password'"
      variant="outlined"
      density="comfortable"
      :append-inner-icon="showNewPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
      @click:append-inner="showNewPassword = !showNewPassword"
      autocomplete="new-password"
      class="mb-2"
    />

    <v-text-field
      v-model="passwordForm.password_confirmation"
      label="Confirm New Password"
      :type="showConfirmPassword ? 'text' : 'password'"
      variant="outlined"
      density="comfortable"
      :append-inner-icon="showConfirmPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
      @click:append-inner="showConfirmPassword = !showConfirmPassword"
      autocomplete="new-password"
      class="mb-2"
    />

    <v-alert v-if="successMessage" type="success" variant="tonal" density="compact" class="mb-4">
      {{ successMessage }}
    </v-alert>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, watch } from 'vue'

interface Props {
  current_step?: number
  errors?: any
  successMessage?: string
}

const props = defineProps<Props>()

const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const passwordForm = reactive({
  password: '',
  password_confirmation: '',
})

// Emit changes to parent
defineEmits<{
  'update:passwordForm': [value: typeof passwordForm]
}>()

// Watch for changes and emit to parent
watch(passwordForm, (newValue) => {
  // Parent can access the form via template ref
}, { deep: true })

// Expose form data to parent component
defineExpose({
  passwordForm
})
</script>
