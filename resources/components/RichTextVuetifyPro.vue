<template>
    <v-card class="rich-text" flat>
        <div v-if="showHtmlTools" class="d-flex justify-end ga-2 px-3 pt-3 pb-1">
            <v-btn variant="tonal" size="small" @click="copyHtml">
                Copy HTML
            </v-btn>

            <v-btn variant="tonal" size="small" @click="openHtmlView">
                HTML View
            </v-btn>

            <v-btn variant="tonal" size="small" @click="previewMode = !previewMode">
                {{ previewMode ? "Editor View" : "Preview" }}
            </v-btn>
        </div>

        <v-card-text class="pa-0">
            <!-- Exact HTML Preview -->
            <iframe
                v-if="previewMode"
                :srcdoc="internalValue"
                class="html-preview"
            />

            <!-- Rich Text Editor -->
            <VuetifyTiptap
                v-else
                v-model="internalValue"
                :label="label"
                :placeholder="placeholder"
                :min-height="minHeight"
                :markdown-theme="false"
            />
        </v-card-text>

        <v-dialog v-model="htmlDialog" max-width="1000">
            <v-card>
                <v-card-title class="text-h6">
                    HTML Source
                </v-card-title>

                <v-card-text>
                    <v-textarea
                        v-model="htmlBuffer"
                        rows="18"
                        variant="outlined"
                        label="Paste or edit HTML"
                        class="html-source"
                    />
                </v-card-text>

                <v-card-actions class="justify-end">
                    <v-btn variant="text" @click="htmlDialog = false">
                        Cancel
                    </v-btn>

                    <v-btn color="primary" @click="applyHtml">
                        Apply HTML
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";

const props = withDefaults(
    defineProps<{
        modelValue: string;
        label?: string;
        placeholder?: string;
        minHeight?: number | string;
        showHtmlTools?: boolean;
    }>(),
    {
        modelValue: "",
        label: "",
        placeholder: "Enter some text...",
        minHeight: 240,
        showHtmlTools: true,
    }
);

const emit = defineEmits<{
    (e: "update:modelValue", value: string): void;
}>();

const htmlDialog = ref(false);
const htmlBuffer = ref("");
const previewMode = ref(false);

const internalValue = computed({
    get: () => props.modelValue,
    set: (value: string) => emit("update:modelValue", value),
});

const openHtmlView = () => {
    htmlBuffer.value = props.modelValue ?? "";
    htmlDialog.value = true;
};

const applyHtml = () => {
    emit("update:modelValue", htmlBuffer.value);
    previewMode.value = true;
    htmlDialog.value = false;
};

const copyHtml = async () => {
    try {
        await navigator.clipboard.writeText(props.modelValue ?? "");
    } catch (error) {
        console.error("Failed to copy HTML:", error);
    }
};
</script>

<style scoped>
.html-preview {
    width: 100%;
    min-height: 600px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #ffffff;
}

.html-source :deep(textarea) {
    font-family: monospace;
    font-size: 13px;
    line-height: 1.5;
}
</style>