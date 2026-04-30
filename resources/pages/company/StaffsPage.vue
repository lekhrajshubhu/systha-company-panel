<template>
  <v-container fluid>
    <AppPageHeader title="User Management" subtitle="View and manage company users." show-back>
      <template #action>
        <app-flat-button icon="mdi-store-plus" @click="createUser">Add User</app-flat-button>
      </template>
    </AppPageHeader>
    <v-container fluid>
      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <div class="d-flex align-center justify-space-between mb-4">
            <div class="d-flex align-center ga-3">
              <div>
                <AppSearchTextField v-model="search" label="Search" />
              </div>
              <div>
                <v-select v-model="roleFilter" :items="roleItems" item-title="label" item-value="value"
                width="360px"
                  density="compact" variant="outlined" label="Filter role" clearable hide-details
                  @update:model-value="onRoleFilter" />

              </div>
              <div>
                  <app-flat-button :loading="fetchingState" icon="mdi-magnify" @click="onSearch">
                  Search
                </app-flat-button>
              </div>
            </div>
            <div>
              <!-- <div class="caption text-medium-emphasis mb-0">Total {{ totalItems }} found</div> -->
              <v-btn icon="mdi-refresh" variant="tonal" size="small" color="warning" @click="fetchItems" />
            </div>
          </div>
        </v-card-text>

        <v-data-table-server v-model:items-per-page="itemsPerPage" :headers="headers" :items="serverItems"
          :items-length="totalItems" :loading="loading" :search="search" @update:options="loadItems"
          :hide-default-footer="totalItems <= itemsPerPage">
          <template #[`item.name`]="{ item }">
            <div class="d-flex align-center ga-3 py-2">
              <v-avatar color="primary" variant="tonal" rounded size="32">
                <v-icon size="18">mdi-account</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-medium text-high-emphasis">{{ item.name || '-' }}</div>
                <div class="caption">{{ item.email || '-' }}</div>
              </div>
            </div>
          </template>

          <template #[`item.status`]="{ item }">
            <v-chip :color="getStatusColor(item.status)" size="small" variant="tonal" label class="px-3 text-uppercase">
              {{ item.status }}
            </v-chip>
          </template>

          <template #[`item.roles`]="{ item }">
            <div class="d-flex align-center">
              <v-chip
                v-for="(role, index) in (item.roles ?? [])"
                :key="index"
                size="small"
                variant="outlined"
                color="info"
                label
                class="px-3 text-uppercase mr-2"
              >
                {{role}}
              </v-chip>
            </div>
          </template>

          <template #[`item.joined_at`]="{ item }">
            <span>{{ item.joined_at ? formatDate(item.joined_at) : '-' }}</span>
          </template>

          <!-- <template #[`item.last_login_at`]="{ item }">
            <span>{{ item.last_login_at ? formatDate(item.last_login_at) : '-' }}</span>
          </template> -->

          <template #[`item.actions`]="{ item }">
            <div class="d-flex">
              <!-- <v-btn color="primary" variant="outlined" size="small" @click="viewDetails(item)">Details</v-btn> -->

              <v-btn class="ml-2" color="primary" variant="outlined" size="small" 
              :to="{ name: 'company.user.detail', params: { id: item.id } }"
                aria-label="Edit user">
                Details
              </v-btn>

              <v-btn class="ml-2" color="error" variant="outlined" size="small" @click="confirmDelete(item)"
                aria-label="Delete user">
                Delete
              </v-btn>
            </div>
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
    </v-container>
  </v-container>

</template>

<script setup lang="ts">
import AppFlatButton from '@/components/AppFlatButton.vue'
import AppPageHeader from '@/components/AppPageHeader.vue'
import AppSearchTextField from '@/components/AppSearchTextField.vue'
import UserDeleteModal from '@/components/modals/UserDeleteModal.vue'
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { deleteCompanyUser, getCompanyUsers } from '@/services/users.api'
import { useModalStore } from '@/stores/modal'
import { formatDate, getStatusColor, getRoleColor } from '@/utils'


interface MemberItem {
  id: number
  name: string | null
  email: string
  roles?: Array<string | { name?: string; label?: string }>
  status: string
  last_login_at: string | null
  joined_at?: string | null
  created_at?: string | null
}


const loading = ref(false)
const fetchingState = ref(false)
const router = useRouter()
const modal = useModalStore()
const search = ref('')
const roleFilter = ref(null)
const itemsPerPage = ref(15)
const serverItems = ref<MemberItem[]>([])
const totalItems = ref(0)
const debounceTimeout = ref<number | null>(null)
const deleting = ref(false)
const pendingDeleteUser = ref<MemberItem | null>(null)
const deleteModalProps = reactive({
  userName: 'this user',
  loading: false,
  onConfirm: () => performDelete(),
  onCancel: () => cancelDelete(),
})

const headers = [
  { title: 'Name', key: 'name', align: 'start' as const, sortable: true },
  { title: 'Role', key: 'roles', align: 'start' as const, sortable: true },
  { title: 'Status', key: 'status', align: 'center' as const, sortable: true },
  { title: 'Joined Date', key: 'joined_at', align: 'start' as const, sortable: true },
  // { title: 'Last Login', key: 'last_login_at', align: 'start' as const, sortable: true },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false, width: '140px' },
]

// predefined role filter items
const roleItems = ref([
  { label: 'All Roles', value: null },
  { label: 'Admin', value: 'admin' },
  { label: 'Editor', value: 'editor' },
  { label: 'Viewer', value: 'viewer' },
])

const loadItems = async ({ page, itemsPerPage, sortBy }: any) => {
  loading.value = true
  try {
    const params: any = {
      page,
      per_page: itemsPerPage,
      search: search.value,
      role: roleFilter.value, // include role filter
    }

    if (sortBy.length) {
      params.sort_by = sortBy[0].key
      params.sort_order = sortBy[0].order
    }

    const response: any = await getCompanyUsers(params)

    serverItems.value = (response.data ?? []).map((row: any) => {
      const firstName = row.fname ?? row.first_name ?? ''
      const lastName = row.lname ?? row.last_name ?? ''
      const fullName = `${firstName} ${lastName}`.trim()

      return {
        id: row.company_user_id ?? row.id,
        name: row.name ?? (fullName !== '' ? fullName : null),
        email: row.email ?? '',
        roles: row.roles ?? [],
        status: row.status ?? '-',
        last_login_at: row.last_login_at ?? null,
        joined_at: row.joined_at ?? row.created_at ?? null,
        created_at: row.created_at ?? null,
      } satisfies MemberItem
    })
    totalItems.value = response.meta.total
  } catch (error) {
    console.error('Failed to fetch members:', error)
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

const onRoleFilter = () => {
  fetchItems()
}

const createUser = () => {
  router.push({ name: 'company.staff-create' })
}


const editUser = (item: MemberItem) => {
  router.push({
    name: 'company.staff-edit',
    params: { id: item.id }
  })
}

const confirmDelete = async (item: MemberItem) => {
  pendingDeleteUser.value = item
  deleteModalProps.userName = item.name || item.email || 'this user'
  deleteModalProps.loading = false
  modal.open(UserDeleteModal, deleteModalProps, {
    showHeader: false,
    fullscreen: false,
    maxWidth: 420,
    persistent: true,
  })
}

const cancelDelete = () => {
  modal.close()
  pendingDeleteUser.value = null
  deleteModalProps.loading = false
}

const performDelete = async () => {
  if (!pendingDeleteUser.value || deleting.value) return

  deleting.value = true
  deleteModalProps.loading = true
  try {
    await deleteCompanyUser(pendingDeleteUser.value.id)

    serverItems.value = serverItems.value.filter(i => i.id !== pendingDeleteUser.value?.id)
    totalItems.value = Math.max(0, totalItems.value - 1)
    modal.close()
    pendingDeleteUser.value = null
  } catch (err) {
    console.error('Failed to delete user', err)
  } finally {
    deleting.value = false
    deleteModalProps.loading = false
  }
}
</script>

<style scoped>
.role-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 8px;
  flex-shrink: 0;
}

/* semantic mappings to Vuetify/theme variables (fallback colors included) */
.role-primary {
  background-color: var(--v-theme-primary, #1E88E5);
}

.role-secondary {
  background-color: var(--v-theme-secondary, #8E24AA);
}

.role-info {
  background-color: var(--v-theme-info, #2196F3);
}

.role-success {
  background-color: var(--v-theme-success, #4CAF50);
}

.role-warning {
  background-color: var(--v-theme-warning, #FFC107);
}

.role-error {
  background-color: var(--v-theme-error, #F44336);
}

.role-default {
  background-color: #90A4AE;
}

/* Filters row (copy of VendorsPage style) */
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

.filter-item.search {
  grid-column: span 12;
}

.filter-item.role {
  grid-column: span 12;
}

@media (min-width: 960px) {

  /* Make search and role equal width on desktop (6 columns each) */
  .filter-item.search {
    grid-column: 1 / span 6;
  }

  .filter-item.role {
    grid-column: 7 / span 6;
  }

  .filters-right {
    width: auto;
  }
}
</style>
