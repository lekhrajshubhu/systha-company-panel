<template>
    <v-card-text>
        <v-form ref="sectionFormRef" v-model="isSectionFormValid">
            <v-row>
                <v-col cols="12">
                    <AppTextField v-model="sectionForm.name" label="Section Name" :rules="[rules.required]" />
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="6">
                    <AppTextField v-model="sectionForm.code" label="Code" :rules="[rules.required]"
                    @input="onCodeInput" />
                </v-col>
                <v-col cols="6">
                    <AppTextField v-model="sectionForm.route" label="Route" :rules="[rules.required]"
                        @input="onRouteInput" />
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <AppTextArea v-model="sectionForm.description" label="Description" rows="3" />
                </v-col>
            </v-row>


            <v-row>
                <v-col cols="12">
                    <div class="text-body-2 text-medium-emphasis mb-1">Operations</div>
                    <div class="operations-grid">
                        <v-checkbox v-for="operation in availableOperations" :key="operation" v-model="selectedOperations"
                            :label="operation.charAt(0).toUpperCase() + operation.slice(1)" :value="operation" hide-details
                            density="compact" class="mb-2" />
                    </div>
                </v-col>
            </v-row>
        </v-form>
    </v-card-text>
    <v-card-actions>
        <v-spacer />
        <app-flat-button :loading="isSavingSection" @click="saveSection">
            Create Section
        </app-flat-button>
    </v-card-actions>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useModalStore } from '@/stores/modal'
import { usePermissionSections, type PermissionSection as ComposablePermissionSection } from '@/composables/usePermissionSections'
import { rolesPermissionsApi, type PermissionSection } from '@/services/roles-permissions.api'
import AppTextField from '@/components/AppTextField.vue'
import AppTextArea from '@/components/AppTextArea.vue'
import AppFlatButton from '../AppFlatButton.vue'

const modal = useModalStore()
const { permissionSections } = usePermissionSections()

const sectionFormRef = ref()
const isSectionFormValid = ref(false)
const isSavingSection = ref(false)

const availableOperations = ['view', 'create', 'update', 'delete', 'export']

const sectionForm = ref({
    name: '',
    description: '',
    route: '',
    code: ''
})

const selectedOperations = ref<string[]>([])

const processedRoute = computed(() => {
    if (!sectionForm.value.route) return ''
    return sectionForm.value.route
        .replace(/\s+/g, '.') // Convert spaces to dots
        .replace(/[^a-zA-Z0-9._-]/g, '') // Keep only alphanumeric, dots, dashes, underscores
        .replace(/_{2,}/g, '_') // Replace multiple underscores with single
        .replace(/-{2,}/g, '-') // Replace multiple dashes with single
        .replace(/\.{2,}/g, '.') // Replace multiple dots with single
        .replace(/^[-_\.]+|[-_\.]+$/g, '') // Remove leading/trailing separators
        .toLowerCase()
})

const rules = {
    required: (value: string) => !!value || 'Required'
}

// Watch sectionForm.route and update value by replacing spaces with dots
watch(() => sectionForm.value.route, (newValue: string) => {
    if (typeof newValue === 'string') {
        sectionForm.value.route = newValue.replace(/\s+/g, '.')
            .replace(/_{2,}/g, '_') // Replace multiple underscores with single
            .replace(/-{2,}/g, '-') // Replace multiple dashes with single
            .replace(/\.{2,}/g, '.') // Replace multiple dots with single
            .toLowerCase()
    }
})

const onRouteInput = (value: any) => {
    // Replace spaces with dots in real-time, ensure value is a string
    if (typeof value === 'string') {
        sectionForm.value.route = value.replace(/\s+/g, '.')
    }
}

const onCodeInput = (value: any) => {
    // Replace spaces with underscores and keep only alphabetic characters
    if (typeof value === 'string') {
        sectionForm.value.code = value
            .replace(/\s+/g, '_') // Convert spaces to underscores
            .replace(/_{2,}/g, '_') // Replace multiple underscores with single
            .toLowerCase()
    }
}

// Watch sectionForm.code and update value using all processing rules
watch(() => sectionForm.value.code, (newValue: string) => {
    if (typeof newValue === 'string') {
        sectionForm.value.code = newValue
            .replace(/\s+/g, '_') // Convert spaces to underscores
            .replace(/_{2,}/g, '_') // Replace multiple underscores with single
            .toLowerCase()
    }
})

const closeModal = () => {
    modal.close()
}

const saveSection = async () => {
    const formEl = sectionFormRef.value
    const result = await formEl?.validate?.()

    if (result?.valid === false) return

    if (selectedOperations.value.length === 0) {
        // You can add a toast notification here
        console.error('Please select at least one operation')
        return
    }

    isSavingSection.value = true

    try {
        const newSection = {
            name: sectionForm.value.name,
            route: processedRoute.value,
            code: sectionForm.value.code,
            description: sectionForm.value.description || '',
            operations: selectedOperations.value
        }

        const response = await rolesPermissionsApi.createPermissionSection(newSection)
        
        if (response.success) {
            // Add the new section to the local state
            const sectionToAdd = response.data ? {
                ...response.data,
                description: response.data.description || ''
            } : {
                ...newSection,
                id: Date.now() // Use timestamp as temporary id
            }
            permissionSections.value.push(sectionToAdd)
            
            // Close the modal on success
            closeModal()
            
            // You can add a success toast notification here
            console.log('Permission section created successfully:', response.data)
        } else {
            console.error('Failed to create permission section:', response.message)
            // You can add an error toast notification here
        }
    } catch (error) {
        console.error('Failed to save section:', error)
        // You can add an error toast notification here
    } finally {
        isSavingSection.value = false
    }
}
</script>

<style scoped>
.operations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 8px;
}
</style>
