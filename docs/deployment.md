# Despliegue

## Estructura de despliegue actual

El frontend se despliega en Vercel desde la carpeta `frontend-lab-nova`.
El backend se puede desplegar en su propio servidor PHP/Laravel o en una plataforma que soporte Laravel.

## Vercel

El proyecto Vercel usa `vercel.json` y el build se ejecuta en la carpeta `frontend-lab-nova`.

### Comandos de despliegue actuales

1. Construir localmente:
   ```bash
   cd frontend-lab-nova
   npm run build
   ```

2. Desplegar con Vercel:
   ```bash
   cd frontend-lab-nova
   vercel --prod --yes
   ```

3. Re-alias para dominio principal:
   ```bash
   vercel alias <deployment-id> labnova.vercel.app
   ```

## Configuración importante

- `frontend-lab-nova/vercel.json`
  - Define `builds` para la carpeta `frontend-lab-nova`.
  - Las opciones de build en el panel de Vercel pueden ser ignoradas si `builds` existe.

- `frontend-lab-nova/package.json`
  - `build` ejecuta `tsc -b && vite build`

- `frontend-lab-nova/vite.config.ts`
  - Configura el servidor de desarrollo local.

## Notas sobre dominios y aliases

- El dominio principal debe apuntar al despliegue actual.
- Si el dominio carga en blanco, revisa el alias activo con:
  ```bash
  vercel alias ls
  ```

## Sugerencias de mejora

- Añadir un pipeline CI/CD que ejecute pruebas y build antes de desplegar.
- Versionar la configuración de despliegue con `vercel.json`.
- Documentar los pasos de rollback y las URLs de staging.
