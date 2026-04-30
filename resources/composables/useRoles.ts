import { ref } from 'vue'

export interface Role {
    id: number
    code: string
    name: string
    permissions: string[]
}

export function useRoles() {
    const roles = ref<Role[]>([
        {
            id: 1,
            code: 'admin',
            name: 'Administrator',
            permissions: [
                'company.subscriptions.view', 'company.subscriptions.create', 'company.subscriptions.update', 'company.subscriptions.delete',
                'company.vendors.view', 'company.vendors.create', 'company.vendors.update', 'company.vendors.delete',
                'company.vendor-requests.view', 'company.vendor-requests.create', 'company.vendor-requests.update', 'company.vendor-requests.delete',
                'company.teams.view', 'company.teams.create', 'company.teams.update', 'company.teams.delete',
                'company.reports.view', 'company.reports.create', 'company.reports.delete', 'company.reports.export',
                'company.payment-credentials.view', 'company.payment-credentials.create', 'company.payment-credentials.update', 'company.payment-credentials.delete',
                'company.policy.view', 'company.policy.create', 'company.policy.update', 'company.policy.delete',
                'company.total-sales-revenue.view', 'company.total-sales-revenue.export',
                'company.customer-analytics.view', 'company.customer-analytics.export',
                'company.billing.view', 'company.billing.create', 'company.billing.update', 'company.billing.delete',
                'company.notifications.view', 'company.notifications.create', 'company.notifications.update', 'company.notifications.delete'
            ]
        },
        {
            id: 2,
            code: 'manager',
            name: 'Manager',
            permissions: [
                'company.subscriptions.view', 'company.subscriptions.update',
                'company.vendors.view', 'company.vendors.create', 'company.vendors.update',
                'company.vendor-requests.view', 'company.vendor-requests.update',
                'company.teams.view', 'company.teams.create', 'company.teams.update',
                'company.reports.view', 'company.reports.create', 'company.reports.export',
                'company.billing.view', 'company.billing.update',
                'company.notifications.view', 'company.notifications.update'
            ]
        },
        {
            id: 3,
            code: 'staff',
            name: 'Staff Member',
            permissions: [
                'company.subscriptions.view',
                'company.vendors.view',
                'company.vendor-requests.view',
                'company.teams.view',
                'company.reports.view',
                'company.notifications.view'
            ]
        },
        {
            id: 4,
            code: 'service_provider',
            name: 'Service Provider',
            permissions: [
                'company.assigned-job.view', 'company.assigned-job.update'
            ]
        }
    ])

    return {
        roles
    }
}
