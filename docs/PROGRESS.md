# Progreso del proyecto — Laravel Accesible

> Fecha de inicio: 2026-10-26
> Última actualización: 2026-10-26

## Estado general

| Fase | Nombre | Rama | Estado | PR | Notas |
|------|--------|------|--------|----|-------|
| 0 | Auditoría del entorno y planificación | `chore/fase-00-auditoria` | En curso | Pendiente push | PHP 8.5.12, Laravel 13.38, Tailwind v4 |
| 1 | Bootstrap de Laravel | `feature/fase-01-bootstrap` | Pendiente | — | — |
| 2 | Internacionalización y layout accesible | `feature/fase-02-internacionalizacion` | Pendiente | — | — |
| 3 | Datos y modelos Eloquent | `feature/fase-03-datos-modelos` | Pendiente | — | — |
| 4 | Panel de administración privado | `feature/fase-04-panel-privado` | Pendiente | — | — |
| 5 | Páginas públicas del blog | `feature/fase-05-paginas-publicas` | Pendiente | — | — |
| 6 | SEO y rendimiento | `feature/fase-06-seo-rendimiento` | Pendiente | — | — |
| 7 | Auditoría WCAG 2.2 AA | `feature/fase-07-accesibilidad` | Pendiente | — | — |
| 8 | Seguridad y preparación de producción | `chore/fase-08-seguridad-produccion` | Pendiente | — | — |
| 9 | Contenido y lanzamiento | `docs/fase-09-contenido-lanzamiento` | Pendiente | — | — |

---

## Detalle por fase

### Fase 0 — Auditoría del entorno y planificación
- **Estado:** En curso — rama creada, pendiente push y PR
- **Rama:** `chore/fase-00-auditoria`
- **Criterios de aceptación:**
  - [✓] Verificar versiones: PHP 8.5.12, Composer 2.10.3, Node 24.13.0, npm 11.17.0, Git 2.51.0
  - [✓] Comprobar extensiones PHP necesarias (todas presentes)
  - [✓] Confirmar acceso a MySQL local (laravel_accesible accesible)
  - [✓] Revisar repositorio Git (1 commit inicial, rama main)
  - [✓] Entregar matriz de compatibilidad y riesgos
  - [✓] Crear documentación base (PROGRESS, DECISIONS, ACCESSIBILITY_CHECKLIST, DEPLOYMENT, README)
- **Riesgos registrados:**
  - R1 (Baja): PHP dual en sistema — 8.4.27 CLI / 8.5.12 Herd; usar Herd
  - R2 (Media): Tailwind v4 instalado — API diferente a v3; verificar con Livewire 4 en Fase 1
  - R3 (Baja): APP_URL apunta a localhost:8000; ajustar al dominio Herd en Fase 1
  - R4 (Baja): Xdebug activo en local; desactivar en producción (Fase 8)
  - R5 (Baja): Editor Markdown y hosting pendientes de decidir (Fases 4 y 8)
- **Commits:** `docs: add project documentation and phase tracking`
- **PR:** Pendiente push autorizado
- **Merge por propietario:** Pendiente

---

### Fase 1 — Bootstrap de Laravel
- **Estado:** Pendiente
- **Rama:** `feature/fase-01-bootstrap`
- **Criterios de aceptación:**
  - [ ] Laravel 13 instalado
  - [ ] `.env.example` configurado
  - [ ] MySQL local conectado
  - [ ] Pest instalado y funcionando
  - [ ] Livewire 4 instalado sin duplicar Alpine
  - [ ] Tailwind CSS configurado con build exitoso
  - [ ] Pint, `.editorconfig`, `.gitignore` configurados
  - [ ] Prueba smoke en `/` verde
  - [ ] Migraciones base ejecutadas
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 2 — Internacionalización y layout accesible
- **Estado:** Pendiente
- **Rama:** `feature/fase-02-internacionalizacion`
- **Criterios de aceptación:**
  - [ ] Middleware de locale con validación estricta
  - [ ] Rutas `/es` y `/en` funcionando
  - [ ] Layout Blade semántico (`<html lang>`, skip link, header, main, footer)
  - [ ] Selector de idioma funcional
  - [ ] Focus-visible implementado
  - [ ] Diseño responsive
  - [ ] Tests de rutas e idiomas verdes
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 3 — Datos y modelos Eloquent
- **Estado:** Pendiente
- **Rama:** `feature/fase-03-datos-modelos`
- **Criterios de aceptación:**
  - [ ] Migración `users` (con `is_admin`)
  - [ ] Migración `posts` con soft deletes
  - [ ] Migración `post_translations` (UNIQUE post_id+locale, locale+slug)
  - [ ] Migraciones `categories` y `category_translations`
  - [ ] Migraciones `tags`, `tag_translations` y `post_tag`
  - [ ] Factories y seeders locales
  - [ ] Scopes para artículos visibles por idioma
  - [ ] Protección contra N+1
  - [ ] Tests de integridad y colisiones de slug verdes
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 4 — Panel de administración privado
- **Estado:** Pendiente
- **Rama:** `feature/fase-04-panel-privado`
- **Criterios de aceptación:**
  - [ ] Filament verificado (o alternativa propuesta y aprobada)
  - [ ] Autenticación segura sin registro público
  - [ ] Políticas de acceso para admin
  - [ ] CRUD de posts, categorías, etiquetas y traducciones
  - [ ] Editor Markdown con vista previa privada
  - [ ] Sanitización de Markdown → HTML (allowlist, sin XSS)
  - [ ] Tests de permisos, CSRF y validación verdes
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 5 — Páginas públicas del blog
- **Estado:** Pendiente
- **Rama:** `feature/fase-05-paginas-publicas`
- **Criterios de aceptación:**
  - [ ] Portada con artículos recientes
  - [ ] Índice paginado de artículos
  - [ ] Detalle de artículo con Markdown renderizado
  - [ ] Código resaltado con etiquetas semánticas
  - [ ] Botón copiar código con feedback accesible
  - [ ] Filtros por categoría y etiqueta
  - [ ] Búsqueda por título/resumen (locale)
  - [ ] Página 404 accesible
  - [ ] Ningún borrador visible en público
  - [ ] Tests de XSS y visibilidad verdes
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 6 — SEO y rendimiento
- **Estado:** Pendiente
- **Rama:** `feature/fase-06-seo-rendimiento`
- **Criterios de aceptación:**
  - [ ] `<title>` y `<meta description>` por idioma
  - [ ] Canonical correcto en paginación
  - [ ] Open Graph implementado
  - [ ] hreflang solo para traducciones publicadas
  - [ ] Sitemap XML generado
  - [ ] `robots.txt` configurado
  - [ ] Caché con invalidación al publicar
  - [ ] Tests de canonical/hreflang/sitemap verdes
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 7 — Auditoría WCAG 2.2 AA
- **Estado:** Pendiente
- **Rama:** `feature/fase-07-accesibilidad`
- **Criterios de aceptación:**
  - [ ] Playwright + @axe-core/playwright instalados
  - [ ] Pruebas automáticas en todas las páginas clave
  - [ ] Prueba manual: solo teclado
  - [ ] Prueba manual: NVDA/Firefox o Chrome
  - [ ] Prueba manual: zoom 200% y 400%
  - [ ] Prueba manual: reflow 320 CSS px
  - [ ] Prueba manual: reduced motion
  - [ ] Hallazgos registrados con severidad y WCAG
  - [ ] Hallazgos corregidos y pruebas repetidas
  - [ ] Ver `docs/ACCESSIBILITY_CHECKLIST.md`
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 8 — Seguridad y preparación de producción
- **Estado:** Pendiente
- **Rama:** `chore/fase-08-seguridad-produccion`
- **Criterios de aceptación:**
  - [ ] Revisión de dependencias (composer audit, npm audit)
  - [ ] XSS, CSRF, CSP revisados
  - [ ] Rate limiting configurado
  - [ ] `APP_DEBUG=false` en producción
  - [ ] HTTPS forzado
  - [ ] Logs sin datos sensibles
  - [ ] `docs/DEPLOYMENT.md` completo con rollback
  - [ ] No despliegue sin permiso del propietario
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 9 — Contenido y lanzamiento
- **Estado:** Pendiente
- **Rama:** `docs/fase-09-contenido-lanzamiento`
- **Criterios de aceptación:**
  - [ ] Artículo 1 (borrador ES+EN): Formularios Blade accesibles
  - [ ] Artículo 2 (borrador ES+EN): Mensajes de error accesibles
  - [ ] Artículo 3 (borrador ES+EN): Teclado en Livewire
  - [ ] Artículo 4 (borrador ES+EN): Pruebas con axe-core
  - [ ] Revisión técnica y de lenguaje completada
  - [ ] SEO y accesibilidad de artículos verificados
  - [ ] Release v1.0 documentado
  - [ ] Autorización del propietario antes de publicar
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

## Registro de merges en main

| Fecha | Fase | PR | Merged por |
|-------|------|----|------------|
| — | — | — | — |
