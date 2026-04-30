<template>
  <v-container  fluid>
    <AppPageHeader title="Email Templates" subtitle="Manage your company's email templates for various notifications." />

    <v-container fluid>
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
                  label="Search Template"
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
                <v-btn color="primary" variant="tonal" @click="createEmailTemplate">New Template</v-btn>
              </v-col>
            </v-row>

            <v-list dense nav>
              <v-list-item
                v-for="item in filteredTemplates"
                :key="item.localKey"
                class="border-b pb-2"
                :class="{ 'bg-surface-2': selectedEmailTemplateKey === item.localKey }"
                @click="selectEmailTemplate(item)"
              >
                <v-list-item-content>
                  <v-list-item-title class="text-high-emphasis">{{ item.title || 'Untitled Template' }}</v-list-item-title>
                  <v-list-item-subtitle class="text-medium-emphasis">{{ item.code || '-' }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>

              <template v-if="!filteredTemplates.length">
                <v-list-item>
                  <v-list-item-content class="text-center">
                    <div class="text-medium-emphasis pa-6">No template found</div>
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list>
          </AppCard>
        </v-col>

        <v-col cols="12" md="8">
          <AppCard padding="pa-6" class="elevation-0">
            <div v-if="selectedEmailTemplate">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field v-model="selectedEmailTemplate.title" label="Title" variant="outlined" density="comfortable" />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="selectedEmailTemplate.subject" label="Subject" variant="outlined" density="comfortable" />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="selectedEmailTemplate.receiver" label="Receiver" variant="outlined" density="comfortable" placeholder="e.g. user,email" />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="selectedEmailTemplate.code"
                    label="Code"
                    variant="outlined"
                    density="comfortable"
                    hint="Used as slug"
                    persistent-hint
                    @update:model-value="onCodeInput"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="selectedEmailTemplate!.temp_json" label="Dynamic Fields" variant="outlined" density="comfortable" placeholder="value1,value2" />

                </v-col>
              </v-row>

              <v-divider class="my-4" />

              <RichText v-model="selectedEmailTemplate.content" :minHeight="360" />

              <div class="d-flex justify-end mt-4">
                <v-btn color="primary" :loading="saving" :disabled="saving || !selectedEmailTemplate.title || !selectedEmailTemplate.code" @click="saveEmailTemplate">
                  {{ selectedEmailTemplate.id ? 'Update Page' : 'Save Page' }}
                </v-btn>
              </div>
            </div>

            <div v-else class="text-center pa-12 text-medium-emphasis">
              <v-icon size="48" class="mb-4">mdi-file-document-outline</v-icon>
              <div class="text-h6">Create or select a template</div>
              <p class="text-body-2 text-disabled">Use New Template to start authoring your page content.</p>
            </div>
          </AppCard>
        </v-col>
      </v-row>
    </v-container>
  </v-container>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import AppCard from "@/components/AppCard.vue";
import RichText from "@/components/RichText.vue";
import { getEmailTemplates, getEmailTemplateDetail } from "@/services/emailTemplates.api";
import AppPageHeader from "@/components/AppPageHeader.vue";

type EmailTemplateItem = {
  id: number | null;
  localKey: string;
  title: string; // temp_name
  subject: string;
  receiver: string;
  code: string;
  content: string; // temp_html
  temp_json: string; // comma-separated values from detail
  tempJsonValues: string[];
};

const search = ref("");
const saving = ref(false);
const counter = ref(1);

const emailTemplates = ref<EmailTemplateItem[]>([]);
const selectedEmailTemplate = ref<EmailTemplateItem | null>(null);
const selectedEmailTemplateKey = ref<string | null>(null);

const filteredTemplates = computed(() => {
  const q = search.value.trim().toLowerCase();
  if (!q) return emailTemplates.value;

  return emailTemplates.value.filter((item) => {
    return (
      item.title.toLowerCase().includes(q) ||
      item.code.toLowerCase().includes(q) ||
      item.subject.toLowerCase().includes(q) ||
      item.receiver.toLowerCase().includes(q) ||
      item.content.toLowerCase().includes(q)
    );
  });
});

const createEmailTemplate = () => {
  const key = `new-${counter.value++}`;
  const item: EmailTemplateItem = {
    id: null,
    localKey: key,
    title: "",
    subject: "",
    receiver: "",
    code: "",
    content: "",
    temp_json: '',
    tempJsonValues: [],
  };

  emailTemplates.value.unshift(item);
  selectedEmailTemplate.value = { ...item };
  selectedEmailTemplateKey.value = item.localKey;
};

const selectEmailTemplate = (item: EmailTemplateItem) => {
  selectedEmailTemplate.value = { ...item };
  selectedEmailTemplateKey.value = item.localKey;
};

const resetSelection = () => {
  selectedEmailTemplate.value = null;
  selectedEmailTemplateKey.value = null;
};

const onCodeInput = (value: string) => {
  if (!selectedEmailTemplate.value) return;

  selectedEmailTemplate.value.code = value
    .trim()
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
};

const mapApiItemToTemplate = (item: any): EmailTemplateItem => ({
  id: item.id,
  localKey: `template-${item.id}`,
  title: item.temp_name ?? item.title ?? '',
  code: item.code ?? '',
  content: item.temp_html ?? item.content ?? "",
  subject: item.subject ?? '',
  receiver: item.receiver ?? '',
  temp_json: item.temp_json ?? '',
  tempJsonValues: Array.isArray(item.temp_json)
    ? item.temp_json
    : (item.temp_json || '')
        .split(',')
        .map((s: string) => s.trim())
        .filter(Boolean),
});

const loadEmailTemplates = async (selectedId?: number | null) => {
  try {
    const response: any = await getEmailTemplates();
    emailTemplates.value = Array.isArray(response?.data) ? response.data.map(mapApiItemToTemplate) : [];

    if (!emailTemplates.value.length) {
      selectedEmailTemplate.value = null;
      selectedEmailTemplateKey.value = null;
      return;
    }

    const targetId = selectedId ?? selectedEmailTemplate.value?.id ?? null;
    const found = targetId ? emailTemplates.value.find((item) => item.id === targetId) : null;
    const target = found ?? emailTemplates.value[0];
    selectedEmailTemplate.value = { ...target };
    selectedEmailTemplateKey.value = target.localKey;
  } catch (error) {
    console.error("Failed to load email templates:", error);
  }
};

const saveEmailTemplate = async () => {
  if (!selectedEmailTemplate.value || saving.value) return;

  saving.value = true;
  try {
    const payload = {
      temp_name: selectedEmailTemplate.value.title,
      code: selectedEmailTemplate.value.code,
      temp_html: selectedEmailTemplate.value.content || null,
      subject: selectedEmailTemplate.value.subject || null,
      receiver: selectedEmailTemplate.value.receiver || null,
      temp_json: selectedEmailTemplate.value.temp_json || (selectedEmailTemplate.value.tempJsonValues || []).join(','),
    };

    if (selectedEmailTemplate.value.id) {
      const updated: any = await getEmailTemplateDetail(selectedEmailTemplate.value.id);
      const updatedId = (updated?.data?.id) ?? updated?.id ?? selectedEmailTemplate.value.id;
      await loadEmailTemplates(updatedId);
    } else {
      await loadEmailTemplates();
    }
  } catch (error) {
    console.error("Failed to save email template:", error);
  } finally {
    saving.value = false;
  }
};


onMounted(() => {
  loadEmailTemplates();
});
</script>
