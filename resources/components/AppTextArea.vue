<template>
  <div>
    <div v-if="label" :class="labelClass">{{ label }}</div>
    <v-textarea
      :model-value="modelValue"
      :error-messages="computedErrorMessages"
      variant="outlined"
      density="comfortable"
      hide-details="auto"
      v-bind="attrs"
      @update:model-value="onUpdateModelValue"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, useAttrs } from 'vue'

defineOptions({ inheritAttrs: false })

const props = withDefaults(defineProps<{
  modelValue?: string | number | null
  label?: string
  labelClass?: string
  /**
   * A single field error message (e.g. vee-validate's `errorMessage`).
   */
  errorMessage?: string | null
  /**
   * Validation errors coming from API responses (string or array of strings).
   */
  errorMessages?: string | string[]
}>(), {
  errorMessage: null,
  errorMessages: undefined,
  labelClass: 'text-body-2 text-medium-emphasis mb-1'
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number | null]
}>()

const attrs = useAttrs()

const computedErrorMessages = computed(() => {
  // Prioritize single error message, then array of messages
  if (props.errorMessage) {
    return [props.errorMessage]
  }
  if (props.errorMessages) {
    return Array.isArray(props.errorMessages) ? props.errorMessages : [props.errorMessages]
  }
  return []
})

const onUpdateModelValue = (value: string | number | null) => {
  emit('update:modelValue', value)
}
</script>
