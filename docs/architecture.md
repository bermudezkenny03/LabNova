# Arquitectura de Lab Nova

## Resumen

Lab Nova es una aplicación web fullstack con dos capas principales:

- `frontend-lab-nova` - Aplicación React + TypeScript + Vite para la interfaz de usuario.
- `backend-lab-nova` - API Laravel PHP para autenticación, permisos, reservas, equipos, reportes y administración.

## Tecnologías principales

- Frontend:
  - React 18
  - TypeScript
  - Vite
  - Tailwind CSS
  - Axios
  - React Router DOM
  - Zustand

- Backend:
  - Laravel 13
  - PHP 8.3+
  - Sanctum para autenticación
  - Pest y PHPUnit para pruebas

## Flujo de interacción

1. El usuario accede al frontend en el navegador.
2. El frontend consume la API REST en `backend-lab-nova` mediante `axios`.
3. El backend valida con `sanctum` y controla permisos por roles.
4. Los datos de reservas, usuarios, equipos y reportes se envían como JSON.

## Estructura de carpetas

### `frontend-lab-nova`

- `src/`
  - `components/` – UI compartida y layout.
  - `pages/` – pantallas principales como `Dashboard`, `Reservations`, `Equipment`, `Users`, `Login`.
  - `services/` – clientes API (`authService`, `reservationService`, `equipmentService`, `userService`, `reportService`).
  - `context/` – estado global y helpers.
  - `hooks/` – permisos, autenticación y notificaciones.

### `backend-lab-nova`

- `app/` – lógica de negocio, controladores, modelos, políticas y servicios.
- `routes/api.php` – endpoints REST principales.
- `database/` – migraciones, seeders y datos de prueba.
- `tests/` – pruebas automáticas.
- `config/` – configuración del proyecto.

## Módulos clave

- Autenticación y tokens (`sanctum`).
- Gestión de roles y permisos.
- Reservas de equipos con estados: `pending`, `approved`, `rejected`, `cancelled`, `completed`.
- Administración de equipos, categorías y usuarios.
- Reportes y estadísticas del dashboard.

## Principios de diseño

- API-first: el frontend depende de una API REST clara.
- Separación de responsabilidades: UI en React, lógica de negocio en Laravel.
- Control de acceso basado en permisos por módulo.
- Experiencia de usuario responsiva y accesible.
