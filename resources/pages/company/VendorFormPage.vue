<template>
  <v-container class="py-6 px-14" fluid>
    <div class="pa-0">
      <app-page-header
        title="Add Vendor"
        subtitle="Create a new vendor."
        show-back
      />

      <v-row>
        <v-col cols="12" md="6">
          <app-card flat class="mb-4">
            <app-form-section-header
              title="Basic Detail"
              subtitle="Core vendor profile and business information."
            />
            <v-card-text class="pa-4 pt-0">
              <v-row>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.basic.name" label="Vendor name" required clearable hide-details="auto"
                    placeholder="Sparkle Cleaners" aria-label="Vendor name" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-select-field v-model="form.basic.type" label="Business type" :items="typeItems"
                    item-title="label" item-value="value" required hide-details="auto" placeholder="Cleaning"
                    aria-label="Business type" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.basic.email" label="Email" required type="email" clearable
                    hide-details="auto" placeholder="hello@sparklecleaners.com" aria-label="Vendor email" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-phone-field v-model="form.basic.phone" label="Phone" clearable hide-details="auto"
                    placeholder="+1 555 987 6543" aria-label="Vendor phone" />
                </v-col>
              </v-row>
            </v-card-text>
          </app-card>

          <app-card flat class="mb-4">
            <app-form-section-header
              title="Address"
              subtitle="Vendor office location and mailing address."
            />

            <v-card-text class="pa-4 pt-0">
              <v-row>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.address.line_1" label="Address line 1" clearable hide-details="auto"
                    placeholder="456 Market St" aria-label="Address line 1" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.address.line_2" label="Address line 2" clearable hide-details="auto"
                    placeholder="Suite 210" aria-label="Address line 2" />
                </v-col>
                <v-col cols="12" md="4">
                  <app-text-field v-model="form.address.city" label="City" clearable hide-details="auto"
                    placeholder="Dallas" aria-label="City" />
                </v-col>
                <v-col cols="12" md="4">
                  <app-text-field v-model="form.address.state" label="State" clearable hide-details="auto"
                    placeholder="Texas" aria-label="State" />
                </v-col>
                <v-col cols="12" md="4">
                  <app-text-field v-model="form.address.zip" label="Zip" clearable hide-details="auto"
                    placeholder="75001" aria-label="Zip" />
                </v-col>
              </v-row>
            </v-card-text>

          </app-card>
          <app-card flat class="mb-4">
            <app-form-section-header
              title="Contact Person"
              subtitle="Primary point of contact for this vendor."
            />
            <v-card-text class="pa-4 pt-0">
              <v-row>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.contact.first_name" label="First Name" clearable hide-details="auto"
                    placeholder="Jane" aria-label="Contact first name" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.contact.last_name" label="Last Name" clearable hide-details="auto"
                    placeholder="Smith" aria-label="Contact last name" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-text-field v-model="form.contact.email" label="Email" type="email" clearable hide-details="auto"
                    placeholder="jane.smith@example.com" aria-label="Contact email" />
                </v-col>
                <v-col cols="12" md="6">
                  <app-phone-field v-model="form.contact.phone" label="Phone" clearable hide-details="auto"
                    placeholder="+1 555 321 6789" aria-label="Contact phone" />
                </v-col>
                <!-- <v-col cols="12">
                 
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

const typeItems = [
  { value: 'pest control', label: 'Pest Control' },
  { value: 'cleaning', label: 'Cleaning' },
  { value: 'tax', label: 'Tax' },
  { value: 'plumbing', label: 'Plumbing' },
  { value: 'electrical', label: 'Electrical' },
]

const form = ref({
  basic: {
    name: '',
    type: 'cleaning',
    email: '',
    phone: '',
  },
  address: {
    line_1: '',
    line_2: '',
    city: '',
    state: '',
    zip: '',
  },
  contact: {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
  },
})
const submitting = ref(false)

const isFormValid = computed(() => {
  return form.value.basic.name.trim() !== '' && form.value.basic.email.trim() !== ''
})

const save = async () => {
  if (!isFormValid.value || submitting.value) return
  submitting.value = true
  try {
    await router.push({ name: 'company.vendors' })
  } finally {
    submitting.value = false
  }
}
</script>
