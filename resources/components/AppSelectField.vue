<template>
  <div>
    <div v-if="label" :class="labelClass">{{ label }}</div>
    <v-select
      :model-value="modelValue"
      :item-title="itemTitle"
      :item-value="itemValue"
      :multiple="multiple"
      :chips="chips ?? multiple"
      :error-messages="computedErrorMessages"
      variant="outlined"
      density="comfortable"
      placeholder="Select an option"
      :clearable="clearable"
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
  modelValue?: unknown
  label?: string
  labelClass?: string
  itemTitle?: string
  itemValue?: string
  multiple?: boolean
  chips?: boolean
  clearable?: boolean
  /**
   * A single field error message (e.g. vee-validate's `errorMessage`).
   */
  errorMessage?: string | null
  /**
   * Validation errors coming from API responses (string or array of strings).
   */
  validationError?: string | string[] | null
}>(), {
  modelValue: undefined,
  label: '',
  labelClass: 'text-body-2 text-medium-emphasis mb-1',
  itemTitle: 'label',
  itemValue: 'value',
  multiple: false,
  chips: undefined,
  errorMessage: null,
  validationError: null,
  clearable: true,
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: unknown): void
}>()

const rawAttrs = useAttrs()

const attrs = computed(() => {
  const omitKeys = new Set([
    'errorMessages',
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

const onUpdateModelValue = (value: unknown) => {
  emit('update:modelValue', value)
}
</script>
