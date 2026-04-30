<template>
  <app-card>
    <v-card-title class="section-card-title">Recent Activities</v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <v-timeline dense side="end">
        <v-timeline-item
          v-for="(evt, i) in audit"
          :key="i"
          dot-color="primary"
          size="x-small"
        >
          <div class="text-subtitle-2 text-capitalize">{{ evt.title }}</div>
          <div class="caption text-medium-emphasis">{{ evt.by }} • {{ formatDate(evt.at) }}</div>
        </v-timeline-item>

        <v-timeline-item v-if="audit.length === 0" color="grey-darken-1" size="x-small">
          <div class="caption text-medium-emphasis">No audit events</div>
        </v-timeline-item>
      </v-timeline>
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
