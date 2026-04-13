<template>
  <app-card class="mb-6">
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <v-row class="align-start summary-two-col-row">
            <v-col cols="12" md="6" class="summary-left-col">
              <div class="summary-block">
                <div class="summary-row mb-3">
                  <div class="summary-title">Legal Name</div>
                  <div class="summary-value text-subtitle-1">{{ summary.name || '-' }}</div>
                </div>

                <div class="summary-row mb-3">
                  <div class="summary-title">Business Type</div>
                  <div class="summary-value text-body-1 text-capitalize">{{ summary.type || '-' }}</div>
                </div>

                <div class="summary-row mb-3">
                  <div class="summary-title">Vendor Code</div>
                  <div class="summary-value">{{ summary.vendorCode || '-' }}</div>
                </div>

                <div class="summary-row mb-3">
                  <div class="summary-title">Application Date</div>
                  <div class="summary-value">{{ formatDate(summary.applicationDate) }}</div>
                </div>

                <div class="summary-row">
                  <div class="summary-title">Approved Date</div>
                  <div class="summary-value">{{ formatDate(summary.approvedAt) }}</div>
                </div>
              </div>
            </v-col>

            <v-col cols="12" md="6" class="pl-8">
              <div class="address-block mb-4">
                <div class="mb-1 d-flex align-center justify-space-between">
                  <h4>Business Address</h4>
                  <v-btn
                    size="small"
                    icon
                    color="info"
                    variant="tonal"
                    aria-label="Edit contact person"
                  ><v-icon>mdi-lead-pencil</v-icon> </v-btn>
                </div>
                <div class="mb-2 pl-2">
                  <div>{{ summary.address?.add1 || '-' }}</div>
                  <div v-if="summary.address?.add2">{{ summary.address.add2 || '-' }}</div>
                  <div class="mt-1 text-medium-emphasis">
                    {{ [summary.address?.city, summary.address?.state, summary.address?.zip].filter(Boolean).join(', ') }}
                  </div>
                </div>
              </div>

              <div class="contact-block">
                <div class="mb-1 d-flex align-center justify-space-between">
                  <h4>Contact Person</h4>
                  <v-btn
                    size="small"
                    icon
                    color="info"
                    variant="tonal"
                    aria-label="Edit contact person"
                  ><v-icon>mdi-lead-pencil</v-icon> </v-btn>
                </div>
                <div class="pl-2">
                  <div class="mb-1">
                    {{ summary.contact?.first_name ? (summary.contact.first_name + ' ' + (summary.contact.last_name || '')) : (summary.contact?.name ?? '-') }}
                  </div>
                  <div class="caption text-medium-emphasis">{{ summary.contact?.phone || summary.phone || '-' }}</div>
                  <div class="caption text-medium-emphasis">{{ summary.contact?.email || summary.email || '-' }}</div>
                </div>
              </div>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-card-text>
  </app-card>
</template>

<script setup lang="ts">
import AppCard from '@/components/AppCard.vue'

defineProps<{
  summary: {
    name: string
    type: string
    vendorCode: string
    applicationDate: string | null
    approvedAt: string | null
    phone: string
    email: string
    address: {
      add1?: string
      add2?: string
      city?: string
      state?: string
      zip?: string
    }
    contact: {
      first_name?: string
      last_name?: string
      name?: string
      phone?: string
      email?: string
    }
  }
}>()

const emit = defineEmits<{
  (e: 'update-address'): void
  (e: 'update-contact'): void
}>()

function formatDate(value: string | null | undefined) {
  if (!value) return '-'
  try {
    return new Date(value).toLocaleString()
  } catch {
    return String(value)
  }
}
</script>

<style scoped>
.address-block,
.contact-block {
  text-align: left;
}

.summary-left-col {
  padding-right: 20px;
}

.summary-two-col-row {
  position: relative;
}

@media (min-width: 960px) {
  .summary-two-col-row::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 50%;
    width: 1px;
    background: rgba(0, 0, 0, 0.12);
  }
}

@media (max-width: 959px) {
  .summary-left-col {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
    padding-right: 12px;
    padding-bottom: 16px;
    margin-bottom: 8px;
  }

  .address-block,
  .contact-block,
  .text-right {
    text-align: left !important;
  }
}

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
</style>
