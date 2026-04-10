<template>
    <v-card class="pa-4 elevation-0 rounded">
        <div class="d-flex align-center justify-space-between mb-3">
            <div class="text-subtitle-2">ASSIGNED SERVICES</div>
            <v-btn
                icon
                size="small"
                variant="flat"
                color="primary"
                prepend-icon="mdi-plus"
                @click="openAssignServiceForm"
            >
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </div>
        <v-divider></v-divider>

        <v-list density="compact" class="mt-2">
            <template v-for="(service, index) in services" :key="index">
                <v-list-item class="px-0">
                    <template #prepend>
                        <v-avatar size="28" :color="service.color || 'primary'" variant="tonal" rounded>
                            <v-icon size="16">{{ service.icon }}</v-icon>
                        </v-avatar>
                    </template>

                    <div class="d-flex flex-column ml-4 w-100">
                        <div class="d-flex align-center flex-wrap ga-2">
                            <div class="text-body-2 font-weight-medium">
                                {{ service.name }}
                            </div>
                        </div>

                        <div class="text-caption text-medium-emphasis">
                            <span
                                v-if="
                                    service.duration !== undefined &&
                                    service.duration !== null
                                "
                            >
                                {{ formatDurationMinutes(service.duration) }}
                            </span>
                            <span v-if="service.duration && service.base_price">
                                •
                            </span>
                            <span v-if="service.base_price">
                                ${{ service.base_price }}
                            </span>
                        </div>
                    </div>
                </v-list-item>

                <v-divider v-if="index < services.length - 1" class="my-2" />
            </template>
        </v-list>
    </v-card>
</template>

<script setup lang="ts">
import { formatDurationMinutes } from "@/utils";
import AssignServiceList from "@/components/member/forms/AssignServiceList.vue";
import { useModalStore } from "@/stores/modal";
const modal = useModalStore();
interface ServiceItem {
    name: string;
    icon: string;
    color?: string;
    slug: string;
    duration?: number;
    base_price?: number;
}

defineProps<{
    services: ServiceItem[];
}>();

const openAssignServiceForm = () => {
    modal.open(
        AssignServiceList,
        {
            onSubmit: (payload: Record<string, unknown>) => {
                console.log("Services submitted", payload);
            },
        },
        {
            title: "Assign Services",
            showHeader: true,
            fullscreen: false,
            maxWidth: 800,
        },
    );
};
</script>
