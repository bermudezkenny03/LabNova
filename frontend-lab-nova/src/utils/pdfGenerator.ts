import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

export type ReportType = 'reservations' | 'equipment_usage' | 'user_activity'

export interface ReportData {
  type: ReportType
  start_date: string | null
  end_date: string | null
  generated_at: string
  generated_by: string
  stats: Record<string, number>
  records: Record<string, unknown>[]
}

const TYPE_LABELS: Record<ReportType, string> = {
  reservations: 'Reporte de Reservas',
  equipment_usage: 'Reporte de Uso de Equipos',
  user_activity: 'Reporte de Actividad de Usuarios',
}

const STATUS_ES: Record<string, string> = {
  pending: 'Pendiente',
  approved: 'Aprobada',
  rejected: 'Rechazada',
  cancelled: 'Cancelada',
  completed: 'Completada',
  available: 'Disponible',
  maintenance: 'Mantenimiento',
  out_of_service: 'Fuera de servicio',
}

const STAT_LABELS: Record<string, string> = {
  total: 'Total',
  pending: 'Pendientes',
  approved: 'Aprobadas',
  rejected: 'Rechazadas',
  cancelled: 'Canceladas',
  completed: 'Completadas',
  available: 'Disponibles',
  maintenance: 'Mantenimiento',
  out_of_service: 'Fuera de Servicio',
  total_usuarios: 'Total Usuarios',
  usuarios_activos: 'Usuarios Activos',
  total_reservas: 'Total Reservas',
}

function fmtDate(d: string): string {
  if (!d) return '—'
  try {
    return new Date(d).toLocaleDateString('es-CO', {
      day: '2-digit', month: 'short', year: 'numeric',
    })
  } catch {
    return d
  }
}

function fmtDateTime(d: string): string {
  if (!d) return '—'
  try {
    return new Date(d).toLocaleString('es-CO', {
      day: '2-digit', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit',
    })
  } catch {
    return d
  }
}

function buildTableData(data: ReportData): { head: string[]; body: (string | number)[][] } {
  if (data.type === 'reservations') {
    return {
      head: ['#', 'Usuario', 'Equipo', 'Inicio', 'Fin', 'Estado'],
      body: data.records.map(r => [
        r.id as number,
        r.usuario as string,
        r.equipo as string,
        fmtDateTime(r.inicio as string),
        fmtDateTime(r.fin as string),
        STATUS_ES[r.estado as string] ?? (r.estado as string),
      ]),
    }
  }
  if (data.type === 'equipment_usage') {
    return {
      head: ['#', 'Nombre', 'Código', 'Categoría', 'Estado', 'Reservas'],
      body: data.records.map(r => [
        r.id as number,
        r.nombre as string,
        r.codigo as string,
        r.categoria as string,
        STATUS_ES[r.estado as string] ?? (r.estado as string),
        r.reservas as number,
      ]),
    }
  }
  // user_activity
  return {
    head: ['#', 'Nombre completo', 'Correo', 'Rol', 'Reservas'],
    body: data.records.map((r, i) => [
      i + 1,
      r.nombre as string,
      r.email as string,
      r.rol as string,
      r.reservas as number,
    ]),
  }
}

export function generatePDF(data: ReportData): void {
  const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' })
  const pageW = 210
  const margin = 14
  const contentW = pageW - margin * 2

  // ─── Header azul ─────────────────────────────────
  doc.setFillColor(29, 78, 216)
  doc.rect(0, 0, pageW, 30, 'F')

  // Círculo blanco como "logo"
  doc.setFillColor(255, 255, 255)
  doc.circle(margin + 9, 15, 9, 'F')

  // Iniciales LN
  doc.setTextColor(29, 78, 216)
  doc.setFontSize(10)
  doc.setFont('helvetica', 'bold')
  doc.text('LN', margin + 9, 17, { align: 'center' })

  // Nombre empresa
  doc.setTextColor(255, 255, 255)
  doc.setFontSize(17)
  doc.setFont('helvetica', 'bold')
  doc.text('LabNova', margin + 22, 13)
  doc.setFontSize(8)
  doc.setFont('helvetica', 'normal')
  doc.text('Sistema de reservas LabNova', margin + 22, 20)

  // Tipo de reporte (derecha)
  doc.setFontSize(10)
  doc.setFont('helvetica', 'bold')
  doc.text(TYPE_LABELS[data.type], pageW - margin, 11, { align: 'right' })

  const period =
    data.start_date && data.end_date
      ? `${fmtDate(data.start_date)} – ${fmtDate(data.end_date)}`
      : 'Período completo'
  doc.setFontSize(7.5)
  doc.setFont('helvetica', 'normal')
  doc.text(period, pageW - margin, 18, { align: 'right' })
  doc.text(
    `Generado: ${fmtDateTime(data.generated_at)}`,
    pageW - margin, 24, { align: 'right' }
  )

  // ─── Banda gris (metadatos) ───────────────────────
  doc.setFillColor(241, 245, 249)
  doc.rect(0, 30, pageW, 13, 'F')

  doc.setTextColor(55, 65, 81)
  doc.setFontSize(8)
  doc.setFont('helvetica', 'normal')
  doc.text(`Generado por: ${data.generated_by}`, margin, 38)
  doc.text(
    `Total registros: ${data.records.length}`,
    pageW - margin, 38, { align: 'right' }
  )

  // ─── Tarjetas de estadísticas ────────────────────
  let y = 50
  const statsEntries = Object.entries(data.stats)
  const cols = statsEntries.length
  const boxW = contentW / cols

  statsEntries.forEach(([key, value], i) => {
    const x = margin + i * boxW
    // Sombra suave
    doc.setFillColor(224, 231, 255) // indigo-100
    doc.roundedRect(x + 0.5, y + 0.5, boxW - 3, 20, 2, 2, 'F')
    // Caja
    doc.setFillColor(239, 246, 255)
    doc.roundedRect(x, y, boxW - 3, 20, 2, 2, 'F')
    // Valor
    doc.setTextColor(29, 78, 216)
    doc.setFontSize(16)
    doc.setFont('helvetica', 'bold')
    doc.text(String(value), x + (boxW - 3) / 2, y + 11, { align: 'center' })
    // Etiqueta
    doc.setTextColor(100, 116, 139)
    doc.setFontSize(6.5)
    doc.setFont('helvetica', 'normal')
    const label = STAT_LABELS[key] ?? key
    doc.text(label, x + (boxW - 3) / 2, y + 17, { align: 'center' })
  })

  y += 26

  // ─── Línea divisora ──────────────────────────────
  doc.setDrawColor(226, 232, 240)
  doc.setLineWidth(0.3)
  doc.line(margin, y, pageW - margin, y)
  y += 4

  // ─── Tabla ───────────────────────────────────────
  const { head, body } = buildTableData(data)

  autoTable(doc, {
    startY: y,
    head: [head],
    body,
    margin: { left: margin, right: margin },
    styles: {
      fontSize: 7.5,
      cellPadding: { top: 3, bottom: 3, left: 4, right: 4 },
      lineColor: [226, 232, 240],
      lineWidth: 0.15,
      textColor: [30, 41, 59],
    },
    headStyles: {
      fillColor: [29, 78, 216],
      textColor: [255, 255, 255],
      fontStyle: 'bold',
      fontSize: 8,
      cellPadding: { top: 4, bottom: 4, left: 4, right: 4 },
    },
    alternateRowStyles: {
      fillColor: [248, 250, 252],
    },
    columnStyles:
      data.type === 'reservations'
        ? { 0: { cellWidth: 10 }, 5: { cellWidth: 22 } }
        : data.type === 'equipment_usage'
        ? { 0: { cellWidth: 10 }, 2: { cellWidth: 22 }, 5: { cellWidth: 18 } }
        : { 0: { cellWidth: 10 }, 4: { cellWidth: 18 } },
    didDrawPage: (d) => {
      const pgH = doc.internal.pageSize.height
      doc.setFillColor(29, 78, 216)
      doc.rect(0, pgH - 11, pageW, 11, 'F')
      doc.setTextColor(200, 215, 255)
      doc.setFontSize(7)
      doc.setFont('helvetica', 'normal')
      doc.text(
        `LabNova © ${new Date().getFullYear()} – Sistema de Gestión de Laboratorios`,
        margin, pgH - 4
      )
      doc.text(
        `Página ${d.pageNumber}`,
        pageW - margin, pgH - 4, { align: 'right' }
      )
    },
  })

  const dateStr = new Date().toISOString().slice(0, 10)
  const typePart = TYPE_LABELS[data.type].replace(/\s+/g, '_')
  doc.save(`LabNova_${typePart}_${dateStr}.pdf`)
}
