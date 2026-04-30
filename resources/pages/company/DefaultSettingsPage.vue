<template>
    <v-container class="py-6 px-4 px-md-8" fluid>
        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-card class="pa-6" variant="flat">
                    <div class="text-h6 font-weight-bold mb-6">
                        Company Default Settings
                    </div>

                    <v-form @submit.prevent="onSubmit">
                        <v-row dense>
                            <v-col cols="12">
                                <div class="d-flex align-center ga-4 mb-4">
                                    <v-avatar color="grey-lighten-3" size="84">
                                        <v-img
                                            v-if="logoPreview"
                                            :src="logoPreview"
                                            cover
                                        />
                                        <v-icon
                                            v-else
                                            icon="mdi-domain"
                                            size="36"
                                        />
                                    </v-avatar>
                                    <div>
                                        <div
                                            class="text-subtitle-2 font-weight-medium mb-1"
                                        >
                                            Company Logo
                                        </div>
                                        <v-btn
                                            color="primary"
                                            variant="outlined"
                                            prepend-icon="mdi-image-edit"
                                            @click="logoInput?.click()"
                                        >
                                            Choose Logo
                                        </v-btn>
                                        <v-btn
                                            class="ml-2"
                                            color="primary"
                                            :loading="savingLogo"
                                            :disabled="loading || submitting || savingLogo || !form.logo"
                                            @click="onSaveLogo"
                                        >
                                            Save Logo
                                        </v-btn>
                                        <v-btn
                                            class="ml-2"
                                            color="error"
                                            variant="text"
                                            :loading="savingLogo"
                                            :disabled="loading || submitting || savingLogo || !initialLogoUrl"
                                            @click="onRemoveLogo"
                                        >
                                            Remove
                                        </v-btn>
                                        <input
                                            ref="logoInput"
                                            class="d-none"
                                            type="file"
                                            accept="image/*"
                                            @change="onLogoChange"
                                        />
                                    </div>
                                </div>
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.company_name"
                                    label="Company Name"
                                    variant="outlined"
                                    density="comfortable"
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.company_email"
                                    label="Company Email"
                                    type="email"
                                    variant="outlined"
                                    density="comfortable"
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.company_phone"
                                    label="Company Phone"
                                    variant="outlined"
                                    v-mask="'(###) ###-####'"
                                    density="comfortable"
                                />
                            </v-col>

                            <v-col cols="12" class="mt-2">
                                <div class="text-subtitle-1 font-weight-medium mb-2">
                                    Address
                                </div>
                            </v-col>

                            <v-col cols="12">
                                <v-text-field
                                    v-model="form.address.line_1"
                                    label="Line 1"
                                    variant="outlined"
                                    density="comfortable"
                                />
                            </v-col>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="form.address.line_2"
                                    label="Line 2"
                                    variant="outlined"
                                    density="comfortable"
                                />
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    v-model="form.address.city"
                                    label="City"
                                    variant="outlined"
                                    density="comfortable"
                                />
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    v-model="form.address.state"
                                    label="State"
                                    variant="outlined"
                                    density="comfortable"
                                />
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    v-model="form.address.zip"
                                    label="Zip"
                                    variant="outlined"
                                    density="comfortable"
                                />
                            </v-col>

                            <v-col cols="12" class="d-flex justify-end mt-2">
                                <v-btn
                                    color="primary"
                                    type="submit"
                                    :loading="submitting"
                                    :disabled="loading || submitting || savingLogo"
                                >
                                    <v-icon start icon="mdi-content-save" />
                                    Save
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup lang="ts">
import {
    type CompanyGeneralSettingsResponse,
    getCompanyGeneralSettings,
    updateCompanyGeneralSettings,
    updateCompanyLogo,
} from "@/services/generalSettings.api";
import { onBeforeUnmount, onMounted, reactive, ref } from "vue";

const form = reactive({
    logo: null as File | null,
    company_name: "",
    company_email: "",
    company_phone: "",
    address: {
        line_1: "",
        line_2: "",
        city: "",
        state: "",
        zip: "",
    },
});

const logoInput = ref<HTMLInputElement | null>(null);
const logoPreview = ref<string>("");
const initialLogoUrl = ref<string>("");
const logoObjectUrl = ref<string>("");
const loading = ref(false);
const submitting = ref(false);
const savingLogo = ref(false);

const revokeLogoObjectUrl = () => {
    if (logoObjectUrl.value) {
        URL.revokeObjectURL(logoObjectUrl.value);
        logoObjectUrl.value = "";
    }
};

const onLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    revokeLogoObjectUrl();
    form.logo = file;
    if (file) {
        logoObjectUrl.value = URL.createObjectURL(file);
        logoPreview.value = logoObjectUrl.value;
        return;
    }

    logoPreview.value = initialLogoUrl.value;
};

onBeforeUnmount(() => {
    revokeLogoObjectUrl();
});

const applySettings = (payload: CompanyGeneralSettingsResponse) => {
    const data = payload?.data ?? payload ?? {};

    const company = data?.company ?? {};
    const address = company?.address ?? data?.address ?? {};

    form.company_name = company?.name ?? data?.company_name ?? "";
    form.company_email = company?.email ?? data?.company_email ?? "";
    form.company_phone = company?.phone ?? data?.company_phone ?? "";
    form.address.line_1 = company?.address?.line1 ?? "";
    form.address.line_2 = company?.address?.line2 ?? "";
    form.address.city = company?.address?.city ?? "";
    form.address.state = company?.address?.state ?? "";
    form.address.zip = company?.address?.zip ?? "";
    form.logo = null;

    initialLogoUrl.value = data?.logo_url ?? "";
    logoPreview.value = initialLogoUrl.value;
};

const loadSettings = async () => {
    loading.value = true;
    try {
        const response = await getCompanyGeneralSettings();
        applySettings(response);
    } catch (error) {
        console.error("Failed to load default settings:", error);
    } finally {
        loading.value = false;
    }
};

const onSubmit = async () => {
    if (loading.value || submitting.value || savingLogo.value) return;

    submitting.value = true;
    try {
        const response = await updateCompanyGeneralSettings({
            company: {
                name: form.company_name,
                email: form.company_email,
                phone: form.company_phone,
                address: {
                    line1: form.address.line_1,
                    line2: form.address.line_2,
                    city: form.address.city,
                    state: form.address.state,
                    zip: form.address.zip,
                },
            },
        });

        applySettings(response);
    } catch (error) {
        console.error("Failed to update default settings:", error);
    } finally {
        submitting.value = false;
    }
};

const onSaveLogo = async () => {
    if (loading.value || submitting.value || savingLogo.value || !form.logo) return;

    savingLogo.value = true;
    try {
        const response = await updateCompanyLogo({
            logo: form.logo,
        });

        applySettings(response);
    } catch (error) {
        console.error("Failed to update company logo:", error);
    } finally {
        savingLogo.value = false;
    }
};

const onRemoveLogo = async () => {
    if (loading.value || submitting.value || savingLogo.value || !initialLogoUrl.value) return;

    savingLogo.value = true;
    try {
        const response = await updateCompanyLogo({
            remove_logo: true,
        });

        applySettings(response);
    } catch (error) {
        console.error("Failed to remove company logo:", error);
    } finally {
        savingLogo.value = false;
    }
};

onMounted(() => {
    loadSettings();
});
</script>
