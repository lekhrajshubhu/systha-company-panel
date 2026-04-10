<template>
    <v-card-text class="" elevation="0">
        <div class="pt-2">
            <template
                v-for="(category, index) in serviceCategories"
                :key="category.title"
            >
                <v-subheader class="text-subtitle-2">
                    {{ category.title }}
                </v-subheader>
                <v-row dense class="mt-2">
                    <v-col
                        v-for="service in category.services"
                        :key="service.slug"
                        cols="12"
                        sm="6"
                    >
                        <v-checkbox
                            v-model="selectedSlugs"
                            :value="service.slug"
                            density="compact"
                        >
                            <template #label>
                                <div class="d-flex align-start ga-2">
                                    <v-avatar
                                        size="32"
                                        rounded
                                        :color="service.color || 'primary'"
                                        variant="tonal"
                                    >
                                        <v-icon size="24">{{ service.icon }}</v-icon>
                                    </v-avatar>
                                    <div class="d-flex flex-column">
                                        <span class="text-body-2">
                                            {{ service.name }}
                                        </span>
                                        <span
                                            class="text-caption text-medium-emphasis"
                                        >
                                            {{
                                                formatDurationMinutes(
                                                    service.duration,
                                                )
                                            }}
                                            • ${{ service.base_price }}
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </v-checkbox>
                    </v-col>
                </v-row>
                <v-divider
                    v-if="index < serviceCategories.length - 1"
                    class="my-2"
                />
            </template>
        </div>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
        <div class="d-flex justify-space-around px-4 w-100">
            <v-btn
                color="primary"
                class="px-4"
                type="button"
                variant="flat"
                @click="onSubmit"
            >
                <v-icon>mdi-content-save</v-icon> &nbsp; Save
            </v-btn>
        </div>
    </v-card-actions>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useModalStore } from "@/stores/modal";
import { formatDurationMinutes } from "@/utils";

interface ServiceItem {
    name: string;
    slug: string;
    icon: string;
    color: string;
    duration: number;
    base_price: number;
}

interface ServiceCategory {
    title: string;
    services: ServiceItem[];
}

const emit = defineEmits<{
    (e: "submit", payload: Record<string, unknown>): void;
}>();

const modal = useModalStore();

const serviceCategories: ServiceCategory[] = [
    {
        title: "Pest Control",
        services: [
            {
                name: "Termite Control",
                slug: "termite-control",
                icon: "mdi-home-search-outline",
                color: "#0f766e",
                duration: 60,
                base_price: 120,
            },
            {
                name: "Ant Treatment",
                slug: "ant-treatment",
                icon: "mdi-bug",
                color: "#0f766e",
                duration: 45,
                base_price: 90,
            },
        ],
    },
    {
        title: "Cleaning",
        services: [
            {
                name: "Deep Home Cleaning",
                slug: "deep-home-cleaning",
                icon: "mdi-spray-bottle",
                color: "#2563eb",
                duration: 240,
                base_price: 320,
            },
            {
                name: "Move Out Cleaning",
                slug: "move-out-cleaning",
                icon: "mdi-spray-bottle",
                color: "#2563eb",
                duration: 300,
                base_price: 380,
            },
        ],
    },
];

const selectedSlugs = ref<string[]>([]);

const close = () => {
    modal.close();
};

const onSubmit = () => {
    emit("submit", { selected_slugs: selectedSlugs.value });
    close();
};
</script>

<style scoped></style>
