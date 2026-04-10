<template>
    <div>
        <v-card class="pa-4 elevation-0">
            <div class="d-flex ga-4">
                <div class="profile-image">
                    <v-btn
                        icon
                        size="x-small"
                        variant="tonal"
                        class="profile-edit"
                    >
                        <v-icon size="16">mdi-pencil</v-icon>
                    </v-btn>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-start justify-space-between">
                        <div class="text-h6">
                            {{ customer?.name || "-" }}
                        </div>
                        <v-btn icon size="x-small" variant="text" @click="openProfileForm">
                            <v-icon size="18">mdi-square-edit-outline</v-icon>
                        </v-btn>
                    </div>
                    <div
                        class="d-flex align-center text-caption text-medium-emphasis mb-1"
                    >
                        <v-icon size="14" class="mr-1">
                            mdi-map-marker-outline
                        </v-icon>
                        <span>300 Harold Square, Oak Park, TX, 78197-6597</span>
                    </div>
                    <div
                        class="d-flex align-center text-caption text-medium-emphasis mb-1"
                    >
                        <v-icon size="14" class="mr-1">
                            mdi-phone-outline
                        </v-icon>
                        <span>{{ customer?.phone || "-" }}</span>
                    </div>

                    <div class="d-flex align-center text-caption text-medium-emphasis">
                        <v-icon size="14" class="mr-1">
                            mdi-email-outline
                        </v-icon>
                        <span>{{ customer?.email || "-" }} (Primary)</span>
                    </div>
                    <div class="mt-3">
                        <v-chip
                            size="x-small"
                            variant="tonal"
                            label
                            :color="
                                customer?.status === 'active'
                                    ? 'success'
                                    : 'grey-darken-1'
                            "
                        >
                            {{ customer?.status || '-' }}
                        </v-chip>
                    </div>
                    <div class="mt-3">
                        <v-btn
                            size="small"
                            variant="tonal"
                            color="primary"
                            @click="openPasswordForm"
                            prepend-icon="mdi-lock-reset"
                        >
                            Update Password
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-card>
    </div>
</template>

<script setup lang="ts">

import CustomerPasswordForm from "@/components/customer/forms/CustomerPasswordForm.vue";
import CustomerProfileForm from "@/components/customer/forms/CustomerProfileForm.vue";
import { useModalStore } from "@/stores/modal";

interface CustomerDetail {
    id: number;
    customer_id: number;
    name: string | null;
    email: string;
    phone: string | null;
    status: string;
    last_login_at: string | null;
}

const props = defineProps<{ customer: CustomerDetail | null }>();



const modal = useModalStore();

const openPasswordForm = () => {
    modal.open(
        CustomerPasswordForm,
        {
            onSubmit: (payload: Record<string, unknown>) => {
                console.log("Address submitted", payload);
            },
        },
        {
            title: "Update Password",
            showHeader: true,
            fullscreen: false,
            maxWidth: 600,
        }
    );
};
const splitName = (name: string | null): { first: string; last: string } => {
    if (!name) return { first: "", last: "" };
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) {
        return { first: parts[0], last: "" };
    }
    return { first: parts[0], last: parts.slice(1).join(" ") };
};

const openProfileForm = () => {
    const nameParts = splitName(props.customer?.name ?? null);
    const email = props.customer?.email ?? "";
    const username = email ? email.split("@")[0] : "";

    modal.open(
        CustomerProfileForm,
        {
            initialValues: {
                first_name: nameParts.first,
                last_name: nameParts.last,
                username,
                email,
            },
            onSubmit: (payload: Record<string, unknown>) => {
                console.log("Address submitted", payload);
            },
        },
        {
            title: "Update Profile",
            showHeader: true,
            fullscreen: false,
            maxWidth: 500,
        }
    );
};

</script>

<style scoped>
.profile-image {
    width: 84px;
    height: 84px;
    border-radius: 12px;
    background: #f3f4f6;
    position: relative;
    flex: 0 0 auto;
}

.profile-edit {
    position: absolute;
    top: -8px;
    right: -8px;
}
</style>
