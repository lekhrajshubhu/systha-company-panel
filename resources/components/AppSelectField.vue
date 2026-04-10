<template>
  <div>
    <div v-if="label" :class="labelClass">{{ label }}</div>
    <v-select
      v-bind="$attrs"
      :model-value="modelValue"
      variant="outlined"
      density="comfortable"
      @update:model-value="onUpdateModelValue"
    />
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  modelValue?: unknown
  label?: string
  labelClass?: string
}>(), {
  modelValue: undefined,
  label: '',
  labelClass: 'text-body-2 text-medium-emphasis mb-1',
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: unknown): void
}>()

const onUpdateModelValue = (value: unknown) => {
  emit('update:modelValue', value)
}
</script>
