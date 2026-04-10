<template>
  <v-row class="mb-4" align="center" justify="space-between">
    <v-col cols="auto">
      <h2 class="pa-0">{{ title }}</h2>
      <v-card-subtitle class="pa-0">{{ subtitle }}</v-card-subtitle>
    </v-col>
    <v-col v-if="showBack" cols="auto">
      <v-btn variant="tonal" color="primary" size="large" @click="onBack">
        <v-icon class="mr-1">mdi-arrow-left</v-icon>
        {{ backLabel }}
      </v-btn>
    </v-col>
  </v-row>
</template>

<script setup lang="ts">
import { useAttrs } from 'vue'
import { useRouter } from 'vue-router'

withDefaults(defineProps<{
  title: string
  subtitle: string
  showBack?: boolean
  backLabel?: string
}>(), {
  showBack: false,
  backLabel: 'Back',
})

const emit = defineEmits<{
  (e: 'back'): void
}>()

const attrs = useAttrs()
const router = useRouter()

const onBack = () => {
  const hasBackListener = !!attrs.onBack
  if (hasBackListener) {
    emit('back')
    return
  }

  router.back()
}
</script>
