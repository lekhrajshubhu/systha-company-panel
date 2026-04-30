<template>
  <v-container fluid>
    <v-row align="center" justify="space-between">
      <v-col cols="auto">
        <h2 class="pa-0">{{ title }}</h2>
        <v-card-subtitle class="pa-0">{{ subtitle }}</v-card-subtitle>
      </v-col>
      <v-col cols="auto">
        <div class="d-flex align-center ga-2">
          <slot name="action" />
          <v-btn v-if="showBack" variant="flat" color="primary" @click="onBack">
            <v-icon class="mr-1">mdi-arrow-left</v-icon>
            Back
          </v-btn>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { useAttrs } from 'vue'
import { useRouter } from 'vue-router'

withDefaults(defineProps<{
  title: string
  subtitle: string
  showBack?: boolean
}>(), {
  showBack: true,
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
