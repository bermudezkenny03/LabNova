import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { useAuth } from '../hooks'
import { equipmentService } from '../services/equipmentService'
import { reservationService } from '../services/reservationService'
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
}> = ({ title, value, subtitle, color, icon, to }) => {
  const content = (
    <div className={`bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md transition-shadow ${to ? 'cursor-pointer' : ''}`}>
      <div className={`text-2xl p-3 rounded-xl ${color} shrink-0`}>{icon}</div>
      <div className="min-w-0">
        <p className="text-sm text-gray-500 truncate">{title}</p>
        <p className="text-3xl font-bold text-gray-800 mt-0.5">{value}</p>
        {subtitle && <p className="text-xs text-gray-400 mt-1">{subtitle}</p>}
      </div>
    </div>
  )
  return to ? <Link to={to}>{content}</Link> : content
}

const Dashboard: React.FC = () => {
  const { user } = useAuth()
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
      try {
        setLoading(true)
        setError(null)

        const [equipRes, resRes] = await Promise.all([
          equipmentService.getAllEquipment(),
          reservationService.getAllReservations(1, 100),
        ])

        const equipment = equipRes.data ?? []
        const reservations = resRes.data ?? []

        setStats({
          totalEquipment: equipRes.total,
          availableEquipment: equipment.filter((e) => e.status === 'available').length,
          maintenanceEquipment: equipment.filter((e) => e.status === 'maintenance').length,
          outOfServiceEquipment: equipment.filter((e) => e.status === 'out_of_service').length,
          totalReservations: resRes.total,
          pendingReservations: reservations.filter((r) => r.status === 'pending').length,
          approvedReservations: reservations.filter((r) => r.status === 'approved').length,
        })

        setRecentReservations(reservations.slice(0, 8))
      } catch (err) {
        setError('No se pudo cargar la informacion. Verifica la conexion con el servidor.')
        console.error(err)
      } finally {
        setLoading(false)
      }
    }
    load()
  }, [])

  const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' })

  const Skeleton = () => (
    <div className="animate-pulse bg-gray-200 rounded-xl h-24 w-full" />
  )

  return (
    <div className="space-y-6">
      {/* Encabezado */}
      <div>
        <h1 className="text-2xl font-bold text-gray-800">
          Bienvenido, {user?.name ?? 'Usuario'}
        </h1>
        <p className="text-gray-500 mt-1 text-sm">
          {new Date().toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
        </p>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
          {error}
        </div>
      )}

      {/* Stats: Equipos */}
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

      {/* Stats: Reservas */}
      <div>
        <h2 className="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Reservas</h2>
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {loading ? (
            [...Array(3)].map((_, i) => <Skeleton key={i} />)
          ) : (
            <>
              <StatCard title="Total Reservas" value={stats.totalReservations} color="bg-purple-100 text-purple-600" icon="📋" to="/reservations" />
              <StatCard title="Pendientes" value={stats.pendingReservations} subtitle="Esperando aprobacion" color="bg-yellow-100 text-yellow-600" icon="⏳" to="/reservations" />
              <StatCard title="Aprobadas" value={stats.approvedReservations} color="bg-green-100 text-green-600" icon="📅" to="/reservations" />
            </>
          )}
        </div>
      </div>

      {/* Reservas recientes */}
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
                  <th className="px-6 py-3 font-medium">#</th>
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
                    <td className="px-6 py-3 text-gray-400 font-mono text-xs">{r.id}</td>
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

      {/* Accesos rapidos */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 className="text-base font-semibold text-gray-800 mb-4">Accesos Rapidos</h2>
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <Link
            to="/reservations"
            className="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition-colors text-center"
          >
            <span className="text-2xl mb-2">📅</span>
            <span className="text-sm font-medium text-gray-600">Nueva Reserva</span>
          </Link>
          <Link
            to="/equipment"
            className="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-green-400 hover:bg-green-50 transition-colors text-center"
          >
            <span className="text-2xl mb-2">🔬</span>
            <span className="text-sm font-medium text-gray-600">Ver Equipos</span>
          </Link>
          <Link
            to="/users"
            className="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-purple-400 hover:bg-purple-50 transition-colors text-center"
          >
            <span className="text-2xl mb-2">👥</span>
            <span className="text-sm font-medium text-gray-600">Usuarios</span>
          </Link>
          <Link
            to="/reports"
            className="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-orange-400 hover:bg-orange-50 transition-colors text-center"
          >
            <span className="text-2xl mb-2">📊</span>
            <span className="text-sm font-medium text-gray-600">Reportes</span>
          </Link>
        </div>
      </div>
    </div>
  )
}

export default Dashboard
