import { http } from "@/services/http.config";

export interface PermissionSection {
  id: string
  route: string
  name: string
  code: string
  description?: string
  operations: string[]
}

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

export const rolesPermissionsApi = {
  /**
   * Create a new permission section
   */
  createPermissionSection: async (data: CreatePermissionSectionRequest): Promise<CreatePermissionSectionResponse> => {
    try {
      const response = await http.post('/api/company/permission-sections', data)
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
      const response = await http.get('/api/company/permission-sections')
      return response.data
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
      const response = await http.put(`/api/company/permission-sections/${id}`, data)
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
      const response = await http.delete(`/api/company/permission-sections/${id}`)
      return response.data
    } catch (error) {
      console.error('Failed to delete permission section:', error)
      throw error
    }
  }
}
