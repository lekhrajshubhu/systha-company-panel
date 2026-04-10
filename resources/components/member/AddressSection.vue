<template>
    <v-card class="pa-4 elevation-0">
        <div class="d-flex align-center justify-space-between mb-3">
            <div class="text-subtitle-2">ADDRESS</div>
            <v-btn
                icon
                size="small"
                variant="flat"
                color="primary"
                prepend-icon="mdi-plus"
                @click="openAddressModal"
            >
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </div>
        <v-divider></v-divider>
        <v-list density="compact" class="mt-2">
            <template v-for="(address, index) in addresses" :key="index">
                <v-list-item class="px-0">
                    <template #prepend>
                        <v-avatar size="28" color="primary" variant="tonal" rounded>
                            <v-icon size="16">{{ address.icon }}</v-icon>
                        </v-avatar>
                    </template>
                    <template #append>
                        <v-btn
                            icon
                            variant="text"
                            size="small"
                            @click="openAddressModal(address)"
                        >
                            <v-icon size="18">mdi-square-edit-outline</v-icon>
                        </v-btn>
                    </template>
                    <div class="d-flex flex-column ml-4">
                        <div>
                            <v-chip size="x-small" label color="primary">
                                {{ address.label }}
                            </v-chip>
                        </div>
                        <div class="text-body-2">
                            {{ address.line1 }}
                            <span v-if="address.line2"
                                >, {{ address.line2 }}</span
                            >
                        </div>
                        <div class="text-caption text-medium-emphasis">
                            {{ address.city }}, {{ address.state }}
                            {{ address.zip }}
                        </div>
                    </div>
                </v-list-item>
                <v-divider v-if="index < addresses.length - 1" class="my-2" />
            </template>
        </v-list>
    </v-card>
</template>

<script setup lang="ts">
import AddressForm from "@/components/member/forms/AddressForm.vue";
import { useModalStore } from "@/stores/modal";

interface AddressItem {
    label: string;
    icon: string;
    line1: string;
    line2?: string;
    city: string;
    state: string;
    zip: string;
}

defineProps<{
    addresses: AddressItem[];
}>();

const modal = useModalStore();

const openAddressModal = (address?: AddressItem) => {
    const initialValues = address
        ? {
              type: address.label
                  ? address.label.toLowerCase()
                  : "",
              line_1: address.line1,
              line_2: address.line2 ?? "",
              city: address.city,
              state: address.state,
              zip: address.zip,
          }
        : {
              type: "",
              line_1: "",
              line_2: "",
              city: "",
              state: "",
              zip: "",
          };

    modal.open(
        AddressForm,
        {
            initialValues,
            onSubmit: (payload: Record<string, unknown>) => {
                console.log("Address submitted", payload);
            },
        },
        {
            title: "Add Address",
            showHeader: true,
            fullscreen: false,
            maxWidth: 620,
        },
    );
};
</script>
