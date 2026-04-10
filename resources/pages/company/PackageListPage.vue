<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <div class="mb-4">
        <h2 class="pa-0">Membership Package</h2>
        <v-card-subtitle class="pa-0">View and manage company membership packages.</v-card-subtitle>
      </div>
      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <div class="filters-row">
            <div class="filters-left">
              <div class="filter-item search">
                <v-text-field v-model="search" density="compact" variant="outlined" prepend-inner-icon="mdi-magnify"
                  label="Search packages" clearable hide-details @update:model-value="onSearch" />
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

          <template #[`item.name`]="{ item }">
            <div>
              <div class="font-weight-medium">{{ item.name || '-' }}</div>
              <div class="caption text-medium-emphasis">{{ item.short || '' }}</div>
            </div>
          </template>

          <template #[`item.monthly_price`]="{ item }">
            <div class="text-right">{{ formatCurrency(item.monthly_price) }}/ <span class="caption text-medium-emphasis">mo</span> </div>
          </template>

          <template #[`item.yearly_price`]="{ item }">
            <div class="text-right">{{ formatCurrency(item.yearly_price) }}/ <span class="caption text-medium-emphasis">yr</span></div>
          </template>

          <template #[`item.features`]="{ item }">
            <div>
              <div class="caption text-medium-emphasis">{{ (item.features && item.features.length) ? item.features.join(', ') : '-' }}</div>
            </div>
          </template>

          <template #[`item.stripe_product`]="{ item }">
            <div class="d-flex align-center ga-2">
              <div class="caption text-medium-emphasis" :title="item.stripe_product || '-'">{{ maskStripeProduct(item.stripe_product) }}</div>
              <v-btn icon size="small" variant="text" color="primary" @click="copyToClipboard(item.stripe_product, item.id)">
                <v-icon v-if="copiedId !== item.id">mdi-content-copy</v-icon>
                <v-icon v-else color="success">mdi-check</v-icon>
              </v-btn>
            </div>
          </template>

          <template #[`item.status`]="{ item }">
            <v-chip :color="item.status === 'active' ? 'success' : 'grey-darken-1'" size="small" variant="tonal" label
              class="px-3 text-uppercase">
              {{ item.status || '-' }}
            </v-chip>
          </template>

          <template #[`item.actions`]="{ item }">
            <div class="d-flex align-center justify-end ga-2">

              <v-btn variant="outlined" size="small" color="info">
                Edit
              </v-btn>

              <v-btn variant="outlined" size="small" color="error">
                Delete
              </v-btn>
            </div>
          </template>

          <template #no-data>
            <div class="pa-12 text-center">
              <v-icon size="64" color="disabled" class="mb-4">mdi-package-variant-closed</v-icon>
              <div class="text-h6 text-medium-emphasis">No packages found</div>
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
import { getCompanyPackages } from '../../services/packages.api'

interface PackageItem {
  id: number
  name?: string
  short?: string
  monthly_price?: string | number
  yearly_price?: string | number
  features?: string[]
  stripe_product?: string | null
  price_count?: number
  status?: string
}

const loading = ref(false)
const search = ref('')
const statusFilter = ref<string | null>(null)
const itemsPerPage = ref(15)
const serverItems = ref<PackageItem[]>([
  { id: 1, name: 'Starter', short: 'Basic access', monthly_price: '0.00', yearly_price: '0.00', features: ['Listings'], stripe_product: 'prod_Starter_001', price_count: 1, status: 'active' },
  { id: 2, name: 'Pro', short: 'Most popular', monthly_price: '49.99', yearly_price: '499.00', features: ['Priority support', 'Analytics'], stripe_product: 'prod_Pro_002', price_count: 3, status: 'active' },
  { id: 3, name: 'Enterprise', short: 'Advanced features', monthly_price: '199.00', yearly_price: '1999.00', features: ['SLA', 'Dedicated account'], stripe_product: 'prod_Ent_003', price_count: 5, status: 'draft' },
])
const totalItems = ref(serverItems.value.length)
const debounceTimeout = ref<number | null>(null)
const copiedId = ref<number | null>(null)

const headers = [
  { title: 'Name', key: 'name', align: 'start' as const, sortable: true, minWidth:200 },
  { title: 'Features', key: 'features', align: 'start' as const, sortable: false, minWidth: 200 },
  { title: 'Monthly', key: 'monthly_price', align: 'end' as const, sortable: true, maxWidth: 150 },
  { title: 'Yearly', key: 'yearly_price', align: 'end' as const, sortable: true, maxWidth: 150 },
  { title: 'Stripe Product', key: 'stripe_product', align: 'start' as const, sortable: false, width: 200 },
  { title: 'Status', key: 'status', align: 'center' as const, sortable: true, width: 100 },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false, width: 200 },
]

const formatCurrency = (value: string | number | undefined) => {
  if (value == null || value === '') return '-'
  const num = typeof value === 'number' ? value : parseFloat(String(value))
  if (Number.isNaN(num)) return String(value)
  return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD', minimumFractionDigits: 2 }).format(num)
}

const copyToClipboard = async (text: string | null | undefined, id?: number) => {
  if (text == null) return
  try {
    await navigator.clipboard.writeText(String(text))
    if (id) {
      copiedId.value = id
      setTimeout(() => (copiedId.value = null), 2000)
    }
  } catch (e) {
    console.error('Copy failed', e)
  }
}

// mask a stripe product id for display (show start/end with *** in middle)
const maskStripeProduct = (val: string | null | undefined) => {
  if (!val) return '-'
  const s = String(val)
  if (s.length <= 6) return s.slice(0, 2) + '***'
  const start = s.slice(0, 4)
  const end = s.slice(-4)
  return `${start}***${end}`
}

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

    const response: any = await getCompanyPackages(params)
    serverItems.value = response.data
    totalItems.value = response.meta.total
  } catch (error) {
    console.error('Failed to fetch packages:', error)
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

// local delete handler (placeholder - replace with API call)
const deletePackage = (id: number) => {
  if (!confirm('Are you sure you want to delete this package?')) return
  serverItems.value = serverItems.value.filter(p => p.id !== id)
  totalItems.value = serverItems.value.length
}

// Package status options
const statusOptions = [
  'active',
  'inactive',
  'draft',
  'archived',
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
.filter-item.status { grid-column: span 12; }
.filter-item.type { grid-column: span 12; }

@media (min-width: 960px) {
  /* Make all three filter controls equal width (4 columns each) and sequential */
  .filter-item.search { grid-column: 1 / span 4; }
  .filter-item.status { grid-column: 5 / span 4; }
  .filter-item.type { grid-column: 9 / span 4; }
  .filters-right { width: auto; }
}
</style>
