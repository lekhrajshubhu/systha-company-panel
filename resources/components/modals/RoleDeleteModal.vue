<template>
    <v-card-text>
        Are you sure you want to delete the role "{{ roleToDelete?.name }}"? This action cannot be undone.
    </v-card-text>
    <v-card-actions>
        <v-spacer />
        <v-btn variant="text" @click="closeModal">
            Cancel
        </v-btn>
        <v-btn 
            color="error" 
            :loading="isDeletingRole"
            @click="confirmDeleteRole"
        >
            Delete
        </v-btn>
    </v-card-actions>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useModalStore } from '@/stores/modal'
import { useRoles } from '@/composables/useRoles'

interface Role {
    id: string
    name: string
    description: string
    permissions: string[]
}

interface Props {
    roleToDelete: Role
}

const props = defineProps<Props>()

const modal = useModalStore()
const { roles } = useRoles()

const isDeletingRole = ref(false)

const closeModal = () => {
    modal.close()
}

const confirmDeleteRole = async () => {
    const role = props.roleToDelete
    isDeletingRole.value = true
    
    try {
        const index = roles.value.findIndex(r => r.id === role.id)
        if (index > -1) {
            roles.value.splice(index, 1)
        }
        
        closeModal()
    } catch (error) {
        console.error('Failed to delete role:', error)
    } finally {
        isDeletingRole.value = false
    }
}
</script>
