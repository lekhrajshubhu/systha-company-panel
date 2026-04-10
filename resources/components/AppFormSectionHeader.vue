<template>
  <div class="mb-3">
    <v-card-title class="text-h6 font-weight-bold mb-0 pb-0">{{ title }}</v-card-title>
    <v-card-subtitle class="pt-0 text-high-emphasis">{{ subtitle }}</v-card-subtitle>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string
  subtitle: string
}>()
</script>
