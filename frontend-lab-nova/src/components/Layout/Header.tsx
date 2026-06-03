import React from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useAuth } from '../../hooks'

interface HeaderProps {
  onToggleSidebar: () => void
  onOpenGuide: () => void
}

const Header: React.FC<HeaderProps> = ({ onToggleSidebar, onOpenGuide }) => {
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
  const roleBadgeStyle =
    roleName === 'Super Admin'
      ? 'bg-amber-100 text-amber-700'
      : roleName === 'Lab Manager'
        ? 'bg-emerald-100 text-emerald-700'
        : 'bg-slate-100 text-slate-600'

  return (
    <header className="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between shrink-0 shadow-sm">

      <div className="flex items-center gap-3">
        <button
          type="button"
          onClick={onToggleSidebar}
          className="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-100 p-2 text-slate-600 hover:bg-slate-200 transition lg:hidden"
          aria-label="Abrir menú"
        >
          <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <div className="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs sm:text-sm text-slate-500 flex items-center gap-2 shadow-sm">
          <svg className="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span className="font-medium text-slate-700">LabNova</span>
          <span className="rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-[11px] font-semibold">App</span>
        </div>
      </div>

      <div className="flex items-center gap-2 sm:gap-3">
        <button
          type="button"
          onClick={onOpenGuide}
          title="Abrir guía rápida"
          className="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-slate-600 hover:bg-slate-200 transition"
          aria-label="Abrir guía rápida"
        >
          <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M8 10h.01M12 18h.01M16 10h.01M12 6a8 8 0 100 16 8 8 0 000-16z" />
          </svg>
          <span className="hidden sm:inline ml-2 text-sm font-medium">Guía</span>
        </button>

        <Link
          to="/profile"
          className="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm hover:shadow-md transition"
          title="Ver perfil"
        >
          <div className="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-sm font-semibold text-white shadow-sm">
            {initials}
          </div>
          <div className="hidden sm:block text-left">
            <p className="text-sm font-semibold text-slate-800">{fullName}</p>
            {roleName && (
              <span className={`inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-semibold ${roleBadgeStyle}`}>
                {roleName}
              </span>
            )}
          </div>
        </Link>

        <button
          onClick={handleLogout}
          title="Cerrar sesión"
          className="inline-flex items-center gap-2 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-red-600 hover:bg-red-100 transition"
        >
          <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span className="hidden sm:inline text-sm font-medium">Salir</span>
        </button>
      </div>
    </header>
  )
}

export default Header
