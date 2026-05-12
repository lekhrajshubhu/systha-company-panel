<template>
    <v-card flat class="rich-text">
        <div class="d-flex justify-end ga-2 px-3 pt-3 pb-2">
            <v-btn size="small" variant="tonal" @click="mode = 'editor'">
                Editor
            </v-btn>

            <v-btn size="small" variant="tonal" @click="openHtmlMode">
                HTML
            </v-btn>

            <v-btn size="small" variant="tonal" @click="mode = 'preview'">
                Preview
            </v-btn>

            <v-btn size="small" variant="tonal" @click="copyHtml">
                Copy HTML
            </v-btn>
        </div>

        <v-divider />

        <v-card-text class="pa-0">
            <!-- Normal Rich Text Editor -->
            <VuetifyTiptap
                v-if="mode === 'editor'"
                v-model="internalValue"
                :label="label"
                :placeholder="placeholder"
                :min-height="minHeight"
                :markdown-theme="false"
            />

            <!-- Raw HTML Editor -->
            <v-textarea
                v-else-if="mode === 'html'"
                v-model="htmlBuffer"
                rows="20"
                auto-grow
                variant="outlined"
                label="HTML Source"
                class="html-source ma-3"
                @blur="applyHtml"
            />

            <!-- HTML Renderer -->
            <div
                v-else
                class="html-renderer"
                v-html="internalValue"
            />
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";

type Mode = "editor" | "html" | "preview";

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

const mode = ref<Mode>("editor");
const htmlBuffer = ref("");

const internalValue = computed({
    get: () => props.modelValue,
    set: (value: string) => emit("update:modelValue", value),
});

const openHtmlMode = () => {
    htmlBuffer.value = props.modelValue ?? "";
    mode.value = "html";
};

const applyHtml = () => {
    emit("update:modelValue", htmlBuffer.value);
};

const copyHtml = async () => {
    await navigator.clipboard.writeText(props.modelValue ?? "");
};
</script>

<style scoped>
.html-renderer {
    width: 100%;
    min-height: 600px;
    padding: 20px;
    background: #ffffff;
    overflow: auto;
}

.html-renderer :deep(table) {
    border-collapse: initial;
}

.html-source :deep(textarea) {
    font-family: monospace;
    font-size: 13px;
    line-height: 1.5;
}
</style>