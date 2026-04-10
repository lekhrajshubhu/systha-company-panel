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
                  <app-text-field v-model="form.first_name" label="First Name" required clearable hide-details="auto"
                    placeholder="John" aria-label="First name" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.last_name" label="Last Name" required clearable hide-details="auto"
                    placeholder="Doe" aria-label="Last name" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.email" label="Email" required type="email" clearable hide-details="auto"
                    placeholder="john.doe@example.com" aria-label="Email" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-phone-field v-model="form.phone" label="Phone" required clearable hide-details="auto"
                    placeholder="+1 555 123 4567" aria-label="Phone" />
                </v-col>
                <!-- <v-col cols="12" md="6">
                  <app-select-field v-model="form.role" label="Role" :items="roleItems" item-title="label"
                    item-value="value" required hide-details="auto" placeholder="Manager" aria-label="Role" />
                </v-col> -->

              </v-row>
            </v-card-text>

          </app-card>
          <app-card class="mb-4">
            <app-form-section-header title="Address" subtitle="Residential address details." />
            <v-card-text class="pa-4 pt-0">
              <v-row>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.address1" label="Address line 1" clearable hide-details="auto"
                    placeholder="123 Main St" aria-label="Address line 1" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.address2" label="Address line 2" clearable hide-details="auto"
                    placeholder="Apt 4B" aria-label="Address line 2" />
                </v-col>
                <v-col cols="12" md="4">
                  <app-text-field v-model="form.city" label="City" clearable hide-details="auto" placeholder="Austin"
                    aria-label="City" />
                </v-col>
                <v-col cols="12" md="4">
                  <app-text-field v-model="form.state" label="State" clearable hide-details="auto" placeholder="Texas"
                    aria-label="State" />
                </v-col>
                <v-col cols="12" md="4">
                  <app-text-field v-model="form.zip" label="ZIP / Postal code" clearable hide-details="auto"
                    placeholder="78701" aria-label="ZIP / Postal code" />
                </v-col>
                <!-- <v-col cols="12">
                  <div class="d-flex align-center justify-end mt-4">
                    <app-flat-button :loading="submitting" @click="save">Submit</app-flat-button>
                  </div>
                </v-col> -->
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
import AppSelectField from '@/components/AppSelectField.vue'
import AppTextField from '@/components/AppTextField.vue'
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const roleItems = [
  { value: 'admin', label: 'Admin' },
  { value: 'manager', label: 'Manager' },
  { value: 'staff', label: 'Staff' },
]

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  role: 'staff',
  active: true,
  address1: '',
  address2: '',
  city: '',
  state: '',
  zip: '',
})
const submitting = ref(false)

const isStep1Valid = computed(() => {
  return form.value.first_name.trim() !== '' &&
    form.value.last_name.trim() !== '' &&
    form.value.email.trim() !== '' &&
    form.value.phone.trim() !== '' &&
    form.value.role.trim() !== ''
})
const save = async () => {
  if (!isStep1Valid.value || submitting.value) return

  submitting.value = true
  try {
    await router.push({ name: 'company.users' })
  } finally {
    submitting.value = false
  }
}

</script>
