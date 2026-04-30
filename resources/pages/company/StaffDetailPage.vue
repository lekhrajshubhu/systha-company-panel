<template>
  <v-container fluid>
    <AppPageHeader title="User Detail" subtitle="View and manage user details." show-back />

    <v-container fluid>
      <v-card flat class="mb-6">
        <v-progress-linear
          v-if="loading"
          indeterminate
          color="primary"
        />

        <v-card-text class="pa-4">
          <v-alert
            v-if="errorMessage"
            type="error"
            variant="tonal"
            class="mb-4"
          >
            {{ errorMessage }}
          </v-alert>

          <template v-if="detail">
            <div class="d-flex align-start ga-4 flex-wrap">
              <v-avatar
                color="primary"
                variant="tonal"
                rounded
                size="56"
              >
                <v-img
                  v-if="detail.client.avatar"
                  :src="detail.client.avatar"
                  cover
                />
                <v-icon v-else>mdi-account</v-icon>
              </v-avatar>

              <div class="flex-1">
                <div class="d-flex align-center ga-2 flex-wrap">
                  <div class="text-h6 font-weight-medium">
                    {{ fullName || '-' }}
                  </div>
                  <v-chip
                    v-if="detail.status"
                    size="small"
                    variant="tonal"
                    class="text-uppercase"
                  >
                    {{ detail.status }}
                  </v-chip>
                </div>

                <div class="text-body-2 text-medium-emphasis">
                  {{ detail.client.email || '-' }}
                </div>
                <div class="text-body-2 text-medium-emphasis">
                  {{ detail.client.phone || '-' }}
                </div>
              </div>
            </div>

            <v-divider class="my-4" />

            <v-row>
              <v-col cols="12" md="6">
                <div class="text-subtitle-1 font-weight-medium mb-2">Address</div>
                <div class="text-body-2">
                  <div>{{ detail.address.line1 || '-' }}</div>
                  <div v-if="detail.address.line2">{{ detail.address.line2 }}</div>
                  <div>
                    {{ [detail.address.city, detail.address.state].filter(Boolean).join(', ') }}
                    <span v-if="detail.address.zip"> {{ detail.address.zip }}</span>
                  </div>
                </div>

                <div class="mt-4">
                  <div class="text-subtitle-1 font-weight-medium mb-2">Assigned Roles</div>
                  <div class="d-flex align-center flex-wrap ga-2">
                    <v-chip
                      v-for="role in detail.roles.assigned"
                      :key="role.id"
                      size="small"
                      variant="tonal"
                      class="text-uppercase"
                    >
                      {{ role.label || role.name || '-' }}
                    </v-chip>
                    <span v-if="detail.roles.assigned.length === 0" class="text-body-2 text-disabled">-</span>
                  </div>
                </div>
              </v-col>

              <v-col cols="12" md="6">
                <div class="text-subtitle-1 font-weight-medium mb-2">Roles</div>

                <div class="d-flex flex-column ga-1">
                  <v-checkbox
                    v-for="role in detail.roles.available"
                    :key="role.id"
                    v-model="selectedRoleIds"
                    :value="role.id"
                    density="compact"
                    hide-details="auto"
                  >
                    <template #label>
                      <span class="text-body-2">
                        {{ role.label || role.name || '-' }}
                      </span>
                    </template>
                  </v-checkbox>
                </div>

                <v-alert
                  v-if="rolesUpdateError"
                  type="error"
                  variant="tonal"
                  class="mt-3"
                >
                  {{ rolesUpdateError }}
                </v-alert>

                <v-alert
                  v-if="rolesUpdateSuccess"
                  type="success"
                  variant="tonal"
                  class="mt-3"
                >
                  Roles updated successfully.
                </v-alert>

                <div class="d-flex justify-end mt-4">
                  <v-btn
                    color="primary"
                    variant="flat"
                    :loading="savingRoles"
                    :disabled="savingRoles || !hasRoleChanges"
                    @click="saveRoles"
                  >
                    Update
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </template>
        </v-card-text>
      </v-card>
    </v-container>
  </v-container>
</template>

<script setup lang="ts">
import AppPageHeader from '@/components/AppPageHeader.vue'
import { getCompanyUserDetail, updateCompanyUser } from '@/services/users.api'
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

type Role = {
  id: number
  label: string
  name: string
}

type Client = {
  id: number
  first_name: string
  last_name: string
  email: string
  phone: string
  avatar: string
}

type Address = {
  line1: string
  line2: string | null
  city: string
  state: string
  zip: string
  lat: number | null
  lng: number | null
}

type UserDetail = {
  company_user_id: number
  status: string
  client: Client
  address: Address
  roles: {
    available: Role[]
    assigned: Role[]
  }
}

const route = useRoute()
const loading = ref(false)
const errorMessage = ref('')
const detail = ref<UserDetail | null>(null)

const userId = computed(() => route.params.id as string | number | undefined)

const selectedRoleIds = ref<number[]>([])
const initialAssignedRoleIds = ref<number[]>([])
const savingRoles = ref(false)
const rolesUpdateError = ref('')
const rolesUpdateSuccess = ref(false)

const fullName = computed(() => {
  if (!detail.value) return ''
  const first = detail.value.client.first_name ?? ''
  const last = detail.value.client.last_name ?? ''
  return `${first} ${last}`.trim()
})

const sameIdSet = (a: number[], b: number[]): boolean => {
  if (a.length !== b.length) return false
  const setA = new Set(a)
  if (setA.size !== b.length) return false
  return b.every((id) => setA.has(id))
}

const hasRoleChanges = computed(() => {
  return !sameIdSet(selectedRoleIds.value, initialAssignedRoleIds.value)
})

const loadUserDetail = async (): Promise<void> => {
  if (!userId.value) {
    detail.value = null
    errorMessage.value = 'User id is missing.'
    return
  }

  loading.value = true
  errorMessage.value = ''
  try {
    const res: any = await getCompanyUserDetail(userId.value)
    const payload = res?.data ?? res
    detail.value = payload as UserDetail

    const assignedIds = detail.value.roles.assigned.map((r) => r.id)
    selectedRoleIds.value = [...assignedIds]
    initialAssignedRoleIds.value = [...assignedIds]
    rolesUpdateError.value = ''
    rolesUpdateSuccess.value = false
  } catch (error) {
    console.error('Failed to load user detail:', error)
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load user detail.'
    detail.value = null
  } finally {
    loading.value = false
  }
}

const saveRoles = async (): Promise<void> => {
  if (!detail.value || !userId.value || savingRoles.value) return

  savingRoles.value = true
  rolesUpdateError.value = ''
  rolesUpdateSuccess.value = false

  try {
    await updateCompanyUser(userId.value, { roles: selectedRoleIds.value })

    const assigned = detail.value.roles.available.filter((role) =>
      selectedRoleIds.value.includes(role.id),
    )
    detail.value = {
      ...detail.value,
      roles: {
        ...detail.value.roles,
        assigned,
      },
    }
    initialAssignedRoleIds.value = [...selectedRoleIds.value]
    rolesUpdateSuccess.value = true
  } catch (error) {
    console.error('Failed to update roles:', error)
    rolesUpdateError.value = error instanceof Error ? error.message : 'Failed to update roles.'
  } finally {
    savingRoles.value = false
  }
}

onMounted(() => {
  loadUserDetail()
})
</script>
