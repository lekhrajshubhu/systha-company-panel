<template>
  <v-container fluid>
    <app-page-header title="Subscription Detail" subtitle="Subscription detail information." show-back />
    <v-container fluid>
      <v-progress-linear v-if="loading" indeterminate color="primary" class="mb-4" />

      <v-row>
        <v-col cols="12" md="8">
          <AppCard class="mb-4">
            <v-card-text>
              <div class="d-flex align-center ga-4 mb-4">
                <v-avatar v-if="vendor?.logo" size="64" rounded>
                  <v-img :src="vendor.logo" />
                </v-avatar>
                <v-avatar v-else size="64" rounded color="primary">
                  <v-icon size="32">mdi-domain</v-icon>
                </v-avatar>
                <div>
                  <h2 class="text-h6 font-weight-medium">{{ vendor?.name || 'Vendor' }}</h2>
                  <div class="text-body-2 text-medium-emphasis">{{ vendor?.email }}</div>
                  <div v-if="vendor?.phone" class="text-body-2 text-medium-emphasis">{{ vendor.phone }}</div>
                </div>
              </div>

              <v-row>
                <v-col cols="12" md="6">
                  <div class="text-subtitle-2 font-weight-medium mb-3">Vendor Details</div>
                  <div class="text-body-2 mb-2"><strong>Company:</strong> {{ vendor?.company || '-' }}</div>
                  <div class="text-body-2 mb-2"><strong>Contact:</strong> {{ vendor?.contact_person || '-' }}</div>
                  <div class="text-body-2 mb-2"><strong>Address:</strong> {{ vendor?.address || '-' }}</div>
                  <div class="text-body-2"><strong>Website:</strong>
                    <a v-if="vendor?.website" :href="vendor.website" target="_blank" class="text-primary text-decoration-none">{{ vendor.website }}</a>
                    <span v-else>-</span>
                  </div>
                </v-col>

                <v-col cols="12" md="6">
                  <div class="text-subtitle-2 font-weight-medium mb-3">Package Details</div>
                  <div class="text-body-2 mb-2"><strong>Name:</strong> {{ packageDetails?.name || '-' }}</div>
                  <div class="text-body-2 mb-2"><strong>Price:</strong> {{ formattedPrice }}</div>
                  <div class="text-body-2 mb-2"><strong>Billing:</strong> {{ packageDetails?.billing_cycle || 'monthly' }}</div>
                  <div class="mt-3">
                    <div class="text-body-2 font-weight-medium mb-2">Features</div>
                    <ul class="text-body-2 ml-4 mb-0">
                      <li v-for="(f, i) in (packageDetails?.features || [])" :key="i">{{ f }}</li>
                      <li v-if="!(packageDetails?.features || []).length">No features listed</li>
                    </ul>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </AppCard>

          <AppCard class="mb-4">
            <v-card-text>
              <div class="text-subtitle-2 font-weight-medium mb-3">Subscription Info</div>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="text-body-2 mb-2">
                    <strong>Status:</strong> 
                    <span :class="statusClass">{{ subscription?.status || 'unknown' }}</span>
                  </div>
                  <div class="text-body-2 mb-2"><strong>Start:</strong> {{ formattedStart }}</div>
                  <div class="text-body-2 mb-2"><strong>End:</strong> {{ formattedEnd }}</div>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="text-body-2 mb-2"><strong>Days Left:</strong> {{ daysLeft }}</div>
                  <div class="text-body-2 mb-2"><strong>Auto Renew:</strong> {{ subscription?.auto_renew ? 'Yes' : 'No' }}</div>
                  <div class="mt-3">
                    <div class="text-body-2 font-weight-medium mb-2">Last Payment</div>
                    <div class="text-body-2">{{ lastPaymentDisplay }}</div>
                  </div>
                </v-col>
              </v-row>

              <div class="mt-4 d-flex ga-2">
                <v-btn color="success" size="small" @click="onRenew">Renew now</v-btn>
                <v-btn color="error" size="small" :disabled="isCanceled" @click="onCancel">Cancel</v-btn>
              </div>
            </v-card-text>
          </AppCard>
        </v-col>

        <v-col cols="12" md="4">
          <AppCard>
            <v-card-text>
              <div class="text-subtitle-2 font-weight-medium mb-3">Subscription ID</div>
              <div class="text-body-2">{{ subscription?.id || '—' }}</div>
            </v-card-text>
          </AppCard>
        </v-col>
      </v-row>
    </v-container>
  </v-container>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import AppPageHeader from '@/components/AppPageHeader.vue'
import AppCard from '@/components/AppCard.vue'

const loading = ref(false)

const props = defineProps({
  vendor: { type: Object, default: () => ({}) },
  packageDetails: { type: Object, default: () => ({}) },
  subscription: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['renew', 'cancel'])

// Demo data used when no props are passed (for local/demo purposes)
const demoVendor = {
  id: 1,
  name: 'Acme Corp',
  company: 'Acme Corporation',
  contact_person: 'Jane Doe',
  email: 'hello@acme.example',
  phone: '+1 (555) 123-4567',
  address: '123 Market Street, Suite 100',
  website: 'https://acme.example',
  logo: '/company-panel/assets/acme-logo.png'
}

const demoPackage = {
  id: 101,
  name: 'Pro Plan',
  price: 49.99,
  currency: 'USD',
  billing_cycle: 'monthly',
  features: ['Unlimited listings', 'Priority support', 'Custom branding']
}

const demoSubscription = {
  id: 'sub_demo_0001',
  status: 'active',
  start_date: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000).toISOString(), // 5 days ago
  end_date: new Date(Date.now() + 25 * 24 * 60 * 60 * 1000).toISOString(), // in 25 days
  auto_renew: true,
  last_payment: { date: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(), amount: 49.99 },
  currency: 'USD'
}

// Use props when provided, otherwise fall back to demo data
const vendor = computed(() => {
  return props.vendor && Object.keys(props.vendor).length ? props.vendor : demoVendor
})

const packageDetails = computed(() => {
  return props.packageDetails && Object.keys(props.packageDetails).length ? props.packageDetails : demoPackage
})

const subscription = computed(() => {
  return props.subscription && Object.keys(props.subscription).length ? props.subscription : demoSubscription
})

function formatDate(dateStr: string | null | undefined) {
  if (!dateStr) return '—'
  try {
    const d = new Date(dateStr)
    return new Intl.DateTimeFormat(undefined, { year: 'numeric', month: 'short', day: 'numeric' }).format(d)
  } catch (e) {
    return dateStr
  }
}

const formattedPrice = computed(() => {
  const p = packageDetails.value?.price
  if (p == null) return '—'
  try {
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: packageDetails.value.currency || 'USD' }).format(p)
  } catch (e) {
    return String(p)
  }
})

const formattedStart = computed(() => formatDate(subscription.value?.start_date))
const formattedEnd = computed(() => formatDate(subscription.value?.end_date))

const daysLeft = computed(() => {
  if (!subscription.value?.end_date) return '—'
  const now = new Date()
  const end = new Date(subscription.value.end_date)
  const diff = Math.ceil((end.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))
  return diff >= 0 ? diff + ' days' : (Math.abs(diff) + ' days ago')
})

const lastPaymentDisplay = computed(() => {
  const lp = subscription.value?.last_payment
  if (!lp) return 'No payments yet'
  const amount = lp.amount != null ? (new Intl.NumberFormat(undefined, { style: 'currency', currency: subscription.value.currency || packageDetails.value?.currency || 'USD' }).format(lp.amount)) : ''
  return `${formatDate(lp.date)}${amount ? ' — ' + amount : ''}`
})

const isCanceled = computed(() => (subscription.value?.status || '').toLowerCase() === 'canceled' || (subscription.value?.status || '').toLowerCase() === 'cancelled')

const statusClass = computed(() => {
  const s = (subscription.value?.status || '').toLowerCase()
  if (s === 'active') return 'text-success font-weight-medium'
  if (s === 'past_due' || s === 'past-due') return 'text-warning font-weight-medium'
  if (s === 'canceled' || s === 'cancelled') return 'text-error font-weight-medium'
  return 'text-medium-emphasis'
})

function onCancel() {
  if (isCanceled.value) return
  emit('cancel')
}

function onRenew() {
  emit('renew')
}
</script>

