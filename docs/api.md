# Documentación de API

## Base URL

- `http://localhost:8000/api` en local
- En producción, debe apuntar a la URL del backend desplegado.

## Autenticación

### POST /login

- Request:
  - `email`
  - `password`

- Response:
  - `token`
  - `user`

### POST /logout
- Requiere autenticación `Bearer`.

## Perfil de usuario

### GET /profile
- Devuelve el usuario autenticado.

### PUT /profile
- Actualiza datos del perfil.

### POST /profile/password
- Cambia la contraseña.

## Usuarios

### GET /users
- Listado de usuarios.

### GET /users/{id}
- Ver usuario.

### POST /users
- Crear usuario.

### PUT /users/{id}
- Actualizar usuario.

### DELETE /users/{id}
- Eliminar usuario.

### GET /users/general-data
- Retorna roles y géneros para formularios.

## Permisos

### POST /permissions/general-data
- Obtiene configuración de permisos.

### GET /permissions/roles/{role}
- Permisos asignados a un rol.

### POST /permissions/roles/{role}/assign
- Asigna permisos a un rol.

## Categorías

### GET /categories
- Lista categorías.

### GET /categories/{id}
- Ver categoría.

### POST /categories
- Crear categoría.

### PUT /categories/{id}
- Actualizar categoría.

### DELETE /categories/{id}
- Eliminar categoría.

## Equipos

### GET /equipment
- Listado de equipos.

### GET /equipment/{id}
- Ver equipo.

### GET /equipment/search
- Búsqueda de equipo por término.

### POST /equipment
- Crear equipo.

### PUT /equipment/{id}
- Actualizar equipo.

### POST /equipment/{id}/image
- Subir imagen del equipo.

### DELETE /equipment/{id}
- Eliminar equipo.

## Reservas

### GET /reservations
- Listado de reservas (para el usuario autenticado o según permisos).

### GET /reservations/{id}
- Ver reserva específica.

### POST /reservations
- Crear nueva reserva.

### GET /reservations/availability
- Verificar disponibilidad de equipo.

### GET /reservations/next-available
- Consultar siguiente disponibilidad.

### POST /reservations/{id}/approve
- Aprobar reserva.

### POST /reservations/{id}/reject
- Rechazar reserva.

### POST /reservations/{id}/cancel
- Cancelar reserva.

### POST /reservations/{id}/complete
- Completar reserva.

### GET /reservations/{id}/logs
- Ver historial de cambios de reserva.

## Reportes

### GET /reports/stats/reservations
- Estadísticas de reservaciones para dashboard.

### GET /reports/stats/equipment
- Estadísticas de equipos para dashboard.

### POST /reports/generate
- Generar reporte.

### GET /reports
- Listar reportes.

### GET /reports/{id}
- Ver reporte.

### GET /reports/{id}/download
- Descargar reporte.

### POST /reports
- Crear reporte.

## Formato general de respuesta

El backend devuelve JSON estándar con campos como:

- `success`
- `data`
- `message`
- `meta` (paginación)

## Nota de implementación frontend

Los clientes frontend en `frontend-lab-nova/src/services/` usan las funciones:

- `authService`
- `userService`
- `equipmentService`
- `reservationService`
- `reportService`

Estas capas documentan qué endpoint consume cada pantalla.
