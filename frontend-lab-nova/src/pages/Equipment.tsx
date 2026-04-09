import React, { useEffect, useState, useCallback } from 'react'
import { equipmentService } from '../services/equipmentService'
import { Equipment, Category } from '../types'
import { Modal } from '../components/common/Modal'

const STATUS_LABELS: Record<Equipment['status'], string> = {
  available: 'Disponible',
  maintenance: 'Mantenimiento',
  out_of_service: 'Fuera de servicio',
}

const STATUS_COLORS: Record<Equipment['status'], string> = {
  available: 'bg-green-100 text-green-800',
  maintenance: 'bg-orange-100 text-orange-800',
  out_of_service: 'bg-red-100 text-red-700',
}

interface EquipmentForm {
  name: string
  code: string
  description: string
  category_id: string
  stock: string
  status: Equipment['status']
  is_active: boolean
}

const EMPTY_FORM: EquipmentForm = {
  name: '',
  code: '',
  description: '',
  category_id: '',
  stock: '',
  status: 'available',
  is_active: true,
}

const EquipmentPage: React.FC = () => {
  const [equipment, setEquipment] = useState<Equipment[]>([])
  const [categories, setCategories] = useState<Category[]>([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [successMsg, setSuccessMsg] = useState<string | null>(null)

  const [total, setTotal] = useState(0)
  const [filterStatus, setFilterStatus] = useState('all')
  const [searchInput, setSearchInput] = useState('')
  const [search, setSearch] = useState('')

  const [showModal, setShowModal] = useState(false)
  const [editingId, setEditingId] = useState<number | null>(null)
  const [form, setForm] = useState<EquipmentForm>(EMPTY_FORM)
  const [formErrors, setFormErrors] = useState<Partial<Record<keyof EquipmentForm, string>>>({})

  const [deleteId, setDeleteId] = useState<number | null>(null)
  const [deleting, setDeleting] = useState(false)

  const showSuccess = (msg: string) => {
    setSuccessMsg(msg)
    setTimeout(() => setSuccessMsg(null), 3000)
  }

  const loadEquipment = useCallback(async () => {
    try {
      setLoading(true)
      setError(null)
      const res = await equipmentService.getAllEquipment()
      setEquipment(res.data ?? [])
      setTotal(res.total)
    } catch {
      setError('No se pudo cargar la lista de equipos.')
    } finally {
      setLoading(false)
    }
  }, [])

  const loadCategories = useCallback(async () => {
    try {
      const cats = await equipmentService.getCategories()
      setCategories(cats ?? [])
    } catch {
      // optional
    }
  }, [])

  useEffect(() => {
    loadEquipment()
    loadCategories()
  }, [loadEquipment, loadCategories])

  const handleSearch = async () => {
    if (!searchInput.trim()) {
      setSearch('')
      loadEquipment()
      return
    }
    try {
      setLoading(true)
      setSearch(searchInput)
      const results = await equipmentService.searchEquipment(searchInput.trim())
      setEquipment(results ?? [])
      setTotal(results?.length ?? 0)
    } catch {
      setError('Error al buscar equipos.')
    } finally {
      setLoading(false)
    }
  }

  const handleClearSearch = () => {
    setSearchInput('')
    setSearch('')
    loadEquipment()
  }

  const filtered = filterStatus === 'all'
    ? equipment
    : equipment.filter((e) => e.status === filterStatus)

  const openCreate = () => {
    setEditingId(null)
    setForm(EMPTY_FORM)
    setFormErrors({})
    setShowModal(true)
  }

  const openEdit = (eq: Equipment) => {
    setEditingId(eq.id)
    setForm({
      name: eq.name,
      code: eq.code ?? '',
      description: eq.description ?? '',
      category_id: eq.category_id ? String(eq.category_id) : '',
      stock: eq.stock ? String(eq.stock) : '',
      status: eq.status,
      is_active: eq.is_active !== false,
    })
    setFormErrors({})
    setShowModal(true)
  }

  const validate = (): boolean => {
    const errors: Partial<Record<keyof EquipmentForm, string>> = {}
    if (!form.name.trim()) errors.name = 'El nombre es requerido'
    if (!form.code.trim()) errors.code = 'El codigo es requerido'
    if (form.stock && isNaN(Number(form.stock))) errors.stock = 'El stock debe ser un numero'
    if (form.stock && Number(form.stock) < 1) errors.stock = 'El stock debe ser al menos 1'
    setFormErrors(errors)
    return Object.keys(errors).length === 0
  }

  const handleSave = async () => {
    if (!validate()) return
    try {
      setSaving(true)
      const payload: Partial<Equipment> = {
        name: form.name.trim(),
        code: form.code.trim(),
        description: form.description.trim() || undefined,
        category_id: form.category_id ? Number(form.category_id) : undefined,
        stock: form.stock ? Number(form.stock) : undefined,
        status: form.status,
        is_active: form.is_active,
      }
      if (editingId) {
        await equipmentService.updateEquipment(editingId, payload)
        showSuccess('Equipo actualizado correctamente.')
      } else {
        await equipmentService.createEquipment(payload)
        showSuccess('Equipo creado correctamente.')
      }
      setShowModal(false)
      loadEquipment()
    } catch (err: unknown) {
      const axiosErr = err as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
      const msg = axiosErr?.response?.data?.message ?? 'No se pudo guardar el equipo.'
      const backendErrors = axiosErr?.response?.data?.errors
      if (backendErrors) {
        const mapped: Partial<Record<keyof EquipmentForm, string>> = {}
        if (backendErrors.code) mapped.code = backendErrors.code[0]
        if (backendErrors.name) mapped.name = backendErrors.name[0]
        if (backendErrors.stock) mapped.stock = backendErrors.stock[0]
        setFormErrors(mapped)
      }
      setError(msg)
    } finally {
      setSaving(false)
    }
  }

  const handleDelete = async () => {
    if (!deleteId) return
    try {
      setDeleting(true)
      await equipmentService.deleteEquipment(deleteId)
      setDeleteId(null)
      showSuccess('Equipo eliminado correctamente.')
      loadEquipment()
    } catch {
      setError('No se pudo eliminar el equipo.')
    } finally {
      setDeleting(false)
    }
  }

  const field = (
    label: string,
    key: keyof EquipmentForm,
    input: React.ReactNode,
  ) => (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
      {input}
      {formErrors[key] && <p className="text-red-500 text-xs mt-1">{formErrors[key]}</p>}
    </div>
  )

  const cls = (key: keyof EquipmentForm, base = '') =>
    `w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 ${base} ${formErrors[key] ? 'border-red-400' : 'border-gray-200'}`

  return (
    <div className="space-y-5">
      {/* Encabezado */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-800">Equipos</h1>
          <p className="text-gray-500 text-sm mt-0.5">{total} equipos registrados</p>
        </div>
        <button
          onClick={openCreate}
          className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
          + Nuevo Equipo
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

      {/* Filtros */}
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">
        <div className="flex gap-2 flex-1 min-w-[200px]">
          <input
            type="text"
            placeholder="Buscar por nombre o codigo..."
            value={searchInput}
            onChange={(e) => setSearchInput(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleSearch()}
            className="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
          />
          <button onClick={handleSearch} className="bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm transition-colors">
            Buscar
          </button>
          {search && (
            <button onClick={handleClearSearch} className="text-gray-400 hover:text-gray-600 text-sm px-2">
              &times; Limpiar
            </button>
          )}
        </div>
        <select
          value={filterStatus}
          onChange={(e) => setFilterStatus(e.target.value)}
          className="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
        >
          <option value="all">Todos los estados</option>
          <option value="available">Disponible</option>
          <option value="maintenance">Mantenimiento</option>
          <option value="out_of_service">Fuera de servicio</option>
        </select>
      </div>

      {/* Tabla */}
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        {loading ? (
          <div className="p-6 space-y-3">
            {[...Array(5)].map((_, i) => <div key={i} className="h-10 bg-gray-100 rounded animate-pulse" />)}
          </div>
        ) : filtered.length === 0 ? (
          <div className="p-12 text-center text-gray-400">
            <p className="text-lg mb-1">No hay equipos</p>
            <p className="text-sm">{search ? `Sin resultados para "${search}"` : 'Crea el primer equipo.'}</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left">
              <thead className="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                  <th className="px-5 py-3 font-medium">#</th>
                  <th className="px-5 py-3 font-medium">Nombre</th>
                  <th className="px-5 py-3 font-medium">Codigo</th>
                  <th className="px-5 py-3 font-medium">Categoria</th>
                  <th className="px-5 py-3 font-medium">Stock</th>
                  <th className="px-5 py-3 font-medium">Estado</th>
                  <th className="px-5 py-3 font-medium">Activo</th>
                  <th className="px-5 py-3 font-medium text-right">Acciones</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {filtered.map((eq) => (
                  <tr key={eq.id} className="hover:bg-gray-50 transition-colors">
                    <td className="px-5 py-3 text-gray-400 font-mono text-xs">{eq.id}</td>
                    <td className="px-5 py-3 font-medium text-gray-800">{eq.name}</td>
                    <td className="px-5 py-3 font-mono text-xs text-gray-600">{eq.code}</td>
                    <td className="px-5 py-3 text-gray-600">
                      {eq.category?.name ?? categories.find((c) => c.id === eq.category_id)?.name ?? '—'}
                    </td>
                    <td className="px-5 py-3 text-gray-600 text-center">{eq.stock ?? '—'}</td>
                    <td className="px-5 py-3">
                      <span className={`px-2.5 py-1 rounded-full text-xs font-semibold ${STATUS_COLORS[eq.status]}`}>
                        {STATUS_LABELS[eq.status]}
                      </span>
                    </td>
                    <td className="px-5 py-3">
                      <span className={`px-2.5 py-1 rounded-full text-xs font-semibold ${eq.is_active !== false ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>
                        {eq.is_active !== false ? 'Si' : 'No'}
                      </span>
                    </td>
                    <td className="px-5 py-3 text-right">
                      <button onClick={() => openEdit(eq)} className="text-blue-600 hover:text-blue-800 text-xs font-medium mr-3">
                        Editar
                      </button>
                      <button onClick={() => setDeleteId(eq.id)} className="text-red-500 hover:text-red-700 text-xs font-medium">
                        Eliminar
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Modal crear/editar */}
      {showModal && (
        <Modal title={editingId ? 'Editar Equipo' : 'Nuevo Equipo'} onClose={() => setShowModal(false)} size="lg">
          <div className="grid grid-cols-2 gap-4">
            {field('Nombre *', 'name',
              <input type="text" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })}
                className={cls('name')} placeholder="Ej: Microscopio Optico" />
            )}
            {field('Codigo *', 'code',
              <input type="text" value={form.code} onChange={(e) => setForm({ ...form, code: e.target.value })}
                className={cls('code')} placeholder="Ej: MIC-001" />
            )}
            {field('Categoria', 'category_id',
              <select value={form.category_id} onChange={(e) => setForm({ ...form, category_id: e.target.value })}
                className={cls('category_id')}>
                <option value="">Sin categoria</option>
                {categories.map((cat) => <option key={cat.id} value={cat.id}>{cat.name}</option>)}
              </select>
            )}
            {field('Stock', 'stock',
              <input type="number" value={form.stock} onChange={(e) => setForm({ ...form, stock: e.target.value })}
                className={cls('stock')} placeholder="Cantidad disponible" min={1} />
            )}
            {field('Estado', 'status',
              <select value={form.status} onChange={(e) => setForm({ ...form, status: e.target.value as Equipment['status'] })}
                className={cls('status')}>
                <option value="available">Disponible</option>
                <option value="maintenance">Mantenimiento</option>
                <option value="out_of_service">Fuera de servicio</option>
              </select>
            )}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Activo</label>
              <div className="flex gap-4 mt-1">
                <label className="flex items-center gap-2 cursor-pointer">
                  <input type="radio" checked={form.is_active} onChange={() => setForm({ ...form, is_active: true })} />
                  <span className="text-sm text-gray-700">Si</span>
                </label>
                <label className="flex items-center gap-2 cursor-pointer">
                  <input type="radio" checked={!form.is_active} onChange={() => setForm({ ...form, is_active: false })} />
                  <span className="text-sm text-gray-700">No</span>
                </label>
              </div>
            </div>
            <div className="col-span-2">
              {field('Descripcion', 'description',
                <textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })}
                  rows={3} className={cls('description', 'resize-none')} placeholder="Descripcion del equipo (opcional)" />
              )}
            </div>
          </div>
          <div className="flex justify-end gap-3 pt-4 mt-2 border-t border-gray-100">
            <button onClick={() => setShowModal(false)} className="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
              Cancelar
            </button>
            <button onClick={handleSave} disabled={saving} className="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors">
              {saving ? 'Guardando...' : editingId ? 'Actualizar' : 'Crear Equipo'}
            </button>
          </div>
        </Modal>
      )}

      {/* Confirmar eliminacion */}
      {deleteId !== null && (
        <Modal title="Confirmar Eliminacion" onClose={() => setDeleteId(null)} size="sm">
          <p className="text-gray-600 text-sm mb-4">
            Esta seguro de eliminar este equipo? Esta accion no se puede deshacer.
          </p>
          <div className="flex justify-end gap-3">
            <button onClick={() => setDeleteId(null)} className="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
              Cancelar
            </button>
            <button onClick={handleDelete} disabled={deleting} className="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors">
              {deleting ? 'Eliminando...' : 'Eliminar'}
            </button>
          </div>
        </Modal>
      )}
    </div>
  )
}

export default EquipmentPage
