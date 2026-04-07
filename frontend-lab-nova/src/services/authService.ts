import apiClient from './api'
import { User, LoginRequest, LoginResponse, AuthUser } from '../types'

export const authService = {
  login: async (credentials: LoginRequest): Promise<LoginResponse> => {
    const response = await apiClient.post('/login', credentials)
    if (response.data.token) {
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
    }
    return response.data
  },

  logout: async (): Promise<void> => {
    await apiClient.post('/logout')
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  },

  getCurrentUser: (): AuthUser | null => {
    const user = localStorage.getItem('user')
    return user ? JSON.parse(user) : null
  },

  isAuthenticated: (): boolean => {
    return !!localStorage.getItem('token')
  },

  refreshToken: async (): Promise<string> => {
    const response = await apiClient.post('/refresh-token')
    const token = response.data.token
    localStorage.setItem('token', token)
    return token
  },

  register: async (userData: Partial<User>): Promise<User> => {
    const response = await apiClient.post('/register', userData)
    return response.data.user
  },
}
