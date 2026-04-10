<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <v-row class="mb-4" align="center" justify="space-between">
        <v-col cols="auto">
          <h2 class="pa-0">Vendor Management</h2>
          <v-card-subtitle class="pa-0">View and manage company vendors.</v-card-subtitle>
        </v-col>
        <v-col cols="auto">
          <app-flat-button icon="mdi-store-plus" @click="createVendor">Add Vendor</app-flat-button>
        </v-col>
      </v-row>

      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <div class="filters-row">
            <div class="filters-left">
              <div class="filter-item search">
                <v-text-field v-model="search" density="compact" variant="outlined" prepend-inner-icon="mdi-magnify"
                  label="Search vendors" clearable hide-details @update:model-value="onSearch" />
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

              <div class="filter-item type">
                <v-select
                  v-model="typeFilter"
                  :items="typeOptions"
                  density="compact"
                  variant="outlined"
                  label="Business Type"
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
          <template #[`item.vendor_code`]="{ item }">
            <div class="font-weight-medium">{{ item.vendor_code || '-' }}</div>
          </template>

          <template #[`item.type`]="{ item }">
            <div class="text-capitalize">{{ item.type || '-' }}</div>
          </template>

          <template #[`item.name`]="{ item }">
            <div class="d-flex align-center ga-3 py-2">
              <v-avatar color="primary" variant="tonal" size="32">
                <v-icon size="18">mdi-storefront</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-medium text-high-emphasis">{{ item.name || '-' }}</div>
                <div class="caption text-medium-emphasis">Code: {{ item.vendor_code || '-' }}</div>
              </div>
            </div>
          </template>

          <template #[`item.phone`]="{ item }">
            <div>{{ item.phone || '-' }}</div>
          </template>

          <template #[`item.email`]="{ item }">
            <div>{{ item.email || '-' }}</div>
          </template>

          <template #[`item.city`]="{ item }">
            <div>{{ item.city || '-' }}</div>
          </template>

          <template #[`item.status`]="{ item }">
            <v-chip :color="item.status === 'active' ? 'success' : 'grey-darken-1'" size="small" variant="tonal" label
              class="px-3 text-uppercase">
              {{ item.status }}
            </v-chip>
          </template>

          <template #no-data>
            <div class="pa-12 text-center">
              <v-icon size="64" color="disabled" class="mb-4">mdi-storefront-outline</v-icon>
              <div class="text-h6 text-medium-emphasis">No vendors found</div>
              <p class="text-body-2 text-disabled">Try adjusting your search.</p>
            </div>
          </template>
        </v-data-table-server>
      </v-card>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import AppFlatButton from '@/components/AppFlatButton.vue'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { getCompanyVendors } from '@/services/vendors.api'

interface VendorItem {
  id: number
  vendor_code?: string
  type?: string
  name?: string | null
  phone?: string | null
  email?: string | null
  city?: string | null
  status?: string
}

const loading = ref(false)
const router = useRouter()
const search = ref('')
const statusFilter = ref<string | null>(null)
const typeFilter = ref<string | null>(null)
const itemsPerPage = ref(15)
const serverItems = ref<VendorItem[]>([
  { id: 1, vendor_code: 'VND-001', type: 'cleaning', name: 'Sparkle Cleaners', phone: '+1 555 0100', email: 'hello@sparkle.example', city: 'Austin', status: 'approved' },
  { id: 2, vendor_code: 'VND-002', type: 'plumbing', name: 'FlowFix Plumbing', phone: '+1 555 0200', email: 'contact@flowfix.example', city: 'Dallas', status: 'pending review' },
  { id: 3, vendor_code: 'VND-003', type: 'electrical', name: 'Bright Sparks', phone: '+1 555 0300', email: 'info@brightsparks.example', city: 'Houston', status: 'approved' },
])
const totalItems = ref(serverItems.value.length)
const debounceTimeout = ref<number | null>(null)

const headers = [
  { title: 'Vendor Code', key: 'vendor_code', align: 'start' as const, sortable: true },
  { title: 'Type', key: 'type', align: 'start' as const, sortable: true },
  { title: 'Name', key: 'name', align: 'start' as const, sortable: true },
  { title: 'Phone', key: 'phone', align: 'start' as const, sortable: false },
  { title: 'Email', key: 'email', align: 'start' as const, sortable: false },
  { title: 'City', key: 'city', align: 'start' as const, sortable: false },
  { title: 'Status', key: 'status', align: 'center' as const, sortable: true },
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

    if (typeFilter.value) {
      params.business_type = typeFilter.value
    }

    const response: any = await getCompanyVendors(params)
    serverItems.value = response.data
    totalItems.value = response.meta.total
  } catch (error) {
    console.error('Failed to fetch vendors:', error)
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
const statusOptions = [
  'new',
  'pending review',
  'approved',
  'rejected',
  'deactivated',
]

const typeOptions = [
  'pest control',
  'cleaning',
  'tax',
  'plumbing',
  'electrical',
]

const createVendor = () => {
  router.push({ name: 'company.vendor-create' })
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
