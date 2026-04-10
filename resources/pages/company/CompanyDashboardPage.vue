<template>
  <v-container fluid>
    <div class="mb-2">
      <v-row>
        <v-col cols="12" md="3" v-for="metric in metrics" :key="metric.key">
          <AppCard>
            <div class="d-flex justify-space-between align-center">
              <div>
                <div class="text-subtitle-1 font-weight-medium">{{ metric.title }}</div>
                <div class="text-h4 font-weight-bold mt-2">{{ metric.display }}</div>
                <div class="caption mt-1 d-flex align-center">
                  <v-icon :class="metric.change >= 0 ? 'text-success' : 'text-error'" size="16">
                    {{ metric.change >= 0 ? 'mdi-arrow-up' : 'mdi-arrow-down' }}
                  </v-icon>
                  <span :class="metric.change >= 0 ? 'text-success' : 'text-error'">
                    {{ formattedChange(metric.change) }}
                  </span>
                  <span class="ms-2">vs last month</span>
                </div>
              </div>
              <v-avatar size="40" :color="avatarColor(metric)">
                <v-icon size="20" class="white--text">{{ metric.icon }}</v-icon>
              </v-avatar>
            </div>
          </AppCard>
        </v-col>
      </v-row>
    </div>
    <div>
      <v-row>
        <v-col cols="12" md="4">
          <AppCard>
            <div class="d-flex flex-column">
              <div class="text-subtitle-1 font-weight-medium">Vendors by Type</div>
              <div class="mt-3 chart-box">
                <!-- Simple donut svg representation (fills chart-box height) -->
                <svg viewBox="0 0 36 36" class="chart-svg donut" preserveAspectRatio="xMidYMid meet">
                  <circle r="16" cx="18" cy="18" fill="transparent" stroke="#f0f0f0" stroke-width="8"/>
                  <!-- segments: 45 / 30 / 25 -->
                  <circle r="16" cx="18" cy="18" fill="transparent" stroke="#42A5F5" stroke-width="8" stroke-dasharray="45 55" stroke-dashoffset="25" transform="rotate(-90 18 18)"/>
                  <circle r="16" cx="18" cy="18" fill="transparent" stroke="#66BB6A" stroke-width="8" stroke-dasharray="30 70" stroke-dashoffset="-20" transform="rotate(-90 18 18)"/>
                  <circle r="16" cx="18" cy="18" fill="transparent" stroke="#FFA726" stroke-width="8" stroke-dasharray="25 75" stroke-dashoffset="85" transform="rotate(-90 18 18)"/>
                  <circle r="10" cx="18" cy="18" fill="#fff"/>
                </svg>
              </div>
              <div class="caption mt-2 text-center">Pest 45% · Cleaning 30% · Tax 25%</div>
            </div>
          </AppCard>
        </v-col>

        <v-col cols="12" md="4">
          <AppCard>
            <div class="d-flex flex-column">
              <div class="text-subtitle-1 font-weight-medium">Sales & Commission (Monthly)</div>
              <div class="mt-3 chart-box">
                <!-- Simple bar chart svg (fills chart-box height) -->
                <svg viewBox="0 0 240 100" class="chart-svg bar-chart" preserveAspectRatio="xMidYMid meet">
                  <rect x="10" y="30" width="20" height="60" rx="3" fill="#42A5F5"/>
                  <rect x="45" y="20" width="20" height="70" rx="3" fill="#64B5F6"/>
                  <rect x="80" y="40" width="20" height="50" rx="3" fill="#90CAF9"/>
                  <rect x="115" y="10" width="20" height="80" rx="3" fill="#1E88E5"/>
                  <rect x="150" y="50" width="20" height="40" rx="3" fill="#42A5F5"/>
                  <rect x="185" y="35" width="20" height="55" rx="3" fill="#64B5F6"/>
                </svg>
              </div>
              <div class="caption mt-2 text-center">Sales vs Commission — last 6 months</div>
            </div>
          </AppCard>
        </v-col>

        <v-col cols="12" md="4">
          <AppCard>
            <div class="d-flex flex-column">
              <div class="text-subtitle-1 font-weight-medium">Membership Revenue</div>
              <div class="mt-3 chart-box">
                <!-- Simple line chart svg (fills chart-box height) -->
                <svg viewBox="0 0 240 120" class="chart-svg line-chart" preserveAspectRatio="xMidYMid meet">
                  <polyline fill="none" stroke="#42A5F5" stroke-width="3" points="0,80 30,70 60,60 90,50 120,45 150,40 180,35 210,30 240,25"/>
                  <polyline fill="none" stroke="#90CAF9" stroke-width="1.5" points="0,90 30,82 60,75 90,70 120,68 150,65 180,60 210,58 240,55"/>
                </svg>
              </div>
              <div class="caption mt-2 text-center">Revenue trend over the last 9 months</div>
            </div>
          </AppCard>
        </v-col>
      </v-row>
    </div>

    <!-- Latest Vendors datatable -->
    <div class="mt-4">
      <v-row>
        <v-col cols="12">
          <AppCard>
            <div class="d-flex justify-space-between align-center mb-3">
              <div>
                <div class="text-subtitle-1 font-weight-medium">Latest Vendors</div>
                <div class="text-caption text-medium-emphasis">Recently added vendors</div>
              </div>
              <v-btn variant="text" color="primary" @click="">View all</v-btn>
            </div>

            <v-data-table :items="latestVendors" :headers="vendorHeaders" dense hover item-key="id" class="elevation-0">

              <template #item.name="{ item }">
                <div class="d-flex align-center">
                  <v-avatar size="32" class="me-3" color="indigo lighten-2">
                    <v-icon size="18" class="white--text">mdi-storefront</v-icon>
                  </v-avatar>
                  <div>
                    <div class="font-weight-medium">{{ item.name }}</div>
                    <div class="caption text-medium-emphasis">{{ capitalize(item.type) }}</div>
                  </div>
                </div>
              </template>

              <template #item.type="{ item }">
                {{ capitalize(item.type) }}
              </template>

              <template #item.contact="{ item }">
                {{ [item.contact_name, item.contact_email, item.contact_phone].filter(Boolean).join(' / ') || '-' }}
              </template>

              <template #item.status="{ item }">
                <v-chip size="small" label class="text-capitalize" :color="item.status === 'approved' ? 'green' : 'grey'" small>{{ item.status }}</v-chip>
              </template>

              <template #item.actions="{ item }">
                <v-btn icon variant="text" size="small" aria-label="View vendor">
                  <v-icon>mdi-eye</v-icon>
                </v-btn>
                <v-btn icon variant="text" size="small" aria-label="Edit vendor">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </AppCard>
        </v-col>
      </v-row>
    </div>

  </v-container>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import AppCard from "@/components/AppCard.vue";

type Metric = {
  key: string;
  title: string;
  value: number | string;
  display: string;
  change: number; // percent, positive or negative
  icon: string;
};

const metrics = reactive<Metric[]>([
  {
    key: 'users',
    title: 'Total Users',
    value: 12847,
    display: '12,847',
    change: 12.5,
    icon: 'mdi-account-multiple'
  },
  {
    key: 'vendors',
    title: 'Total Vendors',
    value: 3241,
    display: '3,241',
    change: 8.2,
    icon: 'mdi-storefront'
  },
  {
    key: 'subscriptions',
    title: 'Active Subscriptions',
    value: 2156,
    display: '2,156',
    change: -2.4,
    icon: 'mdi-calendar-check'
  },
  {
    key: 'revenue',
    title: 'Revenue',
    value: 482390,
    display: '$482,390',
    change: 18.7,
    icon: 'mdi-currency-usd'
  }
]);

// Latest vendors sample data and table headers
const latestVendors = reactive([
  { id: 1, name: 'Alpha Pest', type: 'pest control', contact_name: 'John Doe', contact_email: 'john@alpha.com', contact_phone: '555-0110', phone: '555-0110', email: 'info@alpha.com', status: 'approved' },
  { id: 2, name: 'CleanRight', type: 'cleaning', contact_name: 'Sara Lee', contact_email: 'sara@cleanright.com', contact_phone: '555-0122', phone: '555-0122', email: 'hello@cleanright.com', status: 'approved' },
  { id: 3, name: 'TaxPros', type: 'tax', contact_name: 'Mike Rich', contact_email: 'mike@taxpros.com', contact_phone: '555-0133', phone: '555-0133', email: 'contact@taxpros.com', status: 'inactive' },
  { id: 4, name: 'PlumbWell', type: 'plumbing', contact_name: 'Anna Bell', contact_email: 'anna@plumbwell.com', contact_phone: '555-0144', phone: '555-0144', email: 'support@plumbwell.com', status: 'approved' },
  { id: 5, name: 'ElectroFix', type: 'electrical', contact_name: 'Tom Sparks', contact_email: 'tom@electrofix.com', contact_phone: '555-0155', phone: '555-0155', email: 'info@electrofix.com', status: 'approved' },
])

const vendorHeaders = [
  { title: 'Name', key: 'name' },
  { title: 'Type', key: 'type' },
  { title: 'Contact', key: 'contact' },
  { title: 'Phone', key: 'phone' },
  { title: 'Email', key: 'email' },
  { title: 'Status', key: 'status' },
  { title: 'Actions', key: 'actions' },
]

function formattedChange(n: number) {
  const sign = n >= 0 ? '+' : '';
  return `${sign}${n}%`;
}

function avatarColor(metric: Metric) {
  // Revenue gets an indigo color, positive changes green, negative red, otherwise grey
  if (metric.key === 'revenue') return 'indigo darken-1';
  if (metric.change > 0) return 'green darken-1';
  if (metric.change < 0) return 'red darken-1';
  return 'grey lighten-3';
}

function capitalize(s: string) {
  if (!s) return s;
  return s.charAt(0).toUpperCase() + s.slice(1);
}
</script>

<style scoped>
.metric-number {
  font-size: 1.6rem;
  font-weight: 700;
}
.change {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  font-weight: 600;
}
.change.positive v-icon {
  color: #2e7d32;
}
.change.negative v-icon {
  color: #c62828;
}
.change.positive span {
  color: #2e7d32;
}
.change.negative span {
  color: #c62828;
}

/* Chart helper styles */
.donut { max-width: 140px; height: auto; }
.bar-chart rect { opacity: 0.95; }
.line-chart { width: 100%; height: 120px; }
.line-chart polyline { stroke-linecap: round; stroke-linejoin: round; }
.chart-box {
  height: 160px; /* equal height for all charts */
  display: flex;
  align-items: center;
  justify-content: center;
}
.chart-svg {
  height: 100%;
  width: auto;
  max-width: 100%;
}
</style>
