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
                                <div class="d-flex align-center ga-4">
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
                                            Upload Logo
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
                                <v-btn color="primary" type="submit">
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
import { onBeforeUnmount, reactive, ref } from "vue";

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

const onLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    if (logoPreview.value) {
        URL.revokeObjectURL(logoPreview.value);
    }
    form.logo = file;
    logoPreview.value = file ? URL.createObjectURL(file) : "";
};

onBeforeUnmount(() => {
    if (logoPreview.value) {
        URL.revokeObjectURL(logoPreview.value);
    }
});

const onSubmit = () => {
    // Hook this payload into your API/action.
    console.log("Default settings payload", form);
};
</script>
