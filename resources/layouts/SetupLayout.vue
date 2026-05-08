<template>
    <v-layout class="app-layout">
        <v-app-bar flat height="64" class="px-3">
 
                <div class="d-flex align-center ga-3">
                    <v-avatar color="primary" size="36">
                        <v-icon>mdi-domain</v-icon>
                    </v-avatar>
                    <div>
                        <div class="text-subtitle-1 font-weight-bold">
                            {{ store.user?.company?.name || 'Company Setup' }}
                        </div>
                        <div class="text-caption text-medium-emphasis">
                            {{ store.user?.role || 'Administrator' }}
                        </div>
                    </div>
                </div>
    
                <v-spacer />
                <v-tooltip v-if="hasMultipleContexts" location="bottom">
                    <template #activator="{ props }">
                        <v-btn
                            v-bind="props"
                            variant="tonal"
                            class="mr-1"
                            color="primary"
                            aria-label="Switch context"
                            @click="goToContextSelect"
                        >
                        switch company <v-icon>mdi-swap-horizontal</v-icon>
                    </v-btn>
                    </template>
                    <span>Switch context</span>
                </v-tooltip>
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
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import GlobalModal from "@/pages/shared/GlobalModal.vue";
import { useRoute, useRouter } from "vue-router";
import { useDisplay } from "vuetify";
import { useAppContextStore } from "@/stores/appContext";
import {
    AUTH_ACTIVE_CONTEXT_KEY,
    AUTH_CONTEXTS_KEY,
    AUTH_PENDING_CONTEXT_SELECTION_KEY,
    type LoginContext,
} from "@/services/auth.api";

const route = useRoute();
const router = useRouter();
const display = useDisplay();
const store = useAppContextStore();

const drawer = ref(display.mdAndUp.value);
const menuQuery = ref("");

const authVersion = ref(0);

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

const hasMultipleContexts = computed(() => {
    void authVersion.value;
    const stored = localStorage.getItem(AUTH_CONTEXTS_KEY);
    console.log({stored})
    if (!stored) return false;
    try {
        const contexts = JSON.parse(stored) as LoginContext[];
        return contexts.length > 1;
    } catch {
        return false;
    }
});

const goToContextSelect = async () => {
    localStorage.removeItem(AUTH_ACTIVE_CONTEXT_KEY);
    localStorage.setItem(AUTH_PENDING_CONTEXT_SELECTION_KEY, "true");
    try {
        const existingAccountStr = localStorage.getItem("companypanel_account");
        if (existingAccountStr) {
            const existingAccount = JSON.parse(existingAccountStr);
            if (existingAccount && typeof existingAccount === "object") {
                delete existingAccount.company;
                localStorage.setItem(
                    "companypanel_account",
                    JSON.stringify(existingAccount),
                );
            }
        }
    } catch {
        // ignore
    }
    authVersion.value++;
    await router.push({ name: "company.context-select" });
};

const onAuthChanged = () => {
    authVersion.value++;
    store.syncFromLocalStorage();
};

onMounted(() => {
    store.syncFromLocalStorage();
    window.addEventListener("auth-changed", onAuthChanged);
});

onBeforeUnmount(() => {
    window.removeEventListener("auth-changed", onAuthChanged);
});

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
