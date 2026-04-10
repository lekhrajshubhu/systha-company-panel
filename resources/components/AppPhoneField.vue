<template>
  <div>
    <div v-if="label" :class="labelClass">{{ label }}</div>
    <v-text-field
      v-bind="$attrs"
      :model-value="formattedValue"
      type="tel"
      variant="outlined"
      maxLength="14"
      density="comfortable"
      @update:model-value="onUpdateModelValue"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  modelValue?: string | number | null
  label?: string
  labelClass?: string
  mask?: string
}>(), {
  modelValue: '',
  label: '',
  labelClass: 'text-body-2 text-medium-emphasis mb-1',
  mask: '(###) ###-####',
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number | null): void
}>()

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
