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

---

## 🛠️ Tecnologías

- ⚙️ Backend: Laravel (PHP)
- 🗄️ Base de datos: MySQL
- 🌐 API REST
- 🎨 Frontend: Vue.js
- 📦 Gestión de paquetes: npm

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
