<template>
  <app-card>
    <v-card-title class="section-card-title">Approved By</v-card-title>
    <v-card-text class="pt-2">
      <div class="approved-box d-flex justify-space-between align-center">
        <div>
          <div class="font-weight-medium">{{ approval.approvedByName }}</div>
          <div class="caption text-medium-emphasis">{{ formatDate(approval.approvedAt) }}</div>
          <div class="caption text-medium-emphasis">IP: {{ approval.approvedIp }}</div>
        </div>
        <div class="signature-box">Signature</div>
      </div>
    </v-card-text>
  </app-card>
</template>

<script setup lang="ts">
import AppCard from '@/components/AppCard.vue'

defineProps<{
  approval: {
    approvedByName: string
    approvedAt: string | null
    approvedIp: string
  }
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
.approved-box {
  border: 1px solid rgba(0, 0, 0, 0.06);
  padding: 12px;
  border-radius: 6px;
}

.signature-box {
  width: 160px;
  height: 56px;
  border: 1px solid rgba(0, 0, 0, 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--v-theme-on-surface-variant, #9e9e9e);
}

.section-card-title {
  font-weight: 700;
  font-size: 1rem;
}
</style>
