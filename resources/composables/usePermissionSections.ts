import { ref } from 'vue'

export interface PermissionSection {
    id: string
    name: string
    description: string
    operations: string[]
}

export function usePermissionSections() {
    const permissionSections = ref<PermissionSection[]>([
        {
            id: 'total-sales-revenue',
            name: 'Total Sales Revenue',
            description: 'Access and analyze sales revenue data',
            operations: ['view', 'export']
        },
        {
            id: 'customer-analytics',
            name: 'Customer Analytics',
            description: 'View customer behavior and analytics',
            operations: ['view', 'export']
        },
        {
            id: 'subscriptions',
            name: 'Subscriptions',
            description: 'Manage subscription plans and billing',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 'vendors',
            name: 'Vendors',
            description: 'Manage vendor accounts and information',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 'vendor-requests',
            name: 'Vendor Requests',
            description: 'Handle new vendor applications and requests',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 'teams',
            name: 'Teams',
            description: 'Manage team members and roles',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 'reports',
            name: 'Reports',
            description: 'Access and generate various reports',
            operations: ['view', 'create', 'delete']
        },
        {
            id: 'payment-credentials',
            name: 'Payment Credentials',
            description: 'Manage payment gateway configurations',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 'policy',
            name: 'Policy',
            description: 'Manage company policies and guidelines',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 'billing',
            name: 'Billing',
            description: 'Manage billing and invoicing',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 'notifications',
            name: 'Notifications',
            description: 'Manage system notifications and alerts',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 'assigned-job',
            name: 'Assigned Job',
            description: 'View and manage assigned job tasks',
            operations: ['view', 'update']
        }
    ])

    return {
        permissionSections
    }
}
