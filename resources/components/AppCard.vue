<template>
  <v-card :variant="variant as any" :class="['app-card', padding, customClass]" rounded>
    <slot />
  </v-card>
</template>

<script setup lang="ts">
const props = defineProps({
  variant: { type: String, default: 'flat' },
  padding: { type: String, default: 'pa-4' },
  customClass: { type: String, default: '' },
})
</script>

<style scoped>
.app-card {
  width: 100%;
}
</style>
