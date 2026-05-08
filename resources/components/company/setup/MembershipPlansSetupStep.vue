<template>
  <div>
    <div class="d-flex align-center mb-2">
      <v-icon class="mr-2" color="primary">mdi-gift-outline</v-icon>
      <div class="text-subtitle-1 font-weight-medium">Membership plan</div>
    </div>
    <div class="text-body-2 text-medium-emphasis mb-4">
      Create your first membership plan, then continue to dashboard.
    </div>

    <v-row>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="planForm.name"
          label="Name"
          variant="outlined"
          density="comfortable"
        />
      </v-col>
      <v-col cols="12" md="6">
        <v-select
          v-model="planForm.status"
          label="Status"
          variant="outlined"
          density="comfortable"
          :items="planStatusItems"
          item-title="label"
          item-value="value"
        />
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="planForm.description"
          label="Description"
          variant="outlined"
          density="comfortable"
          rows="2"
          auto-grow
        />
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="planForm.features"
          label="Features (comma-separated)"
          variant="outlined"
          density="comfortable"
          rows="2"
          auto-grow
        />
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="planForm.monthly_amount"
          label="Monthly Price"
          variant="outlined"
          density="comfortable"
          type="number"
          min="0"
          step="0.01"
        />
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="planForm.yearly_amount"
          label="Yearly Price"
          variant="outlined"
          density="comfortable"
          type="number"
          min="0"
          step="0.01"
        />
      </v-col>
    </v-row>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, watch } from 'vue'

interface Props {
  current_step?: number
  errors?: any
}

const props = defineProps<Props>()

const planStatusItems = [
  { label: 'Active', value: 'active' },
  { label: 'Inactive', value: 'inactive' },
]

const planForm = reactive<{
  name: string
  description: string
  features: string
  status: 'active' | 'inactive'
  monthly_amount: string
  yearly_amount: string
}>({
  name: '',
  description: '',
  features: '',
  status: 'inactive',
  monthly_amount: '0.00',
  yearly_amount: '0.00',
})

// Watch for changes and emit to parent
watch(planForm, (newValue) => {
  // Parent can access form via template ref
}, { deep: true })

// Expose form data to parent component
defineExpose({
  planForm
})
</script>
