<template>
  <v-container fluid>

    <app-page-header :title="pageTitle" :subtitle="pageSubtitle" />
    <v-container fluid>
      <v-row>
        <v-col cols="12" md="8" lg="7">
          <v-form ref="formRef">
            <app-card class="mb-4">
              <app-form-section-header title="Plan Information" subtitle="Basic plan profile and Stripe setup." />
              <v-card-text class="pa-4 pt-0">
                <v-row>
                  <v-col cols="8">
                    <app-text-field v-model="form.name" label="Plan Name *" label-class="field-label"
                      placeholder="e.g. Premium Plan" clearable hide-details="auto" :rules="[required]" />
                  </v-col>
	                  <v-col cols="4">
	                    <app-select-field
	                      v-model="form.status"
	                      label="Status"
                        :clearable="false"
	                      label-class="field-label"
	                      :items="statusItems"
	                    />
	                  </v-col>


	                  <v-col cols="12" v-if="hasStripeProduct">
                      <v-chip label color="primary">
                        <span class="font-weight-medium">Stripe Product:</span>
                        <span class="ml-2">{{ form.stripeProductId }}</span>
                      </v-chip>
	                    <!-- <p class="text-body-2 text-medium-emphasis mb-0">
	                      <span class="font-weight-medium">Stripe Product ID:</span>
	                      <span class="ml-2">{{ form.stripeProductId }}</span>
	                    </p> -->
	                  </v-col>

                  <v-col cols="12">
                    <div class="field-label mb-1">Highlight</div>
                    <v-textarea v-model="form.highlight" variant="outlined" density="comfortable"
                      placeholder="Brief description of the package" rows="2" auto-grow hide-details="auto" />
                  </v-col>

                  <v-col cols="12">
                    <div class="field-label mb-1">Features</div>
                    <v-textarea v-model="form.features" variant="outlined" density="comfortable"
                      placeholder="Comma-separated, e.g. 5 listings, Priority support" rows="2" auto-grow
                      hide-details="auto" />
                  </v-col>
                </v-row>


              </v-card-text>
            </app-card>

            <app-card class="mb-4">
              <app-form-section-header title="Pricing Plans" subtitle="Configure monthly and yearly pricing." />
              <v-card-text class="pa-4 pt-0">
                <v-row>
                  <v-col cols="12" md="6">
                    <div>
                      <app-text-field v-model="form.plans.monthly.price" label="Monthly Price (USD *)"
                        label-class="field-label" placeholder="0.00" type="number" min="0" step="0.01"
                        hide-details="auto" :rules="priceRules" />

	                      <div v-if="form.plans.monthly.stripePriceId" class="mt-3">
                          <v-chip label color="primary">
                            <span class="font-weight-medium">Stripe Price ID :</span>
                            <span class="ml-2">{{ form.plans.monthly.stripePriceId }}</span>
                          </v-chip>
	                      </div>
                    </div>
                  </v-col>
                  <v-col cols="12" md="6">
                    <div>
                      <app-text-field v-model="form.plans.yearly.price" label="Yearly Price (USD *)"
                        label-class="field-label" placeholder="0.00" type="number" min="0" step="0.01"
                        hide-details="auto" :rules="priceRules" />

	                      <div v-if="form.plans.yearly.stripePriceId" class="mt-3">
                          <v-chip label color="primary">
                        
                              <span class="font-weight-medium">Stripe Price ID :</span>
                              <span class="ml-2">{{ form.plans.yearly.stripePriceId }}</span>
                     
                          </v-chip>
	                      </div>
                    </div>
                  </v-col>

                </v-row>
              </v-card-text>
            </app-card>

            <div class="d-flex justify-end mb-8">
              <app-flat-button :loading="saving || loadingPlan" :disabled="loadingPlan" @click="savePlan">{{
                submitLabel
                }}</app-flat-button>
            </div>
          </v-form>
        </v-col>
      </v-row>
    </v-container>

  </v-container>
</template>

<script setup lang="ts">
import AppCard from '@/components/AppCard.vue'
import AppFlatButton from '@/components/AppFlatButton.vue'
import AppFormSectionHeader from '@/components/AppFormSectionHeader.vue'
import AppPageHeader from '@/components/AppPageHeader.vue'
import AppTextField from '@/components/AppTextField.vue'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { createCompanyPlan, getCompanyPlanDetail, updateCompanyPlan } from '@/services/company-plans.api'
import AppSelectField from '@/components/AppSelectField.vue'

const router = useRouter()
const route = useRoute()
const saving = ref(false)
const loadingPlan = ref(false)
const formRef = ref(null as any)

const statusItems = [
  { label: 'Active', value: 'active' },
  { label: 'Inactive', value: 'inactive' },
]

const form = ref({
  name: '',
  features: '',
  highlight: '',
  stripeProductId: '',
  status: 'inactive',
  // plans object contains monthly and yearly plan definitions
  plans: {
    monthly: {
      recurring: 'month',
      price: '0.00',
      currency: 'USD',
      stripePriceId: '',
    },
    yearly: {
      recurring: 'year',
      price: '0.00',
      currency: 'USD',
      stripePriceId: '',
    },
  },
})

const planId = computed(() => {
  const id = route.params.id
  if (!id) return null
  const parsed = Number(id)
  return Number.isNaN(parsed) ? null : parsed
})

const isEditMode = computed(() => planId.value !== null)

const pageTitle = computed(() => (isEditMode.value ? 'Edit Plan' : 'Create Plan'))
const pageSubtitle = computed(() => (isEditMode.value ? 'Update membership plan and pricing.' : 'Create a membership plan and pricing plans.'))
const submitLabel = computed(() => (isEditMode.value ? 'Update Plan' : 'Save Plan'))

const hasStripeProduct = computed(() => form.value.stripeProductId.trim() !== '')

// validation rule helpers (match StaffFormPage.vue style)
const required = (v: any) => {
  if (v === null || v === undefined) return 'This field is required'
  if (Array.isArray(v)) return v.length > 0 || 'This field is required'
  return String(v).trim().length > 0 || 'This field is required'
}

const isNonNegativeNumber = (v: any) => {
  if (v === null || v === undefined || String(v).trim() === '') return true
  const n = Number(v)
  return (!Number.isNaN(n) && n >= 0) || 'Enter a valid price'
}

const priceRules = [required, isNonNegativeNumber]

const normalizeRecurringType = (value: any): 'month' | 'year' | null => {
  const s = String(value ?? '').toLowerCase().trim()
  if (s === 'month' || s === 'monthly') return 'month'
  if (s === 'year' || s === 'yearly' || s === 'annual') return 'year'
  return null
}

const pickPlanPrice = (plan: any, recurring: 'month' | 'year') => {
  const prices = Array.isArray(plan?.prices) ? plan.prices : []
  return prices.find((p: any) => normalizeRecurringType(p?.billing_cycle ?? p?.billingCycle) === recurring) ?? null
}

const prefillFromPlanDetail = (plan: any) => {
  form.value.name = plan?.name ?? ''
  form.value.highlight = plan?.highlight ?? ''
  form.value.features = plan?.description ?? ''
  form.value.status = plan?.status ?? form.value.status

  form.value.stripeProductId = plan?.stripe_product_id ?? plan?.stripeProductId ?? plan?.stripe_product ?? ''

  const monthly = pickPlanPrice(plan, 'month')
  const yearly = pickPlanPrice(plan, 'year')

  if (monthly) {
    form.value.plans.monthly.recurring = 'month'
    form.value.plans.monthly.price = String(monthly?.price ?? form.value.plans.monthly.price ?? '0.00')
    form.value.plans.monthly.currency = monthly?.currency ?? form.value.plans.monthly.currency ?? 'USD'
    form.value.plans.monthly.stripePriceId = monthly?.stripe_price_id ?? monthly?.stripePriceId ?? form.value.plans.monthly.stripePriceId ?? ''
  }

  if (yearly) {
    form.value.plans.yearly.recurring = 'year'
    form.value.plans.yearly.price = String(yearly?.price ?? form.value.plans.yearly.price ?? '0.00')
    form.value.plans.yearly.currency = yearly?.currency ?? form.value.plans.yearly.currency ?? 'USD'
    form.value.plans.yearly.stripePriceId = yearly?.stripe_price_id ?? yearly?.stripePriceId ?? form.value.plans.yearly.stripePriceId ?? ''
  }
}

const loadPlanForEdit = async () => {
  if (!planId.value) return

  loadingPlan.value = true
  try {
    const response: any = await getCompanyPlanDetail(planId.value)
    prefillFromPlanDetail(response?.data ?? response)
  } catch (error) {
    console.error('Failed to load plan detail:', error)
  } finally {
    loadingPlan.value = false
  }
}

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

const savePlan = async () => {
  if (saving.value || loadingPlan.value) return

  const { valid } = await formRef.value.validate()
  if (!valid) return

  saving.value = true
  try {
    // log whole form to console for debugging
    console.log('Plan form payload:', JSON.parse(JSON.stringify(form.value)))

    // use API service to create/update plan
    const payload = {
      name: form.value.name,
      description: form.value.features,
      highlight: form.value.highlight,
      status: form.value.status,
      plan_prices: [
        {
          recurring_type: form.value.plans.monthly.recurring,
          price: Number(form.value.plans.monthly.price) || 0,
          currency: form.value.plans.monthly.currency,
        },
        {
          recurring_type: form.value.plans.yearly.recurring,
          price: Number(form.value.plans.yearly.price) || 0,
          currency: form.value.plans.yearly.currency,
        },
      ],
    }

    console.log('Computed payload sent to API:', payload)

    if (isEditMode.value && planId.value !== null) {
      await updateCompanyPlan(planId.value, payload)
    } else {
      await createCompanyPlan(payload)
    }
    await router.push({ name: 'company.plans' })
  } catch (err) {
    console.error('Failed to save plan', err)
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadPlanForEdit()
})
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
