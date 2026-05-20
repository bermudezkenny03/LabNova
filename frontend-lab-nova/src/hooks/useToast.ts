import { useState, useCallback } from 'react'
import type { ToastData, ToastType } from '../components/common/Toast'

export function useToast() {
  const [toasts, setToasts] = useState<ToastData[]>([])

  const show = useCallback((message: string, type: ToastType = 'info', duration?: number) => {
    const id = `${Date.now()}-${Math.random().toString(36).slice(2)}`
    setToasts((prev) => [...prev, { id, type, message, duration }])
  }, [])

  const remove = useCallback((id: string) => {
    setToasts((prev) => prev.filter((t) => t.id !== id))
  }, [])

  return {
    toasts,
    remove,
    success: useCallback((msg: string) => show(msg, 'success', 4500), [show]),
    error:   useCallback((msg: string) => show(msg, 'error',   6000), [show]),
    warning: useCallback((msg: string) => show(msg, 'warning', 5000), [show]),
    info:    useCallback((msg: string) => show(msg, 'info',    4000), [show]),
  }
}
