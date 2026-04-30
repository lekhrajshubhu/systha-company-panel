<template>

    <div v-if="!selectedRole" class="text-center py-8 text-grey-600">
        <v-icon size="48" class="mb-2">mdi-account-multiple-outline</v-icon>
        <p>Select a role to view and manage permissions</p>
    </div>

    <div v-else-if="selectedRole" class="">
        <!-- <div class="d-flex align-center">
            <h4>Permissions for: {{ selectedRole.name }}</h4>
        </div> -->
        <v-card-title class="d-flex align-center justify-space-between py-6">
            <div class="d-flex align-center">
                <h4>Permissions for :  <span class="text-primary text-uppercase">{{ selectedRole.name }}</span></h4>
            </div>
            <!-- <v-btn color="primary" prepend-icon="mdi-plus" variant="flat">
                Add Section
            </v-btn> -->
        </v-card-title>
        <table class="permission-table">
            <thead>
                <tr class="permission-header">
                    <th class="text-left pa-3" style="width: 50%">Section</th>
                    <th v-for="operation in allOperations" :key="operation" class="text-center">
                        {{ crudOperations[operation]?.name || operation }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <template v-for="section in filteredPermissionSections" :key="section.id">
                    <tr class="permission-row">
                        <td class="pa-2">
                            <strong>{{ section.name }}</strong>
                        </td>
                        <td v-for="operation in allOperations" :key="`${section.id}-${operation}`"
                            class="text-center pa-2">
                            <v-checkbox v-if="section.operations.includes(operation)"
                                :model-value="hasCrudPermission(selectedRole, section.id, operation)"
                                @update:model-value="toggleCrudPermission(selectedRole, section.id, operation)"
                                hide-details density="compact" class="d-inline-flex" />
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
interface Permission {
    id: string
    name: string
    description: string
}

interface Role {
    id: string
    name: string
    description: string
    permissions: string[]
}

interface PermissionSection {
    id: string
    name: string
    description: string
    operations: string[]
}

defineProps<{
    selectedRole: Role | null
    allOperations: string[]
    filteredPermissionSections: PermissionSection[]
    crudOperations: Record<string, { name: string; icon: string }>
}>()

const emit = defineEmits<{
    toggleCrudPermission: [role: Role, sectionId: string, operation: string]
}>()

const hasCrudPermission = (role: Role, sectionId: string, operation: string) => {
    const permissionId = `${sectionId}.${operation}`
    return role.permissions.includes(permissionId)
}

const toggleCrudPermission = (role: Role, sectionId: string, operation: string) => {
    emit('toggleCrudPermission', role, sectionId, operation)
}
</script>

<style scoped>
.permission-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid rgba(0, 0, 0, 0.12);
    border-radius: 8px;
    overflow: hidden;
}

.permission-header {
    background-color: rgba(0, 0, 0, 0.04);
    font-weight: 600;
}

.permission-header th {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}

.permission-row td {
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.permission-row:last-child td {
    border-bottom: none;
}

.permission-row:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>
