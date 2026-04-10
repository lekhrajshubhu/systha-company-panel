<template>
    <v-card-text class="" elevation="0">
        <v-form ref="formRef" v-model="isValid" @submit.prevent="onSubmit">
            <div class="pt-6">
                <v-row dense>
                    <v-col cols="12" md="6">
                        <v-select
                            v-model="form.type"
                            :items="types"
                            label="Address Type"
                            density="comfortable"
                            variant="outlined"
                            :rules="[rules.required]"
                        />
                    </v-col>
                   
                    <v-col cols="12">
                        <v-text-field
                            v-model="form.line_1"
                            label="Address Line 1"
                            density="comfortable"
                            variant="outlined"
                            :rules="[rules.required]"
                        />
                    </v-col>
                    <v-col cols="12">
                        <v-text-field
                            v-model="form.line_2"
                            label="Address Line 2"
                            density="comfortable"
                            variant="outlined"
                        />
                    </v-col>
                    <v-col cols="12" md="5">
                        <v-text-field
                            v-model="form.city"
                            label="City"
                            density="comfortable"
                            variant="outlined"
                            :rules="[rules.required]"
                        />
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field
                            v-model="form.state"
                            label="State"
                            density="comfortable"
                            variant="outlined"
                        />
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field
                            v-model="form.zip"
                            label="Zip"
                            density="comfortable"
                            variant="outlined"
                        />
                    </v-col>
                   
                </v-row>
            </div>
        </v-form>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
        <div class="d-flex justify-space-around px-4 w-100">
            <v-btn color="primary" class="px-4" type="submit" variant="flat">
                <v-icon>mdi-content-save</v-icon> &nbsp;
                Save
            </v-btn>
        </div>
    </v-card-actions>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useModalStore } from "@/stores/modal";

const props = defineProps<{
    initialValues?: {
        type?: string;
        line_1?: string;
        line_2?: string;
        city?: string;
        state?: string;
        zip?: string;
    };
}>();

const emit = defineEmits<{
    (e: "submit", payload: Record<string, unknown>): void;
}>();

const modal = useModalStore();
const formRef = ref();
const isValid = ref(false);

const types = [
    { title: "Home", value: "home" },
    { title: "Office", value: "office" },
    { title: "Billing", value: "billing" },
    { title: "Shipping", value: "shipping" },
    { title: "Other", value: "other" },
];

const form = ref({
    type: props.initialValues?.type ?? "",
    line_1: props.initialValues?.line_1 ?? "",
    line_2: props.initialValues?.line_2 ?? "",
    city: props.initialValues?.city ?? "",
    state: props.initialValues?.state ?? "",
    zip: props.initialValues?.zip ?? "",
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
