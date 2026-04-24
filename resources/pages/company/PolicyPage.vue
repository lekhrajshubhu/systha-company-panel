<template>
  <v-container class="py-6 px-14" fluid>
    <h2 class="pa-0">Pages</h2>
    <v-card-subtitle class="pa-0">Manage pages for your company.</v-card-subtitle>

    <div padding="pa-4" class="mb-6">
      <v-row>
        <v-col cols="12" md="4">
          <AppCard padding="pa-6" class="elevation-0">
            <v-row align="center">
              <v-col cols="12">
                <v-text-field
                  v-model="search"
                  density="compact"
                  variant="outlined"
                  prepend-inner-icon="mdi-magnify"
                  label="Search policies"
                  clearable
                  hide-details
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="auto">
                <v-btn icon="mdi-refresh" variant="text" color="medium-emphasis" @click="resetSelection" />
              </v-col>
              <v-spacer />
              <v-col cols="auto">
                <v-btn color="primary" variant="tonal" @click="createPolicy">New Policy</v-btn>
              </v-col>
            </v-row>

            <v-list dense nav>
              <v-list-item
                v-for="item in filteredItems"
                :key="item.localKey"
                class="border-b pb-2"
                :class="{ 'bg-surface-2': selectedItemKey === item.localKey }"
                @click="selectItem(item)"
              >
                <v-list-item-content>
                  <v-list-item-title class="text-high-emphasis">{{ item.title || 'Untitled Policy' }}</v-list-item-title>
                  <v-list-item-subtitle class="text-medium-emphasis">{{ item.code || '-' }}</v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action>
                  <v-chip size="small" variant="tonal" :color="item.status === 'published' ? 'success' : 'grey-darken-1'">
                    {{ item.status }}
                  </v-chip>
                </v-list-item-action>
              </v-list-item>

              <template v-if="!filteredItems.length">
                <v-list-item>
                  <v-list-item-content class="text-center">
                    <div class="text-medium-emphasis pa-6">No policies found</div>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list>
          </AppCard>
        </v-col>

        <v-col cols="12" md="8">
          <AppCard padding="pa-6" class="elevation-0">
            <div v-if="selectedItem">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field v-model="selectedItem.title" label="Title" variant="outlined" density="comfortable" />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="selectedItem.subtitle" label="Subtitle" variant="outlined" density="comfortable" />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="selectedItem.code"
                    label="Code"
                    variant="outlined"
                    density="comfortable"
                    hint="Used as slug"
                    persistent-hint
                    @update:model-value="onCodeInput"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="selectedItem.status"
                    :items="statusOptions"
                    label="Status"
                    variant="outlined"
                    density="comfortable"
                  />
                </v-col>
              </v-row>

              <v-divider class="my-4" />

              <RichText v-model="selectedItem.content" :minHeight="360" />

              <div class="d-flex justify-end mt-4">
                <v-btn color="primary" :loading="saving" :disabled="saving || !selectedItem.title || !selectedItem.code" @click="savePolicy">
                  {{ selectedItem.id ? 'Update Page' : 'Save Page' }}
                </v-btn>
              </div>
            </div>

            <div v-else class="text-center pa-12 text-medium-emphasis">
              <v-icon size="48" class="mb-4">mdi-file-document-outline</v-icon>
              <div class="text-h6">Create or select a policy</div>
              <p class="text-body-2 text-disabled">Use New Policy to start authoring your page content.</p>
            </div>
          </AppCard>
        </v-col>
      </v-row>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import AppCard from "@/components/AppCard.vue";
import RichText from "@/components/RichText.vue";
import { createCompanyPage, getCompanyPages, updateCompanyPage } from "@/services/pages.api";

type PolicyItem = {
  id: number | null;
  localKey: string;
  title: string;
  subtitle: string;
  code: string;
  content: string;
  status: "published" | "draft";
};

const search = ref("");
const saving = ref(false);
const statusOptions: Array<"published" | "draft"> = ["published", "draft"];
const counter = ref(1);

const policies = ref<PolicyItem[]>([]);
const selectedItem = ref<PolicyItem | null>(null);
const selectedItemKey = ref<string | null>(null);

const filteredItems = computed(() => {
  const q = search.value.trim().toLowerCase();
  if (!q) return policies.value;

  return policies.value.filter((item) => {
    return (
      item.title.toLowerCase().includes(q) ||
      item.code.toLowerCase().includes(q) ||
      item.subtitle.toLowerCase().includes(q)
    );
  });
});

const createPolicy = () => {
  const key = `new-${counter.value++}`;
  const item: PolicyItem = {
    id: null,
    localKey: key,
    title: "",
    subtitle: "",
    code: "",
    content: "",
    status: "draft",
  };

  policies.value.unshift(item);
  selectedItem.value = { ...item };
  selectedItemKey.value = item.localKey;
};

const selectItem = (item: PolicyItem) => {
  selectedItem.value = { ...item };
  selectedItemKey.value = item.localKey;
};

const resetSelection = () => {
  selectedItem.value = null;
  selectedItemKey.value = null;
};

const onCodeInput = (value: string) => {
  if (!selectedItem.value) return;

  selectedItem.value.code = value
    .trim()
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
};

const mapApiItemToPolicy = (item: {
  id: number;
  title: string;
  subtitle: string | null;
  slug: string;
  content: string | null;
  status: "published" | "draft";
}): PolicyItem => ({
  id: item.id,
  localKey: `page-${item.id}`,
  title: item.title,
  subtitle: item.subtitle ?? "",
  code: item.slug,
  content: item.content ?? "",
  status: item.status,
});

const loadPolicies = async (selectedId?: number | null) => {
  try {
    const response = await getCompanyPages();
    policies.value = Array.isArray(response?.data) ? response.data.map(mapApiItemToPolicy) : [];

    if (!policies.value.length) {
      selectedItem.value = null;
      selectedItemKey.value = null;
      return;
    }

    const targetId = selectedId ?? selectedItem.value?.id ?? null;
    const found = targetId ? policies.value.find((item) => item.id === targetId) : null;
    const target = found ?? policies.value[0];
    selectedItem.value = { ...target };
    selectedItemKey.value = target.localKey;
  } catch (error) {
    console.error("Failed to load company pages:", error);
  }
};

const savePolicy = async () => {
  if (!selectedItem.value || saving.value) return;

  saving.value = true;
  try {
    const payload = {
      title: selectedItem.value.title,
      subtitle: selectedItem.value.subtitle || null,
      slug: selectedItem.value.code,
      content: selectedItem.value.content || null,
      status: selectedItem.value.status,
    };

    const response = selectedItem.value.id
      ? await updateCompanyPage(selectedItem.value.id, payload)
      : await createCompanyPage(payload);

    if (response?.data) {
      await loadPolicies(response.data.id);
    }
  } catch (error) {
    console.error("Failed to save company page:", error);
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadPolicies();
});
</script>
