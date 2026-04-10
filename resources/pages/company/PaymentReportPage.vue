<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <div class="mb-4">
        <h2 class="pa-0">Payment Report</h2>
        <v-card-subtitle class="pa-0">View and manage payment reports.</v-card-subtitle>
      </div>
      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <div class="filters-row">
            <div class="filters-left">
              <div class="filter-item search">
                <v-text-field v-model="search" density="compact" variant="outlined" prepend-inner-icon="mdi-magnify"
                  label="Search payments" clearable hide-details @update:model-value="onSearch" />
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

              <div class="filter-item package">
                <v-select
                  v-model="packageFilter"
                  :items="packageOptions"
                  density="compact"
                  variant="outlined"
                  label="Package"
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

          <template #[`item.payment_id`]="{ item }">
            <div class="font-weight-medium">{{ item.payment_id || '-' }}</div>
          </template>

          <template #[`item.vendor`]="{ item }">
            <div class="d-flex align-center ga-3 py-2">
              <v-avatar color="primary" variant="tonal" size="32">
                <v-icon size="18">mdi-storefront</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-medium text-high-emphasis">{{ item.vendor?.name || '-' }}</div>
              </div>
            </div>
          </template>

          <template #[`item.package`]="{ item }">
            <div>
              <div class="font-weight-medium text-high-emphasis">{{ item.package?.name || '-' }}</div>
              <div v-if="item.package?.short" class="caption text-medium-emphasis">{{ item.package.short }}</div>
            </div>
          </template>

          <template #[`item.amount`]="{ item }">
            <div class="text-right">{{ formatCurrency(item.amount) }}</div>
          </template>

          <!-- <template #[`item.paid_by`]="{ item }">
            <v-chip size="small" variant="tonal">{{ item.payment_method || '-' }}</v-chip>
          </template> -->

          <template #[`item.payment_status`]="{ item }">
            <v-chip :color="getStatusColor(item.payment_status)" size="small" variant="tonal" label class="px-3 text-uppercase">
              {{ item.payment_status || '-' }}
            </v-chip>
          </template>

          <template #[`item.date`]="{ item }">
            <div>{{ formatDate(item.date || item.created_at) }}</div>
          </template>
          <template #[`item.vendor_code`]="{ item }">
            <div>{{ item.vendor?.vendor_code || '-' }}</div>
          </template>
  
          <template #[`item.actions`]="{ item }">
            <div class="d-flex ga-2 align-center justify-end">
              <!-- <v-btn icon size="small" variant="text" color="primary" @click="downloadInvoice(item)">
              </v-btn> -->
              <v-btn variant="outlined" size="small" color="primary">
                <v-icon>mdi-download</v-icon>
                Download
              </v-btn>
            </div>
          </template>

          <template #no-data>
            <div class="pa-12 text-center">
              <v-icon size="64" color="disabled" class="mb-4">mdi-receipt</v-icon>
              <div class="text-h6 text-medium-emphasis">No payments found</div>
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
import { formatCurrency, formatDate } from '@/utils'

interface PaymentItem {
  id: number
  payment_id?: string
  vendor?: { id?: number; name?: string; vendor_code?: string }
  package?: { id?: number; name?: string; short?: string }
  amount?: string | number
  payment_method?: string
  payment_status?: string
  date?: string
  created_at?: string
  invoice_url?: string
}

const loading = ref(false)
const search = ref('')
const vendorFilter = ref<number | string | null>(null)
const packageFilter = ref<number | string | null>(null)
const statusFilter = ref<string | null>(null)
const dateStart = ref<string | null>(null)
const dateEnd = ref<string | null>(null)
const itemsPerPage = ref(15)
const serverItems = ref<PaymentItem[]>([
  { id: 1, payment_id: 'PAY-1001', vendor: { id: 1, name: 'Sparkle Cleaners', vendor_code: 'VND-001' }, package: { id: 2, name: 'Pro', short: 'Most popular' }, amount: '199.00', payment_method: 'Visa', payment_status: 'succeed', date: '2026-03-01', invoice_url: '/invoices/PAY-1001.pdf' },
  { id: 2, payment_id: 'PAY-1002', vendor: { id: 2, name: 'FlowFix Plumbing', vendor_code: 'VND-002' }, package: { id: 1, name: 'Starter', short: 'Basic access' }, amount: '75.50', payment_method: 'Mastercard', payment_status: 'failed', date: '2026-03-15', invoice_url: '/invoices/PAY-1002.pdf' },
  { id: 3, payment_id: 'PAY-1003', vendor: { id: 3, name: 'Bright Sparks', vendor_code: 'VND-003' }, package: { id: 3, name: 'Enterprise', short: 'Advanced features' }, amount: '320.00', payment_method: 'Visa', payment_status: 'succeed', date: '2026-04-01', invoice_url: '/invoices/PAY-1003.pdf' },
])
const totalItems = ref(serverItems.value.length)
const debounceTimeout = ref<number | null>(null)

const headers = [
  { title: 'Date', key: 'date', align: 'start' as const, sortable: true },
  // { title: 'Vendor Code', key: 'vendor_code', align: 'start' as const, sortable: true },
  { title: 'Vendor Name', key: 'vendor', align: 'start' as const, sortable: true },
  { title: 'Package', key: 'package', align: 'start' as const, sortable: true },
  { title: 'Amount', key: 'amount', align: 'end' as const, sortable: true },
  { title: 'Paid By', key: 'payment_method', align: 'start' as const, sortable: false },
  { title: 'Payment Status', key: 'payment_status', align: 'start' as const, sortable: true },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false },
]

const loadItems = async ({ page, itemsPerPage, sortBy }: any) => {
  loading.value = true
  try {
    const params: any = { page, per_page: itemsPerPage, search: search.value }
    if (sortBy?.length) { params.sort_by = sortBy[0].key; params.sort_order = sortBy[0].order }
    if (vendorFilter.value) params.vendor_id = vendorFilter.value
    if (packageFilter.value) params.package_id = packageFilter.value
    if (statusFilter.value) params.status = statusFilter.value
    if (dateStart.value) params.date_start = dateStart.value
    if (dateEnd.value) params.date_end = dateEnd.value

    // Placeholder - filter local sample data
    let items = serverItems.value.slice()
    if (vendorFilter.value) items = items.filter(i => String(i.vendor?.id) === String(vendorFilter.value))
    if (packageFilter.value) items = items.filter(i => String(i.package?.id) === String(packageFilter.value))
    if (statusFilter.value) items = items.filter(i => String(i.payment_status) === String(statusFilter.value))
    if (dateStart.value) items = items.filter(i => new Date(i.date || '').getTime() >= new Date(dateStart.value!).getTime())
    if (dateEnd.value) items = items.filter(i => new Date(i.date || '').getTime() <= new Date(dateEnd.value!).getTime())

    totalItems.value = items.length
    serverItems.value = items.slice(0, itemsPerPage || 15)
  } catch (e) {
    console.error('Failed to load payments', e)
  } finally {
    loading.value = false
  }
}

const fetchItems = () => loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] })
const onSearch = () => {
  if (debounceTimeout.value) clearTimeout(debounceTimeout.value)
  debounceTimeout.value = window.setTimeout(() => fetchItems(), 500)
}

const statusOptions = ['succeed', 'failed']
const vendorOptions = [ { title: 'All vendors', value: null }, { title: 'Sparkle Cleaners', value: 1 }, { title: 'FlowFix Plumbing', value: 2 }, { title: 'Bright Sparks', value: 3 } ]
const packageOptions = [ { title: 'All packages', value: null }, { title: 'Starter', value: 1 }, { title: 'Pro', value: 2 }, { title: 'Enterprise', value: 3 } ]

const getStatusColor = (status: string | undefined | null) => {
  if (!status) return 'grey-darken-1'
  const s = status.toLowerCase()
  if (s === 'succeed' || s === 'paid' || s === 'success') return 'success'
  if (s === 'pending') return 'warning'
  if (s === 'failed' || s === 'error') return 'error'
  return 'grey-darken-1'
}

const downloadInvoice = (item: PaymentItem) => {
  if (!item.invoice_url) { alert('No invoice available'); return }
  // simple download via opening the URL; replace with proper auth fetch if needed
  window.open(item.invoice_url, '_blank')
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
  display: flex;
  flex-wrap: wrap; /* allow wrap on small screens */
  gap: 12px;
  align-items: center;
  width: 100%;
  max-width: 100%;
}
.filters-right {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}
.filter-item {
  flex: 1 1 220px; /* base size, will shrink/grow */
}
.filter-item.search { width: 180px; }
.filter-item.vendor { width: 180px; }
.filter-item.package { width: 180px; }
.filter-item.status { width: 140px; }
.filter-item.date-start { width: 140px; }
.filter-item.date-end { width: 140px; }

@media (min-width: 960px) {
  /* single-row layout on desktop */
  .filters-left {
    flex-wrap: nowrap;
  }

  /* make each filter take equal available space */
  .filter-item {
    flex: 1 1 0;
    min-width: 0;
  }

  /* make date inputs shorter (fixed width) */
  .filter-item.date-start,
  .filter-item.date-end {
    flex: 0 0 160px;
    max-width: 160px;
  }
}
</style>
