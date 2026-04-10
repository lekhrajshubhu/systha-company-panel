<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <app-page-header
        title="Add Staff"
        subtitle="Create a new staff member."
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
                  <app-text-field v-model="form.contact.email" label="Email" required type="email" clearable hide-details="auto"
                    placeholder="john.doe@example.com" aria-label="Email" />
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
              <app-flat-button :loading="submitting" @click="save">Submit</app-flat-button>
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
import { createCompanyUser } from '@/services/users.api'
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

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
    await createCompanyUser(payload)
    await router.push({ name: 'company.users' })
  } catch (error) {
    console.error('Failed to create user:', error)
  } finally {
    submitting.value = false
  }
}
</script>
