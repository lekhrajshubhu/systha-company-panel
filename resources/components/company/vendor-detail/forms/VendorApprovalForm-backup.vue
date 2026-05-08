<template>
    <v-card-text class="" elevation="0">
        <v-form ref="formRef" v-model="isValid" @submit.prevent="onSubmit">
            <div class="pt-6 px-6">
                <v-row dense>
                    <v-col cols="12">
                        <v-radio-group
                            v-model="form.enableTrial"
                            label="Enable Trial?"
                            density="comfortable"
                            inline
                        >
                            <v-radio label="No" :value="false" />
                            <v-radio label="Yes" :value="true" />
                        </v-radio-group>
                    </v-col>
                </v-row>
                <v-row v-if="form.enableTrial" dense>
                    <v-col cols="12">
                        <app-select-field
                            v-model="form.trialDays"
                            label="Trial Days"
                            :clearable="false"
                            :items="trialDayOptions"
                            :rules="[rules.required]"
                        />
                    </v-col>
                    <v-col cols="12">
                        <div class="pt-2">
                            <div class="text-body-2 text-medium-emphasis">Trial Period:</div>
                            <div class="text-body-1 mt-1">
                                {{ formattedTrialStartDate }} to {{ formattedTrialEndDate }}
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </div>
        </v-form>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
        <div class="d-flex justify-space-around px-4 w-100">
            <app-flat-button icon="mdi-check" :loading="isApproving" @click="onSubmit">
                Approve
            </app-flat-button>  
        </div>
    </v-card-actions>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { useRoute } from "vue-router";
import { useModalStore } from "@/stores/modal";
import AppFlatButton from "@/components/AppFlatButton.vue";
import AppSelectField from "@/components/AppSelectField.vue";
import { applicationApproval } from "@/services/vendor-applications.api";

const emit = defineEmits<{
    (e: "success"): void;
}>();

const modal = useModalStore();
const route = useRoute();
const isApproving = ref(false);
const formRef = ref();
const isValid = ref(false);

const form = ref({
    enableTrial: false,
    trialDays: 7,
});

const trialDayOptions = [
    { label: '3 days', value: 3 },
    { label: '5 days', value: 5 },
    { label: '7 days', value: 7 },
    { label: '14 days', value: 14 },
    { label: '30 days', value: 30 },
    { label: '45 days', value: 45 },
];

const rules = {
    required: (value: string | number) => (!!value || value === 0 ? true : "Required"),
};

const trialStartDate = computed(() => {
    return new Date();
});

const trialEndDate = computed(() => {
    const endDate = new Date();
    endDate.setDate(endDate.getDate() + form.value.trialDays);
    return endDate;
});

const formattedTrialStartDate = computed(() => {
    return trialStartDate.value.toLocaleDateString('en-US', { 
        month: 'long', 
        day: 'numeric' 
    });
});

const formattedTrialEndDate = computed(() => {
    return trialEndDate.value.toLocaleDateString('en-US', { 
        month: 'long', 
        day: 'numeric' 
    });
});

const close = () => {
    modal.close();
};

const onSubmit = async () => {
    const formEl = formRef.value;
    
    const result = await formEl?.validate?.();

    if (result?.valid === false) {
        return;
    }

    isApproving.value = true;

    const payload = {
        enable_trial: form.value.enableTrial,
        trial_days: form.value.trialDays,
        trial_start_date: form.value.enableTrial ? trialStartDate.value.toISOString().split('T')[0] : null,
        trial_end_date: form.value.enableTrial ? trialEndDate.value.toISOString().split('T')[0] : null,
    };
    
    try {
        const idParam = route.params.id;
        if (idParam == null || Array.isArray(idParam)) {
            throw new Error("Missing vendor application id in route params.");
        }

        const id = Number(idParam);
        if (!Number.isInteger(id)) {
            throw new Error("Invalid vendor application id in route params.");
        }

        await applicationApproval(id, payload);

        emit("success");
        close();
    } catch (error) {
        console.error("Failed to approve vendor:", error);
        // TODO: Show error notification
    } finally {
        isApproving.value = false;
    }
};
</script>

<style scoped></style>
