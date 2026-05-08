<template>
  <v-card-text class="pt-4">
    Delete plan <strong>{{ planName }}</strong>? This action cannot be undone.
  </v-card-text>
  <v-card-actions>
    <v-spacer />
    <v-btn variant="text" :disabled="loading" @click="onCancel">Cancel</v-btn>
    <app-flat-button color="error" class="px-3" :loading="loading" @click="handleDelete">Delete</app-flat-button>
  </v-card-actions>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AppFlatButton from '@/components/AppFlatButton.vue'
import { deleteCompanyPlan } from '@/services/company-plans.api'

const props = defineProps<{
  planName: string
  planId: number | string
  onSuccess: () => void
  onCancel: () => void
}>()

const loading = ref(false)

const handleDelete = async () => {
  try {
    loading.value = true
    await deleteCompanyPlan(props.planId)
    props.onSuccess()
  } catch (error) {
    console.error('Failed to delete plan:', error)
  } finally {
    loading.value = false
  }
}
</script>
