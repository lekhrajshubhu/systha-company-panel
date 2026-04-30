<template>
    <v-text-field v-bind="$attrs" :model-value="modelValue" @update:model-value="onInput" density="compact"
        variant="outlined" :width="width" prepend-inner-icon="mdi-magnify" clearable hide-details />
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{ modelValue?: string, width?: string }>(), { modelValue: '', width: '360px' })
const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void
}>()

const onInput = (val: string) => {
    emit('update:modelValue', val)
}
</script>
