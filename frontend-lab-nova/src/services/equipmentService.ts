import apiClient from './api'
import { Equipment, Category, PaginatedResponse } from '../types'

export const equipmentService = {
  // GET /equipment → { success, data: [...] }  (no pagination - flat array)
  getAllEquipment: async (): Promise<PaginatedResponse<Equipment>> => {
    const response = await apiClient.get('/equipment')
    const equipment: Equipment[] = response.data.data ?? []
    return {
      data: equipment,
      current_page: 1,
      last_page: 1,
      per_page: equipment.length,
      total: equipment.length,
    }
  },

  // GET /equipment/:id → { success, data: {...} }
  getEquipmentById: async (id: number): Promise<Equipment> => {
    const response = await apiClient.get(`/equipment/${id}`)
    return response.data.data
  },

  // POST /equipment → { success, data: {...} }
  createEquipment: async (data: Partial<Equipment>): Promise<Equipment> => {
    const response = await apiClient.post('/equipment', data)
    return response.data.data
  },

  // PUT /equipment/:id → { success, data: {...} }
  updateEquipment: async (id: number, data: Partial<Equipment>): Promise<Equipment> => {
    const response = await apiClient.put(`/equipment/${id}`, data)
    return response.data.data
  },

  // DELETE /equipment/:id → { success, message }
  deleteEquipment: async (id: number): Promise<void> => {
    await apiClient.delete(`/equipment/${id}`)
  },

  // GET /categories → { success, data: [...] }
  getCategories: async (): Promise<Category[]> => {
    const response = await apiClient.get('/categories')
    return response.data.data ?? []
  },

  getCategoryById: async (id: number): Promise<Category> => {
    const response = await apiClient.get(`/categories/${id}`)
    return response.data.data
  },

  createCategory: async (data: { name: string; description?: string }): Promise<Category> => {
    const response = await apiClient.post('/categories', data)
    return response.data.data
  },

  updateCategory: async (id: number, data: { name: string; description?: string; status?: boolean }): Promise<Category> => {
    const response = await apiClient.put(`/categories/${id}`, data)
    return response.data.data
  },

  deleteCategory: async (id: number): Promise<void> => {
    await apiClient.delete(`/categories/${id}`)
  },

  searchEquipment: async (query: string): Promise<Equipment[]> => {
    const response = await apiClient.get('/equipment/search', {
      params: { q: query },
    })
    return response.data.data ?? []
  },
}
