import React from 'react'
import { usePermissions } from '../hooks/usePermissions'

interface ProtectedFeatureProps {
  children: React.ReactNode
  permission?: {
    module: 'users' | 'equipment' | 'reservations' | 'categories' | 'reports' | 'dashboard'
    action: 'view' | 'create' | 'edit' | 'delete'
  }
  requiredRole?: string | string[]
  fallback?: React.ReactNode
  className?: string
}

/**
 * Componente que muestra contenido solo si el usuario tiene los permisos requeridos
 */
export const ProtectedFeature: React.FC<ProtectedFeatureProps> = ({
  children,
  permission,
  requiredRole,
  fallback = null,
  className = '',
}) => {
  const perms = usePermissions()

  let hasAccess = true

  // Verificar permisos específicos
  if (permission) {
    hasAccess = perms.can(permission.module, permission.action)
  }

  // Verificar rol requerido
  if (hasAccess && requiredRole) {
    const roles = Array.isArray(requiredRole) ? requiredRole : [requiredRole]
    hasAccess = roles.includes(perms.userRole || '')
  }

  if (!hasAccess) {
    return <>{fallback}</>
  }

  return <div className={className}>{children}</div>
}

/**
 * Componente para botones protegidos
 */
interface ProtectedButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  permission?: {
    module: 'users' | 'equipment' | 'reservations' | 'categories' | 'reports' | 'dashboard'
    action: 'view' | 'create' | 'edit' | 'delete'
  }
  requiredRole?: string | string[]
  children: React.ReactNode
}

export const ProtectedButton: React.FC<ProtectedButtonProps> = ({
  permission,
  requiredRole,
  children,
  disabled = false,
  ...props
}) => {
  const perms = usePermissions()

  let hasAccess = true

  if (permission) {
    hasAccess = perms.can(permission.module, permission.action)
  }

  if (hasAccess && requiredRole) {
    const roles = Array.isArray(requiredRole) ? requiredRole : [requiredRole]
    hasAccess = roles.includes(perms.userRole || '')
  }

  if (!hasAccess) return null

  return (
    <button {...props} disabled={disabled}>
      {children}
    </button>
  )
}

/**
 * Componente para mostrar/ocultar elementos según permisos
 */
interface IfCanProps {
  permission: {
    module: 'users' | 'equipment' | 'reservations' | 'categories' | 'reports' | 'dashboard'
    action: 'view' | 'create' | 'edit' | 'delete'
  }
  children: React.ReactNode
  fallback?: React.ReactNode
}

export const IfCan: React.FC<IfCanProps> = ({ permission, children, fallback = null }) => {
  const perms = usePermissions()

  if (!perms.can(permission.module, permission.action)) {
    return <>{fallback}</>
  }

  return <>{children}</>
}
