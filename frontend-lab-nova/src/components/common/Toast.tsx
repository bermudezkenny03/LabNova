import React, { useEffect, useState, useCallback } from 'react'

export type ToastType = 'success' | 'error' | 'warning' | 'info'

export interface ToastData {
  id: string
  type: ToastType
  message: string
  duration?: number
}

const ICONS: Record<ToastType, React.ReactNode> = {
  success: (
    <div className="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center shrink-0">
      <svg className="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
      </svg>
    </div>
  ),
  error: (
    <div className="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center shrink-0">
      <svg className="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M6 18L18 6M6 6l12 12" />
      </svg>
    </div>
  ),
  warning: (
    <div className="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
      <svg className="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
    </div>
  ),
  info: (
    <div className="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
      <svg className="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    </div>
  ),
}

const LABELS: Record<ToastType, string> = {
  success: 'Éxito',
  error: 'Error',
  warning: 'Advertencia',
  info: 'Información',
}

const BORDER_COLORS: Record<ToastType, string> = {
  success: 'border-l-green-500',
  error: 'border-l-red-500',
  warning: 'border-l-amber-500',
  info: 'border-l-blue-500',
}

const BAR_COLORS: Record<ToastType, string> = {
  success: 'bg-green-500',
  error: 'bg-red-500',
  warning: 'bg-amber-500',
  info: 'bg-blue-500',
}

interface ToastItemProps {
  toast: ToastData
  onRemove: (id: string) => void
}

const ToastItem: React.FC<ToastItemProps> = ({ toast, onRemove }) => {
  const [visible, setVisible] = useState(false)
  const duration = toast.duration ?? 4500

  const dismiss = useCallback(() => {
    setVisible(false)
    setTimeout(() => onRemove(toast.id), 350)
  }, [toast.id, onRemove])

  useEffect(() => {
    const enterTimer = setTimeout(() => setVisible(true), 20)
    const exitTimer = setTimeout(dismiss, duration)
    return () => {
      clearTimeout(enterTimer)
      clearTimeout(exitTimer)
    }
  }, [dismiss, duration])

  return (
    <div
      className={`
        relative overflow-hidden bg-white rounded-xl shadow-xl border border-gray-100 border-l-4
        ${BORDER_COLORS[toast.type]} w-80 flex items-start gap-3 p-4
        transition-all duration-350 ease-out
        ${visible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-10'}
      `}
      style={{ transitionDuration: '350ms' }}
    >
      {ICONS[toast.type]}

      <div className="flex-1 min-w-0 pr-1">
        <p className="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">
          {LABELS[toast.type]}
        </p>
        <p className="text-sm text-gray-800 leading-snug">{toast.message}</p>
      </div>

      <button
        onClick={dismiss}
        className="text-gray-300 hover:text-gray-500 transition-colors shrink-0 mt-0.5"
        aria-label="Cerrar"
      >
        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <div
        className={`absolute bottom-0 left-0 h-0.5 ${BAR_COLORS[toast.type]}`}
        style={{ animation: `toast-shrink ${duration}ms linear forwards` }}
      />
    </div>
  )
}

export interface ToastContainerProps {
  toasts: ToastData[]
  onRemove: (id: string) => void
}

export const ToastContainer: React.FC<ToastContainerProps> = ({ toasts, onRemove }) => (
  <>
    <style>{`
      @keyframes toast-shrink {
        from { width: 100%; }
        to   { width: 0%;   }
      }
    `}</style>
    <div className="fixed top-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none">
      {toasts.map((t) => (
        <div key={t.id} className="pointer-events-auto">
          <ToastItem toast={t} onRemove={onRemove} />
        </div>
      ))}
    </div>
  </>
)
