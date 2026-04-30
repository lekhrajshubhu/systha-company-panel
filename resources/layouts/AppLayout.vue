<template>
    <v-layout class="app-layout">
        <v-navigation-drawer
            v-model="drawer"
            :temporary="!display.mdAndUp.value"
            width="260"
            class="app-drawer"
        >
            <div class="drawer-header px-5 py-3">
                <div class="d-flex align-center ga-3 mb-2">
                    <v-avatar color="primary" size="36">
                        <span class="text-caption font-weight-bold">{{
                            store.userInitials
                        }}</span>
                    </v-avatar>
                    <div>
                        <div class="text-body-2 font-weight-medium">
                            {{ store.userName }}
                        </div>
                        <div
                            v-if="
                                store.context === 'company' && store.user?.role
                            "
                        >
                            <div class="text-caption text-medium-emphasis">
                                {{ store?.user?.name }}
                                <v-chip
                                    label
                                    color="primary"
                                    size="x-small"
                                    class="ml-2"
                                >
                                    {{ store.user.role }}
                                </v-chip>
                            </div>
                        </div>
                        <div class="text-caption text-medium-emphasis" v-else>
                            {{ store.userEmail }}
                        </div>
                    </div>
                </div>
                <!-- <v-divider></v-divider> -->
                <!-- <v-text-field
                    v-model="menuQuery"
                    class="mt-4"
                    density="compact"
                    variant="outlined"
                    rounded="md"
                    hide-details
                    clearable
                    placeholder="Search menu..."
                    prepend-inner-icon="mdi-magnify"
                    @click:clear="resetMenuFilter"
                /> -->
            </div>

            <div class="drawer-scroll">
                <div
                    v-for="section in filteredMenuGroups"
                    :key="section.group"
                    class="mb-2"
                >
                    <div class="px-5 py-2" style="    font-weight: 700;
    font-size: 0.82rem;
    color: #878787;
    text-transform: uppercase;
    letter-spacing: 1.3px;">
                        {{ section.group }}
                    </div>
                    <v-list nav density="comfortable" class="py-0">
                        <v-list-item
                            v-for="item in section.items"
                            :key="item.routeName"
                            :to="{ name: item.routeName }"
                            :prepend-icon="item.icon"
                            :title="item.title"
                            rounded
                            color="primary"
                        />
                    </v-list>
                </div>
            </div>
        </v-navigation-drawer>

        <v-app-bar flat height="64" class="px-3">
            <div class="d-flex align-center ga-3" style="width: 400px;">
                <v-app-bar-nav-icon
                    :icon="drawer ? 'mdi-dock-left' : 'mdi-dock-right'"
                    @click="drawer = !drawer"
                />
                <!-- <div class="app-search-wrap">
                    <v-text-field
                        density="compact"
                        variant="outlined"
                        rounded="md"
                        hide-details
                        autocomplete="off"
                        clearable
                        style="max-width: 400px;"
                        placeholder="Search ..."
                        prepend-inner-icon="mdi-magnify"
                    />
                </div> -->
            </div>

            <v-spacer />

            <v-menu min-width="200px" rounded offset="4">
                <template #activator="{ props }">
                    <v-btn icon v-bind="props" class="mr-2">
                        <v-avatar color="primary" size="36">
                            <span class="text-caption font-weight-bold">{{
                                store.userInitials
                            }}</span>
                        </v-avatar>
                    </v-btn>
                </template>
                <v-card elevation="2" rounded="lg">
                    <v-list density="comfortable" class="py-2">
                        <v-list-item class="px-4">
                            <template #prepend>
                                <v-avatar
                                    color="primary"
                                    size="40"
                                    class="mr-3"
                                >
                                    <span
                                        class="text-subtitle-2 font-weight-bold"
                                        >{{ store.userInitials }}</span
                                    >
                                </v-avatar>
                            </template>
                            <v-list-item-title class="font-weight-bold">{{
                                store?.user?.name
                            }}</v-list-item-title>
                            <v-list-item-subtitle class="text-caption">
                                <span
                                    v-if="store.context === 'company' && store.user"
                                >
                                    <v-chip
                                        label
                                        v-if="store.user.role"
                                        color="primary"
                                        size="small"
                                        class="mr-2"
                                        >{{ store.user.role }}</v-chip
                                    >
                                    <span
                                        v-if="
                                            store.user.company &&
                                            store.user.company.name
                                        "
                                    >
                                        | {{ store.user.company.name }}</span
                                    >
                                </span>
                                <span v-else>
                                    {{ store.userEmail }}
                                </span>
                            </v-list-item-subtitle>
                        </v-list-item>

                        <v-divider class="my-2" />

                        <v-list-item
                            prepend-icon="mdi-account-outline"
                            title="My Profile"
                            rounded="md"
                            class="mx-2"
                        />
                        <v-list-item
                            prepend-icon="mdi-logout"
                            title="Logout"
                            rounded="md"
                            class="mx-2 text-error"
                            @click="store.logout"
                        />
                    </v-list>
                </v-card>
            </v-menu>
        </v-app-bar>

        <v-main class="app-main">
            <router-view />
        </v-main>

        <GlobalModal />
    </v-layout>
</template>

<script setup lang="ts">
import { computed, ref, watch } from "vue";
import GlobalModal from "@/pages/shared/GlobalModal.vue";
import { useRoute } from "vue-router";
import { useDisplay } from "vuetify";
import { useAppContextStore } from "@/stores/appContext";

const route = useRoute();
const display = useDisplay();
const store = useAppContextStore();

const drawer = ref(display.mdAndUp.value);
const menuQuery = ref("");

watch(
    () => display.mdAndUp.value,
    (desktop) => {
        drawer.value = desktop;
    },
);

const filteredMenuGroups = computed(() => {
    const q = menuQuery.value.trim().toLowerCase();
    if (!q) return store.menuGroups;

    return store.menuGroups
        .map((section) => ({
            group: section.group,
            items: section.items.filter((item) =>
                item.title.toLowerCase().includes(q),
            ),
        }))
        .filter((section) => section.items.length > 0);
});

const resetMenuFilter = () => {
    menuQuery.value = "";
};

const breadcrumbItems = computed(() => {
    const labels = (route.meta.breadcrumb as string[] | undefined) ?? [
        (route.meta.title as string) || "Panel",
    ];

    return labels.map((label, index) => ({
        title: label,
        disabled: index === labels.length - 1,
    }));
});
</script>

<style scoped lang="scss">
.app-layout {
    height: 100vh;
    overflow: hidden;
}

.app-main {
    background: linear-gradient(135deg, #f2f5f9, #f9f9f9);
    overflow-y: auto;
}

.app-drawer {
    border-right: 0;
    // background:
    //     repeating-linear-gradient(
    //         45deg,
    //         #ffffff17,
    //         #ffffff17 8px,
    //         #ffffff08 8px,
    //         #ffffff4d 16px
    //     ),
    //     linear-gradient(280deg, #fffffff5, #dfe7f2);

    :deep(.v-navigation-drawer__content) {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
}

.drawer-header {
    position: sticky;
    top: 0;
    z-index: 2;
    background: transparent;
}

.drawer-scroll {
    flex: 1;
    overflow-y: auto;
    padding: 14px !important;
    padding-top: 0 !important;
}

.toolbar-breadcrumb :deep(.v-breadcrumbs) {
    padding: 0;
}

.breadcrumb-item {
    color: #6e6e6e;
    // font-size: 0.875rem;
    font-weight: 500;
}

.breadcrumb-item--active {
    color: #2f3a47;
    font-weight: 600;
}

.breadcrumb-divider {
    color: #a1a1aa;
}

.v-navigation-drawer--temporary.v-navigation-drawer--active {
    box-shadow: none !important;
}

.app-search-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    width: 200px;
}

.app-search {
    max-width: 400px;
    width: 100%;
}
</style>
