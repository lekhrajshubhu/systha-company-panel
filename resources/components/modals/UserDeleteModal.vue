<template>
  <v-card-text class="pt-4">
    <!-- <div class="text-h6 mb-2">Delete User</div> -->
    Delete <strong>{{ userName }}</strong>? This action cannot be undone.
  </v-card-text>
  <v-card-actions>
    <v-spacer />
    <v-btn variant="text" :disabled="loading" @click="onCancel">Cancel</v-btn>
    <app-flat-button color="error" class="px-3" :loading="loading" @click="onConfirm">Delete</app-flat-button>
  </v-card-actions>
</template>

<script setup lang="ts">
import AppFlatButton from '@/components/AppFlatButton.vue'

defineProps<{
  userName: string
  loading: boolean
  onConfirm: () => void | Promise<void>
  onCancel: () => void | Promise<void>
}>()
</script>
