import apiClient from './api'
import { Report, ReportRequest, PaginatedResponse } from '../types'

export const reportService = {
  // GET /reports → { success, data: [...], meta: {...} }
  getAllReports: async (page = 1, perPage = 10): Promise<PaginatedResponse<Report>> => {
    const response = await apiClient.get('/reports', {
      params: { page, per_page: perPage },
    })
    const meta = response.data.meta ?? {}
    return {
      data: response.data.data ?? [],
      current_page: meta.current_page ?? page,
      last_page: meta.last_page ?? 1,
      per_page: meta.per_page ?? perPage,
      total: meta.total ?? 0,
    }
  },

  // GET /reports/:id → { success, data: {...} }
  getReportById: async (id: number): Promise<Report> => {
    const response = await apiClient.get(`/reports/${id}`)
    return response.data.data
  },

  // DELETE /reports/:id
  deleteReport: async (id: number): Promise<void> => {
    await apiClient.delete(`/reports/${id}`)
  },

  // GET /reports/:id/download — returns file info (not a blob in this backend)
  downloadReport: async (id: number): Promise<Blob> => {
    const response = await apiClient.get(`/reports/${id}/download`, {
      responseType: 'blob',
    })
    return response.data
  },

  // --- Report Requests ---

  // GET /report-requests → { success, data: [...], meta: {...} }
  getAllReportRequests: async (page = 1, perPage = 10): Promise<PaginatedResponse<ReportRequest>> => {
    const response = await apiClient.get('/report-requests', {
      params: { page, per_page: perPage },
    })
    const meta = response.data.meta ?? {}
    return {
      data: response.data.data ?? [],
      current_page: meta.current_page ?? page,
      last_page: meta.last_page ?? 1,
      per_page: meta.per_page ?? perPage,
      total: meta.total ?? 0,
    }
  },

  // GET /report-requests/:id → { success, data: {...} }
  getReportRequestById: async (id: number): Promise<ReportRequest> => {
    const response = await apiClient.get(`/report-requests/${id}`)
    return response.data.data
  },

  // POST /report-requests → { success, data: {...} }
  // Validates: type (reservations|equipment_usage|user_activity), start_date, end_date, filters
  createReportRequest: async (data: {
    type: 'reservations' | 'equipment_usage' | 'user_activity'
    start_date?: string
    end_date?: string
    filters?: Record<string, unknown>
  }): Promise<ReportRequest> => {
    const response = await apiClient.post('/report-requests', data)
    return response.data.data
  },

  // POST /report-requests/:id/approve → sets status = 'processing'
  approveReportRequest: async (id: number): Promise<ReportRequest> => {
    const response = await apiClient.post(`/report-requests/${id}/approve`)
    return response.data.data
  },

  // POST /report-requests/:id/reject → sets status = 'failed'
  rejectReportRequest: async (id: number): Promise<ReportRequest> => {
    const response = await apiClient.post(`/report-requests/${id}/reject`)
    return response.data.data
  },

  // POST /report-requests/:id/complete → sets status = 'completed'
  completeReportRequest: async (id: number): Promise<ReportRequest> => {
    const response = await apiClient.post(`/report-requests/${id}/complete`)
    return response.data.data
  },

  // GET /reports/stats/reservations → { success, data: { total, pending, approved, rejected, cancelled, completed } }
  getReservationStats: async (startDate?: string, endDate?: string) => {
    const response = await apiClient.get('/reports/stats/reservations', {
      params: { start_date: startDate, end_date: endDate },
    })
    return response.data.data as {
      total: number
      pending: number
      approved: number
      rejected: number
      cancelled: number
      completed: number
    }
  },

  // GET /reports/stats/equipment → { success, data: { total, available, maintenance, out_of_service } }
  getEquipmentStats: async () => {
    const response = await apiClient.get('/reports/stats/equipment')
    return response.data.data as {
      total: number
      available: number
      maintenance: number
      out_of_service: number
    }
  },

  // POST /reports/generate → returns full data ready for client-side PDF generation
  generateReport: async (data: {
    type: 'reservations' | 'equipment_usage' | 'user_activity'
    start_date?: string
    end_date?: string
  }) => {
    const response = await apiClient.post('/reports/generate', data)
    return response.data.data
  },
}
