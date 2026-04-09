// Role Types
export interface Role {
  id: number
  name: string
  description: string
  created_at: string
  updated_at: string
}

// User Types — fields match backend User model + StoreUserRequest
export interface User {
  id: number
  name: string
  last_name: string
  email: string
  phone?: string
  status?: boolean
  role_id?: number
  role?: Role
  gender?: string
  birthdate?: string
  address?: string
  addon_address?: string
  notes?: string
  userDetail?: UserDetail
  created_at: string
  updated_at: string
}

export interface UserDetail {
  id: number
  user_id: number
  phone: string
  department: string
  position: string
  created_at: string
  updated_at: string
}

// Category Types
export interface Category {
  id: number
  name: string
  slug?: string
  description?: string
  status?: boolean
  created_at: string
  updated_at: string
}

// Equipment Types — status values from backend
export interface Equipment {
  id: number
  name: string
  code: string
  description?: string
  stock?: number
  category_id: number
  category?: Category
  status: 'available' | 'maintenance' | 'out_of_service'
  is_active?: boolean
  images?: EquipmentImage[]
  created_at: string
  updated_at: string
}

export interface EquipmentImage {
  id: number
  equipment_id: number
  image_url: string
  created_at: string
  updated_at: string
}

// Reservation Types — uses start_time/end_time as in backend
export interface Reservation {
  id: number
  user_id: number
  equipment_id: number
  start_time: string
  end_time: string
  status: 'pending' | 'approved' | 'rejected' | 'cancelled' | 'completed'
  notes?: string
  approved_by?: number
  approved_at?: string
  rejection_reason?: string
  user?: User
  equipment?: Equipment
  logs?: ReservationLog[]
  created_at: string
  updated_at: string
}

export interface ReservationLog {
  id: number
  reservation_id: number
  user_id: number
  action: string
  description: string
  user?: User
  created_at: string
  updated_at: string
}

// Report Types — linked to ReportRequest via report_request_id
export interface Report {
  id: number
  report_request_id: number
  file_path?: string
  file_name?: string
  file_type?: string
  generated_at: string
  reportRequest?: ReportRequest
  created_at: string
  updated_at: string
}

// Report Request Types — type enum matches backend validation
export interface ReportRequest {
  id: number
  user_id: number
  type: 'reservations' | 'equipment_usage' | 'user_activity'
  start_date?: string
  end_date?: string
  filters?: Record<string, unknown>
  status: 'pending' | 'processing' | 'completed' | 'failed'
  user?: User
  reports?: Report[]
  created_at: string
  updated_at: string
}

// API Response Types
export interface ApiResponse<T> {
  success: boolean
  message: string
  data?: T
  errors?: Record<string, string[]>
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

// Auth Types
export interface LoginRequest {
  email: string
  password: string
}

export interface LoginResponse {
  token: string
  user: AuthUser
}

export interface AuthUser {
  id: number
  name: string
  last_name?: string
  email: string
  role?: Role
  role_id?: number
}
