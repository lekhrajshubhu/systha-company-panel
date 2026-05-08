<template>
  <div>
    <div class="d-flex align-center mb-2">
      <v-icon class="mr-2" color="primary">mdi-credit-card-outline</v-icon>
      <div class="text-subtitle-1 font-weight-medium">Payment credentials</div>
    </div>
    <div class="text-body-2 text-medium-emphasis mb-4">
      Add your payment gateway credentials (Stripe).
    </div>

    <v-row>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="paymentForm.name"
          label="Name"
          variant="outlined"
          density="comfortable"
        />
      </v-col>
      <v-col cols="12" md="6">
        <v-select
          v-model="paymentForm.mode"
          label="Mode"
          variant="outlined"
          density="comfortable"
          :items="modeItems"
          item-title="label"
          item-value="value"
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="paymentForm.credentials.publishable_key"
          label="Publishable Key"
          variant="outlined"
          density="comfortable"
          placeholder="pk_live_xxx"
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="paymentForm.credentials.secret_key"
          label="Secret Key"
          variant="outlined"
          density="comfortable"
          :type="showSecretKey ? 'text' : 'password'"
          :append-inner-icon="showSecretKey ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
          @click:append-inner="showSecretKey = !showSecretKey"
          placeholder="sk_live_xxx"
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="paymentForm.credentials.webhook_secret"
          label="Webhook Secret"
          variant="outlined"
          density="comfortable"
          placeholder="whsec_xxx"
        />
      </v-col>
    </v-row>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, watch } from 'vue'

interface Props {
  current_step?: number
  errors?: any
}

const props = defineProps<Props>()

const modeItems = [
  { label: 'Test', value: 'test' },
  { label: 'Live', value: 'live' },
]

const showSecretKey = ref(false)

const paymentForm = reactive({
  name: '',
  mode: 'test',
  is_active: true,
  credentials: {
    publishable_key: '',
    secret_key: '',
    webhook_secret: '',
  }
})

// Watch for changes and emit to parent
watch(paymentForm, (newValue) => {
  // Parent can access form via template ref
}, { deep: true })

// Expose form data to parent component
defineExpose({
  paymentForm
})
</script>
