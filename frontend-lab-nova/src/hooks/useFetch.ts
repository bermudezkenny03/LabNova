import { useState, useEffect, useCallback } from 'react'
import { AxiosError } from 'axios'

interface UseFetchState<T> {
  data: T | null
  loading: boolean
  error: AxiosError | null
}

export const useFetch = <T,>(
  fetchFunction: () => Promise<T>,
  dependencies: unknown[] = []
) => {
  const [state, setState] = useState<UseFetchState<T>>({
    data: null,
    loading: true,
    error: null,
  })

  const fetchData = useCallback(async () => {
    try {
      setState((prev) => ({ ...prev, loading: true }))
      const result = await fetchFunction()
      setState({ data: result, loading: false, error: null })
    } catch (err) {
      const error = err instanceof AxiosError ? err : new AxiosError('Unknown error')
      setState((prev) => ({ ...prev, error, loading: false }))
    }
  }, [fetchFunction])

  useEffect(() => {
    fetchData()
  }, dependencies)

  const refetch = useCallback(() => {
    fetchData()
  }, [fetchData])

  return { ...state, refetch }
}
