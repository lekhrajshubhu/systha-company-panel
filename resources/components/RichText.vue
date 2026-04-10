<template>
    <v-card variant="outlined" class="rich-text">
        <v-card-text class="pa-0">
            <VuetifyTiptap
                v-model="internalValue"
                :label="label"
                :placeholder="placeholder"
                :min-height="minHeight"
            />
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
import { computed } from "vue";

const props = withDefaults(
    defineProps<{
        modelValue: string;
        label?: string;
        placeholder?: string;
        minHeight?: number | string;
    }>(),
    {
        modelValue: "",
        label: "",
        placeholder: "Enter some text...",
        minHeight: 240,
    }
);

const emit = defineEmits<{
    (e: "update:modelValue", value: string): void;
}>();

const internalValue = computed({
    get: () => props.modelValue,
    set: (value: string) => emit("update:modelValue", value),
});
</script>

<style scoped>
.rich-text :deep(.v-card-text) {
    padding: 0;
}
</style>
