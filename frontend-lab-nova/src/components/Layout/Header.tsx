import React from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useAuth } from '../../hooks'

const Header: React.FC = () => {
  const navigate    = useNavigate()
  const { user }    = useAuth()

  const handleLogout = () => {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    navigate('/login')
  }

  const fullName = user
    ? `${user.name}${(user as { last_name?: string }).last_name ? ' ' + (user as { last_name?: string }).last_name : ''}`
    : 'Usuario'

  const initials = user
    ? `${user.name.charAt(0)}${((user as { last_name?: string }).last_name ?? '').charAt(0)}`.toUpperCase()
    : 'U'

  const roleName = (user?.role as { name?: string })?.name ?? ''

  return (
    <header className="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between shrink-0">

      {/* Breadcrumb / page context (left) */}
      <div className="flex items-center gap-2 text-sm text-gray-400">
        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
          <path strokeLinecap="round" strokeLinejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span className="text-gray-300">/</span>
        <span className="text-gray-600 font-medium">LabNova</span>
      </div>

      {/* Right section */}
      <div className="flex items-center gap-3">

        {/* Profile */}
        <Link
          to="/profile"
          className="flex items-center gap-3 pl-3 pr-2 py-1.5 rounded-lg hover:bg-gray-50 transition-colors group"
          title="Ver perfil"
        >
          <div className="text-right hidden sm:block">
            <p className="text-sm font-semibold text-gray-800 leading-tight group-hover:text-blue-600 transition-colors">
              {fullName}
            </p>
            {roleName && (
              <p className="text-xs text-gray-400 leading-tight">{roleName}</p>
            )}
          </div>
          <div className="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 shadow-sm">
            <span className="text-xs font-bold text-white">{initials}</span>
          </div>
        </Link>

        {/* Divider */}
        <div className="w-px h-6 bg-gray-200" />

        {/* Logout */}
        <button
          onClick={handleLogout}
          title="Cerrar sesión"
          className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors"
        >
          <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span className="hidden sm:inline">Salir</span>
        </button>

      </div>
    </header>
  )
}

export default Header
