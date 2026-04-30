<template>
    <v-container fluid>
        <app-page-header title="Roles & Permissions" subtitle="Manage user roles and permissions." />
        
        <v-row>
            <v-col cols="12" md="4">
                <v-card flat>
                    <v-card-title class="d-flex align-center justify-space-between">
                        <div class="d-flex align-center">
                            <v-icon class="mr-2">mdi-account-key-outline</v-icon>
                            Roles
                        </div>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <div class="text-right">
                            <v-btn 
                                color="primary" 
                                variant="flat"
                                @click="openCreateRoleModal"
                                prepend-icon="mdi-plus"
                            >
                                Create Role
                            </v-btn>
                        </div>
                        <v-list>
                            <v-list-item
                                v-for="role in roles"
                                :key="role.id"
                                class="role-item py-4"
                                :class="{ 'selected-role': selectedRole?.id === role.id }"
                                @click="selectRole(role)"
                            >
                                <template v-slot:prepend>
                                    <v-icon>mdi-account-outline</v-icon>
                                </template>
                                
                                <v-list-item-title>{{ role.name }}</v-list-item-title>
                                <v-list-item-subtitle>{{ role.description }}</v-list-item-subtitle>
                                
                                <template v-slot:append>
                                    <v-btn
                                        size="small"
                                        variant="text"
                                        icon="mdi-pencil"
                                        @click="openEditRoleModal(role)"
                                    />
                                    <v-btn
                                        size="small"
                                        variant="text"
                                        color="error"
                                        icon="mdi-delete"
                                        @click="deleteRole(role)"
                                    />
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-col>
            
            <v-col cols="12" md="8">
                <v-card flat>
                    <v-card-text class="pa-0">
                        <v-tabs v-model="activeTab">
                            <v-tab value="matrix" prepend-icon="mdi-shield-key-outline">Permission Matrix</v-tab>
                            <v-tab value="sections" prepend-icon="mdi-format-list-bulleted">Permission Sections</v-tab>
                        </v-tabs>
                        <v-divider></v-divider>
                        
                        <v-window v-model="activeTab">
                            <v-window-item value="matrix">
                                <PermissionMatrixTab
                                    :selected-role="selectedRole"
                                    :all-operations="allOperations"
                                    :filtered-permission-sections="filteredPermissionSections"
                                    :crud-operations="crudOperations"
                                    @toggle-crud-permission="toggleCrudPermission"
                                />
                            </v-window-item>

                            <v-window-item value="sections">
                                <PermissionSectionsTab />
                            </v-window-item>
                        </v-window>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        
        <!-- Create/Edit Role Dialog -->
    </v-container>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import AppPageHeader from '@/components/AppPageHeader.vue'
import PermissionMatrixTab from '@/components/role-permission/PermissionMatrixTab.vue'
import PermissionSectionsTab from '@/components/role-permission/PermissionSectionsTab.vue'
import { usePermissionSections } from '@/composables/usePermissionSections'
import { useRoles } from '@/composables/useRoles'
import { useModalStore } from '@/stores/modal'
import RoleCreateEditModal from '@/components/modals/RoleCreateEditModal.vue'
import RoleDeleteModal from '@/components/modals/RoleDeleteModal.vue'

interface Role {
    id: string
    name: string
    description: string
    permissions: string[]
}

const modal = useModalStore()
const editingRole = ref<Role | null>(null)
const roleToDelete = ref<Role | null>(null)
const selectedRole = ref<Role | null>(null)
const activeTab = ref('matrix')


const { roles } = useRoles()

const { permissionSections } = usePermissionSections()

const crudOperations: Record<string, { name: string; icon: string }> = {
    create: { name: 'Create', icon: 'mdi-plus' },
    view: { name: 'View', icon: 'mdi-eye' },
    update: { name: 'Update', icon: 'mdi-pencil' },
    delete: { name: 'Delete', icon: 'mdi-delete' },
    export: { name: 'Export', icon: 'mdi-download' }
}

const allOperations = computed(() => {
    const operations = new Set<string>()
    permissionSections.value.forEach(section => {
        section.operations.forEach(operation => {
            operations.add(operation)
        })
    })
    return Array.from(operations)
})


const filteredPermissionSections = computed(() => {
    if (!selectedRole.value) return permissionSections.value
    
    // Only show Assigned Job section for service_provider role
    if (selectedRole.value.id === 'service_provider') {
        return permissionSections.value.filter(section => 
            section.id === 'assigned-job' || 
            !['total-sales-revenue', 'customer-analytics', 'payment-credentials', 'policy'].includes(section.id)
        )
    }
    
    return permissionSections.value
})

const toggleCrudPermission = (role: Role, sectionId: string, operation: string) => {
    const permissionId = `${sectionId}.${operation}`
    const roleIndex = roles.value.findIndex(r => r.id === role.id)
    if (roleIndex > -1) {
        const permissionIndex = roles.value[roleIndex].permissions.indexOf(permissionId)
        if (permissionIndex > -1) {
            roles.value[roleIndex].permissions.splice(permissionIndex, 1)
        } else {
            roles.value[roleIndex].permissions.push(permissionId)
        }
    }
}

const openCreateRoleModal = () => {
    editingRole.value = null
    modal.open(RoleCreateEditModal, { editingRole: null }, {
        title: 'Create Role',
        maxWidth: 500
    })
}

const openEditRoleModal = (role: Role) => {
    editingRole.value = { ...role }
    modal.open(RoleCreateEditModal, { editingRole: role }, {
        title: 'Edit Role',
        maxWidth: 500
    })
}

const selectRole = (role: Role) => {
    selectedRole.value = role
}

const deleteRole = (role: Role) => {
    roleToDelete.value = role
    modal.open(RoleDeleteModal, { roleToDelete: role }, {
        title: 'Delete Role',
        maxWidth: 400
    })
}





</script>

<style scoped>
.role-item {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.role-item:hover {
    background-color: rgba(0, 0, 0, 0.04);
}

.selected-role {
    background-color: rgba(25, 118, 210, 0.08);
    border-left: 3px solid rgb(25, 118, 210);
}

</style>
