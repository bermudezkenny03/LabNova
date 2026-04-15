# 🚀 LabNova

Sistema web para la gestión y reserva de equipos de laboratorio de manera eficiente y organizada.

---

## 📌 Descripción

**LabNova** es una plataforma diseñada para facilitar la administración y reserva de equipos de laboratorio, permitiendo a estudiantes, docentes e investigadores gestionar recursos de forma sencilla, evitando conflictos y optimizando el uso del laboratorio.

---

## ✨ Características

- 📅 Reserva de equipos en tiempo real
- 👥 Gestión de usuarios y roles
- 🧪 Control de disponibilidad de equipos
- 🔔 Notificaciones de reservas
- 📊 Historial de uso
- 🔒 Sistema de autenticación seguro
- 🛡️ **Control de acceso basado en roles (RBAC)** - Multi-capa de seguridad
- 🔐 Validación de permisos en frontend y backend
- 📋 5 roles predefinidos con permisos granulares

---

## 🛠️ Tecnologías

- ⚙️ Backend: Laravel 11 (PHP) con Sanctum para autenticación
- 🗄️ Base de datos: SQLite (desarrollo) / MySQL (producción)
- 🌐 API REST con middleware de permisos
- 🎨 Frontend: React 18 + TypeScript
- 🔒 RBAC: Sistema de control de acceso basado en roles
- 📦 Gestión de paquetes: npm + Composer

---

## 🚀 Instalación

```bash
# Clonar repositorio
git clone https://github.com/bermudezkenny03/LabNova.git

# Entrar al proyecto
cd LabNova

# Instalar dependencias backend
composer install

# Configurar entorno
cp .env.example .env

# Generar clave
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# O ejecutar migraciones + seeders desde cero
php artisan migrate:fresh --seed

# Instalar dependencias frontend
npm install

# Ejecutar frontend (Vite)
npm run dev

# Iniciar servidor backend
php artisan serve
```

---

## 🔐 Sistema RBAC (Control de Acceso Basado en Roles)

LabNova implementa un sistema robusto de control de acceso con **tres capas de seguridad**:

### Roles Disponibles

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| **Super Admin** | Acceso sin restricciones | Todos los módulos |
| **Administrador** | Gestión de usuarios y sistema | Usuarios, Equipos, Reportes, Reservas (aprobar) |
| **Encargado de Laboratorio** | Gestión de equipos y aprobación | Equipos (CRUD), Reservas (aprobar) |
| **Docente** | Docencia y reserva | Reservas, Equipos (lectura), Reportes |
| **Estudiante** | Uso de laboratorio | Sus propias reservas, Equipos (lectura) |

### Capas de Seguridad

1. **Nivel de Rutas** - Protección en React
   - `ProtectedRoute` - Bloquea navegación no autorizada
   - Redirección a login si no autenticado

2. **Nivel de Componentes** - Protección en la UI
   - `ProtectedButton` - Oculta/desactiva botones
   - `IfCan` - Renderizado condicional
   - Feedback visual de permisos denegados

3. **Nivel de API Backend** - Validación en el servidor
   - Middleware `CheckPermission` en todas las rutas
   - Validación de permisos en controladores
   - Respuestas 403 para acceso denegado

### Credenciales de Prueba

```
Contraseña para todos: Password123!

- Super Admin: superadmin@test.com
- Administrador: admin@test.com
- Encargado: encargado@test.com
- Docente: docente@test.com
- Estudiante: estudiante@test.com
```

Para más detalles, ver [RBAC_IMPLEMENTATION_SUMMARY.md](./RBAC_IMPLEMENTATION_SUMMARY.md) y [TESTING_RBAC_GUIDE.md](./TESTING_RBAC_GUIDE.md)

---