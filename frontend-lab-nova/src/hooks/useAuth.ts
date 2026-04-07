import { useEffect, useState } from 'react'
import { authService } from '../services'
import { AuthUser } from '../types'

export const useAuth = () => {
  const [user, setUser] = useState<AuthUser | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [isAuthenticated, setIsAuthenticated] = useState(false)

  useEffect(() => {
    const currentUser = authService.getCurrentUser()
    const authenticated = authService.isAuthenticated()
    
    setUser(currentUser)
    setIsAuthenticated(authenticated)
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
