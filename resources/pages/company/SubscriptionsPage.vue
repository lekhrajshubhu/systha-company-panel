<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <div class="mb-4">
        <h2 class="pa-0">Membership Subscriptions</h2>
        <v-card-subtitle class="pa-0">View and manage membership subscriptions.</v-card-subtitle>
      </div>
      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <div class="filters-row">
            <div class="filters-left">
              <div class="filter-item search">
                <v-text-field v-model="search" density="compact" variant="outlined" prepend-inner-icon="mdi-magnify"
                  label="Search subscriptions" clearable hide-details @update:model-value="onSearch" />
              </div>

              <div class="filter-item vendor">
                <v-select
                  v-model="vendorFilter"
                  :items="vendorOptions"
                  density="compact"
                  variant="outlined"
                  label="Vendor"
                  clearable
                  hide-details
                />
              </div>

              <div class="filter-item status">
                <v-select
                  v-model="statusFilter"
                  :items="statusOptions"
                  density="compact"
                  variant="outlined"
                  label="Status"
                  clearable
                  hide-details
                />
              </div>

              <div class="filter-item date-start">
                <v-date-input
                  v-model="dateStart"
                  prepend-inner-icon="mdi-calendar"
                  density="compact"
                  variant="outlined"
                  prepend-icon=""
                  label="Start date"
                  format="YYYY-MM-DD"
                  open-on-focus
                  hide-details
                />
              </div>

              <div class="filter-item date-end">
                <v-date-input
                  v-model="dateEnd"
                  prepend-inner-icon="mdi-calendar"
                  density="compact"
                  prepend-icon=""
                  variant="outlined"
                  label="End date"
                  format="YYYY-MM-DD"
                  open-on-focus
                  hide-details
                />
              </div>
            </div>

            <div class="filters-right">
              <v-btn icon="mdi-refresh" variant="tonal"  size="small" color="warning" @click="fetchItems" />
            </div>
          </div>

          <v-row class="mt-2">
            <v-col cols="12">
              <div class="caption text-medium-emphasis mb-0">Total {{ totalItems }} found</div>
            </v-col>
          </v-row>

        </v-card-text>

        <v-data-table-server v-model:items-per-page="itemsPerPage" :headers="headers" :items="serverItems"
          :items-length="totalItems" :loading="loading" :search="search" @update:options="loadItems"
          :hide-default-footer="totalItems <= itemsPerPage">
          <template #[`item.subs_code`]="{ item }">
            <div class="font-weight-medium">{{ item.subs_code || '-' }}</div>
          </template>

          <template #[`item.vendor`]="{ item }">
            <div class="d-flex align-center ga-3 py-2">
              <v-avatar color="primary" variant="tonal" size="32">
                <v-icon size="18">mdi-storefront</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-medium text-high-emphasis">{{ item.vendor?.name || '-' }}</div>
                <!-- <div class="caption text-medium-emphasis">Code: {{ item.vendor?.vendor_code || '-' }}</div> -->
              </div>
            </div>
          </template>

          <template #[`item.package`]="{ item }">
            <div>
              <div class="font-weight-medium text-high-emphasis">{{ item.package?.name || '-' }}</div>
              <div v-if="item.package?.short" class="caption text-medium-emphasis">{{ item.package.short }}</div>

            </div>
          </template>

          <!-- <template #[`item.plan`]="{ item }">
            <v-chip :color="item.plan === 'yearly' ? 'primary' : 'info'" size="small" variant="tonal" label class="px-3 text-uppercase">
              {{ item.plan ? item.plan.charAt(0).toUpperCase() + item.plan.slice(1) : '-' }}
            </v-chip>
          </template> -->

          <template #[`item.date`]="{ item }">
            <div>{{ formatDate(item.date || item.created_at) }}</div>
          </template>

          <template #[`item.status`]="{ item }">
            <v-chip :color="item.status === 'active' ? 'success' : 'grey-darken-1'" size="small" variant="tonal" label
              class="px-3 text-uppercase">
              {{ item.status || '-' }}
            </v-chip>
          </template>

          <template #[`item.amount`]="{ item }">
            <div>{{ formatCurrency(item.amount) }} / <span class="caption text-medium-emphasis">{{item.plan}}</span></div>
          </template>

          <template #[`item.actions`]="{ item }">
            <v-btn variant="outlined" size="small" color="primary">
              Details
            </v-btn>
          </template>

          <template #no-data>
            <div class="pa-12 text-center">
              <v-icon size="64" color="disabled" class="mb-4">mdi-receipt</v-icon>
              <div class="text-h6 text-medium-emphasis">No subscriptions found</div>
              <p class="text-body-2 text-disabled">Try adjusting your search.</p>
            </div>
          </template>
        </v-data-table-server>
      </v-card>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { getCompanySubscriptions } from '@/services/subscriptions.api'
import { formatCurrency, formatDate } from '@/utils'

interface SubscriptionItem {
  id: number
  subs_code?: string
  vendor?: { id?: number; name?: string; vendor_code?: string }
  package?: { id?: number; name?: string; short?: string }
  plan?: string
  date?: string
  created_at?: string
  status?: string
  amount?: string | number
}

const loading = ref(false)
const search = ref('')
const statusFilter = ref<string | null>(null)
const vendorFilter = ref<number | string | null>(null)
const dateStart = ref<string | null>(null)
const dateEnd = ref<string | null>(null)
const itemsPerPage = ref(15)
const serverItems = ref<SubscriptionItem[]>([
  { id: 1, subs_code: 'SUB-1001', vendor: { id: 1, name: 'Sparkle Cleaners', vendor_code: 'VND-001' }, package: { id: 1, name: 'Pro', short: 'Most popular' }, plan: 'monthly', date: '2026-03-01', status: 'active', amount: '49.99' },
  { id: 2, subs_code: 'SUB-1002', vendor: { id: 2, name: 'FlowFix Plumbing', vendor_code: 'VND-002' }, package: { id: 2, name: 'Starter', short: 'Basic access' }, plan: 'yearly', date: '2026-03-15', status: 'inactive', amount: '19.99' },
  { id: 3, subs_code: 'SUB-1003', vendor: { id: 3, name: 'Bright Sparks', vendor_code: 'VND-003' }, package: { id: 3, name: 'Enterprise', short: 'Advanced features' }, plan: 'monthly', date: '2026-04-01', status: 'active', amount: '99.00' },
])
const totalItems = ref(serverItems.value.length)
const debounceTimeout = ref<number | null>(null)

const headers = [
  { title: 'Subscription#', key: 'subs_code', align: 'start' as const, sortable: true, width:100 },
  { title: 'Vendor', key: 'vendor', align: 'start' as const, sortable: true, minWidth: 270 },
  { title: 'Package', key: 'package', align: 'start' as const, sortable: true, minWidth: 270 },
  { title: 'Status', key: 'status', align: 'start' as const, sortable: true, minWidth: 120 },
  { title: 'Amount', key: 'amount', align: 'end' as const, sortable: true, minWidth: 150 },
  { title: 'Date', key: 'date', align: 'start' as const, sortable: true, minWidth: 150 },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false, minWidth: 120 },
]

const loadItems = async ({ page, itemsPerPage, sortBy }: any) => {
  loading.value = true
  try {
    const params: any = {
      page,
      per_page: itemsPerPage,
      search: search.value,
    }

    if (sortBy.length) {
      params.sort_by = sortBy[0].key
      params.sort_order = sortBy[0].order
    }

    if (statusFilter.value) {
      params.status = statusFilter.value
    }

    if (vendorFilter.value) {
      params.vendor_id = vendorFilter.value
    }

    if (dateStart.value) {
      params.date_start = dateStart.value
    }

    if (dateEnd.value) {
      params.date_end = dateEnd.value
    }

    const response: any = await getCompanySubscriptions(params)
    serverItems.value = response.data
    totalItems.value = response.meta.total
  } catch (error) {
    console.error('Failed to fetch subscriptions:', error)
  } finally {
    loading.value = false
  }
}

const fetchItems = () => {
  loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] })
}

const onSearch = () => {
  if (debounceTimeout.value) clearTimeout(debounceTimeout.value)
  debounceTimeout.value = window.setTimeout(() => {
    fetchItems()
  }, 500)
}

// Package status options
const statusOptions = [
  'active',
  'inactive',
  'draft',
  'archived',
]

// Vendor options (placeholder - can be populated from API)
const vendorOptions = [
  { title: 'All vendors', value: null },
  { title: 'Sparkle Cleaners', value: 1 },
  { title: 'FlowFix Plumbing', value: 2 },
  { title: 'Bright Sparks', value: 3 },
]
</script>

<style scoped>
.filters-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}
.filters-left {
  display: grid;
  gap: 12px;
  grid-template-columns: repeat(12, 1fr);
  align-items: center;
  width: 100%;
  max-width: 960px;
}
.filters-right {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}
.filter-item.search { grid-column: span 12; }
.filter-item.vendor { grid-column: span 12; }
.filter-item.status { grid-column: span 12; }
.filter-item.date-start { grid-column: span 12; }
.filter-item.date-end { grid-column: span 12; }

@media (min-width: 960px) {
  /* switch to 5 equal columns so each filter item is the same width */
  .filters-left {
    grid-template-columns: repeat(5, 1fr);
    max-width: none;
  }

  /* make every filter item occupy exactly one column (equal width) */
  .filter-item {
    grid-column: span 1 !important;
  }
}
</style>
