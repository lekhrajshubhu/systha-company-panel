<template>
  <v-container class="py-6 px-14" fluid>
    <h2 class="pa-0">Documents Templates</h2>
    <v-card-subtitle class="pa-0">Manage your quotation templates for customer communication.</v-card-subtitle>

    <div padding="pa-4" class="mb-6">
      <v-row>
        <v-col cols="12" md="4">
            <AppCard padding="pa-6" class="elevation-0">
              <v-row align="center">
                <v-col cols="12">
                  <v-text-field v-model="search" density="compact" variant="outlined" prepend-inner-icon="mdi-magnify"
                    label="Search templates" clearable hide-details @update:model-value="onSearch" />
                </v-col>
              </v-row>
    
              <!-- <v-divider class="my-2" /> -->
    
              <v-row>
                <v-col cols="auto">
                  <v-btn icon="mdi-refresh" variant="text" color="medium-emphasis" @click="fetchItems" />
                </v-col>
                <v-spacer />
                <v-col cols="auto">
                  <v-btn color="primary" variant="tonal" @click="createTemplate">New Template</v-btn>
                </v-col>
              </v-row>
              <v-list dense nav>
                <v-list-item v-for="item in serverItems" :key="item.id"
                  class="border-b pb-2"
                  :class="{ 'bg-surface-2': selectedItem && selectedItem.id === item.id }" @click="selectItem(item)">
                  <v-list-item-content>
                    <v-list-item-title class="text-high-emphasis">{{ item.name }}</v-list-item-title>
                    <v-list-item-subtitle class="text-medium-emphasis">{{ item.subject || '-' }}</v-list-item-subtitle>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-chip size="small" variant="tonal" :color="item.active ? 'success' : 'grey-darken-1'">
                      {{ item.active ? 'Active' : 'Inactive' }}
                    </v-chip>
                  </v-list-item-action>
                </v-list-item>
    
                <template v-if="!serverItems.length">
                  <v-list-item>
                    <v-list-item-content class="text-center">
                      <div class="text-medium-emphasis pa-6">No templates found</div>
                    </v-list-item-content>
                  </v-list-item>
                </template>
              </v-list>
            </AppCard>


        </v-col>

        <v-col cols="12" md="8">
          <AppCard padding="pa-6" class="elevation-0">
            <div v-if="selectedItem">
              <div class="d-flex justify-space-between align-center mb-4">
                <div>
                  <h3 class="ma-0">{{ selectedItem.name }}</h3>
                  <div class="text-caption text-medium-emphasis">Subject: {{ selectedItem.subject || '-' }}</div>
                </div>
                <div>
                  <v-chip size="small" variant="tonal" :color="selectedItem.active ? 'success' : 'grey-darken-1'">
                    {{ selectedItem.active ? 'Active' : 'Inactive' }}
                  </v-chip>
                </div>
              </div>

              <v-divider class="my-4" />

              <div class="text-body-1 text-high-emphasis" v-html="selectedItemBody(selectedItem)"></div>
            </div>

            <div v-else class="text-center pa-12 text-medium-emphasis">
              <v-icon size="48" class="mb-4">mdi-file-document-outline</v-icon>
              <div class="text-h6">Select a template to view</div>
              <p class="text-body-2 text-disabled">Choose a template from the left to see its contents here.</p>
            </div>
          </AppCard>
        </v-col>
      </v-row>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppCard from '../../components/AppCard.vue'
import { getCompanyEmailTemplates } from '../../services/emailTemplates.api'

interface EmailTemplateItem {
  id: number
  name: string
  subject?: string
  body?: string
  active: boolean
  created_at?: string
}

// Default set of common document templates to show in the left list
const defaultTemplates: EmailTemplateItem[] = [
  {
    id: 1,
    name: 'Terms & Conditions',
    subject: 'Terms and conditions',
    body: '<h2>Terms & Conditions</h2><p>Default terms and conditions content. Replace this with your company specific terms.</p>',
    active: true,
    created_at: '2024-01-01',
  },
  {
    id: 2,
    name: 'Privacy Policy',
    subject: 'Privacy policy',
    body: '<h2>Privacy Policy</h2><p>Default privacy policy content. Update to reflect your data practices.</p>',
    active: true,
    created_at: '2024-01-01',
  },
  {
    id: 3,
    name: 'About Us',
    subject: 'About our company',
    body: '<h2>About Us</h2><p>Short about us content. Introduce your company here.</p>',
    active: true,
    created_at: '2024-01-01',
  },
  {
    id: 4,
    name: 'Refund & Returns Policy',
    subject: 'Refund policy',
    body: '<h2>Refund & Returns</h2><p>Default refund and returns information.</p>',
    active: true,
    created_at: '2024-01-01',
  },
  {
    id: 5,
    name: 'Contact Template',
    subject: 'Contact information',
    body: '<h2>Contact Us</h2><p>Provide contact details and support information here.</p>',
    active: true,
    created_at: '2024-01-01',
  },
]

const loading = ref(false)
const search = ref('')
const itemsPerPage = ref(15)
// initialize with default templates so users see items immediately
const serverItems = ref<EmailTemplateItem[]>([...defaultTemplates])
const totalItems = ref(serverItems.value.length)
const debounceTimeout = ref<number | null>(null)
const selectedItem = ref<EmailTemplateItem | null>(serverItems.value.length ? serverItems.value[0] : null)

const loadItems = async (params: any = { page: 1, per_page: itemsPerPage.value }) => {
  loading.value = true
  try {
    const p: any = { ...params, search: search.value }
    const response: any = await getCompanyEmailTemplates(p)
    // if API returns data use it, otherwise fallback to default templates
    const data = response?.data && response.data.length ? response.data : defaultTemplates
    serverItems.value = data
    totalItems.value = response?.meta?.total ?? serverItems.value.length
    // default select first item
    if (!selectedItem.value && serverItems.value.length) selectedItem.value = serverItems.value[0]
  } catch (err) {
    // on error, keep defaults
    console.error('Failed to load templates, using default list', err)
    serverItems.value = defaultTemplates
    totalItems.value = serverItems.value.length
    if (!selectedItem.value && serverItems.value.length) selectedItem.value = serverItems.value[0]
  } finally {
    loading.value = false
  }
}

const fetchItems = () => loadItems({ page: 1, per_page: itemsPerPage.value })

onMounted(() => {
  fetchItems()
})

const onSearch = () => {
  if (debounceTimeout.value) clearTimeout(debounceTimeout.value)
  debounceTimeout.value = window.setTimeout(() => {
    fetchItems()
  }, 400)
}

const selectItem = (item: EmailTemplateItem) => {
  selectedItem.value = item
}

const selectedItemBody = (item: EmailTemplateItem | null) => {
  if (!item) return ''
  return item.body || '<div class="text-caption text-medium-emphasis">No content</div>'
}

const createTemplate = () => {
  // placeholder - open dialog or navigate to create page
  console.log('create template')
}
</script>
