import React, { useEffect, useState, useCallback } from 'react'
import { reportService } from '../services/reportService'
import { Report, ReportRequest } from '../types'
import { Modal } from '../components/common/Modal'

// Backend statuses for ReportRequest: pending | processing | completed | failed
const REQUEST_STATUS_LABELS: Record<ReportRequest['status'], string> = {
  pending: 'Pendiente',
  processing: 'En proceso',
  completed: 'Completada',
  failed: 'Fallida',
}

const REQUEST_STATUS_COLORS: Record<ReportRequest['status'], string> = {
  pending: 'bg-yellow-100 text-yellow-800',
  processing: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  failed: 'bg-red-100 text-red-800',
}

// Report type labels
const REPORT_TYPE_LABELS: Record<string, string> = {
  reservations: 'Reservas',
  equipment_usage: 'Uso de Equipos',
  user_activity: 'Actividad de Usuarios',
}

type ActiveTab = 'requests' | 'reports'

const ReportsPage: React.FC = () => {
  const [activeTab, setActiveTab] = useState<ActiveTab>('requests')

  // Report requests
  const [requests, setRequests] = useState<ReportRequest[]>([])
  const [requestsLoading, setRequestsLoading] = useState(true)
  const [requestsPage, setRequestsPage] = useState(1)
  const [requestsTotalPages, setRequestsTotalPages] = useState(1)
  const [requestsTotal, setRequestsTotal] = useState(0)

  // Reports
  const [reports, setReports] = useState<Report[]>([])
  const [reportsLoading, setReportsLoading] = useState(true)
  const [reportsPage, setReportsPage] = useState(1)
  const [reportsTotalPages, setReportsTotalPages] = useState(1)
  const [reportsTotal, setReportsTotal] = useState(0)

  // Modals
  const [showRequestModal, setShowRequestModal] = useState(false)
  const [requestForm, setRequestForm] = useState({
    type: 'reservations' as ReportRequest['type'],
    start_date: '',
    end_date: '',
  })
  const [requesting, setRequesting] = useState(false)

  const [actionLoading, setActionLoading] = useState<number | null>(null)

  const [error, setError] = useState<string | null>(null)
  const [successMsg, setSuccessMsg] = useState<string | null>(null)

  const showSuccess = (msg: string) => {
    setSuccessMsg(msg)
    setTimeout(() => setSuccessMsg(null), 3000)
  }

  const loadRequests = useCallback(async (page: number) => {
    try {
      setRequestsLoading(true)
      const res = await reportService.getAllReportRequests(page, 10)
      setRequests(res.data ?? [])
      setRequestsTotal(res.total)
      setRequestsTotalPages(res.last_page)
      setRequestsPage(res.current_page)
    } catch {
      setError('No se pudieron cargar las solicitudes.')
    } finally {
      setRequestsLoading(false)
    }
  }, [])

  const loadReports = useCallback(async (page: number) => {
    try {
      setReportsLoading(true)
      const res = await reportService.getAllReports(page, 10)
      setReports(res.data ?? [])
      setReportsTotal(res.total)
      setReportsTotalPages(res.last_page)
      setReportsPage(res.current_page)
    } catch {
      setError('No se pudieron cargar los reportes.')
    } finally {
      setReportsLoading(false)
    }
  }, [])

  useEffect(() => {
    loadRequests(1)
    loadReports(1)
  }, [loadRequests, loadReports])

  const handleCreateRequest = async () => {
    try {
      setRequesting(true)
      await reportService.createReportRequest({
        type: requestForm.type,
        start_date: requestForm.start_date || undefined,
        end_date: requestForm.end_date || undefined,
      })
      showSuccess('Solicitud creada correctamente.')
      setShowRequestModal(false)
      setRequestForm({ type: 'reservations', start_date: '', end_date: '' })
      loadRequests(requestsPage)
    } catch {
      setError('No se pudo crear la solicitud.')
    } finally {
      setRequesting(false)
    }
  }

  const handleApproveRequest = async (id: number) => {
    try {
      setActionLoading(id)
      await reportService.approveReportRequest(id)
      showSuccess('Solicitud aprobada.')
      loadRequests(requestsPage)
    } catch {
      setError('No se pudo aprobar la solicitud.')
    } finally {
      setActionLoading(null)
    }
  }

  const handleRejectRequest = async (id: number) => {
    try {
      setActionLoading(id)
      await reportService.rejectReportRequest(id)
      showSuccess('Solicitud rechazada.')
      loadRequests(requestsPage)
    } catch {
      setError('No se pudo rechazar la solicitud.')
    } finally {
      setActionLoading(null)
    }
  }

  const handleCompleteRequest = async (id: number) => {
    try {
      setActionLoading(id)
      await reportService.completeReportRequest(id)
      showSuccess('Solicitud marcada como completada.')
      loadRequests(requestsPage)
    } catch {
      setError('No se pudo completar la solicitud.')
    } finally {
      setActionLoading(null)
    }
  }

  const handleDeleteReport = async (id: number) => {
    try {
      setActionLoading(id)
      await reportService.deleteReport(id)
      showSuccess('Reporte eliminado.')
      loadReports(reportsPage)
    } catch {
      setError('No se pudo eliminar el reporte.')
    } finally {
      setActionLoading(null)
    }
  }

  const handleDownload = async (report: Report) => {
    try {
      setActionLoading(report.id)
      const blob = await reportService.downloadReport(report.id)
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `reporte_${report.id}.pdf`
      a.click()
      URL.revokeObjectURL(url)
    } catch {
      setError('No se pudo descargar el reporte.')
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
          <h1 className="text-2xl font-bold text-gray-800">Reportes</h1>
          <p className="text-gray-500 text-sm mt-0.5">
            {requestsTotal} solicitudes · {reportsTotal} reportes generados
          </p>
        </div>
        <button
          onClick={() => {
            setRequestForm({ type: 'reservations', start_date: '', end_date: '' })
            setShowRequestModal(true)
          }}
          className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
          + Nueva Solicitud
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

      {/* Tabs */}
      <div className="flex border-b border-gray-200">
        <button
          onClick={() => setActiveTab('requests')}
          className={`px-6 py-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'requests'
              ? 'border-blue-600 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700'
          }`}
        >
          Solicitudes ({requestsTotal})
        </button>
        <button
          onClick={() => setActiveTab('reports')}
          className={`px-6 py-3 text-sm font-medium border-b-2 transition-colors ${
            activeTab === 'reports'
              ? 'border-blue-600 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700'
          }`}
        >
          Reportes ({reportsTotal})
        </button>
      </div>

      {/* Tab: Solicitudes */}
      {activeTab === 'requests' && (
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
          {requestsLoading ? (
            <div className="p-6 space-y-3">
              {[...Array(4)].map((_, i) => (
                <div key={i} className="h-10 bg-gray-100 rounded animate-pulse" />
              ))}
            </div>
          ) : requests.length === 0 ? (
            <div className="p-12 text-center text-gray-400">
              <p className="text-lg mb-1">No hay solicitudes</p>
              <p className="text-sm">Crea una solicitud con el boton de arriba.</p>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm text-left">
                <thead className="bg-gray-50 text-gray-500 text-xs uppercase">
                  <tr>
                    <th className="px-6 py-3 font-medium">#</th>
                    <th className="px-6 py-3 font-medium">Tipo</th>
                    <th className="px-6 py-3 font-medium">Rango de fechas</th>
                    <th className="px-6 py-3 font-medium">Estado</th>
                    <th className="px-6 py-3 font-medium">Creada</th>
                    <th className="px-6 py-3 font-medium text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-50">
                  {requests.map((req) => (
                    <tr key={req.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-6 py-3 text-gray-400 font-mono text-xs">{req.id}</td>
                      <td className="px-6 py-3 font-medium text-gray-800">
                        {REPORT_TYPE_LABELS[req.type] ?? req.type}
                      </td>
                      <td className="px-6 py-3 text-gray-500 text-xs">
                        {req.start_date && req.end_date
                          ? `${formatDate(req.start_date)} — ${formatDate(req.end_date)}`
                          : <span className="italic text-gray-300">Sin rango</span>}
                      </td>
                      <td className="px-6 py-3">
                        <span className={`px-2.5 py-1 rounded-full text-xs font-semibold ${REQUEST_STATUS_COLORS[req.status]}`}>
                          {REQUEST_STATUS_LABELS[req.status]}
                        </span>
                      </td>
                      <td className="px-6 py-3 text-gray-500 text-xs">
                        {formatDateTime(req.created_at)}
                      </td>
                      <td className="px-6 py-3 text-right">
                        <div className="flex items-center justify-end gap-2">
                          {req.status === 'pending' && (
                            <>
                              <button
                                onClick={() => handleApproveRequest(req.id)}
                                disabled={actionLoading === req.id}
                                className="text-blue-600 hover:text-blue-800 text-xs font-medium disabled:opacity-50"
                              >
                                Procesar
                              </button>
                              <button
                                onClick={() => handleRejectRequest(req.id)}
                                disabled={actionLoading === req.id}
                                className="text-red-500 hover:text-red-700 text-xs font-medium disabled:opacity-50"
                              >
                                Rechazar
                              </button>
                            </>
                          )}
                          {req.status === 'processing' && (
                            <button
                              onClick={() => handleCompleteRequest(req.id)}
                              disabled={actionLoading === req.id}
                              className="text-green-600 hover:text-green-800 text-xs font-medium disabled:opacity-50"
                            >
                              Completar
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

          {requestsTotalPages > 1 && (
            <div className="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
              <span className="text-gray-500">
                Pagina {requestsPage} de {requestsTotalPages}
              </span>
              <div className="flex gap-2">
                <button
                  onClick={() => loadRequests(requestsPage - 1)}
                  disabled={requestsPage <= 1}
                  className="px-3 py-1.5 border border-gray-200 rounded-lg disabled:opacity-40 hover:bg-gray-50"
                >
                  &larr; Anterior
                </button>
                <button
                  onClick={() => loadRequests(requestsPage + 1)}
                  disabled={requestsPage >= requestsTotalPages}
                  className="px-3 py-1.5 border border-gray-200 rounded-lg disabled:opacity-40 hover:bg-gray-50"
                >
                  Siguiente &rarr;
                </button>
              </div>
            </div>
          )}
        </div>
      )}

      {/* Tab: Reportes */}
      {activeTab === 'reports' && (
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
          {reportsLoading ? (
            <div className="p-6 space-y-3">
              {[...Array(4)].map((_, i) => (
                <div key={i} className="h-10 bg-gray-100 rounded animate-pulse" />
              ))}
            </div>
          ) : reports.length === 0 ? (
            <div className="p-12 text-center text-gray-400">
              <p className="text-lg mb-1">No hay reportes generados</p>
              <p className="text-sm">Los reportes aparecen aqui cuando se completan las solicitudes.</p>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm text-left">
                <thead className="bg-gray-50 text-gray-500 text-xs uppercase">
                  <tr>
                    <th className="px-6 py-3 font-medium">#</th>
                    <th className="px-6 py-3 font-medium">Archivo</th>
                    <th className="px-6 py-3 font-medium">Tipo</th>
                    <th className="px-6 py-3 font-medium">Generado</th>
                    <th className="px-6 py-3 font-medium text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-50">
                  {reports.map((r) => (
                    <tr key={r.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-6 py-3 text-gray-400 font-mono text-xs">{r.id}</td>
                      <td className="px-6 py-3 font-medium text-gray-800">
                        {r.file_name ?? `reporte_${r.id}`}
                        {r.file_type && (
                          <span className="ml-2 text-xs text-gray-400 font-normal">.{r.file_type}</span>
                        )}
                      </td>
                      <td className="px-6 py-3">
                        <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                          Solicitud #{r.report_request_id}
                        </span>
                      </td>
                      <td className="px-6 py-3 text-gray-500 text-xs">
                        {formatDateTime(r.generated_at)}
                      </td>
                      <td className="px-6 py-3 text-right">
                        <div className="flex items-center justify-end gap-3">
                          {r.file_path && (
                            <button
                              onClick={() => handleDownload(r)}
                              disabled={actionLoading === r.id}
                              className="text-blue-600 hover:text-blue-800 text-xs font-medium disabled:opacity-50"
                            >
                              {actionLoading === r.id ? 'Descargando...' : 'Descargar'}
                            </button>
                          )}
                          <button
                            onClick={() => handleDeleteReport(r.id)}
                            disabled={actionLoading === r.id}
                            className="text-red-500 hover:text-red-700 text-xs font-medium disabled:opacity-50"
                          >
                            Eliminar
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {reportsTotalPages > 1 && (
            <div className="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
              <span className="text-gray-500">
                Pagina {reportsPage} de {reportsTotalPages}
              </span>
              <div className="flex gap-2">
                <button
                  onClick={() => loadReports(reportsPage - 1)}
                  disabled={reportsPage <= 1}
                  className="px-3 py-1.5 border border-gray-200 rounded-lg disabled:opacity-40 hover:bg-gray-50"
                >
                  &larr; Anterior
                </button>
                <button
                  onClick={() => loadReports(reportsPage + 1)}
                  disabled={reportsPage >= reportsTotalPages}
                  className="px-3 py-1.5 border border-gray-200 rounded-lg disabled:opacity-40 hover:bg-gray-50"
                >
                  Siguiente &rarr;
                </button>
              </div>
            </div>
          )}
        </div>
      )}

      {/* Modal nueva solicitud */}
      {showRequestModal && (
        <Modal title="Nueva Solicitud de Reporte" onClose={() => setShowRequestModal(false)}>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Tipo de reporte *</label>
              <select
                value={requestForm.type}
                onChange={(e) => setRequestForm({ ...requestForm, type: e.target.value as ReportRequest['type'] })}
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
              >
                <option value="reservations">Reservas</option>
                <option value="equipment_usage">Uso de Equipos</option>
                <option value="user_activity">Actividad de Usuarios</option>
              </select>
            </div>

            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Fecha inicio (opcional)</label>
                <input
                  type="date"
                  value={requestForm.start_date}
                  onChange={(e) => setRequestForm({ ...requestForm, start_date: e.target.value })}
                  className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Fecha fin (opcional)</label>
                <input
                  type="date"
                  value={requestForm.end_date}
                  onChange={(e) => setRequestForm({ ...requestForm, end_date: e.target.value })}
                  min={requestForm.start_date}
                  className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                />
              </div>
            </div>

            <div className="flex justify-end gap-3 pt-2">
              <button
                onClick={() => setShowRequestModal(false)}
                className="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button
                onClick={handleCreateRequest}
                disabled={requesting}
                className="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
              >
                {requesting ? 'Enviando...' : 'Enviar Solicitud'}
              </button>
            </div>
          </div>
        </Modal>
      )}
    </div>
  )
}

export default ReportsPage
