import { ref } from 'vue'

export interface Role {
    id: string
    name: string
    description: string
    permissions: string[]
}

export function useRoles() {
    const roles = ref<Role[]>([
        {
            id: 'admin',
            name: 'Administrator',
            description: 'Full system access with all permissions',
            permissions: [
                'subscriptions.view', 'subscriptions.create', 'subscriptions.update', 'subscriptions.delete',
                'vendors.view', 'vendors.create', 'vendors.update', 'vendors.delete',
                'vendor-requests.view', 'vendor-requests.create', 'vendor-requests.update', 'vendor-requests.delete',
                'teams.view', 'teams.create', 'teams.update', 'teams.delete',
                'reports.view', 'reports.create', 'reports.delete', 'reports.export',
                'payment-credentials.view', 'payment-credentials.create', 'payment-credentials.update', 'payment-credentials.delete',
                'policy.view', 'policy.create', 'policy.update', 'policy.delete',
                'total-sales-revenue.view', 'total-sales-revenue.export',
                'customer-analytics.view', 'customer-analytics.export',
                'billing.view', 'billing.create', 'billing.update', 'billing.delete',
                'notifications.view', 'notifications.create', 'notifications.update', 'notifications.delete'
            ]
        },
        {
            id: 'manager',
            name: 'Manager',
            description: 'Management access with limited permissions',
            permissions: [
                'subscriptions.view', 'subscriptions.update',
                'vendors.view', 'vendors.create', 'vendors.update',
                'vendor-requests.view', 'vendor-requests.update',
                'teams.view', 'teams.create', 'teams.update',
                'reports.view', 'reports.create', 'reports.export',
                'billing.view', 'billing.update',
                'notifications.view', 'notifications.update'
            ]
        },
        {
            id: 'staff',
            name: 'Staff Member',
            description: 'Limited access for daily operations',
            permissions: [
                'subscriptions.view',
                'vendors.view',
                'vendor-requests.view',
                'teams.view',
                'reports.view',
                'notifications.view'
            ]
        },
        {
            id: 'service_provider',
            name: 'Service Provider',
            description: 'External service provider with specific job access',
            permissions: [
                'assigned-job.view', 'assigned-job.update'
            ]
        }
    ])

    return {
        roles
    }
}
