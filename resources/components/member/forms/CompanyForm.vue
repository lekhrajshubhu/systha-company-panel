<template>
    <v-card-text class="" elevation="0">
        <v-form ref="formRef" v-model="isValid" @submit.prevent="onSubmit">
            <v-stepper v-model="step" elevation="0" class="company-stepper">
                <v-stepper-header>
                    <v-stepper-item :value="1" title="Company" />
                    <v-divider />
                    <v-stepper-item :value="2" title="Contact" />
                    <v-divider />
                    <v-stepper-item :value="3" title="Address" />
                </v-stepper-header>
                <v-stepper-window>
                    <v-stepper-window-item :value="1">
                        <div class="pt-4">
                            <v-row dense>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.name"
                                        label="Company Name"
                                        density="comfortable"
                                        variant="outlined"
                                        :rules="[rules.required]"
                                    />
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.dba_name"
                                        label="DBA Name"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                                
                                <v-col cols="6">
                                    <v-text-field
                                        v-model="form.registration_state"
                                        label="Registration State"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field
                                        v-model="form.license_number"
                                        label="License Number"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                                <!-- <v-col cols="4">
                                    <v-text-field
                                        v-model="form.ein"
                                        label="EIN"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                                <v-col cols="4">
                                    <v-text-field
                                        v-model="form.naics_code"
                                        label="NAICS Code"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col> -->
                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.entity_type"
                                        :items="entityTypes"
                                        label="Entity Type"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                                <!-- <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.tax_classification"
                                        :items="taxClassifications"
                                        label="Tax Classification"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col> -->
                                <!-- <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.status"
                                        :items="statuses"
                                        label="Status"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col> -->
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.industry"
                                        label="Industry"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                               
                            </v-row>
                        </div>
                    </v-stepper-window-item>

                    <v-stepper-window-item :value="2">
                        <div class="pt-6">
                            <v-row dense>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.email"
                                        label="Email"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.phone"
                                        label="Phone"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.website"
                                        label="Website"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                            </v-row>
                        </div>
                    </v-stepper-window-item>

                    <v-stepper-window-item :value="3">
                        <div class="pt-6">
                            <v-row dense>
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
                                <v-col cols="12" md="6">
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
                                <v-col cols="12" md="3">
                                    <v-text-field
                                        v-model="form.zip"
                                        label="Zip"
                                        density="comfortable"
                                        variant="outlined"
                                    />
                                </v-col>
                                <!-- <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.country"
                                        label="Country"
                                        density="comfortable"
                                        variant="outlined"
                                        :rules="[rules.required]"
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-switch
                                        v-model="form.is_primary"
                                        label="Primary"
                                        color="primary"
                                        inset
                                    />
                                </v-col> -->
                            </v-row>
                        </div>
                    </v-stepper-window-item>
                </v-stepper-window>
            </v-stepper>
        </v-form>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
        <div class="d-flex align-center justify-space-between px-4 w-100">
            <v-btn variant="text" :disabled="step === 1" @click="prevStep">
                Back
            </v-btn>
            <div class="d-flex ga-2">
                <v-btn
                    v-if="step < 3"
                    color="primary"
                    variant="flat"
                    @click="nextStep"
                >
                    Next
                </v-btn>
                <v-btn
                    v-else
                    color="primary"
                    class="px-4"
                    type="submit"
                    variant="flat"
                >
                    <v-icon>mdi-content-save</v-icon> &nbsp; Save
                </v-btn>
            </div>
        </div>
    </v-card-actions>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useModalStore } from "@/stores/modal";

const props = defineProps<{
    initialValues?: {
        name?: string;
        dba_name?: string;
        entity_type?: string;
        tax_classification?: string;
        registration_state?: string;
        naics_code?: string;
        license_number?: string;
        ein?: string;
        status?: string;
        email?: string;
        phone?: string;
        website?: string;
        industry?: string;
        notes?: string;
        line_1?: string;
        line_2?: string;
        city?: string;
        state?: string;
        zip?: string;
        country?: string;
        is_primary?: boolean;
    };
}>();

const emit = defineEmits<{
    (e: "submit", payload: Record<string, unknown>): void;
}>();

const modal = useModalStore();
const formRef = ref();
const isValid = ref(false);
const step = ref(1);

const form = ref({
    name: props.initialValues?.name ?? "",
    dba_name: props.initialValues?.dba_name ?? "",
    entity_type: props.initialValues?.entity_type ?? "",
    tax_classification: props.initialValues?.tax_classification ?? "",
    registration_state: props.initialValues?.registration_state ?? "",
    naics_code: props.initialValues?.naics_code ?? "",
    license_number: props.initialValues?.license_number ?? "",
    ein: props.initialValues?.ein ?? "",
    status: props.initialValues?.status ?? "active",
    email: props.initialValues?.email ?? "",
    phone: props.initialValues?.phone ?? "",
    website: props.initialValues?.website ?? "",
    industry: props.initialValues?.industry ?? "",
    notes: props.initialValues?.notes ?? "",
    line_1: props.initialValues?.line_1 ?? "",
    line_2: props.initialValues?.line_2 ?? "",
    city: props.initialValues?.city ?? "",
    state: props.initialValues?.state ?? "",
    zip: props.initialValues?.zip ?? "",
    country: props.initialValues?.country ?? "USA",
    is_primary: props.initialValues?.is_primary ?? false,
});

const entityTypes = [
    { title: "LLC", value: "llc" },
    { title: "Corporation", value: "corporation" },
    { title: "Sole Proprietor", value: "sole_proprietor" },
    { title: "Partnership", value: "partnership" },
    { title: "Nonprofit", value: "nonprofit" },
];

const taxClassifications = [
    { title: "C Corp", value: "c_corp" },
    { title: "S Corp", value: "s_corp" },
    { title: "Partnership", value: "partnership" },
    { title: "Sole Proprietor", value: "sole_proprietor" },
];

const statuses = [
    { title: "Active", value: "active" },
    { title: "Inactive", value: "inactive" },
    { title: "Suspended", value: "suspended" },
];

const rules = {
    required: (value: string) => (!!value ? true : "Required"),
};

const close = () => {
    modal.close();
};

const nextStep = async () => {
    const formEl = formRef.value;
    if (step.value === 1 || step.value === 3) {
        const result = await formEl?.validate?.();
        if (result?.valid === false) {
            return;
        }
    }
    step.value = Math.min(3, step.value + 1);
};

const prevStep = () => {
    step.value = Math.max(1, step.value - 1);
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
