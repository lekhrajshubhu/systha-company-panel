<template>
    <v-card-text class="" elevation="0">
        <div>
            <div class="text-body-1">Are you sure you want to approve the application for {{ vendorName }}?</div>
        </div>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
        <div class="d-flex justify-end px-4 w-100">
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
import { applicationApproval } from "@/services/vendor-applications.api";

const props = defineProps<{ vendor?: { name?: string } }>();

const vendorName = computed(() => props.vendor?.name || 'this vendor');

const emit = defineEmits<{
    (e: "success"): void;
}>();

const modal = useModalStore();
const route = useRoute();
const isApproving = ref(false);

const close = () => {
    modal.close();
};

const onSubmit = async () => {
    isApproving.value = true;

    const payload = {
        enable_trial: false,
        trial_days: null,
        trial_start_date: null,
        trial_end_date: null,
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
