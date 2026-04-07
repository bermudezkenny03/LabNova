import React, { useEffect, useState, useCallback } from 'react'
import { reservationService } from '../services/reservationService'
import { equipmentService } from '../services/equipmentService'
import { useAuth } from '../hooks'
import { Reservation, Equipment } from '../types'
import { Modal } from '../components/common/Modal'

const STATUS_LABELS: Record<Reservation['status'], string> = {
  pending: 'Pendiente',
  approved: 'Aprobada',
  rejected: 'Rechazada',
  cancelled: 'Cancelada',
}

const STATUS_COLORS: Record<Reservation['status'], string> = {
  pending: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800',
  cancelled: 'bg-gray-100 text-gray-600',
}

interface ReservationForm {
  equipment_id: string
  start_time: string
  end_time: string
  notes: string
}

const EMPTY_FORM: ReservationForm = {
  equipment_id: '',
  start_time: '',
  end_time: '',
  notes: '',
}

const ReservationsPage: React.FC = () => {
  const { user } = useAuth()

  const [reservations, setReservations] = useState<Reservation[]>([])
  const [equipment, setEquipment] = useState<Equipment[]>([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [successMsg, setSuccessMsg] = useState<string | null>(null)

  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const [total, setTotal] = useState(0)
  const [filterStatus, setFilterStatus] = useState('all')

  const [showModal, setShowModal] = useState(false)
  const [form, setForm] = useState<ReservationForm>(EMPTY_FORM)
  const [formErrors, setFormErrors] = useState<Partial<ReservationForm>>({})

  const [rejectModal, setRejectModal] = useState<{ id: number } | null>(null)
  const [rejectReason, setRejectReason] = useState('')
  const [actionLoading, setActionLoading] = useState<number | null>(null)

  const [detailReservation, setDetailReservation] = useState<Reservation | null>(null)

  const showSuccess = (msg: string) => {
    setSuccessMsg(msg)
    setTimeout(() => setSuccessMsg(null), 3000)
  }

  const loadReservations = useCallback(async (page: number) => {
    try {
      setLoading(true)
      setError(null)
      const res = await reservationService.getAllReservations(page, 10)
      setReservations(res.data ?? [])
      setTotal(res.total)
      setTotalPages(res.last_page)
      setCurrentPage(res.current_page)
    } catch {
      setError('No se pudo cargar la lista de reservas.')
    } finally {
      setLoading(false)
    }
  }, [])

  const loadEquipment = useCallback(async () => {
    try {
      const res = await equipmentService.getAllEquipment()
      setEquipment((res.data ?? []).filter((e) => e.status === 'available'))
    } catch {
      // no critical
    }
  }, [])

  useEffect(() => {
    loadReservations(1)
    loadEquipment()
  }, [loadReservations, loadEquipment])

  const filtered = filterStatus === 'all'
    ? reservations
    : reservations.filter((r) => r.status === filterStatus)

  const validate = (): boolean => {
    const errors: Partial<ReservationForm> = {}
    if (!form.equipment_id) errors.equipment_id = 'Selecciona un equipo'
    if (!form.start_time) errors.start_time = 'La fecha de inicio es requerida'
    if (!form.end_time) errors.end_time = 'La fecha de fin es requerida'
    if (form.start_time && form.end_time && form.start_time >= form.end_time)
      errors.end_time = 'La fecha de fin debe ser posterior al inicio'
    setFormErrors(errors)
    return Object.keys(errors).length === 0
  }

  const handleCreate = async () => {
    if (!validate()) return
    try {
      setSaving(true)
      await reservationService.createReservation({
        equipment_id: Number(form.equipment_id),
        start_time: form.start_time,
        end_time: form.end_time,
        notes: form.notes || undefined,
      })
      showSuccess('Reserva creada exitosamente.')
      setShowModal(false)
      setForm(EMPTY_FORM)
      loadReservations(currentPage)
    } catch {
      setError('No se pudo crear la reserva. Verifica la disponibilidad del equipo.')
    } finally {
      setSaving(false)
    }
  }

  const handleApprove = async (id: number) => {
    try {
      setActionLoading(id)
      await reservationService.approveReservation(id)
      showSuccess('Reserva aprobada.')
      loadReservations(currentPage)
    } catch {
      setError('No se pudo aprobar la reserva.')
    } finally {
      setActionLoading(null)
    }
  }

  const handleReject = async () => {
    if (!rejectModal) return
    try {
      setActionLoading(rejectModal.id)
      await reservationService.rejectReservation(rejectModal.id, rejectReason)
      setRejectModal(null)
      setRejectReason('')
      showSuccess('Reserva rechazada.')
      loadReservations(currentPage)
    } catch {
      setError('No se pudo rechazar la reserva.')
    } finally {
      setActionLoading(null)
    }
  }

  const handleCancel = async (id: number) => {
    try {
      setActionLoading(id)
      await reservationService.cancelReservation(id)
      showSuccess('Reserva cancelada.')
      loadReservations(currentPage)
    } catch {
      setError('No se pudo cancelar la reserva.')
    } finally {
      setActionLoading(null)
    }
  }

  const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' })

  const formatDateTime = (d: string) =>
    new Date(d).toLocaleString('es-CO', {
      day: '2-digit', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit',
    })

  return (
    <div className="space-y-5">
      {/* Encabezado */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-800">Reservas</h1>
          <p className="text-gray-500 text-sm mt-0.5">{total} reservas en total</p>
        </div>
        <button
          onClick={() => { setForm(EMPTY_FORM); setFormErrors({}); setShowModal(true) }}
          className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
          + Nueva Reserva
        </button>
      </div>

      {/* Alertas */}
      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex justify-between">
          {error}
          <button onClick={() => setError(null)} className="text-red-400 hover:text-red-600 ml-4">&times;</button>
        </div>
      )}
      {successMsg && (
        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
          {successMsg}
        </div>
      )}

      {/* Filtro por estado */}
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-2">
        {(['all', 'pending', 'approved', 'rejected', 'cancelled'] as const).map((s) => (
          <button
            key={s}
            onClick={() => setFilterStatus(s)}
            className={`px-4 py-1.5 rounded-full text-xs font-semibold transition-colors ${
              filterStatus === s
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            }`}
          >
            {s === 'all' ? 'Todas' : STATUS_LABELS[s]}
          </button>
        ))}
      </div>

      {/* Tabla */}
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        {loading ? (
          <div className="p-6 space-y-3">
            {[...Array(5)].map((_, i) => (
              <div key={i} className="h-10 bg-gray-100 rounded animate-pulse" />
            ))}
          </div>
        ) : filtered.length === 0 ? (
          <div className="p-12 text-center text-gray-400">
            <p className="text-lg mb-1">No hay reservas</p>
            <p className="text-sm">
              {filterStatus !== 'all'
                ? `No hay reservas con estado "${STATUS_LABELS[filterStatus as Reservation['status']]}"`
                : 'Crea la primera reserva con el boton de arriba.'}
            </p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left">
              <thead className="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                  <th className="px-5 py-3 font-medium">#</th>
                  <th className="px-5 py-3 font-medium">Usuario</th>
                  <th className="px-5 py-3 font-medium">Equipo</th>
                  <th className="px-5 py-3 font-medium">Inicio</th>
                  <th className="px-5 py-3 font-medium">Fin</th>
                  <th className="px-5 py-3 font-medium">Estado</th>
                  <th className="px-5 py-3 font-medium text-right">Acciones</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {filtered.map((r) => (
                  <tr key={r.id} className="hover:bg-gray-50 transition-colors">
                    <td className="px-5 py-3 text-gray-400 font-mono text-xs">{r.id}</td>
                    <td className="px-5 py-3 font-medium text-gray-800">
                      {r.user?.name ?? `Usuario #${r.user_id}`}
                    </td>
                    <td className="px-5 py-3 text-gray-600">
                      {r.equipment?.name ?? `Equipo #${r.equipment_id}`}
                    </td>
                    <td className="px-5 py-3 text-gray-500 text-xs">{formatDate(r.start_time)}</td>
                    <td className="px-5 py-3 text-gray-500 text-xs">{formatDate(r.end_time)}</td>
                    <td className="px-5 py-3">
                      <span className={`px-2.5 py-1 rounded-full text-xs font-semibold ${STATUS_COLORS[r.status]}`}>
                        {STATUS_LABELS[r.status]}
                      </span>
                    </td>
                    <td className="px-5 py-3 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <button
                          onClick={() => setDetailReservation(r)}
                          className="text-gray-500 hover:text-gray-700 text-xs font-medium"
                        >
                          Ver
                        </button>
                        {r.status === 'pending' && (
                          <>
                            <button
                              onClick={() => handleApprove(r.id)}
                              disabled={actionLoading === r.id}
                              className="text-green-600 hover:text-green-800 text-xs font-medium disabled:opacity-50"
                            >
                              Aprobar
                            </button>
                            <button
                              onClick={() => { setRejectModal({ id: r.id }); setRejectReason('') }}
                              disabled={actionLoading === r.id}
                              className="text-red-500 hover:text-red-700 text-xs font-medium disabled:opacity-50"
                            >
                              Rechazar
                            </button>
                          </>
                        )}
                        {(r.status === 'pending' || r.status === 'approved') && r.user_id === user?.id && (
                          <button
                            onClick={() => handleCancel(r.id)}
                            disabled={actionLoading === r.id}
                            className="text-orange-500 hover:text-orange-700 text-xs font-medium disabled:opacity-50"
                          >
                            Cancelar
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Paginacion */}
      {totalPages > 1 && (
        <div className="flex items-center justify-between text-sm">
          <span className="text-gray-500">
            Pagina {currentPage} de {totalPages} &mdash; {total} reservas
          </span>
          <div className="flex gap-2">
            <button
              onClick={() => loadReservations(currentPage - 1)}
              disabled={currentPage <= 1}
              className="px-3 py-1.5 border border-gray-200 rounded-lg disabled:opacity-40 hover:bg-gray-50 transition-colors"
            >
              &larr; Anterior
            </button>
            <button
              onClick={() => loadReservations(currentPage + 1)}
              disabled={currentPage >= totalPages}
              className="px-3 py-1.5 border border-gray-200 rounded-lg disabled:opacity-40 hover:bg-gray-50 transition-colors"
            >
              Siguiente &rarr;
            </button>
          </div>
        </div>
      )}

      {/* Modal nueva reserva */}
      {showModal && (
        <Modal title="Nueva Reserva" onClose={() => setShowModal(false)}>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Equipo *</label>
              <select
                value={form.equipment_id}
                onChange={(e) => setForm({ ...form, equipment_id: e.target.value })}
                className={`w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 ${formErrors.equipment_id ? 'border-red-400' : 'border-gray-200'}`}
              >
                <option value="">Seleccionar equipo disponible...</option>
                {equipment.map((eq) => (
                  <option key={eq.id} value={eq.id}>{eq.name}</option>
                ))}
              </select>
              {formErrors.equipment_id && <p className="text-red-500 text-xs mt-1">{formErrors.equipment_id}</p>}
              {equipment.length === 0 && (
                <p className="text-orange-500 text-xs mt-1">No hay equipos disponibles en este momento.</p>
              )}
            </div>

            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Fecha de inicio *</label>
                <input
                  type="datetime-local"
                  value={form.start_time}
                  onChange={(e) => setForm({ ...form, start_time: e.target.value })}
                  min={new Date().toISOString().slice(0, 16)}
                  className={`w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 ${formErrors.start_time ? 'border-red-400' : 'border-gray-200'}`}
                />
                {formErrors.start_time && <p className="text-red-500 text-xs mt-1">{formErrors.start_time}</p>}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Fecha de fin *</label>
                <input
                  type="datetime-local"
                  value={form.end_time}
                  onChange={(e) => setForm({ ...form, end_time: e.target.value })}
                  min={form.start_time || new Date().toISOString().slice(0, 16)}
                  className={`w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 ${formErrors.end_time ? 'border-red-400' : 'border-gray-200'}`}
                />
                {formErrors.end_time && <p className="text-red-500 text-xs mt-1">{formErrors.end_time}</p>}
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
              <textarea
                value={form.notes}
                onChange={(e) => setForm({ ...form, notes: e.target.value })}
                rows={3}
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none"
                placeholder="Proposito o detalles adicionales..."
              />
            </div>

            <div className="flex justify-end gap-3 pt-2">
              <button
                onClick={() => setShowModal(false)}
                className="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button
                onClick={handleCreate}
                disabled={saving}
                className="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
              >
                {saving ? 'Creando...' : 'Crear Reserva'}
              </button>
            </div>
          </div>
        </Modal>
      )}

      {/* Modal rechazar */}
      {rejectModal && (
        <Modal title="Rechazar Reserva" onClose={() => setRejectModal(null)} size="sm">
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Motivo del rechazo</label>
              <textarea
                value={rejectReason}
                onChange={(e) => setRejectReason(e.target.value)}
                rows={3}
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"
                placeholder="Indica el motivo..."
              />
            </div>
            <div className="flex justify-end gap-3">
              <button
                onClick={() => setRejectModal(null)}
                className="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button
                onClick={handleReject}
                disabled={actionLoading !== null}
                className="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors"
              >
                {actionLoading !== null ? 'Rechazando...' : 'Rechazar'}
              </button>
            </div>
          </div>
        </Modal>
      )}

      {/* Modal detalle */}
      {detailReservation && (
        <Modal title={`Reserva #${detailReservation.id}`} onClose={() => setDetailReservation(null)}>
          <div className="space-y-4 text-sm">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-gray-400 text-xs uppercase font-medium mb-0.5">Usuario</p>
                <p className="text-gray-800 font-medium">
                  {detailReservation.user?.name ?? `#${detailReservation.user_id}`}
                </p>
              </div>
              <div>
                <p className="text-gray-400 text-xs uppercase font-medium mb-0.5">Equipo</p>
                <p className="text-gray-800 font-medium">
                  {detailReservation.equipment?.name ?? `#${detailReservation.equipment_id}`}
                </p>
              </div>
              <div>
                <p className="text-gray-400 text-xs uppercase font-medium mb-0.5">Inicio</p>
                <p className="text-gray-800">{formatDateTime(detailReservation.start_time)}</p>
              </div>
              <div>
                <p className="text-gray-400 text-xs uppercase font-medium mb-0.5">Fin</p>
                <p className="text-gray-800">{formatDateTime(detailReservation.end_time)}</p>
              </div>
              <div>
                <p className="text-gray-400 text-xs uppercase font-medium mb-0.5">Estado</p>
                <span className={`px-2.5 py-1 rounded-full text-xs font-semibold ${STATUS_COLORS[detailReservation.status]}`}>
                  {STATUS_LABELS[detailReservation.status]}
                </span>
              </div>
              <div>
                <p className="text-gray-400 text-xs uppercase font-medium mb-0.5">Creada</p>
                <p className="text-gray-800">{formatDateTime(detailReservation.created_at)}</p>
              </div>
            </div>
            {detailReservation.rejection_reason && (
              <div>
                <p className="text-gray-400 text-xs uppercase font-medium mb-0.5">Motivo de rechazo</p>
                <p className="text-gray-700 bg-red-50 rounded-lg p-3 text-xs">{detailReservation.rejection_reason}</p>
              </div>
            )}
            {detailReservation.notes && (
              <div>
                <p className="text-gray-400 text-xs uppercase font-medium mb-0.5">Notas</p>
                <p className="text-gray-700 bg-gray-50 rounded-lg p-3">{detailReservation.notes}</p>
              </div>
            )}
            <div className="flex justify-end pt-2">
              <button
                onClick={() => setDetailReservation(null)}
                className="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cerrar
              </button>
            </div>
          </div>
        </Modal>
      )}
    </div>
  )
}

export default ReservationsPage
