<template>
    <v-container class="py-6 px-14" fluid>
        <div class="pa-0">
            <v-row class="mb-4" align="center" justify="space-between">
              <v-col cols="auto">
                <h2 class="pa-0">Payment Credentials</h2>
                <v-card-subtitle class="pa-0">Manage payment credentials.</v-card-subtitle>
              </v-col>
              <v-col cols="auto">
                <app-flat-button icon="mdi-plus" @click="addCredential">Add Credential</app-flat-button>
              </v-col>
            </v-row>
      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <v-row align="center">
            <v-col cols="12" sm="6" md="4">
              <v-text-field
                v-model="search"
                density="compact"
                variant="outlined"
                prepend-inner-icon="mdi-magnify"
                label="Search credentials"
                clearable
                hide-details
                style="max-width: 360px"
                @update:model-value="onSearch"
              />
            </v-col>
            <v-spacer />
            <v-col cols="auto">
              <v-btn
                icon="mdi-refresh"
                variant="text"
                color="medium-emphasis"
                @click="fetchItems"
              />
            </v-col>
          </v-row>
        </v-card-text>

        <v-data-table-server
          v-model:items-per-page="itemsPerPage"
          v-model:expanded="expanded"
          :headers="headers"
          :items="serverItems"
          :items-length="totalItems"
          :loading="loading"
          :search="search"
          @update:options="loadItems"
          :hide-default-footer="totalItems <= itemsPerPage"
          show-expand
        >
          <template #[`item.name`]="{ item }">
            <div class="font-weight-medium text-high-emphasis">
              {{ item.name || '-' }}
            </div>
          </template>

          <template #[`item.is_default`]="{ item }">
            <v-chip
              :color="item.is_default ? 'primary' : 'grey-darken-1'"
              size="small"
              variant="tonal"
              label
              class="px-3 text-uppercase"
            >
              {{ item.is_default ? 'Yes' : 'No' }}
            </v-chip>
          </template>

          <template #[`item.is_active`]="{ item }">
            <v-chip
              :color="item.is_active ? 'success' : 'grey-darken-1'"
              size="small"
              variant="tonal"
              label
              class="px-3 text-uppercase"
            >
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </v-chip>
          </template>

          <template #[`item.mode`]="{ item }">
            <v-chip
              :color="item.mode === 'live' ? 'success' : (item.mode === 'test' ? 'warning' : 'grey-darken-1')"
              size="small"
              variant="tonal"
              label
              class="px-3 text-uppercase"
            >
              {{ item.mode || '-' }}
            </v-chip>
          </template>

          <template #[`item.actions`]="{ item }">
            <div class="d-flex ga-2 justify-end">
              <v-btn size="small" variant="outlined" color="primary" @click="editCredential(item)">
                edit
              </v-btn>
              <v-btn size="small" variant="outlined" color="error" @click="confirmDelete(item)">
                delete
              </v-btn>
            </div>
          </template>

          <template #expanded-row="{ columns, item }">
            <tr>
              <td :colspan="columns.length" class="py-4">
                <v-container>
                  <v-col cols="12" md="10" lg="8" offset-md="1" offset-lg="2">
                    <div>
                      <div class="text-subtitle-2 text-high-emphasis mb-2">Credentials</div>
                      <v-table
                        v-if="credentialEntries(item.credentials).length"
                        density="compact"
                        class="credentials-table border rounded"
                      >
                        <thead>
                          <tr>
                            <th class="text-left">Key</th>
                            <th class="text-left">Value</th>
                            <th class="text-right">Copy</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="entry in credentialEntries(item.credentials)" :key="entry.key">
                            <td class="text-caption text-medium-emphasis">{{ entry.key }}</td>
                            <td class="text-caption text-high-emphasis">{{ entry.value }}</td>
                            <td class="text-right">
                              <v-btn
                                icon
                                size="small"
                                variant="text"
                                @click="copyToClipboard(entry.value)"
                              >
                                <v-icon size="18">mdi-content-copy</v-icon>
                              </v-btn>
                            </td>
                          </tr>
                        </tbody>
                      </v-table>
                      <div v-else class="text-caption text-medium-emphasis">No credentials.</div>
                    
                    </div>
                  </v-col>
                </v-container>
              </td>
            </tr>
          </template>

          <template #no-data>
            <div class="pa-12 text-center">
              <v-icon size="64" color="disabled" class="mb-4">mdi-credit-card-outline</v-icon>
              <div class="text-h6 text-medium-emphasis">No payment credentials found</div>
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
import PaymentCredentialDeleteModal from '@/components/modals/PaymentCredentialDeleteModal.vue'
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { deleteCompanyPaymentCredential, getCompanyPaymentCredentials } from '@/services/paymentCredentials.api'
import { useModalStore } from '@/stores/modal'

interface PaymentCredentialItem {
  id: number
  name: string
  code: string
  mode?: string
  credentials: Record<string, any> | null
  is_default: boolean
  is_active: boolean
}

const loading = ref(false)
const router = useRouter()
const modal = useModalStore()
const search = ref('')
const itemsPerPage = ref(15)
const serverItems = ref<PaymentCredentialItem[]>([])
const totalItems = ref(0)
const debounceTimeout = ref<number | null>(null)
const deleting = ref(false)
const pendingDeleteCredential = ref<PaymentCredentialItem | null>(null)
// v-data-table-server expects expanded to be an array of item keys (strings), not full item objects.
const expanded = ref<string[]>([])
const deleteModalProps = reactive({
  credentialName: 'this credential',
  loading: false,
  onConfirm: () => performDelete(),
  onCancel: () => cancelDelete(),
})

const headers = [
  { title: 'Name', key: 'name', align: 'start' as const, sortable: true },
  { title: 'Code', key: 'code', align: 'start' as const, sortable: true },
  { title: 'Mode', key: 'mode', align: 'center' as const, sortable: true },
  { title: 'Default', key: 'is_default', align: 'center' as const, sortable: true },
  { title: 'Status', key: 'is_active', align: 'center' as const, sortable: true },
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

    const response: any = await getCompanyPaymentCredentials(params)
    serverItems.value = response.data
    totalItems.value = response.meta.total
  } catch (error) {
    console.error('Failed to fetch payment credentials:', error)
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

const maskValue = (value: unknown): string => {
  if (value === null || value === undefined) return ''
  const stringValue = typeof value === 'string' ? value : JSON.stringify(value)
  if (stringValue.length <= 4) return '****'
  return `****${stringValue.slice(-4)}`
}

const maskCredentials = (credentials: Record<string, any> | null): string => {
  if (!credentials || typeof credentials !== 'object') return '-'
  const entries = Object.entries(credentials).map(([key, value]) => `${key}: ${maskValue(value)}`)
  return entries.length ? entries.join(', ') : '-'
}

const credentialEntries = (credentials: Record<string, any> | null): Array<{ key: string; value: string }> => {
  if (!credentials || typeof credentials !== 'object') return []
  return Object.entries(credentials).map(([key, value]) => ({
    key,
    value: maskValue(value),
  }))
}

const copyToClipboard = async (value: string) => {
  try {
    await navigator.clipboard.writeText(value)
  } catch (error) {
    console.error('Failed to copy credential:', error)
  }
}

const addCredential = () => {
  router.push({ name: 'company.config.payment-create' })
}

const editCredential = (item: PaymentCredentialItem) => {
  router.push({ name: 'company.config.payment-edit', params: { id: item.id } })
}

const confirmDelete = (item: PaymentCredentialItem) => {
  pendingDeleteCredential.value = item
  deleteModalProps.credentialName = item.name || item.code || 'this credential'
  deleteModalProps.loading = false
  modal.open(PaymentCredentialDeleteModal, deleteModalProps, {
    showHeader: false,
    fullscreen: false,
    maxWidth: 420,
    persistent: true,
  })
}

const cancelDelete = () => {
  modal.close()
  pendingDeleteCredential.value = null
  deleteModalProps.loading = false
}

const performDelete = async () => {
  if (!pendingDeleteCredential.value || deleting.value) return

  deleting.value = true
  deleteModalProps.loading = true
  try {
    await deleteCompanyPaymentCredential(pendingDeleteCredential.value.id)

    serverItems.value = serverItems.value.filter(i => i.id !== pendingDeleteCredential.value?.id)
    totalItems.value = Math.max(0, totalItems.value - 1)
    modal.close()
    pendingDeleteCredential.value = null
  } catch (error) {
    console.error('Failed to delete payment credential:', error)
  } finally {
    deleting.value = false
    deleteModalProps.loading = false
  }
}
</script>

<style scoped>
.credentials-table {
  width: 100%;
}

.credentials-table th {
  /* font-size: 0.75rem; */
  color: rgba(0, 0, 0, 0.6);
  font-weight: 600;
}

.credentials-table td {
  vertical-align: middle;
}
</style>
