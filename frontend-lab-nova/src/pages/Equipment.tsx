import React, { useEffect, useState, useCallback } from 'react'
import { equipmentService } from '../services/equipmentService'
import { Equipment, Category } from '../types'
import { Modal } from '../components/common/Modal'

const STATUS_LABELS: Record<Equipment['status'], string> = {
  available: 'Disponible',
  in_use: 'En uso',
  maintenance: 'Mantenimiento',
  out_of_service: 'Fuera de servicio',
  inactive: 'Inactivo',
}

const STATUS_COLORS: Record<Equipment['status'], string> = {
  available: 'bg-green-100 text-green-800',
  in_use: 'bg-blue-100 text-blue-800',
  maintenance: 'bg-orange-100 text-orange-800',
  out_of_service: 'bg-red-100 text-red-700',
  inactive: 'bg-gray-100 text-gray-600',
}

interface EquipmentForm {
  name: string
  description: string
  category_id: string
  status: Equipment['status']
}

const EMPTY_FORM: EquipmentForm = {
  name: '',
  description: '',
  category_id: '',
  status: 'available',
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
  const [formErrors, setFormErrors] = useState<Partial<EquipmentForm>>({})

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
      // categories are optional
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
      description: eq.description ?? '',
      category_id: eq.category_id ? String(eq.category_id) : '',
      status: eq.status,
    })
    setFormErrors({})
    setShowModal(true)
  }

  const validate = (): boolean => {
    const errors: Partial<EquipmentForm> = {}
    if (!form.name.trim()) errors.name = 'El nombre es requerido'
    if (!form.category_id) errors.category_id = 'La categoria es requerida'
    setFormErrors(errors)
    return Object.keys(errors).length === 0
  }

  const handleSave = async () => {
    if (!validate()) return
    try {
      setSaving(true)
      const payload = {
        name: form.name.trim(),
        description: form.description.trim(),
        category_id: Number(form.category_id),
        status: form.status,
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
    } catch {
      setError('No se pudo guardar el equipo. Intenta de nuevo.')
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

      {/* Filtros y busqueda */}
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">
        <div className="flex gap-2 flex-1 min-w-[200px]">
          <input
            type="text"
            placeholder="Buscar equipo..."
            value={searchInput}
            onChange={(e) => setSearchInput(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleSearch()}
            className="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
          />
          <button
            onClick={handleSearch}
            className="bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm transition-colors"
          >
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
          <option value="in_use">En uso</option>
          <option value="maintenance">Mantenimiento</option>
          <option value="out_of_service">Fuera de servicio</option>
          <option value="inactive">Inactivo</option>
        </select>
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
            <p className="text-lg mb-1">No hay equipos</p>
            <p className="text-sm">
              {search ? `Sin resultados para "${search}"` : 'Crea el primer equipo con el boton de arriba.'}
            </p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left">
              <thead className="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                  <th className="px-6 py-3 font-medium">#</th>
                  <th className="px-6 py-3 font-medium">Nombre</th>
                  <th className="px-6 py-3 font-medium">Categoria</th>
                  <th className="px-6 py-3 font-medium">Descripcion</th>
                  <th className="px-6 py-3 font-medium">Estado</th>
                  <th className="px-6 py-3 font-medium text-right">Acciones</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {filtered.map((eq) => (
                  <tr key={eq.id} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-3 text-gray-400 font-mono text-xs">{eq.id}</td>
                    <td className="px-6 py-3 font-medium text-gray-800">{eq.name}</td>
                    <td className="px-6 py-3 text-gray-600">
                      {eq.category?.name ?? categories.find((c) => c.id === eq.category_id)?.name ?? `Cat. #${eq.category_id}`}
                    </td>
                    <td className="px-6 py-3 text-gray-500 max-w-xs truncate">
                      {eq.description || <span className="italic text-gray-300">Sin descripcion</span>}
                    </td>
                    <td className="px-6 py-3">
                      <span className={`px-2.5 py-1 rounded-full text-xs font-semibold ${STATUS_COLORS[eq.status]}`}>
                        {STATUS_LABELS[eq.status]}
                      </span>
                    </td>
                    <td className="px-6 py-3 text-right">
                      <button
                        onClick={() => openEdit(eq)}
                        className="text-blue-600 hover:text-blue-800 text-xs font-medium mr-3"
                      >
                        Editar
                      </button>
                      <button
                        onClick={() => setDeleteId(eq.id)}
                        className="text-red-500 hover:text-red-700 text-xs font-medium"
                      >
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
        <Modal title={editingId ? 'Editar Equipo' : 'Nuevo Equipo'} onClose={() => setShowModal(false)}>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
              <input
                type="text"
                value={form.name}
                onChange={(e) => setForm({ ...form, name: e.target.value })}
                className={`w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 ${formErrors.name ? 'border-red-400' : 'border-gray-200'}`}
                placeholder="Ej: Microscopio Optico"
              />
              {formErrors.name && <p className="text-red-500 text-xs mt-1">{formErrors.name}</p>}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
              <select
                value={form.category_id}
                onChange={(e) => setForm({ ...form, category_id: e.target.value })}
                className={`w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 ${formErrors.category_id ? 'border-red-400' : 'border-gray-200'}`}
              >
                <option value="">Seleccionar categoria...</option>
                {categories.map((cat) => (
                  <option key={cat.id} value={cat.id}>{cat.name}</option>
                ))}
              </select>
              {formErrors.category_id && <p className="text-red-500 text-xs mt-1">{formErrors.category_id}</p>}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Estado</label>
              <select
                value={form.status}
                onChange={(e) => setForm({ ...form, status: e.target.value as Equipment['status'] })}
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
              >
                <option value="available">Disponible</option>
                <option value="in_use">En uso</option>
                <option value="maintenance">Mantenimiento</option>
                <option value="out_of_service">Fuera de servicio</option>
                <option value="inactive">Inactivo</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
              <textarea
                value={form.description}
                onChange={(e) => setForm({ ...form, description: e.target.value })}
                rows={3}
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none"
                placeholder="Descripcion del equipo (opcional)"
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
                onClick={handleSave}
                disabled={saving}
                className="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
              >
                {saving ? 'Guardando...' : editingId ? 'Actualizar' : 'Crear Equipo'}
              </button>
            </div>
          </div>
        </Modal>
      )}

      {/* Dialogo de confirmacion */}
      {deleteId !== null && (
        <Modal title="Confirmar Eliminacion" onClose={() => setDeleteId(null)} size="sm">
          <div className="space-y-4">
            <p className="text-gray-600 text-sm">
              Esta seguro de que deseas eliminar este equipo? Esta accion no se puede deshacer.
            </p>
            <div className="flex justify-end gap-3">
              <button
                onClick={() => setDeleteId(null)}
                className="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button
                onClick={handleDelete}
                disabled={deleting}
                className="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors"
              >
                {deleting ? 'Eliminando...' : 'Eliminar'}
              </button>
            </div>
          </div>
        </Modal>
      )}
    </div>
  )
}

export default EquipmentPage
