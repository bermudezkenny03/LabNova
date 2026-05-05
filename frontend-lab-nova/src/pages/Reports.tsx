import React, { useEffect, useState, useCallback } from 'react'
import { reportService } from '../services/reportService'
import { ReportRequest } from '../types'
import { generatePDF, ReportType } from '../utils/pdfGenerator'
import { IfCan } from '../components/ProtectedFeature'

// ─── Constants ───────────────────────────────────────────────────────────────

const REPORT_TYPES: { value: ReportType; label: string; description: string; icon: string }[] = [
  {
    value: 'reservations',
    label: 'Reservas',
    description: 'Estado y detalle de todas las reservas del laboratorio',
    icon: '📋',
  },
  {
    value: 'equipment_usage',
    label: 'Uso de Equipos',
    description: 'Inventario y nivel de uso de cada equipo',
    icon: '🔬',
  },
  {
    value: 'user_activity',
    label: 'Actividad de Usuarios',
    description: 'Participación y reservas realizadas por usuario',
    icon: '👥',
  },
]

const STATUS_LABELS: Record<ReportRequest['status'], string> = {
  pending: 'Pendiente',
  processing: 'En proceso',
  completed: 'Completada',
  failed: 'Fallida',
}

const STATUS_COLORS: Record<ReportRequest['status'], string> = {
  pending: 'bg-yellow-100 text-yellow-700',
  processing: 'bg-blue-100 text-blue-700',
  completed: 'bg-green-100 text-green-700',
  failed: 'bg-red-100 text-red-700',
}

const TYPE_LABELS: Record<string, string> = {
  reservations: 'Reservas',
  equipment_usage: 'Uso de Equipos',
  user_activity: 'Actividad de Usuarios',
}

// ─── Component ───────────────────────────────────────────────────────────────

const ReportsPage: React.FC = () => {
  const [selectedType, setSelectedType] = useState<ReportType>('reservations')
  const [startDate, setStartDate] = useState('')
  const [endDate, setEndDate] = useState('')
  const [generating, setGenerating] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [successMsg, setSuccessMsg] = useState<string | null>(null)

  // History
  const [history, setHistory] = useState<ReportRequest[]>([])
  const [historyLoading, setHistoryLoading] = useState(true)
  const [historyPage, setHistoryPage] = useState(1)
  const [historyTotalPages, setHistoryTotalPages] = useState(1)
  const [historyTotal, setHistoryTotal] = useState(0)

  const showSuccess = (msg: string) => {
    setSuccessMsg(msg)
    setTimeout(() => setSuccessMsg(null), 4000)
  }

  const loadHistory = useCallback(async (page: number) => {
    try {
      setHistoryLoading(true)
      const res = await reportService.getAllReportRequests(page, 8)
      setHistory(res.data ?? [])
      setHistoryTotal(res.total)
      setHistoryTotalPages(res.last_page)
      setHistoryPage(res.current_page)
    } catch {
      // history is secondary — fail silently
    } finally {
      setHistoryLoading(false)
    }
  }, [])

  useEffect(() => {
    loadHistory(1)
  }, [loadHistory])

  const handleGenerate = async () => {
    setError(null)

    if (startDate && !endDate) { setError('La fecha de fin es obligatoria.'); return }
    if (!startDate && endDate) { setError('La fecha de inicio es obligatoria.'); return }
    if (startDate && endDate && startDate > endDate) { setError('El rango de fechas es inválido.'); return }

    setGenerating(true)
    try {
      const data = await reportService.generateReport({
        type: selectedType,
        start_date: startDate || undefined,
        end_date: endDate || undefined,
      })
      generatePDF(data)
      showSuccess('Reporte generado correctamente.')
      loadHistory(1)
    } catch (err: unknown) {
      const axiosErr = err as { response?: { data?: { message?: string } } }
      const msg = axiosErr?.response?.data?.message
      setError(msg || 'Error durante la generación del reporte.')
    } finally {
      setGenerating(false)
    }
  }

  const selectedTypeMeta = REPORT_TYPES.find(t => t.value === selectedType)!

  const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' })

  const formatDateTime = (d: string) =>
    new Date(d).toLocaleString('es-CO', {
      day: '2-digit', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit',
    })

  return (
    <div className="space-y-6">

      {/* ── Page header ──────────────────────────────────────────────── */}
      <div>
        <h1 className="text-2xl font-bold text-gray-800">Reportes</h1>
        <p className="text-sm text-gray-500 mt-0.5">
          Genera reportes en PDF directamente desde el sistema
        </p>
      </div>

      {/* ── Alerts ───────────────────────────────────────────────────── */}
      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-start justify-between gap-3">
          <span>{error}</span>
          <button onClick={() => setError(null)} className="text-red-400 hover:text-red-600 shrink-0 text-base leading-none">×</button>
        </div>
      )}
      {successMsg && (
        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
          <span className="text-base">✓</span>
          {successMsg}
        </div>
      )}

      {/* ── Generator card ───────────────────────────────────────────── */}
      <IfCan permission={{ module: 'reports', action: 'create' }}>
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">

          <div className="flex items-center gap-2">
            <div className="w-1 h-5 rounded-full bg-blue-600" />
            <h2 className="font-semibold text-gray-800">Nuevo reporte</h2>
          </div>

          {/* Type selector */}
          <div>
            <p className="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">
              Tipo de reporte
            </p>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
              {REPORT_TYPES.map(rt => (
                <button
                  key={rt.value}
                  onClick={() => setSelectedType(rt.value)}
                  className={`text-left px-4 py-4 rounded-xl border-2 transition-all ${
                    selectedType === rt.value
                      ? 'border-blue-500 bg-blue-50'
                      : 'border-gray-100 hover:border-gray-200 hover:bg-gray-50'
                  }`}
                >
                  <div className="text-2xl mb-2">{rt.icon}</div>
                  <div className={`text-sm font-semibold mb-0.5 ${selectedType === rt.value ? 'text-blue-700' : 'text-gray-800'}`}>
                    {rt.label}
                  </div>
                  <div className="text-xs text-gray-500 leading-snug">{rt.description}</div>
                </button>
              ))}
            </div>
          </div>

          {/* Date range */}
          <div>
            <p className="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">
              Rango de fechas <span className="normal-case font-normal">(opcional)</span>
            </p>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-xs text-gray-500 mb-1">Desde</label>
                <input
                  type="date"
                  value={startDate}
                  onChange={e => setStartDate(e.target.value)}
                  className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                />
              </div>
              <div>
                <label className="block text-xs text-gray-500 mb-1">Hasta</label>
                <input
                  type="date"
                  value={endDate}
                  min={startDate}
                  onChange={e => setEndDate(e.target.value)}
                  className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                />
              </div>
            </div>
            {!startDate && !endDate && (
              <p className="text-xs text-gray-400 mt-1.5">
                Sin rango seleccionado se incluirán todos los registros disponibles.
              </p>
            )}
          </div>

          {/* Preview + Generate */}
          <div className="flex items-center justify-between pt-2 border-t border-gray-100">
            <div className="text-sm text-gray-500">
              <span className="font-medium text-gray-700">{selectedTypeMeta.icon} {selectedTypeMeta.label}</span>
              {startDate && endDate && (
                <span className="ml-2 text-gray-400">
                  · {formatDate(startDate)} — {formatDate(endDate)}
                </span>
              )}
            </div>
            <button
              onClick={handleGenerate}
              disabled={generating}
              className="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors"
            >
              {generating ? (
                <>
                  <svg className="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                  </svg>
                  Generando...
                </>
              ) : (
                <>
                  <svg className="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clipRule="evenodd" />
                  </svg>
                  Generar PDF
                </>
              )}
            </button>
          </div>

        </div>
      </IfCan>

      {/* ── History ──────────────────────────────────────────────────── */}
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <div className="w-1 h-5 rounded-full bg-gray-300" />
            <h2 className="font-semibold text-gray-700 text-sm">Historial de solicitudes</h2>
          </div>
          {historyTotal > 0 && (
            <span className="text-xs text-gray-400">{historyTotal} registros</span>
          )}
        </div>

        {historyLoading ? (
          <div className="p-6 space-y-3">
            {[...Array(4)].map((_, i) => (
              <div key={i} className="h-9 bg-gray-100 rounded animate-pulse" />
            ))}
          </div>
        ) : history.length === 0 ? (
          <div className="py-12 text-center text-gray-400">
            <div className="text-3xl mb-3">📄</div>
            <p className="text-sm">Aún no hay solicitudes de reporte.</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left">
              <thead className="bg-gray-50 text-gray-400 text-xs">
                <tr>
                  <th className="px-6 py-3 font-medium">Tipo</th>
                  <th className="px-6 py-3 font-medium">Rango de fechas</th>
                  <th className="px-6 py-3 font-medium">Estado</th>
                  <th className="px-6 py-3 font-medium">Fecha solicitud</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {history.map(req => (
                  <tr key={req.id} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-3 font-medium text-gray-800">
                      {TYPE_LABELS[req.type] ?? req.type}
                    </td>
                    <td className="px-6 py-3 text-gray-500 text-xs">
                      {req.start_date && req.end_date
                        ? `${formatDate(req.start_date)} — ${formatDate(req.end_date)}`
                        : <span className="italic text-gray-300">Período completo</span>}
                    </td>
                    <td className="px-6 py-3">
                      <span className={`px-2.5 py-1 rounded-full text-xs font-semibold ${STATUS_COLORS[req.status]}`}>
                        {STATUS_LABELS[req.status]}
                      </span>
                    </td>
                    <td className="px-6 py-3 text-gray-400 text-xs">
                      {formatDateTime(req.created_at)}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {historyTotalPages > 1 && (
          <div className="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
            <span className="text-gray-400 text-xs">
              Página {historyPage} de {historyTotalPages}
            </span>
            <div className="flex gap-2">
              <button
                onClick={() => loadHistory(historyPage - 1)}
                disabled={historyPage <= 1}
                className="px-3 py-1.5 border border-gray-200 rounded-lg text-xs disabled:opacity-40 hover:bg-gray-50 transition-colors"
              >
                ← Anterior
              </button>
              <button
                onClick={() => loadHistory(historyPage + 1)}
                disabled={historyPage >= historyTotalPages}
                className="px-3 py-1.5 border border-gray-200 rounded-lg text-xs disabled:opacity-40 hover:bg-gray-50 transition-colors"
              >
                Siguiente →
              </button>
            </div>
          </div>
        )}
      </div>

    </div>
  )
}

export default ReportsPage
