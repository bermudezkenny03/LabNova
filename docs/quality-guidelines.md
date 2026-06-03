# Guía de calidad y mejores prácticas

## Objetivo

Este documento ayuda a mantener la calidad del proyecto acorde a estándares como ISO/IEC 25010, OWASP y WCAG.

## Calidad de código

- Mantener una estructura clara de carpetas.
- Usar `eslint` y `prettier` para estilo consistente en frontend.
- Añadir comentarios técnicos en componentes complejos.
- Evitar código duplicado y favorecer funciones reutilizables.

## Documentación

- Mantener esta carpeta `docs/` actualizada.
- Añadir descripciones de cada nuevo endpoint y servicio.
- Documentar decisiones técnicas importantes en `docs/architecture.md`.

## Seguridad

- Validar todos los datos en el backend.
- No exponer credenciales en el repositorio.
- Usar tokens seguros y renovar sesiones.
- Revisar dependencias con `npm audit` y `composer audit`.

## Accesibilidad

- Etiquetas ARIA en formularios y botones.
- Contraste de colores adecuado.
- Navegación por teclado funcional.
- Mensajes de error accesibles y descriptivos.

## Pruebas y calidad

- Backend: `php artisan test` o `composer test`.
- Frontend: `npm run lint`, `npm run build`.
- Agregar pruebas unitarias y de integración para flujos críticos.
- Registrar métricas de cobertura a futuro.

## Despliegue y operaciones

- Mantener un proceso reproducible de build y despliegue.
- Usar alias apropiados en Vercel para producción.
- Verificar `vercel.json` y `package.json` cuando se cambien scripts de build.
- Controlar versiones y ramas con Git.

## Mejora continua

- Revisa la documentación cuando se agrega funcionalidad.
- Realiza code reviews antes de mergear cambios.
- Establece checklists para PRs: tests, lint, accesibilidad, seguridad.

## Recomendaciones rápidas

- `cd frontend-lab-nova && npm run lint`
- `cd frontend-lab-nova && npm run build`
- `cd backend-lab-nova && composer test`
- `cd backend-lab-nova && php artisan migrate --force`

## Mapa de calidad mínima

- Calidad funcional: funcionalidad de reservas, roles y panel.
- Rendimiento: builds optimizadas y assets minimizados.
- Seguridad: auth y validaciones.
- Usabilidad: UI clara y mensajes útiles.
- Mantenibilidad: código limpio y documentación actualizada.
