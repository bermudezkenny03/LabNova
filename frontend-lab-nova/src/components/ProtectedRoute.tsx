import React from 'react'
import { Navigate } from 'react-router-dom'
import { useAuth } from '../hooks/useAuth'
import { usePermissions } from '../hooks/usePermissions'

interface ProtectedRouteProps {
  children: React.ReactNode
  requiredModule?: 'users' | 'equipment' | 'reservations' | 'categories' | 'reports' | 'dashboard'
  requiredRoles?: string[]
  fallback?: React.ReactNode
}

/**
 * Componente para rutas protegidas por autenticación y permisos
 */
export const ProtectedRoute: React.FC<ProtectedRouteProps> = ({
  children,
  requiredModule,
  requiredRoles,
  fallback,
}) => {
  const { isAuthenticated } = useAuth()
  const perms = usePermissions()

  // Si no está autenticado, redirigir al login
  if (!isAuthenticated) {
    return <Navigate to="/login" replace />
  }

  // Verificar si tiene acceso al módulo
  if (requiredModule && !perms.canView(requiredModule)) {
    return fallback ? (
      <>{fallback}</>
    ) : (
      <div style={{ padding: '20px', textAlign: 'center' }}>
        <h1>Acceso Denegado</h1>
        <p>No tienes permiso para acceder a esta página.</p>
        <Navigate to="/" replace />
      </div>
    )
  }

  // Verificar rol específico
  if (requiredRoles && !requiredRoles.includes(perms.userRole || '')) {
    return fallback ? (
      <>{fallback}</>
    ) : (
      <div style={{ padding: '20px', textAlign: 'center' }}>
        <h1>Acceso Denegado</h1>
        <p>Tu rol no tiene permisos para acceder a esta página.</p>
        <Navigate to="/" replace />
      </div>
    )
  }

  return <>{children}</>
}
