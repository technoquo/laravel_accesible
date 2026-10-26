# Progreso del proyecto — Laravel Accesible

> Fecha de inicio: 2026-10-26
> Última actualización: 2026-10-26

## Estado general

| Fase | Nombre | Rama | Estado | PR | Notas |
|------|--------|------|--------|----|-------|
| 0 | Auditoría del entorno y planificación | `chore/fase-00-auditoria` | Completada | PR #1 mergeado | PHP 8.5.12, Laravel 13.38, Tailwind v4 |
| 1 | Bootstrap de Laravel | `feature/fase-01-bootstrap` | Completada | PR #2 mergeado | Livewire v4.4.8, build verde, tests verdes |
| 2 | Internacionalización y layout accesible | `feature/fase-02-internacionalizacion` | Completada | PR #3 mergeado | Middleware locale, rutas /es /en, layout semántico |
| 3 | Datos y modelos Eloquent | `feature/fase-03-datos-modelos` | Completada | PR #4 mergeado | Migraciones, modelos, factories, seeders, 27/27 tests |
| 4 | Panel de administración privado | `feature/fase-04-panel-privado` | Completada | PR #5 mergeado | Auth, CRUD, Markdown, 37/37 tests |
| 5 | Páginas públicas del blog | `feature/fase-05-paginas-publicas` | Completada | PR #6 mergeado | Portada, índice, detalle, categorías, etiquetas, búsqueda, 404, 89/89 tests |
| 6 | SEO y rendimiento | `feature/fase-06-seo-rendimiento` | En curso | — | Canonical, OG, hreflang, sitemap, robots.txt, caché, 120/120 tests |
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
- **Estado:** En curso — implementada, pendiente push y PR
- **Rama:** `feature/fase-01-bootstrap`
- **Criterios de aceptación:**
  - [✓] Laravel 13.38.0 instalado (commit inicial)
  - [✓] `.env.example` configurado (MySQL, APP_LOCALE=es, APP_URL=laravel-accesible.test)
  - [✓] MySQL local conectado (laravel_accesible, migraciones confirmadas)
  - [✓] Pest 5.3.3 instalado y funcionando (2/2 tests verdes)
  - [✓] Livewire 4.4.8 instalado sin duplicar Alpine (auto-inyección)
  - [✓] Tailwind CSS v4 configurado con build exitoso (38.78 kB CSS)
  - [✓] Pint 1.32.1, `.editorconfig`, `.gitignore` configurados
  - [✓] Prueba smoke en `/` verde
  - [✓] Migraciones base ejecutadas (users/password_reset_tokens/sessions, cache, jobs)
- **Notas:**
  - Livewire v4.4.8 usa auto-inyección de Alpine; no importar Alpine por separado
  - `.env` corregido: espacios en variables DB_ eliminados, APP_LOCALE=es
  - `phpunit.xml` ya usa SQLite en memoria para tests (aislamiento correcto)
- **Commits:** `chore: bootstrap laravel accesible`
- **PR:** Pendiente push autorizado
- **Merge por propietario:** Pendiente

---

### Fase 2 — Internacionalización y layout accesible
- **Estado:** En curso — implementada, pendiente push y PR
- **Rama:** `feature/fase-02-internacionalizacion`
- **Criterios de aceptación:**
  - [✓] Middleware `SetLocale` con validación estricta (solo `es`/`en`, abort 404 si inválido)
  - [✓] Rutas `/es` y `/en` funcionando; `/` redirige a `/es`
  - [✓] Layout Blade semántico (`<html lang>`, skip link, `<header>`, `<main id="main-content">`, `<footer>`)
  - [✓] Selector de idioma funcional (`aria-current="page"` en locale activo, `hreflang` en alternativas)
  - [✓] Focus-visible implementado (`*:focus-visible { outline: 2px solid currentColor }` en `@layer base`)
  - [✓] Diseño responsive (Tailwind v4, max-w-5xl, px-4 sm:px-6 lg:px-8)
  - [✓] Tests de rutas e idiomas verdes (11/11 — `LocaleTest.php`)
- **Notas:**
  - `URL::defaults(['locale' => $locale])` en middleware para generación automática de rutas con locale
  - `tabindex="-1"` en `<main>` permite recibir foco desde el skip link sin mostrar ring
  - Locale activo en selector → `<span aria-current="page">`, no `<a>` (evita enlace a la página actual)
  - Archivos de traducción: `lang/es/ui.php`, `lang/en/ui.php`
- **Commits:** `feat: add bilingual accessible shell`
- **PR:** Pendiente push autorizado
- **Merge por propietario:** Pendiente

---

### Fase 3 — Datos y modelos Eloquent
- **Estado:** En curso — implementada, pendiente push y PR
- **Rama:** `feature/fase-03-datos-modelos`
- **Criterios de aceptación:**
  - [✓] Migración `users` (columna `is_admin` boolean)
  - [✓] Migración `posts` con soft deletes e índice `status+published_at`
  - [✓] Migración `post_translations` (UNIQUE post_id+locale, locale+slug)
  - [✓] Migraciones `categories` y `category_translations`
  - [✓] Migraciones `tags`, `tag_translations` y `post_tag`
  - [✓] Factories con estados (`published`, `scheduledFuture`, `admin`, `forLocale`)
  - [✓] Seeders locales (AdminUser, Category, Tag, Post)
  - [✓] Scope `publishedForLocale(string $locale)` en `Post`
  - [✓] `Model::preventLazyLoading(!app()->isProduction())` en AppServiceProvider
  - [✓] Tests de integridad, scopes, soft deletes y colisiones de slug verdes (16 tests)
- **Notas:**
  - `PostStatus` enum backed string (Draft/Published)
  - `post_tag` usa clave primaria compuesta, sin `id` autoincremental
  - `author_id` usa `restrictOnDelete` para evitar borrado accidental de autores con posts
  - `category_id` nullable con `nullOnDelete`
  - Índice `(locale, post_id)` en `post_translations` para búsquedas por idioma
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 4 — Panel de administración privado
- **Estado:** En curso — implementada, pendiente push y PR
- **Rama:** `feature/fase-04-panel-privado`
- **Criterios de aceptación:**
  - [✓] Filament verificado (incompatible con Livewire 4); alternativa propuesta y aprobada: panel artesanal
  - [✓] Autenticación segura sin registro público; rate limiting en login (5 intentos/min)
  - [✓] Middleware `EnsureUserIsAdmin` (abort 403 si no admin); `redirectGuestsTo` → `/admin/login`
  - [✓] CRUD de posts con traducciones ES/EN (tabs por idioma), categoría, etiquetas, estado
  - [✓] CRUD inline de categorías con traducciones ES/EN
  - [✓] CRUD inline de etiquetas con traducciones ES/EN
  - [✓] Editor Markdown (`<textarea>`) con vista previa privada (toggle)
  - [✓] Sanitización Markdown → HTML (`league/commonmark` v2.10; `html_input: strip`, `allow_unsafe_links: false`)
  - [✓] Tests de permisos, CRUD, validación, XSS y sanitización verdes (37/37)
- **Notas:**
  - Filament v4 requiere Livewire 3; usar con Livewire 4 requeriría downgrade — descartado (DEC-004)
  - Layout admin separado (`layouts/admin.blade.php`) sin selector de idioma
  - Slug auto-generado desde título si el campo está vacío; preservado si ya fue editado
  - `MarkdownService` registrado como singleton en AppServiceProvider
  - `Post.$fillable` añadido para reforzar protección de asignación masiva
- **Commits:**
  - `feat: add secure editorial admin panel`
  - `fix: use slot instead of yield in admin layout` (Livewire 4 pasa contenido vía `{{ $slot }}`, no `@yield`)
- **PR:** [#5 — feat: fase 4 — panel de administración privado](https://github.com/technoquo/laravel_accesible/pull/5)
- **Merge por propietario:** Pendiente

---

### Fase 5 — Páginas públicas del blog
- **Estado:** En curso — implementada, pendiente push y PR
- **Rama:** `feature/fase-05-paginas-publicas`
- **Criterios de aceptación:**
  - [✓] Portada con artículos recientes (HomeController + home.blade.php)
  - [✓] Índice paginado de artículos (PostController@index, /es/articulos, /en/articles)
  - [✓] Detalle de artículo con Markdown renderizado (PostController@show, MarkdownService)
  - [✓] Código resaltado con etiquetas semánticas (highlight.js CDN + `<pre><code>` semántico)
  - [✓] Botón copiar código con feedback accesible (JS + aria-live region, reduced-motion)
  - [✓] Filtros por categoría y etiqueta (CategoryController, TagController)
  - [✓] Búsqueda por título/resumen (locale) (GET form, LIKE query parameterizada)
  - [✓] Página 404 accesible (resources/views/errors/404.blade.php)
  - [✓] Ningún borrador visible en público (abort_if + publishedForLocale scope)
  - [✓] Tests de XSS y visibilidad verdes (25 nuevos tests, 89/89 total)
- **Notas:**
  - Rutas con segmentos localizados: `/es/articulos`, `/en/articles`, `/es/categorias`, etc.
  - `locale_route()` helper en `app/helpers.php` para generación de URLs locale-aware
  - Parámetros de ruta inyectados posicionalmente en Laravel: `show(string $locale, string $slug)` necesario
  - Estilos prose en `app.css` para el cuerpo de artículos sin dependencia de `@tailwindcss/typography`
  - `LocaleTest.php` actualizado con `RefreshDatabase` al consultar ahora la BD desde `HomeController`
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 6 — SEO y rendimiento
- **Estado:** En curso — implementada, pendiente push y PR
- **Rama:** `feature/fase-06-seo-rendimiento`
- **Criterios de aceptación:**
  - [✓] `<title>` y `<meta description>` por idioma (en layout + vistas)
  - [✓] Canonical correcto en paginación (page 1 sin param, page N con `?page=N`)
  - [✓] Open Graph implementado (`og:type`, `og:title`, `og:description`, `og:url`, `og:locale`, `og:site_name`, `article:published_time`)
  - [✓] hreflang solo para traducciones publicadas (`<link rel="alternate">` + `x-default`)
  - [✓] Sitemap XML generado (`/sitemap.xml`, caché 1h, incluye home/índices/artículos/categorías/etiquetas)
  - [✓] `robots.txt` configurado (`/robots.txt` dinámico con URL del sitemap)
  - [✓] Caché con invalidación al publicar (`PostObserver` limpia `sitemap.xml` en save/delete/restore)
  - [✓] Tests de canonical/hreflang/sitemap verdes (23/23 nuevos tests, 120/120 total)
- **Notas:**
  - `CategoryController` y `TagController` corregidos: ahora cargan traducciones para ambos locales (antes solo el locale activo hacía que `$alternateUrls` siempre cayera al fallback)
  - hreflang en `<link>` tags solo para contenido real; el `<a hreflang>` del selector de idioma es independiente
  - Sitemap excluye borradores y artículos futuros via `Post::published()` scope
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
