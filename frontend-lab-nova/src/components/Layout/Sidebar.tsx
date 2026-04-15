import React from 'react'
import { Link, useLocation } from 'react-router-dom'
import { usePermissions } from '../../hooks/usePermissions'
import { useAuth } from '../../hooks'

// ─── SVG icons (Heroicons outline) ───────────────────────────────────────────

const IconDashboard = () => (
  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.75}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
  </svg>
)

const IconEquipment = () => (
  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.75}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
  </svg>
)

const IconReservations = () => (
  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.75}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
  </svg>
)

const IconReports = () => (
  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.75}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
  </svg>
)

const IconUsers = () => (
  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.75}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
  </svg>
)

// ─── Nav items ────────────────────────────────────────────────────────────────

interface NavItem {
  path: string
  label: string
  icon: React.ReactNode
  visible: boolean
}

// ─── Component ────────────────────────────────────────────────────────────────

const Sidebar: React.FC = () => {
  const location = useLocation()
  const perms    = usePermissions()
  const { user } = useAuth()

  const navItems: NavItem[] = [
    { path: '/',             label: 'Dashboard', icon: <IconDashboard />,     visible: true },
    { path: '/equipment',   label: 'Equipos',    icon: <IconEquipment />,     visible: perms.canView('equipment') },
    { path: '/reservations',label: 'Reservas',   icon: <IconReservations />,  visible: perms.canView('reservations') },
    { path: '/reports',     label: 'Reportes',   icon: <IconReports />,       visible: perms.canView('reports') },
    { path: '/users',       label: 'Usuarios',   icon: <IconUsers />,         visible: perms.canView('users') },
  ]

  const visibleItems = navItems.filter(item => item.visible)

  const fullName = user
    ? `${user.name}${(user as { last_name?: string }).last_name ? ' ' + (user as { last_name?: string }).last_name : ''}`
    : 'Usuario'

  const initials = user
    ? `${user.name.charAt(0)}${((user as { last_name?: string }).last_name ?? '').charAt(0)}`.toUpperCase()
    : 'U'

  return (
    <aside className="w-60 bg-slate-900 flex flex-col shrink-0 border-r border-slate-800">

      {/* Logo */}
      <div className="flex items-center gap-3 px-5 h-16 border-b border-slate-800 shrink-0">
        <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm shrink-0">
          <svg className="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
          </svg>
        </div>
        <div className="leading-tight min-w-0">
          <p className="text-white font-bold text-sm leading-none">LabNova</p>
          <p className="text-slate-500 text-xs mt-0.5 truncate">Sistema de reservas LabNova</p>
        </div>
      </div>

      {/* Navigation */}
      <nav className="flex-1 px-3 py-4 space-y-0.5">
        <p className="px-3 text-xs font-semibold text-slate-600 uppercase tracking-widest mb-2">
          Menú
        </p>
        {visibleItems.map(item => {
          const active = location.pathname === item.path
          return (
            <Link
              key={item.path}
              to={item.path}
              className={`flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all group relative ${
                active
                  ? 'bg-blue-600/15 text-blue-400'
                  : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100'
              }`}
            >
              {/* Accent bar */}
              {active && (
                <span className="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 bg-blue-500 rounded-r-full" />
              )}
              <span className={`shrink-0 transition-colors ${active ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300'}`}>
                {item.icon}
              </span>
              {item.label}
            </Link>
          )
        })}
      </nav>

      {/* User profile */}
      <div className="px-3 py-4 border-t border-slate-800">
        <Link
          to="/profile"
          className="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-colors group"
        >
          <div className="w-8 h-8 rounded-full bg-blue-600/20 border border-blue-600/30 flex items-center justify-center shrink-0">
            <span className="text-xs font-bold text-blue-400">{initials}</span>
          </div>
          <div className="min-w-0 flex-1">
            <p className="text-sm font-medium text-slate-300 truncate group-hover:text-white transition-colors">
              {fullName}
            </p>
            <p className="text-xs text-slate-600 truncate">
              {(user?.role as { name?: string })?.name ?? 'Sin rol'}
            </p>
          </div>
        </Link>
      </div>

    </aside>
  )
}

export default Sidebar
