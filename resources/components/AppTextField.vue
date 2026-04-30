<template>
  <div>
    <div v-if="label" :class="labelClass">{{ label }}</div>
    <v-text-field
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
  validationError?: string | string[] | null
}>(), {
  modelValue: '',
  label: '',
  labelClass: 'text-body-2 text-medium-emphasis mb-1',
  errorMessage: null,
  validationError: null,
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number | null): void
}>()

const rawAttrs = useAttrs()

const attrs = computed(() => {
  const omitKeys = new Set([
    // Vuetify prop (kebab-case becomes camelCase in attrs)
    'errorMessages',
    // convenience aliases
    'errorMessage',
    'validationError',
    'validationErrors',
  ])

  return Object.fromEntries(
    Object.entries(rawAttrs).filter(([key]) => !omitKeys.has(key)),
  )
})

const toStringArray = (value: unknown): string[] => {
  if (typeof value === 'string') {
    const trimmed = value.trim()
    return trimmed ? [trimmed] : []
  }

  if (Array.isArray(value)) {
    return value
      .filter((item): item is string => typeof item === 'string')
      .map((item) => item.trim())
      .filter(Boolean)
  }

  return []
}

const computedErrorMessages = computed(() => {
  const attrErrorMessages = (rawAttrs as any).errorMessages
  const attrErrorMessage = (rawAttrs as any).errorMessage
  const attrValidationError = (rawAttrs as any).validationError ?? (rawAttrs as any).validationErrors

  const merged = [
    ...toStringArray(attrErrorMessages),
    ...toStringArray(attrErrorMessage),
    ...toStringArray(attrValidationError),
    ...toStringArray(props.errorMessage),
    ...toStringArray(props.validationError),
  ]

  return merged.length > 0 ? merged : undefined
})

const onUpdateModelValue = (value: string | number | null) => {
  emit('update:modelValue', value)
}
</script>
