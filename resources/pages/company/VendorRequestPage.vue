<template>
  <v-container fluid>
    <app-page-header title="New Vendor Request" subtitle="Manage vendor requests." />
    <v-container fluid>
      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <div class="d-flex align-center justify-space-between mb-4">
            <div class="d-flex align-center ga-3">
              <div>
                <AppSearchTextField v-model="search" label="Search" width="300" />
              </div>

              <div>
                <v-select v-model="statusFilter" :items="statusOptions" density="compact" variant="outlined"
                  width="300px" label="Status" clearable hide-details />
              </div>

              <div>
                <v-select v-model="typeFilter" :items="typeOptions" density="compact" variant="outlined" width="300px"
                  label="Business Type" clearable hide-details />
              </div>
              <div>
                <app-flat-button color="primary" @click="onSearch" 
                :loading="fetchingState"
                icon="mdi-magnify">
                  Search
                </app-flat-button>
              </div>
            </div>

            <div>
              <v-btn icon="mdi-refresh" variant="tonal" size="small" color="warning" @click="fetchItems" />
            </div>
          </div>
        </v-card-text>

        <v-data-table-server v-model:items-per-page="itemsPerPage" :headers="headers" :items="serverItems"
          :items-length="totalItems" :loading="loading" :search="search" @update:options="loadItems"
          :hide-default-footer="totalItems <= itemsPerPage">
       

          <template #[`item.type`]="{ item }">
            <div class="text-capitalize">{{ item.type || '-' }}</div>
          </template>

          <template #[`item.name`]="{ item }">
            <div class="d-flex align-center ga-3 py-2">
              <v-avatar color="primary" variant="tonal" size="32">
                <v-icon size="18">mdi-storefront</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-medium text-high-emphasis text-capitalize">{{ item.name || '-' }}</div>
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

          <template #[`item.actions`]="{ item }">
            <div class="d-flex justify-end">
              <v-btn color="primary" variant="outlined" size="small" @click="viewDetails(item)">
                Details
              </v-btn>
            </div>
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
    </v-container>
  </v-container>
</template>

<script setup lang="ts">
import AppFlatButton from '@/components/AppFlatButton.vue'
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { fetchVendorApplications } from '@/services/vendor-applications.api'
import AppPageHeader from '@/components/AppPageHeader.vue'
import AppSearchTextField from '@/components/AppSearchTextField.vue'

interface VendorItem {
  id: number
  type?: string
  name?: string | null
  phone?: string | null
  email?: string | null
  city?: string | null
  status?: string
}

const loading = ref(false)
const router = useRouter()
const route = useRoute()
const search = ref('')
const fetchingState = ref(false)
const statusFilter = ref<string | null>(null)
const typeFilter = ref<string | null>(null)
const itemsPerPage = ref(15)
const serverItems = ref<VendorItem[]>([
  { id: 1, type: 'cleaning', name: 'Sparkle Cleaners', phone: '+1 555 0100', email: 'hello@sparkle.example', city: 'Austin', status: 'approved' },
  { id: 2, type: 'plumbing', name: 'FlowFix Plumbing', phone: '+1 555 0200', email: 'contact@flowfix.example', city: 'Dallas', status: 'pending review' },
  { id: 3, type: 'electrical', name: 'Bright Sparks', phone: '+1 555 0300', email: 'info@brightsparks.example', city: 'Houston', status: 'approved' },
])
const totalItems = ref(serverItems.value.length)
const debounceTimeout = ref<number | null>(null)

const headers = [
  // { title: 'Vendor Code', key: 'vendor_code', align: 'start' as const, sortable: true },
  { title: 'Name', key: 'name', align: 'start' as const, sortable: true },
  { title: 'Type', key: 'type', align: 'start' as const, sortable: true },
  { title: 'Phone', key: 'phone', align: 'start' as const, sortable: false },
  { title: 'Email', key: 'email', align: 'start' as const, sortable: false },
  { title: 'City', key: 'city', align: 'start' as const, sortable: false },
  { title: 'Status', key: 'status', align: 'start' as const, sortable: true },
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

    const response: any = await fetchVendorApplications(params)
    serverItems.value = response.data
    totalItems.value = response.meta.total
  } catch (error) {
    console.error('Failed to fetch vendors:', error)
  } finally {
    fetchingState.value = false
    loading.value = false
  }
}

const fetchItems = () => {
  loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] })
}

const onSearch = () => {
  if (debounceTimeout.value) clearTimeout(debounceTimeout.value)

  fetchingState.value = true
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

const viewDetails = (item: VendorItem) => {

  router.push({ name: 'company.vendor-requests.detail', params: { id: item.id } })
}
</script>

<style scoped></style>
