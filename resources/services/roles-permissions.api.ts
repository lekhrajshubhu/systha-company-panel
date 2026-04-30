import { http } from "@/services/http.config";
import { type Role } from "@/composables/useRoles";
import { type PermissionSection } from "@/composables/usePermissionSections";

export { type Role } from "@/composables/useRoles";
export { type PermissionSection } from "@/composables/usePermissionSections";

export interface CreatePermissionSectionRequest {
  name: string
  route: string
  code: string
  description?: string
  operations: string[]
}

export interface CreatePermissionSectionResponse {
  success: boolean
  message: string
  data?: PermissionSection
}

export interface AssignRolePermissionsRequest {
  permissions: string[]
}

export interface AssignRolePermissionsResponse {
  success: boolean
  message: string
  data?: {
    role_id: number
    permissions: string[]
  }
}

export const rolesPermissionsApi = {
  /**
   * Get all roles
   */
  getRoleList: async (): Promise<Role[]> => {
    try {
      const response = await http.get('company/roles')
      return response.data.data
    } catch (error) {
      console.error('Failed to get role list:', error)
      throw error
    }
  },

  /**
   * Create a new permission section
   */
  createPermissionSection: async (data: CreatePermissionSectionRequest): Promise<CreatePermissionSectionResponse> => {
    try {
      const response = await http.post('company/permission-sections', data)
      return response.data
    } catch (error) {
      console.error('Failed to create permission section:', error)
      throw error
    }
  },

  /**
   * Get all permission sections
   */
  getPermissionSections: async (): Promise<PermissionSection[]> => {
    try {
      const response = await http.get('company/permission-sections')
      return response.data.data
    } catch (error) {
      console.error('Failed to fetch permission sections:', error)
      throw error
    }
  },

  /**
   * Update a permission section
   */
  updatePermissionSection: async (id: string, data: Partial<CreatePermissionSectionRequest>): Promise<CreatePermissionSectionResponse> => {
    try {
      const response = await http.put(`company/permission-sections/${id}`, data)
      return response.data
    } catch (error) {
      console.error('Failed to update permission section:', error)
      throw error
    }
  },

  /**
   * Delete a permission section
   */
  deletePermissionSection: async (id: string): Promise<{ success: boolean; message: string }> => {
    try {
      const response = await http.delete(`company/permission-sections/${id}`)
      return response.data
    } catch (error) {
      console.error('Failed to delete permission section:', error)
      throw error
    }
  },

  /**
   * Assign permissions to a role
   */
  assignRolePermissions: async (roleId: number, data: AssignRolePermissionsRequest): Promise<AssignRolePermissionsResponse> => {
    try {
      const response = await http.post(`company/roles/${roleId}/assign-permissions`, data)
      return response.data
    } catch (error) {
      console.error('Failed to assign role permissions:', error)
      throw error
    }
  }
}
