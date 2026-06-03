# Setup y configuración local

## Requisitos previos

- Node.js 20+ (recomendado)
- npm 10+ o yarn
- PHP 8.3+
- Composer
- Base de datos MySQL / MariaDB o SQLite local
- Git

## Configuración del backend

1. En la carpeta `backend-lab-nova`:
   ```bash
   cd backend-lab-nova
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

2. Configura los datos de conexión en `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=labnova
   DB_USERNAME=root
   DB_PASSWORD=secret
   ```

3. Ejecuta migraciones y seeders:
   ```bash
   php artisan migrate --force
   php artisan db:seed
   ```

4. Inicia el servidor de desarrollo backend:
   ```bash
   php artisan serve
   ```

## Configuración del frontend

1. En la carpeta `frontend-lab-nova`:
   ```bash
   cd frontend-lab-nova
   npm install
   ```

2. Crea el archivo de entorno local `.env` con la base de la API:
   ```env
   VITE_API_BASE_URL=http://localhost:8000/api
   ```

3. Inicia el frontend:
   ```bash
   npm run dev
   ```

4. Abre el navegador en la URL que muestre Vite (por defecto `http://localhost:5173`).

## Comandos útiles

- Backend:
  - `composer install`
  - `php artisan serve`
  - `php artisan migrate`
  - `php artisan test`

- Frontend:
  - `npm install`
  - `npm run dev`
  - `npm run build`
  - `npm run lint`

## Notas importantes

- Si el backend está protegido por `sanctum`, el frontend debe usar el token en `localStorage`.
- El frontend usa `axios` con base URL en `VITE_API_BASE_URL`.
- El proyecto raíz contiene un script de build que ejecuta el frontend desde el directorio `frontend-lab-nova`.
