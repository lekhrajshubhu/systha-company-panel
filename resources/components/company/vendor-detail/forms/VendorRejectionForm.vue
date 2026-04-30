<template>
    <v-card-text class="" elevation="0">
        <v-form ref="formRef" v-model="isValid" @submit.prevent="onSubmit">
            <div class="pt-6 px-6">
                <v-row dense>
                    <v-col cols="12">
                        <v-textarea
                            v-model="form.notes"
                            label="Rejection Notes"
                            density="comfortable"
                            variant="outlined"
                            rows="4"
                            :rules="[rules.required]"
                            placeholder="Enter the reason for rejection..."
                        />
                    </v-col>
                </v-row>
            </div>
        </v-form>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
        <div class="d-flex justify-space-around px-4 w-100">
            <v-btn color="error" class="px-4" type="submit" variant="flat">
                <v-icon>mdi-close</v-icon> &nbsp; Reject
            </v-btn>
        </div>
    </v-card-actions>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useModalStore } from "@/stores/modal";

const emit = defineEmits<{
    (e: "submit", payload: Record<string, unknown>): void;
}>();

const modal = useModalStore();
const formRef = ref();
const isValid = ref(false);

const form = ref({
    notes: "",
});

const rules = {
    required: (value: string) => (!!value ? true : "Required"),
};

const close = () => {
    modal.close();
};

const onSubmit = async () => {
    const formEl = formRef.value;
    const result = await formEl?.validate?.();

    if (result?.valid === false) {
        return;
    }

    emit("submit", { ...form.value });
    close();
};
</script>

<style scoped></style>
