<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <div class="mb-4">
        <h2 class="pa-0">Commission Report</h2>
        <v-card-subtitle class="pa-0">View and manage commission reports.</v-card-subtitle>
      </div>
      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <div class="filters-row">
            <div class="filters-left">
              <div class="filter-item search">
                <v-text-field v-model="search" density="compact" variant="outlined" prepend-inner-icon="mdi-magnify"
                  label="Search commissions" clearable hide-details @update:model-value="onSearch" />
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

              <div class="filter-item order-id">
                <v-text-field
                  v-model="orderIdFilter"
                  density="compact"
                  variant="outlined"
                  label="Order ID"
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
          
          
            <template #[`item.vendor_name`]="{ item }">
            <div class="d-flex align-center ga-3 py-2">
              <v-avatar color="primary" variant="tonal" size="32">
                <v-icon size="18">mdi-storefront</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-medium text-high-emphasis">{{ item.vendor?.name || '-' }}</div>
    
              </div>
            </div>
          </template>

          
          <template #[`item.order_date`]="{ item }">
            <div>{{ formatDate(item.order_date) }}</div>
          </template>

          <template #[`item.vendor_code`]="{ item }">
            <div class="font-weight-medium">{{ item.vendor?.vendor_code || '-' }}</div>
          </template>

          <!-- <template #[`item.vendor_name`]="{ item }">
            <div class="font-weight-medium text-high-emphasis">{{ item.vendor?.name || '-' }}</div>
          </template> -->

          <template #[`item.order_id`]="{ item }">
            <div class="font-weight-medium">{{ item.order_id || '-' }}</div>
          </template>

          <template #[`item.order_amount`]="{ item }">
            <div>{{ formatCurrency(item.order_amount) }}</div>
          </template>

          <template #[`item.commission_pct`]="{ item }">
            <div>{{ item.commission_pct !== undefined ? item.commission_pct + '%' : '-' }}</div>
          </template>

          <template #[`item.commission_amount`]="{ item }">
            <div>{{ formatCurrency(item.commission_amount) }}</div>
          </template>

          <template #[`item.payment_status`]="{ item }">
            <v-chip :color="getStatusColor(item.payment_status)" size="small" variant="tonal" label
              class="px-3 text-uppercase">
              {{ item.payment_status || '-' }}
            </v-chip>
          </template>

          <template #[`item.actions`]="{ item }">
            <v-btn variant="outlined" size="small" color="primary">
              Details
            </v-btn>
          </template>

          <template #no-data>
            <div class="pa-12 text-center">
              <v-icon size="64" color="disabled" class="mb-4">mdi-receipt</v-icon>
              <div class="text-h6 text-medium-emphasis">No commission reports found</div>
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
import { formatCurrency, formatDate } from '@/utils/utils'

interface CommissionItem {
  id: number
  order_date?: string
  vendor?: { id?: number; name?: string; vendor_code?: string }
  order_id?: string
  order_amount?: string | number
  commission_pct?: number
  commission_amount?: string | number
  payment_status?: string
}

const loading = ref(false)
const search = ref('')
const orderIdFilter = ref<string | null>(null)
const vendorFilter = ref<number | string | null>(null)
const dateStart = ref<string | null>(null)
const dateEnd = ref<string | null>(null)
const itemsPerPage = ref(15)
const serverItems = ref<CommissionItem[]>([
  { id: 1, order_date: '2026-03-01', vendor: { id: 1, name: 'Sparkle Cleaners', vendor_code: 'VND-001' }, order_id: 'ORD-9001', order_amount: '199.00', commission_pct: 10, commission_amount: '19.90', payment_status: 'paid' },
  { id: 2, order_date: '2026-03-15', vendor: { id: 2, name: 'FlowFix Plumbing', vendor_code: 'VND-002' }, order_id: 'ORD-9002', order_amount: '75.50', commission_pct: 12, commission_amount: '9.06', payment_status: 'pending' },
  { id: 3, order_date: '2026-04-01', vendor: { id: 3, name: 'Bright Sparks', vendor_code: 'VND-003' }, order_id: 'ORD-9003', order_amount: '320.00', commission_pct: 8, commission_amount: '25.60', payment_status: 'failed' },
])
const totalItems = ref(serverItems.value.length)
const debounceTimeout = ref<number | null>(null)

const headers = [
  { title: 'Order Date', key: 'order_date', align: 'start' as const, sortable: true },
  // { title: 'Vendor Code', key: 'vendor_code', align: 'start' as const, sortable: true },
  { title: 'Vendor Name', key: 'vendor_name', align: 'start' as const, sortable: true },
  { title: 'Order ID', key: 'order_id', align: 'start' as const, sortable: true },
  { title: 'Order Amount', key: 'order_amount', align: 'end' as const, sortable: true },
  { title: 'Commission %', key: 'commission_pct', align: 'end' as const, sortable: true },
  { title: 'Commission Amount', key: 'commission_amount', align: 'end' as const, sortable: true },
  { title: 'Payment Status', key: 'payment_status', align: 'start' as const, sortable: true },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false },
]

const loadItems = async ({ page, itemsPerPage, sortBy }: any) => {
  loading.value = true
  try {
    const params: any = {
      page,
      per_page: itemsPerPage,
      search: search.value,
    }

    if (sortBy?.length) {
      params.sort_by = sortBy[0].key
      params.sort_order = sortBy[0].order
    }

    if (orderIdFilter.value) params.order_id = orderIdFilter.value
    if (vendorFilter.value) params.vendor_id = vendorFilter.value
    if (dateStart.value) params.date_start = dateStart.value
    if (dateEnd.value) params.date_end = dateEnd.value

    // Placeholder: replace with real API call
    // const response: any = await getCommissionReport(params)
    // serverItems.value = response.data
    // totalItems.value = response.meta.total

    // For now, just filter local sample data
    let items = serverItems.value.slice()
    if (orderIdFilter.value) items = items.filter(i => i.order_id?.includes(String(orderIdFilter.value)))
    if (vendorFilter.value) items = items.filter(i => String(i.vendor?.id) === String(vendorFilter.value))
    if (dateStart.value) items = items.filter(i => new Date(i.order_date || '').getTime() >= new Date(dateStart.value!).getTime())
    if (dateEnd.value) items = items.filter(i => new Date(i.order_date || '').getTime() <= new Date(dateEnd.value!).getTime())

    totalItems.value = items.length
    // simulate server paging
    serverItems.value = items.slice(0, itemsPerPage || 15)
  } catch (error) {
    console.error('Failed to fetch commission report:', error)
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

// Filter option lists
const statusOptions = ['paid', 'pending', 'failed']
const vendorOptions = [
  { title: 'All vendors', value: null },
  { title: 'Sparkle Cleaners', value: 1 },
  { title: 'FlowFix Plumbing', value: 2 },
  { title: 'Bright Sparks', value: 3 },
]

// helper to map payment status to chip color
const getStatusColor = (status: string | undefined | null) => {
  if (!status) return 'grey-darken-1'
  const s = status.toLowerCase()
  if (s === 'paid' || s === 'success') return 'success'
  if (s === 'pending') return 'warning'
  if (s === 'failed' || s === 'error') return 'error'
  return 'grey-darken-1'
}
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
.filter-item.order-id { grid-column: span 12; }
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
