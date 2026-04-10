<template>
    <v-container class="py-6 px-14" fluid>
        <v-card class="pa-0" variant="flat">
            <v-card class="pa-4 mb-6" variant="flat">
                <div class="d-flex align-center ga-4">
                    <v-avatar color="primary" size="64" variant="tonal">
                        <v-icon icon="mdi-domain" size="32" />
                    </v-avatar>
                    <div class="flex-grow-1">
                        <div class="text-h6 font-weight-bold">Company Name</div>
                        <div class="text-body-2 text-medium-emphasis">
                            123 Main St, Suite 400 • New York, NY 10001
                        </div>
                    </div>
                    <v-btn variant="outlined" color="primary"
                        >Change Logo</v-btn
                    >
                </div>
            </v-card>

            <v-tabs v-model="activeTab" density="comfortable">
                <v-tab value="overview">Overview</v-tab>
                <v-tab value="address">Address</v-tab>
                <v-tab value="social">Social Profile</v-tab>
                <v-tab value="mail">Mail Credentials</v-tab>
                <v-tab value="seo">SEO</v-tab>
            </v-tabs>

            <v-divider />

            <v-tabs-window v-model="activeTab" class="mt-4">
                <v-tabs-window-item value="overview">
                    <v-container>
                        <v-row>
                            <v-col
                                cols="12"
                                lg="8"
                                offset-lg="2"
                                md="10"
                                offset-md="1"
                            >
                                <div>
                                    <v-card class="pa-4" variant="flat">
                                         <div class="mb-4 d-flex align-center justify-space-between">
                                            <div>
                                                <h3>About Company</h3>
                                                <p>Configure the company information.</p>
                                            </div>
                                        
                                            <div>
                                                <v-btn color="primary" variant="flat"> <v-icon>mdi-content-save</v-icon> Save</v-btn>
                                            </div>
                                        </div>
                                        <v-row dense class="mt-2">
                                            <v-col cols="12" md="6">
                                                <v-text-field
                                                    v-model="overview.name"
                                                    label="Name"
                                                    density="comfortable"
                                                    variant="outlined"
                                                />
                                            </v-col>
                                            <v-col cols="12" md="6">
                                                <v-text-field
                                                    v-model="overview.email"
                                                    label="Email"
                                                    density="comfortable"
                                                    variant="outlined"
                                                />
                                            </v-col>
                                            <v-col cols="12" md="6">
                                                <v-text-field
                                                    v-model="overview.phone"
                                                    label="Phone"
                                                    density="comfortable"
                                                    variant="outlined"
                                                />
                                            </v-col>
                                            <v-col cols="12">
                                                <v-textarea
                                                    v-model="
                                                        overview.description
                                                    "
                                                    label="Short Description"
                                                    variant="outlined"
                                                    rows="3"
                                                />
                                            </v-col>
                                        </v-row>
                                    </v-card>
                                </div>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-tabs-window-item>

                <v-tabs-window-item value="address">
                    <v-container>
                        <v-row>
                            <v-col
                                cols="12"
                                lg="8"
                                offset-lg="2"
                                md="10"
                                offset-md="1"
                            >
                                <div>
                                    <v-card class="pa-4" variant="flat">
                                         <div class="mb-4 d-flex align-center justify-space-between">
                                            <div>
                                                <h3>Address Information</h3>
                                                <p>Company address information</p>
                                            </div>
                                        
                                            <div>
                                                <v-btn color="primary" variant="flat"> <v-icon>mdi-content-save</v-icon> Save</v-btn>
                                            </div>
                                        </div>
                                        <v-row dense class="mt-2">
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Line 1</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                address.line1
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Line 2</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                address.line2
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >City</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                address.city
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >State</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                address.state
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Zip</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                address.zip
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Country</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                address.country
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                        </v-row>
                                    </v-card>
                                </div>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-tabs-window-item>

                <v-tabs-window-item value="social">
                    <v-container>
                        <v-row>
                            <v-col
                                cols="12"
                                lg="8"
                                offset-lg="2"
                                md="10"
                                offset-md="1"
                            >
                                <div>
                                    <v-card class="pa-4" variant="flat">
                                         <div class="mb-4 d-flex align-center justify-space-between">
                                            <div>
                                                <h3>Social Profiles</h3>
                                                <p>Configure the social profiles for your company.</p>
                                            </div>
                                        
                                            <div>
                                                <v-btn color="primary" variant="flat"> <v-icon>mdi-content-save</v-icon> Save</v-btn>
                                            </div>
                                        </div>
                                        <v-row dense class="mt-2">
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Facebook</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                social.facebook
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Twitter</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                social.twitter
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >YouTube</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                social.youtube
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >LinkedIn</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                social.linkedin
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                        </v-row>
                                    </v-card>
                                </div>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-tabs-window-item>

                <v-tabs-window-item value="mail">
                    <v-container>
                        <v-row>
                            <v-col
                                cols="12"
                                lg="8"
                                offset-lg="2"
                                md="10"
                                offset-md="1"
                            >
                                <div>
                                    <v-card class="pa-4" variant="flat">
                                       <div class="mb-4 d-flex align-center justify-space-between">
                                            <div>
                                                <h3>Mail Credentials</h3>
                                                <p>Configure the mail settings for your company.</p>
                                            </div>
                                        
                                            <div>
                                                <v-btn color="primary" variant="flat"> <v-icon>mdi-content-save</v-icon> Save</v-btn>
                                            </div>
                                        </div>
                                        <v-row dense class="mt-2">
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Mailer</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                mail.mailer
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Host</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="mail.host"
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Port</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="mail.port"
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Username</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                mail.username
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Password</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                mail.password
                                                            "
                                                            type="password"
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-row align="center" dense>
                                                    <v-col
                                                        cols="12"
                                                        md="3"
                                                        class="text-body-2 text-medium-emphasis"
                                                        >Encryption</v-col
                                                    >
                                                    <v-col cols="12" md="9">
                                                        <v-text-field
                                                            v-model="
                                                                mail.encryption
                                                            "
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hide-details
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                        </v-row>
                                    </v-card>
                                </div>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-tabs-window-item>

                <v-tabs-window-item value="seo">
                    <v-container>
                        <v-row>
                            <v-col
                                cols="12"
                                lg="8"
                                offset-lg="2"
                                md="10"
                                offset-md="1"
                            >
                                <div
                                    class="mb-4 d-flex align-center justify-space-between"
                                >
                                    <div>
                                        <h3>SEO Settings</h3>
                                        <p>
                                            Configure the SEO settings for your
                                            company.
                                        </p>
                                    </div>

                                    <div>
                                        <v-btn color="primary" variant="flat">
                                            <v-icon>mdi-content-save</v-icon>
                                            Save</v-btn
                                        >
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div
                                        class="text-body-2 text-medium-emphasis"
                                    >
                                        Meta Title
                                    </div>
                                    <v-text-field
                                        v-model="seo.meta_title"
                                        variant="outlined"
                                        density="comfortable"
                                        hide-details
                                    />
                                </div>
                                <div class="mb-4">
                                    <div
                                        class="text-body-2 text-medium-emphasis"
                                    >
                                        Meta Keywords
                                    </div>
                                    <v-text-field
                                        v-model="seo.meta_keywords"
                                        variant="outlined"
                                        density="comfortable"
                                        hide-details
                                    />
                                </div>
                                <div class="mb-4">
                                    <div
                                        class="text-body-2 text-medium-emphasis"
                                    >
                                        Meta Description
                                    </div>
                                    <v-textarea
                                        v-model="seo.meta_description"
                                        variant="outlined"
                                        density="comfortable"
                                        hide-details
                                        rows="3"
                                    />
                                </div>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-tabs-window-item>
            </v-tabs-window>
        </v-card>
    </v-container>
</template>

<script setup lang="ts">
import { ref } from "vue";

const activeTab = ref("overview");

const overview = ref({
    name: "",
    email: "",
    phone: "",
    description: "",
});

const address = ref({
    line1: "",
    line2: "",
    city: "",
    state: "",
    zip: "",
    country: "",
});

const social = ref({
    facebook: "",
    twitter: "",
    youtube: "",
    linkedin: "",
});

const mail = ref({
    mailer: "",
    host: "",
    port: "",
    username: "",
    password: "",
    encryption: "",
});

const seo = ref({
    meta_title: "",
    meta_keywords: "",
    meta_description: "",
});
</script>
