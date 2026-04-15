import { useAuth } from './useAuth'

type Permission = 'view' | 'create' | 'edit' | 'delete'
type Module = 'users' | 'equipment' | 'reservations' | 'categories' | 'reports' | 'dashboard'

const rolePermissions: Record<string, Record<Module, Permission[]>> = {
  'Super Admin': {
    users: ['view', 'create', 'edit', 'delete'],
    equipment: ['view', 'create', 'edit', 'delete'],
    reservations: ['view', 'create', 'edit', 'delete'],
    categories: ['view', 'create', 'edit', 'delete'],
    reports: ['view', 'create', 'edit', 'delete'],
    dashboard: ['view'],
  },
  'Administrador': {
    users: ['view', 'create', 'edit', 'delete'],
    equipment: ['view', 'create', 'edit', 'delete'],
    reservations: ['view', 'edit'],
    categories: ['view', 'create', 'edit', 'delete'],
    reports: ['view', 'create', 'edit', 'delete'],
    dashboard: ['view'],
  },
  'Encargado de Laboratorio': {
    users: [],
    equipment: ['view', 'create', 'edit'],
    reservations: ['view', 'edit'],
    categories: ['view'],
    reports: ['view'],
    dashboard: [],
  },
  'Docente': {
    users: [],
    equipment: ['view'],
    reservations: ['view', 'create'],
    categories: ['view'],
    reports: ['view', 'create'],
    dashboard: [],
  },
  'Estudiante': {
    users: [],
    equipment: ['view'],
    reservations: ['view', 'create'],
    categories: ['view'],
    reports: [],
    dashboard: [],
  },
}

export const usePermissions = () => {
  const { user } = useAuth()

  const hasPermission = (module: Module, permission: Permission): boolean => {
    if (!user?.role?.name) return false
    const rolePerms = rolePermissions[user.role.name]
    if (!rolePerms) return false
    return rolePerms[module]?.includes(permission) ?? false
  }

  const can = (module: Module, permission: Permission): boolean => {
    return hasPermission(module, permission)
  }

  const canView = (module: Module): boolean => can(module, 'view')
  const canCreate = (module: Module): boolean => can(module, 'create')
  const canEdit = (module: Module): boolean => can(module, 'edit')
  const canDelete = (module: Module): boolean => can(module, 'delete')

  const isSuperAdmin = (): boolean => user?.role?.name === 'Super Admin'
  const isAdmin = (): boolean => ['Super Admin', 'Administrador'].includes(user?.role?.name || '')
  const isLabManager = (): boolean => user?.role?.name === 'Encargado de Laboratorio'
  const isTeacher = (): boolean => user?.role?.name === 'Docente'
  const isStudent = (): boolean => user?.role?.name === 'Estudiante'

  // Permisos específicos de Reservas
  const canCreateReservation = (): boolean => can('reservations', 'create')
  const canApproveReservation = (): boolean => isAdmin() || isLabManager() || isSuperAdmin()
  const canRejectReservation = (): boolean => isAdmin() || isLabManager() || isSuperAdmin()
  const canCancelReservation = (): boolean => user ? ['Estudiante', 'Docente', 'Encargado de Laboratorio', 'Administrador', 'Super Admin'].includes(user.role?.name || '') : false
  const canViewAllReservations = (): boolean => can('reservations', 'view')
  const canEditReservation = (): boolean => can('reservations', 'edit')

  // Permisos específicos de Usuarios
  const canManageUsers = (): boolean => can('users', 'edit')
  const canDeleteUser = (): boolean => can('users', 'delete')

  // Permisos específicos de Equipo
  const canManageEquipment = (): boolean => can('equipment', 'edit')

  return {
    can,
    hasPermission,
    canView,
    canCreate,
    canEdit,
    canDelete,
    isSuperAdmin,
    isAdmin,
    isLabManager,
    isTeacher,
    isStudent,
    // Permisos de Reservas
    canCreateReservation,
    canApproveReservation,
    canRejectReservation,
    canCancelReservation,
    canViewAllReservations,
    canEditReservation,
    // Permisos de Usuarios
    canManageUsers,
    canDeleteUser,
    // Permisos de Equipo
    canManageEquipment,
    userRole: user?.role?.name,
  }
}
