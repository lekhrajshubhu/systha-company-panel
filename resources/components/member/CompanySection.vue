<template>
    <v-card class="pa-4 elevation-0 rounded">
        <div class="d-flex align-center justify-space-between mb-3">
            <div class="text-subtitle-2">COMPANIES</div>
            <v-btn
                icon
                size="small"
                variant="flat"
                color="primary"
                prepend-icon="mdi-plus"
                @click="openCompanyForm"
            >
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </div>
        <v-divider></v-divider>

        <v-list density="compact" class="mt-2">
            <template v-for="(company, index) in companies" :key="index">
                <v-list-item class="px-0">
                    <template #prepend>
                        <v-avatar size="28" color="secondary" variant="tonal" rounded>
                            <v-icon size="16">mdi-domain</v-icon>
                        </v-avatar>
                    </template>

                    <template #append>
                        <v-btn
                            icon
                            variant="text"
                            size="small"
                            @click="openCompanyForm(company)"
                        >
                            <v-icon size="18">mdi-square-edit-outline</v-icon>
                        </v-btn>
                    </template>

                    <div class="d-flex flex-column ml-4 w-100">
                        <div class="d-flex align-center flex-wrap ga-2">
                            <div class="text-body-2 font-weight-medium">
                                {{ company.name }}
                            </div>
                            
                        </div>

                        <div class="text-caption text-medium-emphasis">
                            <span v-if="company.address?.line1">
                                {{ company.address.line1 }}
                                <!-- <span v-if="company.address.line2">
                                    , {{ company.address.line2 }}
                                </span> -->
                            </span>
                            <span v-else>-</span>
                            <span v-if="company.address?.city">
                                • {{ company.address.city }}
                            </span>
                            <span v-if="company.address?.state">
                                , {{ company.address.state }}
                            </span>
                            <span v-if="company.address?.zip">
                                {{ company.address.zip }}
                            </span>
                        </div>

                        <div
                            class="text-caption text-medium-emphasis"
                            v-if="company.email || company.phone"
                        >
                            <span v-if="company.email">{{ company.email }}</span>
                            <span v-if="company.email && company.phone"> • </span>
                            <span v-if="company.phone">{{ company.phone }}</span>
                        </div>

                        <div
                            class="text-caption text-medium-emphasis text-primary"
                            v-if="company.job_title || company.department"
                        >
                            <span v-if="company.job_title">{{ company.job_title }}</span>
                          
                        </div>

                    </div>
                </v-list-item>

                <v-divider v-if="index < companies.length - 1" class="my-2" />
            </template>
        </v-list>
    </v-card>
</template>

<script setup lang="ts">

import CompanyForm from "@/components/member/forms/CompanyForm.vue";
import { useModalStore } from "@/stores/modal";

interface CompanyItem {
    name: string;
    relation_type: string;
    entity_type: string;
    registration_state: string;
    ein?: string;
    job_title?: string;
    department?: string;
    email?: string;
    phone?: string;
    website?: string;
    address?: {
        line1?: string;
        line2?: string;
        city?: string;
        state?: string;
        zip?: string;
    };
}

defineProps<{
    companies: CompanyItem[];
}>();


const modal = useModalStore();

const openCompanyForm = (company?: CompanyItem) => {
    const initialValues = company
        ? {
              name: company.name,
              entity_type: company.entity_type
                  ? company.entity_type.toLowerCase()
                  : "",
              registration_state: company.registration_state ?? "",
              ein: company.ein ?? "",
              email: company.email ?? "",
              phone: company.phone ?? "",
              website: company.website ?? "",
              line_1: company.address?.line1 ?? "",
              line_2: company.address?.line2 ?? "",
              city: company.address?.city ?? "",
              state: company.address?.state ?? "",
              zip: company.address?.zip ?? "",
          }
        : {
              name: "",
              dba_name: "",
              entity_type: "",
              tax_classification: "",
              status: "active",
              email: "",
              phone: "",
              website: "",
              industry: "",
              notes: "",
              line_1: "",
              line_2: "",
              city: "",
              state: "",
              zip: "",
              country: "USA",
              is_primary: false,
          };

    modal.open(
        CompanyForm,
        {
            initialValues,
            onSubmit: (payload: Record<string, unknown>) => {
                console.log("company submitted", payload);
            },
        },
        {
            title: "Add Company",
            showHeader: true,
            fullscreen: false,
            maxWidth: 600,
        }
    );
};
</script>
