import { ref } from 'vue'

export interface PermissionSection {
    id: number
    code: string
    name: string
    description: string
    operations: string[]
}

export function usePermissionSections() {
    const permissionSections = ref<PermissionSection[]>([
        {
            id: 1,
            code: 'total-sales-revenue',
            name: 'Total Sales Revenue',
            description: 'Access and analyze sales revenue data',
            operations: ['view', 'export']
        },
        {
            id: 2,
            code: 'customer-analytics',
            name: 'Customer Analytics',
            description: 'View customer behavior and analytics',
            operations: ['view', 'export']
        },
        {
            id: 3,
            code: 'subscriptions',
            name: 'Subscriptions',
            description: 'Manage subscription plans and billing',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 4,
            code: 'vendors',
            name: 'Vendors',
            description: 'Manage vendor accounts and information',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 5,
            code: 'vendor-requests',
            name: 'Vendor Requests',
            description: 'Handle new vendor applications and requests',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 6,
            code: 'teams',
            name: 'Teams',
            description: 'Manage team members and roles',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 7,
            code: 'reports',
            name: 'Reports',
            description: 'Access and generate various reports',
            operations: ['view', 'create', 'delete']
        },
        {
            id: 8,
            code: 'payment-credentials',
            name: 'Payment Credentials',
            description: 'Manage payment gateway configurations',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 9,
            code: 'policy',
            name: 'Policy',
            description: 'Manage company policies and guidelines',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 10,
            code: 'billing',
            name: 'Billing',
            description: 'Manage billing and invoicing',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 11,
            code: 'notifications',
            name: 'Notifications',
            description: 'Manage system notifications and alerts',
            operations: ['view', 'create', 'update', 'delete']
        },
        {
            id: 12,
            code: 'assigned-job',
            name: 'Assigned Job',
            description: 'View and manage assigned job tasks',
            operations: ['view', 'update']
        }
    ])

    return {
        permissionSections
    }
}
