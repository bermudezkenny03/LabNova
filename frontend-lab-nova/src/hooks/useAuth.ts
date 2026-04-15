import { useEffect, useState } from 'react'
import { authService } from '../services'
import { AuthUser } from '../types'

export const useAuth = () => {
  const [user, setUser] = useState<AuthUser | null>(() => authService.getCurrentUser())
  const [isLoading, setIsLoading] = useState(false)
  const [isAuthenticated, setIsAuthenticated] = useState(() => authService.isAuthenticated())

  useEffect(() => {
    // Sync in case localStorage changed externally
    setUser(authService.getCurrentUser())
    setIsAuthenticated(authService.isAuthenticated())
    setIsLoading(false)
  }, [])

  const logout = async () => {
    try {
      await authService.logout()
      setUser(null)
      setIsAuthenticated(false)
    } catch (error) {
      console.error('Logout failed:', error)
    }
  }

  return {
    user,
    isLoading,
    isAuthenticated,
    logout,
  }
}
