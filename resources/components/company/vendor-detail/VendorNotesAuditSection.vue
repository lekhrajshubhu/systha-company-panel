<template>
  <app-card>
    <v-card-title class="section-card-title">Notes</v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <div class="mb-4">
        <v-textarea hide-details rows="3" variant="outlined" auto-grow />
        <div class="text-right mt-2">
          <app-flat-button size="default" icon="mdi-content-save">Submit</app-flat-button>
        </div>
      </div>
      <div>
        <v-list-item v-for="(evt, i) in audit" :key="i" class="px-0">
          <v-list-item-content>
            <v-list-item-title class="text-subtitle-2 text-capitalize">{{ evt.title }}</v-list-item-title>
            <v-list-item-subtitle class="caption text-medium-emphasis">{{ evt.by }} • {{ formatDate(evt.at) }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>

        <div v-if="audit.length === 0" class="caption text-medium-emphasis">No notes history</div>
      </div>
     
    </v-card-text>
  </app-card>
</template>

<script setup lang="ts">
import AppCard from '@/components/AppCard.vue'
import AppFlatButton from '@/components/AppFlatButton.vue'

defineProps<{
  audit: Array<{ title?: string; by?: string; at?: string; type?: string }>
}>()

function formatDate(value: string | null | undefined) {
  if (!value) return '-'
  try {
    return new Date(value).toLocaleString()
  } catch {
    return String(value)
  }
}
</script>

<style scoped>
.section-card-title {
  font-weight: 700;
  font-size: 1rem;
}

@media (max-width: 959px) {
  .text-right {
    text-align: left !important;
  }
}
</style>
