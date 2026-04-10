<template>
  <v-card-text class="pa-0">
    <v-stepper non-linear v-model="step" :items="stepItems" elevation="0">
      <template v-slot:item.1>
        <v-card flat>
          <v-card-text>
            <v-form ref="formRefStep1">
              <v-row>
                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Vendor name</div>
                  <v-text-field v-model="form.name" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Vendor name" />
                </v-col>

                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Email</div>
                  <v-text-field v-model="form.email" type="email" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Vendor email" />
                </v-col>

                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Phone</div>
                  <v-text-field v-model="form.phone" type="tel" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Vendor phone" />
                </v-col>

                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Business type</div>
                  <v-select v-model="form.type" :items="typeItems" item-title="label" item-value="value" variant="outlined" density="comfortable" hide-details="auto" aria-label="Business type" />
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </template>

      <template v-slot:item.2>
        <v-card flat>
          <v-card-text>
            <v-form ref="formRefStep2">
              <v-row>
                <v-col cols="12">
                  <div class="text-caption text-medium-emphasis mb-1">Address line 1</div>
                  <v-text-field v-model="form.address1" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Address line 1" />
                </v-col>
                <v-col cols="12">
                  <div class="text-caption text-medium-emphasis mb-1">Address line 2</div>
                  <v-text-field v-model="form.address2" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Address line 2" />
                </v-col>
                <v-col cols="12" md="4">
                  <div class="text-caption text-medium-emphasis mb-1">City</div>
                  <v-text-field v-model="form.city" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="City" />
                </v-col>
                <v-col cols="12" md="4">
                  <div class="text-caption text-medium-emphasis mb-1">State</div>
                  <v-text-field v-model="form.state" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="State" />
                </v-col>

                <v-col cols="12" md="4">
                  <div class="text-caption text-medium-emphasis mb-1">Zip</div>
                  <v-text-field v-model="form.zip" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Zip" />
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </template>

      <template v-slot:item.3>
        <v-card flat>
          <v-card-text>
            <v-form ref="formRefStep3">
              <v-row>
                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Name</div>
                  <v-text-field v-model="form.contact_name" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Contact name" />
                </v-col>
                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Email</div>
                  <v-text-field v-model="form.contact_email" type="email" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Contact email" />
                </v-col>
                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Phone</div>
                  <v-text-field v-model="form.contact_phone" type="tel" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Contact phone" />
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </template>

      <template v-slot:item.4>
        <v-card flat>
          <v-card-text>
            <div class="mb-4">
              <div class="text-subtitle-1 font-weight-medium">Review</div>
              <div class="text-caption text-medium-emphasis">Confirm vendor details before saving.</div>
            </div>

            <v-simple-table dense>
              <thead>
                <tr>
                  <th class="text-left">Field</th>
                  <th class="text-left">Value</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-caption text-medium-emphasis">Name</td>
                  <td>{{ form.name || '-' }}</td>
                </tr>
                <tr>
                  <td class="text-caption text-medium-emphasis">Email</td>
                  <td>{{ form.email || '-' }}</td>
                </tr>
                <tr>
                  <td class="text-caption text-medium-emphasis">Phone</td>
                  <td>{{ form.phone || '-' }}</td>
                </tr>
                <tr>
                  <td class="text-caption text-medium-emphasis">Type</td>
                  <td>{{ getTypeLabel(form.type) }}</td>
                </tr>
                <tr>
                  <td class="text-caption text-medium-emphasis">Contact</td>
                  <td>{{ [form.contact_name, form.contact_email, form.contact_phone].filter(Boolean).join(' / ') || '-' }}</td>
                </tr>
                <tr>
                  <td class="text-caption text-medium-emphasis">Address</td>
                  <td>{{ [form.address1, form.address2, form.city, form.state, form.zip].filter(Boolean).join(', ') || '-' }}</td>
                </tr>
                <tr>
                  <td class="text-caption text-medium-emphasis">Status</td>
                  <td>{{ form.active ? 'Active' : 'Inactive' }}</td>
                </tr>
              </tbody>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </template>

      <!-- suppress default actions -->
      <template v-slot:actions></template>
    </v-stepper>
  </v-card-text>

  <v-divider />
  <v-card-actions>
    <v-spacer />
    <v-btn variant="text" @click="cancel">Cancel</v-btn>
    <v-btn v-if="step > 1" variant="text" @click="prevStep">Prev</v-btn>
    <v-btn v-else variant="text" disabled />
    <v-btn v-if="step < stepItems.length" color="primary" :disabled="step === 1 ? !isStep1Valid : false" @click="nextStep">Next</v-btn>
    <v-btn v-else color="primary" :disabled="isSaveDisabled" @click="save">Save</v-btn>
  </v-card-actions>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useModalStore } from '@/stores/modal'

const props = defineProps<{ onCreated?: (vendor: any) => void }>()
const modal = useModalStore()
const formRef = ref()
const formRefStep1 = ref()
const formRefStep2 = ref()
const formRefStep3 = ref()

const typeItems = [
  { value: 'pest control', label: 'Pest Control' },
  { value: 'cleaning', label: 'Cleaning' },
  { value: 'tax', label: 'Tax' },
  { value: 'plumbing', label: 'Plumbing' },
  { value: 'electrical', label: 'Electrical' },
]

const form = ref({
  name: '',
  type: 'cleaning',
  phone: '',
  email: '',
  city: '',
  state: '',
  zip: '',
  address1: '',
  address2: '',
  active: true,
  contact_name: '',
  contact_email: '',
  contact_phone: '',
})

const step = ref(1)
const stepItems = ['Basic', 'Address', 'Contact', 'Review']

const isStep1Valid = computed(() => {
  // require name and email (phone optional)
  return !!form.value.name && !!form.value.email
})

const isSaveDisabled = computed(() => {
  return !isStep1Valid.value
})

const cancel = () => modal.close()

const nextStep = () => {
  if (step.value < stepItems.length) {
    // only advance if step 1 is valid
    if (step.value === 1 && !isStep1Valid.value) return
    step.value += 1
  }
}

const prevStep = () => {
  if (step.value > 1) step.value -= 1
}

const save = () => {
  if (!form.value.name || !form.value.email) return

  const newVendor = {
    id: Date.now(),
    name: form.value.name,
    type: form.value.type,
    phone: form.value.phone,
    email: form.value.email,
    city: form.value.city,
    state: form.value.state,
    zip: form.value.zip,
    address1: form.value.address1,
    address2: form.value.address2,
    contact_name: form.value.contact_name,
    contact_email: form.value.contact_email,
    contact_phone: form.value.contact_phone,
    status: form.value.active ? 'approved' : 'inactive',
  }

  props.onCreated?.(newVendor)
  modal.close()
}

const getTypeLabel = (value: string) => {
  const found = typeItems.find(t => t.value === value)
  return found ? found.label : value
}
</script>

<style scoped>
</style>
