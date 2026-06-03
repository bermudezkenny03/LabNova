import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { useAuth } from '../hooks'
import { useQuickGuide } from '../context/QuickGuideContext'
import { IfCan } from '../components/ProtectedFeature'
import { reservationService } from '../services/reservationService'
import { reportService } from '../services/reportService'
import { Reservation } from '../types'

interface Stats {
  totalEquipment: number
  availableEquipment: number
  maintenanceEquipment: number
  outOfServiceEquipment: number
  totalReservations: number
  pendingReservations: number
  approvedReservations: number
}

const statusColors: Record<Reservation['status'], string> = {
  pending: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800',
  cancelled: 'bg-gray-100 text-gray-600',
  completed: 'bg-blue-100 text-blue-800',
}

const statusLabels: Record<Reservation['status'], string> = {
  pending: 'Pendiente',
  approved: 'Aprobada',
  rejected: 'Rechazada',
  cancelled: 'Cancelada',
  completed: 'Completada',
}

const StatCard: React.FC<{
  title: string
  value: number
  subtitle?: string
  color: string
  icon: string
  to?: string
  highlight?: boolean
}> = ({ title, value, subtitle, color, icon, to, highlight }) => {
  const content = (
    <div className={`bg-white rounded-3xl shadow-sm border border-slate-200 p-5 flex items-start gap-4 hover:shadow-lg transition-shadow ${to ? 'cursor-pointer' : ''} ${highlight ? 'ring-2 ring-yellow-300/40' : ''}`}>
      <div className={`text-2xl p-3 rounded-3xl ${color} shrink-0`}>{icon}</div>
      <div className="min-w-0">
        <p className="text-sm text-slate-500 truncate">{title}</p>
        <p className="text-3xl font-semibold text-slate-900 mt-1">{value}</p>
        {subtitle && <p className="text-xs text-slate-400 mt-1">{subtitle}</p>}
      </div>
    </div>
  )
  return to ? <Link to={to}>{content}</Link> : content
}

const Dashboard: React.FC = () => {
  const { user } = useAuth()
  const roleName = (user?.role as { name?: string })?.name ?? 'Usuario'
  const rolePhrase =
    roleName === 'Super Admin'
      ? 'Control ejecutivo del sistema y visibilidad completa de reservas, usuarios y equipos.'
      : roleName === 'Lab Manager'
        ? 'Administra el laboratorio con datos claros y decisiones rápidas.'
        : roleName === 'Student'
          ? 'Revisa tus reservas y mantente al día con el estado de tus solicitudes.'
          : 'Accede a tu panel con información relevante para tu rol.'

  const [stats, setStats] = useState<Stats>({
    totalEquipment: 0,
    availableEquipment: 0,
    maintenanceEquipment: 0,
    outOfServiceEquipment: 0,
    totalReservations: 0,
    pendingReservations: 0,
    approvedReservations: 0,
  })
  const [recentReservations, setRecentReservations] = useState<Reservation[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    const load = async () => {
      setLoading(true)
      setError(null)

      const [equipResult, resStatsResult, reservationsResult] = await Promise.allSettled([
        reportService.getEquipmentStats(),
        reportService.getReservationStats(),
        reservationService.getAllReservations(1, 8),
      ])

      const equipStats      = equipResult.status      === 'fulfilled' ? equipResult.value      : null
      const resStats        = resStatsResult.status   === 'fulfilled' ? resStatsResult.value   : null
      const reservationsRes = reservationsResult.status === 'fulfilled' ? reservationsResult.value : null

      // Solo mostrar error si la llamada de reservas recientes falla (todas las vistas la necesitan)
      if (reservationsResult.status === 'rejected') {
        setError('No se pudo cargar la información. Verifica la conexión con el servidor.')
      }

      setStats({
        totalEquipment:       equipStats?.total          ?? 0,
        availableEquipment:   equipStats?.available      ?? 0,
        maintenanceEquipment: equipStats?.maintenance    ?? 0,
        outOfServiceEquipment:equipStats?.out_of_service ?? 0,
        totalReservations:    resStats?.total            ?? 0,
        pendingReservations:  resStats?.pending          ?? 0,
        approvedReservations: resStats?.approved         ?? 0,
      })

      setRecentReservations(reservationsRes?.data ?? [])
      setLoading(false)
    }
    load()
  }, [])

  const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' })

  const Skeleton = () => (
    <div className="animate-pulse bg-gray-200 rounded-xl h-24 w-full" />
  )

  const { openGuide } = useQuickGuide()

  return (
    <div className="space-y-6">
      {/* Encabezado */}
      <div className="grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
        <div className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="text-sm uppercase tracking-[0.3em] text-slate-400 mb-3">Panel principal</p>
              <div className="flex flex-wrap items-center gap-2 sm:gap-3">
                <h1 className="text-3xl font-bold text-slate-900">
                  Bienvenido, {user?.name ?? 'Usuario'}
                </h1>
                <span className="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                  {roleName}
                </span>
              </div>
              <p className="mt-2 text-sm text-slate-500 max-w-2xl">
                {rolePhrase}
              </p>
              <p className="mt-3 text-sm text-slate-500">
                {new Date().toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
              </p>
            </div>
            <button
              type="button"
              onClick={openGuide}
              className="inline-flex items-center gap-2 rounded-3xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/10 hover:bg-blue-700 transition"
            >
              <span>Guía rápida</span>
            </button>
          </div>

          <div className="mt-6 grid gap-3 sm:grid-cols-3">
            <div className="rounded-3xl bg-slate-950 p-4 text-white shadow-sm">
              <p className="text-xs uppercase tracking-[0.3em] text-slate-400">Reservas totales</p>
              <p className="mt-2 text-3xl font-semibold">{stats.totalReservations}</p>
            </div>
            <div className="rounded-3xl bg-amber-100/90 p-4 text-slate-950 shadow-sm">
              <p className="text-xs uppercase tracking-[0.3em] text-slate-500">Pendientes</p>
              <p className="mt-2 text-3xl font-semibold">{stats.pendingReservations}</p>
            </div>
            <div className="rounded-3xl bg-emerald-100/90 p-4 text-slate-950 shadow-sm">
              <p className="text-xs uppercase tracking-[0.3em] text-slate-500">Aprobadas</p>
              <p className="mt-2 text-3xl font-semibold">{stats.approvedReservations}</p>
            </div>
          </div>
        </div>

        <div className="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-950 to-slate-900 p-6 text-white shadow-sm">
          <p className="text-xs uppercase tracking-[0.3em] text-slate-400">Resumen ejecutivo</p>
          <h2 className="mt-3 text-2xl font-semibold">Estado operativo</h2>
          <p className="mt-3 text-sm text-slate-300 leading-relaxed">
            Esta sección muestra el desempeño actual del sistema con los principales indicadores de equipo y reservas.
          </p>
          <div className="mt-5 grid gap-3 sm:grid-cols-2">
            <div className="rounded-3xl bg-slate-900/90 p-4 border border-slate-800 shadow-sm">
              <p className="text-xs uppercase tracking-[0.2em] text-slate-500">Equipos disponibles</p>
              <p className="mt-3 text-3xl font-semibold text-white">{stats.availableEquipment}</p>
            </div>
            <div className="rounded-3xl bg-slate-900/90 p-4 border border-slate-800 shadow-sm">
              <p className="text-xs uppercase tracking-[0.2em] text-slate-500">Reservas aprobadas</p>
              <p className="mt-3 text-3xl font-semibold text-white">{stats.approvedReservations}</p>
            </div>
            <div className="rounded-3xl bg-slate-900/90 p-4 border border-slate-800 shadow-sm">
              <p className="text-xs uppercase tracking-[0.2em] text-slate-500">Solicitudes pendientes</p>
              <p className="mt-3 text-3xl font-semibold text-white">{stats.pendingReservations}</p>
            </div>
            <div className="rounded-3xl bg-slate-900/90 p-4 border border-slate-800 shadow-sm">
              <p className="text-xs uppercase tracking-[0.2em] text-slate-500">Disponibilidad total</p>
              <p className="mt-3 text-3xl font-semibold text-white">{stats.totalEquipment}</p>
            </div>
          </div>
        </div>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
          {error}
        </div>
      )}

      {/* Stats: Equipos */}
      <IfCan permission={{ module: 'equipment', action: 'view' }}>
        <div>
          <h2 className="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Equipos</h2>
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {loading ? (
              [...Array(4)].map((_, i) => <Skeleton key={i} />)
            ) : (
              <>
                <StatCard title="Total Equipos" value={stats.totalEquipment} color="bg-blue-100 text-blue-600" icon="🔬" to="/equipment" />
                <StatCard title="Disponibles" value={stats.availableEquipment} subtitle="Listos para reservar" color="bg-green-100 text-green-600" icon="✅" to="/equipment" />
                <StatCard title="Mantenimiento" value={stats.maintenanceEquipment} color="bg-orange-100 text-orange-600" icon="🔧" to="/equipment" />
                <StatCard title="Fuera de servicio" value={stats.outOfServiceEquipment} color="bg-red-100 text-red-600" icon="⛔" to="/equipment" />
              </>
            )}
          </div>
        </div>
      </IfCan>

      {/* Stats: Reservas */}
      <IfCan permission={{ module: 'reservations', action: 'view' }}>
        <div>
          <h2 className="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Reservas</h2>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {loading ? (
              [...Array(3)].map((_, i) => <Skeleton key={i} />)
            ) : (
              <>
                <StatCard title="Total Reservas" value={stats.totalReservations} color="bg-purple-100 text-purple-600" icon="📋" to="/reservations" />
                <StatCard title="Pendientes" value={stats.pendingReservations} subtitle="Esperando aprobacion" color="bg-yellow-100 text-yellow-600" icon="⏳" to="/reservations" highlight={stats.pendingReservations > 0} />
                <StatCard title="Aprobadas" value={stats.approvedReservations} color="bg-green-100 text-green-600" icon="📅" to="/reservations" />
              </>
            )}
          </div>
        </div>
      </IfCan>

      {/* Reservas recientes */}
      <IfCan permission={{ module: 'reservations', action: 'view' }}>
        <div className="bg-white rounded-xl shadow-sm border border-gray-100">
          <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 className="text-base font-semibold text-gray-800">Reservas Recientes</h2>
            <Link to="/reservations" className="text-sm text-blue-600 hover:text-blue-800 font-medium">
              Ver todas →
            </Link>
          </div>

          {loading ? (
            <div className="p-6 space-y-3">
              {[...Array(5)].map((_, i) => (
                <div key={i} className="h-10 bg-gray-100 rounded-lg animate-pulse" />
              ))}
            </div>
          ) : recentReservations.length === 0 ? (
            <div className="p-10 text-center">
              <p className="text-gray-400 text-sm">No hay reservas registradas aun.</p>
              <Link to="/reservations" className="mt-3 inline-block text-sm text-blue-600 hover:underline">
                Crear primera reserva
              </Link>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm text-left">
                <thead>
                  <tr className="bg-gray-50 text-gray-500 text-xs uppercase">
                    <th className="px-6 py-3 font-medium">Usuario</th>
                    <th className="px-6 py-3 font-medium">Equipo</th>
                    <th className="px-6 py-3 font-medium">Inicio</th>
                    <th className="px-6 py-3 font-medium">Fin</th>
                    <th className="px-6 py-3 font-medium">Estado</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-50">
                  {recentReservations.map((r) => (
                    <tr key={r.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-6 py-3 font-medium text-gray-800">
                        {r.user?.name ?? `Usuario #${r.user_id}`}
                      </td>
                      <td className="px-6 py-3 text-gray-600">
                        {r.equipment?.name ?? `Equipo #${r.equipment_id}`}
                      </td>
                      <td className="px-6 py-3 text-gray-500">{formatDate(r.start_time)}</td>
                      <td className="px-6 py-3 text-gray-500">{formatDate(r.end_time)}</td>
                      <td className="px-6 py-3">
                        <span className={`px-2.5 py-1 rounded-full text-xs font-semibold ${statusColors[r.status]}`}>
                          {statusLabels[r.status]}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </IfCan>

      {/* Accesos rapidos */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 className="text-base font-semibold text-gray-800 mb-4">Accesos Rapidos</h2>
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <IfCan permission={{ module: 'reservations', action: 'create' }}>
            <Link
              to="/reservations"
              className="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition-colors text-center"
            >
              <span className="text-2xl mb-2">📅</span>
              <span className="text-sm font-medium text-gray-600">Nueva Reserva</span>
            </Link>
          </IfCan>
          <IfCan permission={{ module: 'equipment', action: 'view' }}>
            <Link
              to="/equipment"
              className="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-green-400 hover:bg-green-50 transition-colors text-center"
            >
              <span className="text-2xl mb-2">🔬</span>
              <span className="text-sm font-medium text-gray-600">Ver Equipos</span>
            </Link>
          </IfCan>
          <IfCan permission={{ module: 'users', action: 'view' }}>
            <Link
              to="/users"
              className="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-purple-400 hover:bg-purple-50 transition-colors text-center"
            >
              <span className="text-2xl mb-2">👥</span>
              <span className="text-sm font-medium text-gray-600">Usuarios</span>
            </Link>
          </IfCan>
          <IfCan permission={{ module: 'reports', action: 'view' }}>
            <Link
              to="/reports"
              className="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-orange-400 hover:bg-orange-50 transition-colors text-center"
            >
              <span className="text-2xl mb-2">📊</span>
              <span className="text-sm font-medium text-gray-600">Reportes</span>
            </Link>
          </IfCan>
        </div>
      </div>
    </div>
  )
}

export default Dashboard
