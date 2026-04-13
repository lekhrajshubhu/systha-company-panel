<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <app-page-header
        :title="pageTitle"
        :subtitle="pageSubtitle"
        show-back
      />

      <v-row>
        <v-col cols="12" md="6">
          <app-card flat class="mb-4">
            <app-form-section-header title="Basic Detail" subtitle="Staff personal and contact information." />
            <v-card-text class="pa-4 pt-0">
              <v-row>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.contact.first_name" label="First Name" required clearable hide-details="auto"
                    placeholder="John" aria-label="First name" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.contact.last_name" label="Last Name" required clearable hide-details="auto"
                    placeholder="Doe" aria-label="Last name" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.contact.email" label="Email" required type="email" :clearable="!isEditMode" hide-details="auto"
                    placeholder="john.doe@example.com" aria-label="Email" :readonly="isEditMode" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-phone-field v-model="form.contact.phone1" label="Phone" required clearable hide-details="auto"
                    placeholder="+1 555 123 4567" aria-label="Phone" />
                </v-col>
              </v-row>
            </v-card-text>

          </app-card>
          <app-card class="mb-4">
            <app-form-section-header title="Address" subtitle="Residential address details." />
            <v-card-text class="pa-4 pt-0">
              <v-row>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.address.line1" label="Address line 1" clearable hide-details="auto"
                    placeholder="123 Main St" aria-label="Address line 1" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.address.line2" label="Address line 2" clearable hide-details="auto"
                    placeholder="Apt 4B" aria-label="Address line 2" />
                </v-col>
                <v-col cols="12" md="4">
                  <app-text-field v-model="form.address.city" label="City" clearable hide-details="auto" placeholder="Austin"
                    aria-label="City" />
                </v-col>
                <v-col cols="12" md="4">
                  <app-text-field v-model="form.address.state" label="State" clearable hide-details="auto" placeholder="Texas"
                    aria-label="State" />
                </v-col>
                <v-col cols="12" md="4">
                  <app-text-field v-model="form.address.zip" label="ZIP / Postal code" clearable hide-details="auto"
                    placeholder="78701" aria-label="ZIP / Postal code" />
                </v-col>
              </v-row>
            </v-card-text>
          </app-card>
          <div class="mb-10">
            <div class="d-flex align-center justify-end mt-4">
              <app-flat-button :loading="submitting || loadingStaff" @click="save">
                {{ submitLabel }}
              </app-flat-button>
            </div>
          </div>
        </v-col>
      </v-row>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import AppCard from '@/components/AppCard.vue'
import AppFlatButton from '@/components/AppFlatButton.vue'
import AppFormSectionHeader from '@/components/AppFormSectionHeader.vue'
import AppPageHeader from '@/components/AppPageHeader.vue'
import AppPhoneField from '@/components/AppPhoneField.vue'
import AppTextField from '@/components/AppTextField.vue'
import { createCompanyUser, getCompanyUserDetail, updateCompanyUser } from '@/services/users.api'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()

const form = ref({
  contact: {
    first_name: '',
    last_name: '',
    email: '',
    phone1: '',
  },
  address: {
    line1: '',
    line2: '',
    city: '',
    state: '',
    zip: '',
    lat: null as number | null,
    lng: null as number | null,
  },
})
const submitting = ref(false)
const loadingStaff = ref(false)

const staffId = computed(() => {
  const id = route.params.id
  if (!id) return null
  const parsed = Number(id)
  return Number.isNaN(parsed) ? null : parsed
})

const isEditMode = computed(() => staffId.value !== null)

const pageTitle = computed(() => isEditMode.value ? 'Edit Staff' : 'Add Staff')
const pageSubtitle = computed(() => isEditMode.value ? 'Update staff member details.' : 'Create a new staff member.')
const submitLabel = computed(() => isEditMode.value ? 'Update' : 'Submit')

const splitFullName = (fullName: string | null | undefined) => {
  if (!fullName) {
    return { first_name: '', last_name: '' }
  }

  const parts = fullName.trim().split(/\s+/).filter(Boolean)
  if (parts.length === 0) {
    return { first_name: '', last_name: '' }
  }

  const first_name = parts[0] ?? ''
  const last_name = parts.slice(1).join(' ')
  return { first_name, last_name }
}

const prefillFromStaffDetail = (staff: any) => {
  const contact = staff?.contact
  const address = staff?.address

  if (contact) {
    form.value.contact.first_name = contact.first_name ?? ''
    form.value.contact.last_name = contact.last_name ?? ''
    form.value.contact.email = contact.email ?? ''
    form.value.contact.phone1 = contact.phone1 ?? ''
  } else {
    const { first_name, last_name } = splitFullName(staff?.name)
    form.value.contact.first_name = first_name
    form.value.contact.last_name = last_name
    form.value.contact.email = staff?.email ?? ''
    form.value.contact.phone1 = staff?.phone1 ?? ''
  }

  if (address) {
    form.value.address.line1 = address.line1 ?? ''
    form.value.address.line2 = address.line2 ?? ''
    form.value.address.city = address.city ?? ''
    form.value.address.state = address.state ?? ''
    form.value.address.zip = address.zip ?? ''
    form.value.address.lat = address.lat ?? null
    form.value.address.lng = address.lng ?? null
  }
}

const prefillFromRouteQuery = () => {
  const queryName = typeof route.query.name === 'string' ? route.query.name : ''
  const queryEmail = typeof route.query.email === 'string' ? route.query.email : ''
  const queryPhone = typeof route.query.phone1 === 'string' ? route.query.phone1 : ''

  if (!queryName && !queryEmail && !queryPhone) return

  const { first_name, last_name } = splitFullName(queryName)
  form.value.contact.first_name = first_name
  form.value.contact.last_name = last_name
  form.value.contact.email = queryEmail
  form.value.contact.phone1 = queryPhone
}

const loadStaffForEdit = async () => {
  if (!staffId.value) return

  loadingStaff.value = true
  try {
    const response: any = await getCompanyUserDetail(staffId.value)
    prefillFromStaffDetail(response?.data ?? response)
  } catch (error) {
    console.warn('Failed to load staff detail. Using route data fallback if available.', error)
  } finally {
    loadingStaff.value = false
  }
}

const isStep1Valid = computed(() => {
  return form.value.contact.first_name.trim() !== '' &&
    form.value.contact.last_name.trim() !== '' &&
    form.value.contact.email.trim() !== '' &&
    form.value.contact.phone1.trim() !== ''
})

const save = async () => {
  if (!isStep1Valid.value || submitting.value) return

  const payload = {
    contact: {
      first_name: form.value.contact.first_name,
      last_name: form.value.contact.last_name,
      email: form.value.contact.email,
      phone1: form.value.contact.phone1,
    },
    address: {
      line1: form.value.address.line1,
      line2: form.value.address.line2,
      city: form.value.address.city,
      state: form.value.address.state,
      zip: form.value.address.zip,
      lat: form.value.address.lat,
      lng: form.value.address.lng,
    },
  }

  submitting.value = true
  try {
    if (isEditMode.value && staffId.value !== null) {
      await updateCompanyUser(staffId.value, payload)
    } else {
      await createCompanyUser(payload)
    }
    await router.push({ name: 'company.users' })
  } catch (error) {
    console.error(`Failed to ${isEditMode.value ? 'update' : 'create'} user:`, error)
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  prefillFromRouteQuery()
  loadStaffForEdit()
})
</script>
