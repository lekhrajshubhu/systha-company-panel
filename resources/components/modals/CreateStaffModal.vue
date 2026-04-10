<template>
  <v-card-text class="pa-0">
    <v-stepper non-linear v-model="step" :items="stepItems" elevation="0">
      <template v-slot:item.1>
        <v-card flat>
          <v-card-text>
            <v-form ref="formRefStep1">
              <v-row>
                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Full name</div>
                  <v-text-field v-model="form.name" required variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Full name" />
                </v-col>
                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Email</div>
                  <v-text-field v-model="form.email" required type="email" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Email" />
                </v-col>
                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Phone</div>
                  <v-text-field v-model="form.phone" required type="tel" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="Phone" />
                </v-col>
                <v-col cols="12" md="6">
                  <div class="text-caption text-medium-emphasis mb-1">Role</div>
                  <v-select
                    v-model="form.role"
                    :items="roleItems"
                    item-title="label"
                    item-value="value"
                    variant="outlined"
                    density="comfortable"
                    required
                    hide-details="auto"
                    aria-label="Role"
                  />
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
                <v-col cols="12" md="4"><div class="text-caption text-medium-emphasis mb-1">City</div><v-text-field v-model="form.city" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="City" /></v-col>
                <v-col cols="12" md="4"><div class="text-caption text-medium-emphasis mb-1">State</div><v-text-field v-model="form.state" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="State" /></v-col>
                <v-col cols="12" md="4"><div class="text-caption text-medium-emphasis mb-1">ZIP / Postal code</div><v-text-field v-model="form.zip" variant="outlined" density="comfortable" clearable hide-details="auto" aria-label="ZIP / Postal code" /></v-col>
                <v-col cols="12" class="d-flex align-center"><div class="text-caption text-medium-emphasis mr-4">Active</div><v-switch v-model="form.active" hide-details="auto" aria-label="Active" /></v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </template>

      <template v-slot:item.3>
        <v-card flat>
          <v-card-text>
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
                  <td class="text-caption text-medium-emphasis">Role</td>
                  <td>{{ getRoleLabel(form.role) }}</td>
                </tr>
                <tr>
                  <td class="text-caption text-medium-emphasis">Address</td>
                  <td>{{ [form.address1, form.address2, form.city, form.state, form.zip].filter(Boolean).join(', ') || '-' }}</td>
                </tr>
              </tbody>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </template>

      <!-- override default actions to avoid duplicate nav buttons -->
      <template v-slot:actions></template>
    </v-stepper>
  </v-card-text>

  <v-divider></v-divider>
  <v-card-actions>
    <v-spacer />
    <!-- <v-btn variant="text" @click="cancel">Cancel</v-btn> -->
    <v-btn v-if="step > 1" variant="text" @click="prevStep">Prev</v-btn>
    <v-btn v-else variant="text" disabled />
    <v-btn v-if="step < stepItems.length" color="primary" :disabled="step === 1 ? !isStep1Valid : false" @click="nextStep">Next</v-btn>
    <v-btn v-else color="primary" :disabled="isSaveDisabled" @click="save">Save</v-btn>
  </v-card-actions>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useModalStore } from '@/stores/modal'

const props = defineProps<{ onCreated?: (user: any) => void }>()

const modal = useModalStore()
const formRef = ref()
const formRefStep1 = ref()
const formRefStep2 = ref()

// role items with capitalized labels
const roleItems = [
  { value: 'admin', label: 'Admin' },
  { value: 'manager', label: 'Manager' },
  { value: 'staff', label: 'Staff' },
]

const roles = roleItems.map(r => r.value)

const form = ref({
  name: '',
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

const step = ref(1)
const stepItems = ['Basic', 'Address', 'Review']

const isStep1Valid = computed(() => {
  return !!form.value.name && !!form.value.email && !!form.value.phone
})

const isSaveDisabled = computed(() => {
  return !isStep1Valid.value
})

const cancel = () => {
  modal.close()
}

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
  // basic validation
  if (!form.value.name || !form.value.email || !form.value.phone) {
    // could show a toast - for now just return
    return
  }

  const newUser = {
    id: Date.now(),
    name: form.value.name,
    email: form.value.email,
    phone: form.value.phone,
    role: form.value.role,
    status: form.value.active ? 'active' : 'inactive',
    address: {
      address1: form.value.address1,
      address2: form.value.address2,
      city: form.value.city,
      state: form.value.state,
      zip: form.value.zip,
    },
    last_login_at: null,
    created_at: new Date().toISOString(),
  }

  props.onCreated?.(newUser)
  modal.close()
}

const getRoleLabel = (roleValue: string) => {
  const found = roleItems.find(r => r.value === roleValue)
  return found ? found.label : roleValue
}
</script>
<style lang="scss" scoped>

</style>
