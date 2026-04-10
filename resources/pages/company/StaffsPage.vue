<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <v-row class="mb-4" align="center" justify="space-between">
        <v-col cols="auto">
          <h2 class="pa-0">Staffs</h2>
          <v-card-subtitle class="pa-0">View and manage staff members.</v-card-subtitle>
        </v-col>
        <v-col cols="auto">
          <app-flat-button icon="mdi-account-plus" @click="createUser">Add User</app-flat-button>
        </v-col>
      </v-row>

      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <v-row align="center">
            <v-col cols="12" sm="6" md="4">
              <v-text-field v-model="search" density="compact" variant="outlined" prepend-inner-icon="mdi-magnify"
                label="Search members" clearable hide-details style="max-width: 360px" @update:model-value="onSearch" />
            </v-col>
            <v-spacer />
            <v-col cols="auto">
              <v-btn icon="mdi-refresh" variant="text" size="small" color="medium-emphasis" @click="fetchItems" />
            </v-col>
          </v-row>

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
            <div class="d-flex align-center ga-3 py-2">
              <v-avatar color="primary" variant="tonal" size="32">
                <v-icon size="18">mdi-account</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-medium text-high-emphasis">{{ item.name || '-' }}</div>
                <div class="caption text-medium-emphasis">{{ item.email || '-' }}</div>
              </div>
            </div>
          </template>

          <template #[`item.status`]="{ item }">
            <v-chip :color="item.status === 'active' ? 'success' : 'grey-darken-1'" size="small" variant="tonal" label
              class="px-3 text-uppercase">
              {{ item.status }}
            </v-chip>
          </template>

          <template #[`item.role`]="{ item }">
            <v-chip size="small" variant="tonal" label class="px-3 text-capitalize">
              {{ item.role || '-' }}
            </v-chip>
          </template>

          <template #[`item.joined_at`]="{ item }">
            <span>{{ item.joined_at || item.created_at || '-' }}</span>
          </template>

          <template #[`item.last_login_at`]="{ item }">
            <span>{{ item.last_login_at || '-' }}</span>
          </template>

          <template #[`item.actions`]="{ item }">
            <v-btn color="primary" variant="outlined" size="small" @click="$router.push({ name: 'company.staff-detail', params: { id: item.id } })">details</v-btn>
          </template>

          <template #no-data>
            <div class="pa-12 text-center">
              <v-icon size="64" color="disabled" class="mb-4">mdi-account-tie-outline</v-icon>
              <div class="text-h6 text-medium-emphasis">No staff members found</div>
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
import { getCompanyMembers } from '../../services/members.api'

interface MemberItem {
  id: number
  name: string | null
  email: string
  role: string
  status: string
  last_login_at: string | null
  joined_at?: string | null
  created_at?: string | null
}

const loading = ref(false)
const router = useRouter()
const search = ref('')
const itemsPerPage = ref(15)
const serverItems = ref<MemberItem[]>([])
const totalItems = ref(0)
const debounceTimeout = ref<number | null>(null)

const headers = [
  { title: 'Name', key: 'name', align: 'start' as const, sortable: true },
  { title: 'Role', key: 'role', align: 'start' as const, sortable: true },
  { title: 'Status', key: 'status', align: 'center' as const, sortable: true },
  { title: 'Joined Date', key: 'joined_at', align: 'start' as const, sortable: true },
  { title: 'Last Login', key: 'last_login_at', align: 'start' as const, sortable: true },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false, width: '140px' },
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

    const response: any = await getCompanyMembers(params)
    serverItems.value = response.data
    totalItems.value = response.meta.total
  } catch (error) {
    console.error('Failed to fetch members:', error)
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

const createUser = () => {
  router.push({ name: 'company.staff-create' })
}
</script>
