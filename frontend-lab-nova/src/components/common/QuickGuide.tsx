import React from 'react'
import { Modal } from './Modal'

interface QuickGuideProps {
  open: boolean
  onClose: () => void
  currentPath: string
}

const pageHelp: Record<string, { title: string; items: string[] }> = {
  '/': {
    title: 'Panel principal',
    items: [
      'La vista principal muestra tus indicadores generales y accesos rápidos.',
      'Usa el menú izquierdo para navegar entre equipos, reservas, reportes y usuarios.',
      'Revisa el estado de tu cuenta desde el perfil y cierra sesión con el botón superior derecho.',
    ],
  },
  '/equipment': {
    title: 'Gestión de equipos',
    items: [
      'Busca y filtra equipos por nombre, estado o categoría para encontrar lo que necesitas rápido.',
      'Cambia entre vista de cuadrícula y lista para ver más detalles o una visión más compacta.',
      'Haz clic en editar o eliminar en cada tarjeta para gestionar el equipo seleccionado.',
    ],
  },
  '/reservations': {
    title: 'Reservas',
    items: [
      'Filtra reservas por fechas y tipo de usuario para administrar la agenda con claridad.',
      'Usa el formulario de reserva para seleccionar equipo, horario y notas adicionales.',
      'Revisa el estado de cada reserva y cancela o modifica según tus permisos.',
    ],
  },
  '/reports': {
    title: 'Reportes',
    items: [
      'Selecciona un rango de fechas para generar reportes precisos de actividad.',
      'Descarga informes en PDF directamente desde la interfaz.',
      'Si no seleccionas rango, se incluirán todos los registros disponibles.',
    ],
  },
  '/users': {
    title: 'Usuarios',
    items: [
      'Registra nuevos usuarios y asigna roles desde el formulario de usuario.',
      'Actualiza información personal, estado y permisos con cuidado para mantener seguridad.',
      'Busca usuarios por nombre o correo para encontrar perfiles rápidamente.',
    ],
  },
  '/profile': {
    title: 'Perfil',
    items: [
      'Actualiza tu información personal y contraseña de forma segura.',
      'Asegúrate de usar un correo válido para recibir comunicaciones del sistema.',
      'Mantén tus datos actualizados para facilitar la gestión de tu cuenta.',
    ],
  },
}

const getHelp = (path: string) => {
  const normalized = path.split('?')[0].split('#')[0]
  return pageHelp[normalized] ?? {
    title: 'Guía rápida',
    items: [
      'Utiliza el menú lateral para navegar entre secciones importantes del sistema.',
      'Accede a tu perfil y cierra sesión desde el botón superior derecho.',
      'Cada página incluye acciones claves para crear, editar y filtrar datos.',
    ],
  }
}

export const QuickGuide: React.FC<QuickGuideProps> = ({ open, onClose, currentPath }) => {
  if (!open) return null

  const help = getHelp(currentPath)

  return (
    <Modal title="Guía rápida" onClose={onClose} size="lg" allowBackdropClick={true}>
      <p className="text-sm text-gray-600 mb-4">
        Esta guía te ayuda a orientarte dentro de la aplicación y usar las funciones más importantes.
      </p>
      <div className="space-y-4">
        <div className="rounded-xl bg-slate-50 border border-slate-200 p-4">
          <h4 className="text-sm font-semibold text-slate-900 mb-2">{help.title}</h4>
          <ul className="list-disc list-inside space-y-2 text-sm text-slate-700">
            {help.items.map((item, index) => (
              <li key={index}>{item}</li>
            ))}
          </ul>
        </div>
        <div className="rounded-xl bg-blue-50 border border-blue-100 p-4">
          <h4 className="text-sm font-semibold text-blue-900 mb-2">Consejo general</h4>
          <p className="text-sm text-blue-800">
            Usa la barra lateral para un acceso rápido y recuerda que puedes cambiar de vista en listas y tarjetas donde haya opciones de visualización.
          </p>
        </div>
      </div>
      <div className="mt-6 text-right">
        <button
          type="button"
          onClick={onClose}
          className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition"
        >
          Entendido
        </button>
      </div>
    </Modal>
  )
}
