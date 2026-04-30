<template>
  <div>
    <div v-if="label" :class="labelClass">{{ label }}</div>
    <v-text-field
      :model-value="formattedValue"
      :error-messages="computedErrorMessages"
      type="tel"
      variant="outlined"
      maxLength="14"
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
  mask?: string
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
  mask: '(###) ###-####',
  errorMessage: null,
  validationError: null,
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number | null): void
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

const formattedValue = computed(() => {
  return applyMask(String(props.modelValue ?? ''), props.mask)
})

const onUpdateModelValue = (value: string | number | null) => {
  const masked = applyMask(String(value ?? ''), props.mask)
  emit('update:modelValue', masked)
}

const applyMask = (input: string, mask: string): string => {
  const digits = input.replace(/\D/g, '')
  let result = ''
  let digitIndex = 0

  for (let i = 0; i < mask.length; i += 1) {
    const maskChar = mask[i]

    if (maskChar === '#') {
      if (digitIndex >= digits.length) break
      result += digits[digitIndex]
      digitIndex += 1
      continue
    }

    if (digitIndex < digits.length) {
      result += maskChar
    } else {
      break
    }
  }

  return result
}
</script>
