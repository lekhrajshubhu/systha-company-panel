<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <app-page-header
        :title="pageTitle"
        :subtitle="pageSubtitle"
        show-back
      />

      <v-row>
        <v-col cols="12" md="7" lg="6">
          <app-card class="mb-4">
            <app-form-section-header
              title="Stripe Credential"
              subtitle="Store Stripe API keys and gateway flags."
            />
            <v-card-text class="pa-4 pt-0">
              <v-row>
                <v-col cols="12" md="6">
                  <app-text-field
                    v-model="form.name"
                    label="Credential Name *"
                    label-class="field-label"
                    placeholder="e.g. Stripe Main Account"
                    clearable
                    hide-details="auto"
                  />
                </v-col>

                 <v-col cols="12" md="3">
                  <app-text-field
                    v-model="form.code"
                    label="Code *"
                    readonly
                    label-class="field-label"
                    placeholder="stripe"
                    hide-details="auto"
                  />
                </v-col>

                 <v-col cols="12" md="3">
                  <app-select-field
                    v-model="form.mode"
                    label="Mode *"
                    label-class="field-label"
                    :items="modeItems"
                    item-title="label"
                    item-value="value"
                    hide-details="auto"
                  />
                </v-col>

                <v-col cols="12">
                  <app-text-field
                    v-model="form.credentials.publishable_key"
                    label="Publishable Key *"
                    label-class="field-label"
                    placeholder="pk_live_xxx"
                    clearable
                    hide-details="auto"
                  />
                </v-col>

                <v-col cols="12">
                  <app-text-field
                    v-model="form.credentials.secret_key"
                    label="Secret Key *"
                    label-class="field-label"
                    placeholder="sk_live_xxx"
                    :type="showSecretKey ? 'text' : 'password'"
                    :append-inner-icon="showSecretKey ? 'mdi-eye-off' : 'mdi-eye'"
                    @click:append-inner="showSecretKey = !showSecretKey"
                    hide-details="auto"
                  />
                </v-col>

                <v-col cols="12">
                  <app-text-field
                    v-model="form.credentials.webhook_secret"
                    label="Webhook Secret"
                    label-class="field-label"
                    placeholder="whsec_xxx"
                    type="password"
                    hide-details="auto"
                  />
                </v-col>

                <v-col cols="12">
                  <v-switch
                    v-model="form.is_active"
                    color="success"
                    hide-details
                    inset
                    label="Active"
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </app-card>

          <div class="d-flex justify-end mb-8">
            <app-flat-button :loading="submitting" @click="save">
              Save Credential
            </app-flat-button>
          </div>
        </v-col>
      </v-row>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import AppCard from '@/components/AppCard.vue'
import AppFlatButton from '@/components/AppFlatButton.vue'
import AppFormSectionHeader from '@/components/AppFormSectionHeader.vue'
import AppPageHeader from '@/components/AppPageHeader.vue'
import AppSelectField from '@/components/AppSelectField.vue'
import AppTextField from '@/components/AppTextField.vue'
import {
  createCompanyPaymentCredential,
  getCompanyPaymentCredentialDetail,
  updateCompanyPaymentCredential,
} from '@/services/paymentCredentials.api'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()
const submitting = ref(false)
const loadingDetail = ref(false)
const showSecretKey = ref(false)
const modeItems = [
  { label: 'Test', value: 'test' },
  { label: 'Live', value: 'live' },
]

const credentialId = computed(() => {
  const id = route.params.id
  if (!id) return null
  const parsed = Number(id)
  return Number.isNaN(parsed) ? null : parsed
})

const isEditMode = computed(() => credentialId.value !== null)
const pageTitle = computed(() => isEditMode.value ? 'Edit Payment Credential' : 'Add Payment Credential')
const pageSubtitle = computed(() => isEditMode.value ? 'Update Stripe payment gateway credentials.' : 'Configure Stripe payment gateway credentials.')

const form = ref({
  name: '',
  code: 'stripe',
  mode: 'test',
  credentials: {
    publishable_key: '',
    secret_key: '',
    webhook_secret: '',
  },
  is_active: false,
})

const prefillForm = (payload: any) => {
  const data = payload?.data ?? payload
  const credentials = data?.credentials ?? {}

  form.value.name = data?.name ?? ''
  form.value.code = data?.code ?? 'stripe'
  form.value.mode = data?.mode ?? 'test'
  form.value.credentials.publishable_key = credentials?.publishable_key ?? ''
  form.value.credentials.secret_key = credentials?.secret_key ?? ''
  form.value.credentials.webhook_secret = credentials?.webhook_secret ?? ''
  form.value.is_active = Boolean(data?.is_active)
}

const loadCredential = async () => {
  if (!credentialId.value) return
  loadingDetail.value = true
  try {
    const response: any = await getCompanyPaymentCredentialDetail(credentialId.value)
    prefillForm(response)
  } catch (error) {
    console.error('Failed to load payment credential:', error)
  } finally {
    loadingDetail.value = false
  }
}

const isValid = () => {
  return form.value.name.trim() !== '' &&
    form.value.code.trim() !== '' &&
    form.value.mode.trim() !== '' &&
    form.value.credentials.publishable_key.trim() !== '' &&
    form.value.credentials.secret_key.trim() !== ''
}

const save = async () => {
  if (!isValid() || submitting.value || loadingDetail.value) return

  submitting.value = true
  try {
    const payload = {
      name: form.value.name,
      code: form.value.code,
      mode: form.value.mode,
      credentials: {
        publishable_key: form.value.credentials.publishable_key,
        secret_key: form.value.credentials.secret_key,
        webhook_secret: form.value.credentials.webhook_secret || null,
      },
      is_active: form.value.is_active,
    }

    if (isEditMode.value && credentialId.value !== null) {
      await updateCompanyPaymentCredential(credentialId.value, payload)
    } else {
      await createCompanyPaymentCredential(payload)
    }

    await router.push({ name: 'company.config.payment' })
  } catch (error) {
    console.error(`Failed to ${isEditMode.value ? 'update' : 'create'} payment credential:`, error)
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadCredential()
})
</script>

<style scoped>
.field-label {
  color: var(--v-theme-on-surface, #111) !important;
  font-weight: 500;
}
</style>
