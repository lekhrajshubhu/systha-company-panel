<template>
  <v-btn
    v-bind="$attrs"
    variant="flat"
    class="text-capitalize"
    :color="color"
    :disabled="disabled || loading"
    @click="onClick"
  >
    <template #prepend>
      <v-progress-circular
        v-if="loading"
        indeterminate
        size="16"
        width="2"
      />
      <v-icon v-else-if="icon">{{ icon }}</v-icon>
    </template>
    <slot />
  </v-btn>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  color?: string
  icon?: string
  loading?: boolean
  disabled?: boolean
}>(), {
  color: 'primary',
  icon: 'mdi-content-save',
  loading: false,
  disabled: false,
})

const emit = defineEmits<{
  (e: 'click', event: MouseEvent): void
}>()

const onClick = (event: MouseEvent) => {
  emit('click', event)
}
</script>
