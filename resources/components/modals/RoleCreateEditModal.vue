<template>
    <v-card-text>
        <v-form ref="roleFormRef" v-model="isRoleFormValid">
            <v-text-field
                v-model="roleForm.name"
                label="Role Name"
                variant="outlined"
                :rules="[rules.required]"
                class="mb-4"
            />
            
            <v-textarea
                v-model="roleForm.description"
                label="Description"
                variant="outlined"
                rows="3"
                class="mb-4"
            />
        </v-form>
    </v-card-text>
    <v-card-actions>
        <v-spacer />
        <v-btn variant="text" @click="closeModal">
            Cancel
        </v-btn>
        <v-btn 
            color="primary" 
            :loading="isSavingRole"
            @click="saveRole"
        >
            {{ editingRole ? 'Update' : 'Create' }}
        </v-btn>
    </v-card-actions>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useModalStore } from '@/stores/modal'
import { useRoles } from '@/composables/useRoles'

interface Role {
    id: string
    name: string
    description: string
    permissions: string[]
}

interface Props {
    editingRole?: Role | null
}

const props = withDefaults(defineProps<Props>(), {
    editingRole: null
})

const modal = useModalStore()
const { roles } = useRoles()

const roleFormRef = ref()
const isRoleFormValid = ref(false)
const isSavingRole = ref(false)

const roleForm = ref({
    name: '',
    description: ''
})

const rules = {
    required: (value: string) => !!value || 'Required'
}

const closeModal = () => {
    modal.close()
}

const saveRole = async () => {
    const formEl = roleFormRef.value
    const result = await formEl?.validate?.()
    
    if (result?.valid === false) return
    
    isSavingRole.value = true
    
    try {
        if (props.editingRole) {
            const index = roles.value.findIndex(r => r.id === props.editingRole?.id)
            if (index > -1) {
                roles.value[index] = {
                    ...props.editingRole,
                    name: roleForm.value.name,
                    description: roleForm.value.description
                }
            }
        } else {
            const newRole: Role = {
                id: Date.now().toString(),
                name: roleForm.value.name,
                description: roleForm.value.description,
                permissions: []
            }
            roles.value.push(newRole)
        }
        
        closeModal()
    } catch (error) {
        console.error('Failed to save role:', error)
    } finally {
        isSavingRole.value = false
    }
}

// Initialize form when modal opens
if (props.editingRole) {
    roleForm.value = {
        name: props.editingRole.name,
        description: props.editingRole.description
    }
}
</script>
