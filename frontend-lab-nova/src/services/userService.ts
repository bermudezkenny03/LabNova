import apiClient from './api'
import { User, UserDetail, PaginatedResponse } from '../types'

export const userService = {
  // GET /users → { message, users: [...] }  (no pagination)
  getAllUsers: async (): Promise<PaginatedResponse<User>> => {
    const response = await apiClient.get('/users')
    const users: User[] = response.data.users ?? []
    return {
      data: users,
      current_page: 1,
      last_page: 1,
      per_page: users.length,
      total: users.length,
    }
  },

  // GET /users/:id → { message, user: {...} }
  getUserById: async (id: number): Promise<User> => {
    const response = await apiClient.get(`/users/${id}`)
    return response.data.user
  },

  // POST /users → { message, user: {...} }
  createUser: async (data: Record<string, unknown>): Promise<User> => {
    const response = await apiClient.post('/users', data)
    return response.data.user
  },

  // PUT /users/:id → { message, user: {...} }
  updateUser: async (id: number, data: Record<string, unknown>): Promise<User> => {
    const response = await apiClient.put(`/users/${id}`, data)
    return response.data.user
  },

  // DELETE /users/:id → { message }
  deleteUser: async (id: number): Promise<void> => {
    await apiClient.delete(`/users/${id}`)
  },

  getUserDetail: async (userId: number): Promise<UserDetail> => {
    const response = await apiClient.get(`/users/${userId}/detail`)
    return response.data.data
  },

  updateUserDetail: async (userId: number, data: Partial<UserDetail>): Promise<UserDetail> => {
    const response = await apiClient.put(`/users/${userId}/detail`, data)
    return response.data.data
  },

  // GET /users/general-data → { message, roles: [...] }
  getGeneralData: async (): Promise<{ roles: Array<{ id: number; name: string; description: string }> }> => {
    const response = await apiClient.get('/users/general-data')
    return { roles: response.data.roles ?? [] }
  },

  searchUsers: async (query: string): Promise<User[]> => {
    const response = await apiClient.get('/users/search', {
      params: { q: query },
    })
    return response.data.data ?? response.data.users ?? []
  },
}
