<template>
    <v-container fluid>
        <app-page-header title="Vendor Detail" subtitle="Vendor detail information.">

            <template #action v-if="vendorSummary.status=='pending'">
                <app-flat-button color="success" icon="mdi-file-document-check-outline" @click="onApprove()">Approve</app-flat-button>
                <app-flat-button color="error" icon="mdi-file-document-remove-outline" @click="onReject()">Reject</app-flat-button>
            </template>

        </app-page-header>
        <v-container fluid>
            <v-progress-linear v-if="loading" indeterminate color="primary" class="mb-4" />

            <v-row>
                <v-col cols="12" md="8">
                    <vendor-summary-section :summary="vendorSummary" @update-address="onUpdateAddress"
                        @update-contact="onUpdateContact" />
                    <vendor-attachments-section :attachments="vendorAttachments" />
                    <vendor-approval-section :approval="vendorApproval" />
                </v-col>

                <v-col cols="12" md="4">
                    <div class="mb-6">
                        <vendor-notes-audit-section :audit="vendorAudit" />
                    </div>
                    <div>
                        <activity-timeline :audit="vendorAudit" />
                    </div>
                </v-col>
            </v-row>

        </v-container>

    </v-container>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import AppPageHeader from '@/components/AppPageHeader.vue'
import VendorApprovalSection from '@/components/company/vendor-detail/VendorApprovalSection.vue'
import VendorAttachmentsSection from '@/components/company/vendor-detail/VendorAttachmentsSection.vue'
import VendorNotesAuditSection from '@/components/company/vendor-detail/VendorNotesAuditSection.vue'
import VendorSummarySection from '@/components/company/vendor-detail/VendorSummarySection.vue'
import { getVendorApplicationDetail } from '@/services/vendor-applications.api'
import ActivityTimeline from '@/components/company/vendor-detail/ActivityTimeline.vue'
import AppFlatButton from '@/components/AppFlatButton.vue'
import { useModalStore } from '@/stores/modal'
import VendorApprovalForm from '@/components/company/vendor-detail/forms/VendorApprovalForm.vue'
import VendorRejectionForm from '@/components/company/vendor-detail/forms/VendorRejectionForm.vue'
const modal = useModalStore()
const route = useRoute()
const loading = ref(false)

const vendor = ref({
    id: null as number | null,
    vendor_code: '',
    name: '',
    type: '',
    status: '',
    phone: '',
    email: '',
    address: {
        id: null as number | null,
        add1: '',
        add2: '',
        city: '',
        state: '',
        zip: '',
    },
    contact: {
        id: null as number | null,
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
    },
    application_date: null as string | null,
    approved_at: null as string | null,
    approved_by_name: '',
    approved_ip: '',
    notes: '',
    attachments: [] as Array<{ label: string }>,
    audit: [] as Array<{ title: string; by: string; at: string; type: string }>,
})

const mapVendorDetail = (data: any) => {
    vendor.value = {
        id: data?.id ?? null,
        vendor_code: data?.vendor_code ?? '',
        name: data?.name ?? '',
        type: data?.type ?? '',
        status: data?.status ?? '',
        phone: data?.phone ?? '',
        email: data?.email ?? '',
        address: {
            id: data?.address?.id ?? null,
            add1: data?.address?.add1 ?? '',
            add2: data?.address?.add2 ?? '',
            city: data?.address?.city ?? data?.city ?? '',
            state: data?.address?.state ?? '',
            zip: data?.address?.zip ?? '',
        },
        contact: {
            id: data?.contact?.id ?? null,
            first_name: data?.contact?.first_name ?? data?.contact?.name ?? '',
            last_name: data?.contact?.last_name ?? '',
            phone: data?.contact?.phone ?? data?.phone ?? '',
            email: data?.contact?.email ?? data?.email ?? '',
        },
        application_date: data?.application_date ?? null,
        approved_at: data?.approved_at ?? null,
        approved_by_name: data?.approved_by_name ?? '',
        approved_ip: data?.approved_ip ?? '',
        notes: data?.notes ?? '',
        attachments: Array.isArray(data?.attachments) ? data.attachments : [],
        audit: Array.isArray(data?.audit) ? data.audit : [],
    }
}

const sampleAudit = [
    { title: 'profile reviewed', by: 'Admin', at: '2026-01-10T10:30:00Z', type: 'info' },
    { title: 'Primary contact verified', by: 'Support Team', at: '2026-01-12T15:45:00Z', type: 'info' },
    { title: 'approved', by: 'Operations Lead', at: '2026-01-15T09:00:00Z', type: 'approved' },
]

const sampleAttachments = [
    { label: 'Business Registration.pdf' },
    { label: 'Tax Certificate.pdf' },
    { label: 'Insurance Document.pdf' },
    { label: 'Service Agreement.pdf' },
]

const sampleApproval = {
    approvedByName: 'Operations Lead',
    approvedAt: '2026-01-15T09:00:00Z',
    approvedIp: '192.0.2.21',
}

const vendorSummary = computed(() => ({
    name: vendor.value.name,
    type: vendor.value.type,
    status: vendor.value.status,
    vendorCode: vendor.value.vendor_code,
    applicationDate: vendor.value.application_date,
    approvedAt: vendor.value.approved_at,
    phone: vendor.value.phone,
    email: vendor.value.email,
    address: vendor.value.address,
    contact: vendor.value.contact,
}))

const vendorAudit = computed(() => {
    return vendor.value.audit.length ? vendor.value.audit : sampleAudit
})

const vendorAttachments = computed(() => {
    return vendor.value.attachments.length ? vendor.value.attachments : sampleAttachments
})

const vendorApproval = computed(() => ({
    approvedByName: vendor.value.approved_by_name || sampleApproval.approvedByName,
    approvedAt: vendor.value.approved_at || sampleApproval.approvedAt,
    approvedIp: vendor.value.approved_ip || sampleApproval.approvedIp,
}))

const fetchVendor = async () => {
    const id = route.params.id
    if (!id) return

    loading.value = true
    try {
        const response: any = await getVendorApplicationDetail(id as string)
        mapVendorDetail(response?.data ?? response)
    } catch (error) {
        console.error('Failed to load vendor detail:', error)
    } finally {
        loading.value = false
    }
}
function onApprove() {
    modal.open(
        VendorApprovalForm,
        {
            onSubmit: (payload: Record<string, unknown>) => {
                console.log('Approval submitted for vendor:', vendor.value.id, payload);
                // TODO: Call approval API with payload
            },
        },
        {
            title: 'Approve Vendor',
            showHeader: true,
            fullscreen: false,
            maxWidth: 500,
        }
    );
}
function onReject() {
    modal.open(
        VendorRejectionForm,
        {
            onSubmit: (payload: Record<string, unknown>) => {
                console.log('Rejection submitted for vendor:', vendor.value.id, payload);
                // TODO: Call rejection API with payload
            },
        },
        {
            title: 'Reject Vendor',
            showHeader: true,
            fullscreen: false,
            maxWidth: 500,
        }
    );
}

onMounted(() => {
    fetchVendor()
})

function onUpdateAddress() {
    console.info('Update address action clicked for vendor:', vendor.value.id)
}

function onUpdateContact() {
    console.info('Update contact action clicked for vendor:', vendor.value.id)
}
</script>
