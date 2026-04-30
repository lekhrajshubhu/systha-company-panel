<template>
  <v-container fluid>
    <div class="pa-0">
      <AppPageHeader :title="pageTitle" :subtitle="pageSubtitle" />

      <v-container fluid>
        <v-row>
          <v-col cols="12" md="6">
            <v-form ref="formRef" class="mb-4">
              <app-card flat class="">
                <app-form-section-header title="Basic Detail" subtitle="Staff personal and contact information." />
                <v-card-text class="pa-4 pt-0">
                  <v-row>
                    <v-col cols="12" md="6">
                      <app-text-field v-model="form.contact.first_name" label="First Name" placeholder="John"
                        aria-label="First name" :rules="[required]" />
                    </v-col>
                    <v-col cols="12" md="6">
                      <app-text-field v-model="form.contact.last_name" label="Last Name" placeholder="Doe"
                        aria-label="Last name" :rules="[required]" />
                    </v-col>
                    <v-col cols="12" md="6">
                      <app-text-field v-model="form.contact.email" label="Email" type="email" :clearable="!isEditMode"
                        placeholder="john.doe@example.com" aria-label="Email" :readonly="isEditMode"
                        :rules="[required, ...emailRules]" />
                    </v-col>
                    <v-col cols="12" md="6">
                      <app-phone-field v-model="form.contact.phone" label="Phone" placeholder="+1 555 123 4567"
                        aria-label="Phone" :rules="[required]" />
                    </v-col>
                    <v-col cols="12" md="12">
                      <div class="text-body-2 text-medium-emphasis mb-1">Roles</div>
                      <app-select-field v-model="form.roles" :items="roleOptions" multiple :rules="[required]"
                        :loading="loadingRoles" aria-label="Roles" />
                    </v-col>
                  </v-row>
                </v-card-text>

              </app-card>
              <app-card class="mb-4">
                <app-form-section-header title="Address" subtitle="Residential address details." />
                <v-card-text class="pa-4 pt-0">
                  <v-row>
                    <v-col cols="12" md="6">
                      <app-text-field v-model="form.address.line1" label="Address line 1" placeholder="123 Main St"
                        aria-label="Address line 1" :rules="[required]" />
                    </v-col>
                    <v-col cols="12" md="6">
                      <app-text-field v-model="form.address.line2" label="Address line 2" placeholder="Apt 4B"
                        aria-label="Address line 2" />
                    </v-col>
                    <v-col cols="12" md="4">
                      <app-text-field v-model="form.address.city" label="City" placeholder="Austin" aria-label="City"
                        :rules="[required]" />
                    </v-col>
                    <v-col cols="12" md="4">
                      <app-text-field v-model="form.address.state" label="State" placeholder="Texas" aria-label="State"
                        :rules="[required]" />
                    </v-col>
                    <v-col cols="12" md="4">
                      <app-text-field v-model="form.address.zip" label="ZIP / Postal code" placeholder="78701"
                        aria-label="ZIP / Postal code" :rules="[required, ...zipRules]" />
                    </v-col>
                  </v-row>
                </v-card-text>
              </app-card>
            </v-form>
            <div class="mb-10">
              <div class="d-flex align-center justify-end mt-4">
                <app-flat-button :loading="submitting || loadingStaff" @click="save">
                  {{ submitLabel }}
                </app-flat-button>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
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
import AppSelectField from '@/components/AppSelectField.vue'
import { createCompanyUser, getCompanyUserDetail, updateCompanyUser } from '@/services/users.api'
import { lookupCompanyRoles } from '@/services/roles.api'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()

const formRef = ref(null as any)

const form = ref({
  contact: {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
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
  roles: [] as number[],
})
const submitting = ref(false)
const loadingStaff = ref(false)
const loadingRoles = ref(false)

// validation rule helpers
const required = (v: any) => {
  if (v === null || v === undefined) return 'This field is required'
  if (Array.isArray(v)) return v.length > 0 || 'This field is required'
  return String(v).trim().length > 0 || 'This field is required'
}

const emailRules = [(v: any) => {
  if (!v) return true
  // simple email regex
  return /\S+@\S+\.\S+/.test(String(v)) || 'Enter a valid email address'
}]

const zipRules = [(v: any) => {
  if (!v) return true
  return /^[A-Za-z0-9\- ]+$/.test(String(v)) || 'Enter a valid ZIP / Postal code'
}]

const fallbackRoleOptions = [
  { value: 1, label: 'Staff' },
  { value: 2, label: 'Admin' },
  { value: 3, label: 'Customer' },
  { value: 4, label: 'Employee' },
  { value: 5, label: 'Manager' },
  { value: 6, label: 'Supervisor' },
]

const roleOptions = ref(fallbackRoleOptions)

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
    form.value.contact.phone = contact.phone ?? ''
  } else {
    const { first_name, last_name } = splitFullName(staff?.name)
    form.value.contact.first_name = first_name
    form.value.contact.last_name = last_name
    form.value.contact.email = staff?.email ?? ''
    form.value.contact.phone = staff?.phone ?? ''
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

  form.value.roles = staff?.roles ?? []
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
  form.value.contact.phone = queryPhone
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
    form.value.contact.phone.trim() !== ''
})


const save = async () => {

  const { valid } = await formRef.value.validate()
  if (!valid) return

  const payload = {
    contact: {
      first_name: form.value.contact.first_name,
      last_name: form.value.contact.last_name,
      email: form.value.contact.email,
      phone: form.value.contact.phone,
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
    roles: form.value.roles,
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
  loadRoleOptions()
})

const loadRoleOptions = async (): Promise<void> => {
  loadingRoles.value = true
  try {
    const options: any[] = await lookupCompanyRoles()

    console.log('Fetched role options:', options)
    roleOptions.value = options
      .map((o: any) => {
        const valueNum = typeof o.value === 'number' ? o.value : Number(o.value)
        return { value: valueNum, label: o.label ?? String(o.value ?? '') }
      })
      .filter((o: any) => !Number.isNaN(o.value))

    console.log('Processed role options:', roleOptions.value)
  } catch (error) {
    console.error('Failed to load role options:', error)
    roleOptions.value = fallbackRoleOptions
  } finally {
    loadingRoles.value = false
  }
}
</script>
