<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <app-page-header title="Add Package" subtitle="Create a membership package and pricing plans." show-back />
      <v-row>
        <v-col cols="12" md="8" lg="7">
          <app-card class="mb-4">
            <app-form-section-header title="Package Information" subtitle="Basic package profile and Stripe product setup." />
            <v-card-text class="pa-4 pt-0">
              <v-row>
                <v-col cols="12">
                  <app-text-field
                    v-model="form.name"
                    label="Package Name *"
                    label-class="field-label"
                    placeholder="e.g. Premium Plan"
                    clearable
                    hide-details="auto"
                  />
                </v-col>

                <v-col cols="12">
                  <div class="field-label mb-1">Highlight</div>
                  <v-textarea
                    v-model="form.description"
                    variant="outlined"
                    density="comfortable"
                    placeholder="Brief description of the package"
                    rows="2"
                    auto-grow
                    hide-details="auto"
                  />
                </v-col>

                <v-col cols="12">
                  <div class="field-label mb-1">Features</div>
                  <v-textarea
                    v-model="form.features"
                    variant="outlined"
                    density="comfortable"
                    placeholder="Comma-separated, e.g. 5 listings, Priority support"
                    rows="2"
                    auto-grow
                    hide-details="auto"
                  />
                </v-col>

                <v-col cols="12" md="7">
                  <app-text-field
                    v-model="form.stripeProductId"
                    label="Stripe Product"
                    label-class="field-label"
                    placeholder="Stripe Product ID"
                    readonly
                    hide-details="auto"
                  />
                  <div class="caption text-medium-emphasis mt-1">Will be auto-generated</div>
                </v-col>

                <v-col cols="12" md="5" class="d-flex align-center">
                  <app-flat-button color="primary" block @click="createStripeProduct">
                    Create Product in Stripe
                  </app-flat-button>
                </v-col>
              </v-row>

              <div class="caption text-medium-emphasis mt-2">
                Stripe Product must be created before adding prices.
              </div>
            </v-card-text>
          </app-card>

          <app-card class="mb-4">
            <app-form-section-header title="Pricing Plans" subtitle="Configure monthly and yearly pricing." />
            <v-card-text class="pa-4 pt-0">
              <div class="plan-title mb-2">Monthly Plan</div>
              <v-row>
                <v-col cols="12" md="4">
                  <app-text-field
                    v-model="form.plans.monthly.price"
                    label="Price *"
                    label-class="field-label"
                    placeholder="0.00"
                    type="number"
                    min="0"
                    step="0.01"
                    hide-details="auto"
                  />
                </v-col>
                <v-col cols="12" md="4">
                  <app-select-field
                    v-model="form.plans.monthly.currency"
                    label="Currency"
                    label-class="field-label"
                    :items="currencyItems"
                    hide-details="auto"
                  />
                </v-col>
                <v-col cols="12" md="8">
                  <app-text-field
                    v-model="form.plans.monthly.stripePriceId"
                    label="Stripe Price ID"
                    label-class="field-label"
                    placeholder="Auto-generated"
                    readonly

                  />
                </v-col>
                <v-col cols="12" md="4" class="d-flex align-center">
                  <app-flat-button color="primary" block :disabled="!hasStripeProduct" @click="createMonthlyPrice">
                    Create Monthly Price
                  </app-flat-button>
                </v-col>
              </v-row>

              <v-divider class="my-4" />

              <div class="plan-title mb-2">Yearly Plan</div>
              <v-row>
                <v-col cols="12" md="4">
                  <app-text-field
                    v-model="form.plans.yearly.price"
                    label="Price *"
                    label-class="field-label"
                    placeholder="0.00"
                    type="number"
                    min="0"
                    step="0.01"
                    hide-details="auto"
                  />
                </v-col>
                <v-col cols="12" md="4">
                  <app-select-field
                    v-model="form.plans.yearly.currency"
                    label="Currency"
                    label-class="field-label"
                    :items="currencyItems"
                    hide-details="auto"
                  />
                </v-col>
                <v-col cols="12" md="8">
                  <app-text-field
                    v-model="form.plans.yearly.stripePriceId"
                    label="Stripe Price ID"
                    label-class="field-label"
                    placeholder="Auto-generated"
                    readonly
                  />
                </v-col>
                <v-col cols="12" md="4" class="d-flex align-center">
                  <app-flat-button color="primary" block :disabled="!hasStripeProduct" @click="createYearlyPrice">
                    Create Yearly Price
                  </app-flat-button>
                </v-col>
              </v-row>

              <div class="caption text-medium-emphasis mt-2">
                Create a Stripe Product first to enable price creation.
              </div>
            </v-card-text>
          </app-card>

          <div class="d-flex justify-end mb-8">
            <app-flat-button :loading="saving" @click="savePackage">Save Package</app-flat-button>
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
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { createCompanyPackage } from '@/services/packages.api'

const router = useRouter()
const saving = ref(false)

const currencyItems = ['USD']

const form = ref({
  name: '',
  description: '',
  features: '',
  stripeProductId: '',
  // plans object contains monthly and yearly plan definitions
  plans: {
    monthly: {
      recurring: 'monthly',
      price: '0.00',
      currency: 'USD',
      stripePriceId: '',
    },
    yearly: {
      recurring: 'yearly',
      price: '0.00',
      currency: 'USD',
      stripePriceId: '',
    },
  },
})

const hasStripeProduct = computed(() => form.value.stripeProductId.trim() !== '')

const createStripeProduct = () => {
  console.info('Create Stripe product clicked')
}

const createMonthlyPrice = () => {
  if (!hasStripeProduct.value) return
  console.info('Create monthly stripe price clicked')
}

const createYearlyPrice = () => {
  if (!hasStripeProduct.value) return
  console.info('Create yearly stripe price clicked')
}

const savePackage = async () => {
  if (saving.value) return

  saving.value = true
  try {
    // log whole form to console for debugging
    console.log('Package form payload:', JSON.parse(JSON.stringify(form.value)))

    // use API service to create package
    const payload = {
      name: form.value.name,
      description: form.value.description,
      highlight: form.value.features,
      stripeProductId: form.value.stripeProductId,
      plans: {
        monthly: {
          recurring: form.value.plans.monthly.recurring,
          price: Number(form.value.plans.monthly.price) || 0,
          currency: form.value.plans.monthly.currency,
          stripePriceId: form.value.plans.monthly.stripePriceId || null,
        },
        yearly: {
          recurring: form.value.plans.yearly.recurring,
          price: Number(form.value.plans.yearly.price) || 0,
          currency: form.value.plans.yearly.currency,
          stripePriceId: form.value.plans.yearly.stripePriceId || null,
        },
      },
    }

    console.log('Computed payload sent to API:', payload)

    await createCompanyPackage(payload)
    await router.push({ name: 'company.membership.package' })
  } catch (err) {
    console.error('Failed to save package', err)
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.plan-title {
  font-weight: 600;
  font-size: 0.95rem;
}

.field-label {
  color: var(--v-theme-on-surface, #111) !important;
  font-weight: 500;
}
</style>
