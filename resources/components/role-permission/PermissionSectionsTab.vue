<template>
      <v-card-title class="d-flex align-center justify-space-between py-6">
        <div class="d-flex align-center">
            <h4>Permission Sections</h4>
        </div>
        <v-btn
            color="primary"
            prepend-icon="mdi-plus"
            variant="flat"
            @click="openAddSectionModal"
        >
            Add Section
        </v-btn>
    </v-card-title>
    <v-data-table
        :headers="sectionHeaders"
        :items="permissionSections"
        item-key="id"
        class="elevation-0"
    >
        <template v-slot:item="{ item }">
            <tr>
                <td class="pa-3">
                    <strong>{{ item.name }}</strong>
                </td>
                <td class="pa-3 text-grey-600">
                    {{ item.description }}
                </td>
                <td class="pa-3">
                    <v-chip
                        v-for="operation in item.operations"
                        :key="operation"
                        size="small"
                        color="primary"
                        label
                        class="ma-1"
                    >
                        {{ operation.toUpperCase() }}
                    </v-chip>
                </td>
                <td class="pa-3 text-right">
                    <v-btn
                        size="small"
                        variant="outlined"
                        class="mr-1"
                    >
                        Edit
                    </v-btn>
                    <v-btn
                        size="small"
                        variant="outlined"
                        color="error"
                    >
                        Delete
                    </v-btn>
                </td>
            </tr>
        </template>
    </v-data-table>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { usePermissionSections } from '@/composables/usePermissionSections'
import { useModalStore } from '@/stores/modal'
import PermissionSectionCreateModal from '@/components/modals/PermissionSectionCreateModal.vue'

const { permissionSections } = usePermissionSections()
const modal = useModalStore()

const sectionHeaders = computed(() => [
    { title: 'Section Name', key: 'name', sortable: true },
    { title: 'Description', key: 'description', sortable: true },
    { title: 'Operations', key: 'operations', sortable: false },
    { title: 'Actions', key: 'actions', sortable: false, align: 'end' as const }
])

const openAddSectionModal = () => {
    modal.open(PermissionSectionCreateModal, {}, {
        title: 'Add Permission Section',
        maxWidth: 600
    })
}
</script>
