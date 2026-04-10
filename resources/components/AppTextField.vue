<template>
  <div>
    <div v-if="label" :class="labelClass">{{ label }}</div>
    <v-text-field
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
  modelValue?: string | number | null
  label?: string
  labelClass?: string
}>(), {
  modelValue: '',
  label: '',
  labelClass: 'text-body-2 text-medium-emphasis mb-1',
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number | null): void
}>()

const onUpdateModelValue = (value: string | number | null) => {
  emit('update:modelValue', value)
}
</script>
