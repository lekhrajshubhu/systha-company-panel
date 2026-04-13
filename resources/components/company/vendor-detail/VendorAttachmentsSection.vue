<template>
  <app-card class="mb-6">
    <v-card-title class="section-card-title d-flex align-center justify-space-between">
      <span>Attachments</span>
      <v-btn size="small" icon color="info" variant="tonal"
        aria-label="Edit contact person"><v-icon>mdi-plus</v-icon> </v-btn>
    </v-card-title>
    <v-card-text class="pt-2">
      <v-row>
        <v-col cols="6" md="3" v-for="(att, i) in attachments" :key="i">
          <div class="attachment-box">
            <v-icon size="36" color="primary">mdi-file-document-outline</v-icon>
            <div class="caption mt-2">{{ att.label }}</div>
          </div>
        </v-col>
      </v-row>
    </v-card-text>
  </app-card>
</template>

<script setup lang="ts">
import AppCard from '@/components/AppCard.vue'
import AppFlatButton from '@/components/AppFlatButton.vue'

defineProps<{
  attachments: Array<{ label?: string }>
}>()

const emit = defineEmits<{
  (e: 'action'): void
}>()
</script>

<style scoped>
.attachment-box {
  border: 1px dashed var(--v-theme-border, #cfcfcf);
  height: 96px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  padding: 8px;
  border-radius: 4px;
}

.section-card-title {
  font-weight: 700;
  font-size: 1rem;
}
</style>
