<template>
    <v-container class="py-6 px-14" fluid>
        <div class="pa-0">
            <app-page-header title="Vendor Detail" subtitle="Vendor detail information." show-back />

            <v-row class="mt-4">
                <!-- LEFT COLUMN -->
                <v-col cols="12" md="8">
                    <app-card class="mb-6">
                        <v-card-text>
                            <v-row>
                                <v-col cols="12">
                                    <v-row class="align-start">
                                        <!-- LEFT: key summary fields -->
                                        <v-col cols="12" md="6">
                                            <div class="summary-block">
                                                <div class="summary-row mb-3">
                                                    <div class="summary-title">Legal Name</div>
                                                    <div class="summary-value text-subtitle-1">{{ vendor.name || '-' }}
                                                    </div>
                                                </div>

                                                <div class="summary-row mb-3">
                                                    <div class="summary-title">Business Type</div>
                                                    <div class="summary-value text-body-1 text-capitalize">{{
                                                        vendor.type || '-' }}</div>
                                                </div>

                                                <div class="summary-row mb-3">
                                                    <div class="summary-title">Vendor Code</div>
                                                    <div class="summary-value">{{ vendor.vendor_code || '-' }}</div>
                                                </div>

                                                <div class="summary-row mb-3">
                                                    <div class="summary-title">Application Date</div>
                                                    <div class="summary-value">{{ formatDate(vendor.application_date) }}
                                                    </div>
                                                </div>

                                                <div class="summary-row">
                                                    <div class="summary-title">Approved Date</div>
                                                    <div class="summary-value">{{ formatDate(vendor.approved_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </v-col>

                                        <!-- RIGHT: business address + contact person -->
                                        <v-col cols="12" md="6" class="">
                                            <div class="address-block mb-4">
                                                <div class="mb-1">
                                                    <h4>Business Address</h4>
                                                </div>
                                                <div class="mb-2 pl-2">
                                                    <div>{{ vendor.address?.add1 || '-' }}</div>
                                                    <div v-if="vendor.address?.add2">{{ vendor.address.add2 || '-' }}
                                                    </div>
                                                    <div class="mt-1 text-medium-emphasis">
                                                        {{ [vendor.address?.city, vendor.address?.state,
                                                        vendor.address?.zip].filter(Boolean).join(', ') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="contact-block">
                                                <div class="mb-1">
                                                    <h4>Contact Person</h4>
                                                </div>
                                                <div class="pl-2">
                                                    <div class="mb-1">
                                                        {{ vendor.contact?.first_name ? (vendor.contact.first_name + ' '
                                                            + (vendor.contact.last_name || '')) : (vendor.contact?.name ??
                                                        '-') }}
                                                    </div>
                                                    <div class="caption text-medium-emphasis">{{ vendor.contact?.phone
                                                        || vendor.phone || '-' }}</div>
                                                    <div class="caption text-medium-emphasis">{{ vendor.contact?.email
                                                        || vendor.email || '-' }}</div>
                                                </div>
                                            </div>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </app-card>

                    <app-card class="mb-6">
                        <v-card-title class="section-card-title">Attachments</v-card-title>
                        <v-card-text class="pt-2">
                            <v-row>
                                <v-col cols="6" md="3" v-for="(att, i) in vendor.attachments" :key="i">
                                    <div class="attachment-box">
                                        <v-icon size="36" color="primary">mdi-file-document-outline</v-icon>
                                        <div class="caption mt-2">{{ att.label }}</div>
                                    </div>
                                </v-col>


                            </v-row>
                        </v-card-text>
                    </app-card>

                    <app-card>
                        <v-card-title class="section-card-title">Approved By</v-card-title>
                        <v-card-text class="pt-2">
                            <div class="approved-box d-flex justify-space-between align-center">
                                <div>
                                    <div class="font-weight-medium">{{ vendor.approved_by_name }}</div>
                                    <div class="caption text-medium-emphasis">{{ formatDate(vendor.approved_at) }}</div>
                                    <div class="caption text-medium-emphasis">IP: {{ vendor.approved_ip }}</div>
                                </div>
                                <div class="signature-box">Signature</div>
                            </div>
                        </v-card-text>
                    </app-card>
                </v-col>

                <!-- RIGHT COLUMN: Notes / Audit -->
                <v-col cols="12" md="4">
                    <app-card>
                        <v-card-title class="section-card-title">Notes / Audit</v-card-title>
                        <v-card-text>
                            <div class="mb-4">
                                <v-textarea hide-details rows="3" variant="outlined" auto-grow>
     
                                </v-textarea>
                                <div class="text-right mt-2">
                                    <app-flat-button size="default" icon="mdi-content-save">Submit</app-flat-button>
                                </div>
                            </div>

                            <div class="caption mb-2">Recent Activity</div>
                            <v-timeline dense side="end">
                                <v-timeline-item v-for="(evt, i) in vendor.audit" :key="i"
                                    :color="evt.type === 'approved' ? 'success' : 'primary'" small>
                                    <div class="text-subtitle-2">{{ evt.title }}</div>
                                    <div class="caption text-medium-emphasis">{{ evt.by }} • {{ formatDate(evt.at) }}
                                    </div>
                                </v-timeline-item>

                                <v-timeline-item v-if="vendor.audit.length === 0" color="grey-darken-1" small>
                                    <div class="caption text-medium-emphasis">No audit events</div>
                                </v-timeline-item>
                            </v-timeline>
                        </v-card-text>
                    </app-card>
                </v-col>
            </v-row>
        </div>
    </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppCard from '@/components/AppCard.vue'
import AppPageHeader from '@/components/AppPageHeader.vue'
import AppFlatButton from '@/components/AppFlatButton.vue'

const route = useRoute()
const router = useRouter()

// Demo data for now
const vendor = ref({
    id: 1,
    vendor_code: 'VND-001',
    name: 'Sparkle Cleaners',
    type: 'cleaning',
    status: 'approved',
    phone: '+1 555 0100',
    email: 'hello@sparkle.example',
    address: {
        add1: '3221 Pale Rider Pass',
        add2: '',
        city: 'Leander',
        state: 'TX',
        zip: '78641',
    },
    contact: { first_name: 'Suman', last_name: 'Thapa', phone: '860 986 9303', email: 'nepalnme@gmail.com' },
    application_date: '2012-02-01T00:00:00Z',
    approved_at: '2012-02-02T10:30:00Z',
    approved_by_name: 'Suman Thapa',
    approved_ip: '192.0.2.1',
    notes: 'Demo vendor notes. Replace with real data when API is wired.',
    attachments: [
        { label: 'Tax paper' },
        { label: 'Legal' },
        { label: 'Legal' },
        { label: 'Legal' },
    ],
    audit: [
        { title: 'Vendor created', by: 'Admin', at: '2012-02-01T08:00:00Z', type: 'info' },
        { title: 'Vendor approved', by: 'Suman Thapa', at: '2012-02-02T10:30:00Z', type: 'approved' },
    ],
})

function formatDate(value: string | null | undefined) {
    if (!value) return '-'
    try {
        return new Date(value).toLocaleString()
    } catch {
        return String(value)
    }
}

const goBack = () => router.back()
const goToEdit = () => {
    const companyId = String(route.params.company ?? route.params.companyId ?? '')
    const vendorId = String(vendor.value.id)
    if (!companyId || !vendorId) return
    router.push({ name: 'company.vendor-edit', params: { company: companyId, id: vendorId } })
}
</script>

<style scoped>
.attachment-box {
    border: 1px dashed var(--v-theme-border, #cfcfcf);
    height: 96px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 8px;
    border-radius: 4px;
}

.attachment-large .attachment-placeholder {
    border: 1px dashed var(--v-theme-border, #cfcfcf);
    height: 96px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    color: var(--v-theme-on-surface-variant, #9e9e9e);
}

.approved-box {
    border: 1px solid rgba(0, 0, 0, 0.06);
    padding: 12px;
    border-radius: 6px;
}

.signature-box {
    width: 160px;
    height: 56px;
    border: 1px solid rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--v-theme-on-surface-variant, #9e9e9e);
}

.notes-box {
    min-height: 120px;
    border: 1px solid rgba(0, 0, 0, 0.04);
    padding: 12px;
    border-radius: 4px;
    background: var(--v-theme-surface, #fff);
    color: var(--v-theme-on-surface, #222);
}

.address-block,
.contact-block {
    text-align: left;
}

/* keep readable on small screens: left-align below md breakpoint */
@media (max-width: 959px) {

    .address-block,
    .contact-block,
    .text-right {
        text-align: left !important;
    }
}

/* Title / value separation for summary block */
.summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.summary-title {
    flex: 0 0 38%;
    text-transform: uppercase;
    color: var(--v-theme-on-surface-variant, #6b6b6b);
}

.summary-value {
    flex: 1 1 62%;
    text-align: left;
    color: var(--v-theme-on-surface, #222);
    font-weight: 600;
}

/* On small screens stack and left-align for readability */
@media (max-width: 959px) {
    .summary-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .summary-title,
    .summary-value {
        text-align: left;
        width: 100%;
    }
}

/* make the Attachments card title bold */
.section-card-title {
    font-weight: 700;
    font-size: 1rem;
}
</style>
