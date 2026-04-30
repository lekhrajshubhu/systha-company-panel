<template>
    <v-card class="rich-text" flat>
        <div v-if="showHtmlTools" class="d-flex justify-end ga-2 px-3 pt-3 pb-1">
            <v-btn variant="tonal" size="small" @click="copyHtml">
                Copy HTML
            </v-btn>
            <v-btn variant="tonal" size="small" @click="openHtmlView">
                HTML View
            </v-btn>
        </div>
        <v-card-text class="pa-0">
            <VuetifyTiptap
                v-model="internalValue"
                :label="label"
                :placeholder="placeholder"
                :min-height="minHeight"
                :markdown-theme="false"
            />
        </v-card-text>

        <v-dialog v-model="htmlDialog" max-width="900">
            <v-card>
                <v-card-title class="text-h6">HTML Source</v-card-title>
                <v-card-text>
   
                        <v-textarea
                            v-model="htmlBuffer"
                            auto-grow
                            rows="14"
                            variant="outlined"
                            label="Paste or edit HTML"
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

const internalValue = computed({
    get: () => props.modelValue,
    set: (value: string) => emit("update:modelValue", value),
});

const openHtmlView = () => {
    htmlBuffer.value = formatHtml(props.modelValue ?? "");
    htmlDialog.value = true;
};

const applyHtml = () => {
    emit("update:modelValue", htmlBuffer.value);
    htmlDialog.value = false;
};

const formatHtml = (html: string): string => {
    if (!html) return "";
    
    // Add proper indentation to HTML using tabs
    const formatted = html
        .replace(/></g, '>\n<')
        .split('\n')
        .reduce((lines, line) => {
            const trimmed = line.trim();
            if (!trimmed) return lines;
            
            // Track current indentation level
            const currentIndent = lines.length > 0 ? 
                (lines[lines.length - 1].match(/^\t*/)?.[0]?.length || 0) : 0;
            
            let indentLevel = currentIndent;
            
            // Decrease indent for closing tags
            if (trimmed.startsWith('</')) {
                indentLevel = Math.max(0, currentIndent - 1);
            }
            
            // Add the indented line
            lines.push('\t'.repeat(indentLevel) + trimmed);
            
            // Increase indent for opening tags (except self-closing and closing tags)
            if (trimmed.startsWith('<') && 
                !trimmed.startsWith('</') && 
                !trimmed.endsWith('/>') && 
                !trimmed.includes('</') &&
                !trimmed.match(/^<[^>]*\/>/)) {
                // Next line will have increased indent
                return lines;
            }
            
            return lines;
        }, [] as string[])
        .join('\n');
    
    return formatted;
};

const copyHtml = async () => {
    try {
        await navigator.clipboard.writeText(formatHtml(props.modelValue ?? ""));
    } catch (error) {
        console.error("Failed to copy HTML:", error);
    }
};
</script>

<style scoped>

</style>
