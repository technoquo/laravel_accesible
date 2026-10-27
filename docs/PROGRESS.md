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
| 6 | SEO y rendimiento | `feature/fase-06-seo-rendimiento` | Completada | PR #8 mergeado | Canonical, OG, hreflang, sitemap, robots.txt, caché, 120/120 tests |
| 7 | Auditoría WCAG 2.2 AA | `feature/fase-07-accesibilidad` | Completada | PR #9 mergeado | 8 hallazgos corregidos, 21 tests axe verdes, pruebas manuales responsabilidad del propietario |
| 8 | Seguridad y preparación de producción | `chore/fase-08-seguridad-produccion` | Completada | PR #10 mergeado | SecurityHeaders middleware, HTTPS forzado en prod, .env.example actualizado |
| 9 | Contenido y lanzamiento | `docs/fase-09-contenido-lanzamiento` | En curso | PR #11 mergeado (borradores) | 4 artículos bilingües en borrador con imagen y video; falta revisión, release y autorización |

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
- **Estado:** Completada — PR #9 mergeado
- **Rama:** `feature/fase-07-accesibilidad`
- **Criterios de aceptación:**
  - [✓] Playwright + @axe-core/playwright instalados (npm devDependencies)
  - [✓] Pruebas automáticas en todas las páginas clave (21 tests axe WCAG 2.2 AA — 0 violaciones)
  - [ ] Prueba manual: solo teclado
  - [ ] Prueba manual: NVDA/Firefox o Chrome
  - [ ] Prueba manual: zoom 200% y 400%
  - [ ] Prueba manual: reflow 320 CSS px
  - [ ] Prueba manual: reduced motion
  - [✓] Hallazgos registrados con severidad y WCAG (8 hallazgos en ACCESSIBILITY_CHECKLIST.md)
  - [✓] Hallazgos automatizados corregidos y pruebas repetidas — 0 violaciones
  - [✓] Ver `docs/ACCESSIBILITY_CHECKLIST.md`
- **Archivos creados/modificados:**
  - `playwright.config.js` — configuración Playwright
  - `tests/e2e/a11y/public.spec.js` — tests axe páginas públicas
  - `tests/e2e/a11y/admin.spec.js` — tests axe panel admin
  - `resources/views/layouts/app.blade.php` — nav aria-label corregido
  - `resources/views/layouts/admin.blade.php` — aria-current en nav admin
  - `resources/views/articles/index.blade.php` — paginación + aria-label "Leer más: título"
  - `resources/views/categories/show.blade.php` — paginación localizada
  - `resources/views/tags/show.blade.php` — paginación localizada
  - `resources/views/home.blade.php` — text-gray-400→text-gray-600
  - `resources/views/errors/404.blade.php` — text-gray-300→text-gray-500
  - `resources/views/livewire/admin/post-form.blade.php` — text-gray-400→text-gray-500
  - `resources/css/app.css` — blockquote color #6b7280→#4b5563
  - `lang/es/ui.php` y `lang/en/ui.php` — claves pagination_nav, read_more, read_more_label, nav_main
- **Notas:**
  - axe-core excluye `#herd-ai-host` (badge de Laravel Herd, herramienta de desarrollo)
  - 3 tests omitidos: detalle EN y páginas de categoría/etiqueta por falta de contenido (se activarán en Fase 9)
  - Las pruebas manuales (teclado, NVDA, zoom, reflow, reduced motion) son responsabilidad del propietario; instrucciones detalladas en `ACCESSIBILITY_CHECKLIST.md`
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 8 — Seguridad y preparación de producción
- **Estado:** En curso — implementada, pendiente push y PR
- **Rama:** `chore/fase-08-seguridad-produccion`
- **Criterios de aceptación:**
  - [✓] Revisión de dependencias — `composer audit` y `npm audit`: 0 vulnerabilidades
  - [✓] XSS revisado — Blade `{{ }}` en todo el público; `{!! !!}` solo tras MarkdownService con `html_input: strip`
  - [✓] CSRF activo — middleware `web` de Laravel cubre todos los formularios POST
  - [✓] Headers de seguridad — middleware `SecurityHeaders` (X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy)
  - [✓] Rate limiting en login — 5 intentos/min, clave email|IP
  - [✓] HTTPS forzado en producción — `URL::forceScheme('https')` en AppServiceProvider cuando `APP_ENV=production`
  - [✓] Logs sin datos sensibles — sin llamadas `Log::` con passwords/tokens; `LOG_LEVEL=error` documentado para producción
  - [✓] `APP_DEBUG=false` documentado en `.env.example` con sección de producción
  - [✓] `SESSION_SECURE_COOKIE=true` y `SESSION_ENCRYPT=true` documentados en `.env.example` para producción
  - [✓] `docs/DEPLOYMENT.md` completado con checklist, headers, variables de entorno y rollback
  - [ ] No despliegue sin permiso del propietario
- **Archivos creados/modificados:**
  - `app/Http/Middleware/SecurityHeaders.php` — nuevo middleware de cabeceras HTTP
  - `bootstrap/app.php` — registra `SecurityHeaders` en el stack web
  - `app/Providers/AppServiceProvider.php` — `URL::forceScheme('https')` en producción
  - `.env.example` — secciones de producción comentadas (APP_DEBUG, LOG_LEVEL, SESSION_*)
  - `docs/DEPLOYMENT.md` — completado con variables, headers, checklist y nota sobre CSP
  - `docs/PROGRESS.md` — fase 7 marcada como completada
- **Nota sobre CSP:** CSP completa requiere nonces para Livewire y whitelist de CDNs (highlight.js, YouTube). Documentado en DEPLOYMENT.md como trabajo post-lanzamiento.
- **Commits:** —
- **PR:** —
- **Merge por propietario:** —

---

### Fase 9 — Contenido y lanzamiento
- **Estado:** En curso — borradores mergeados en PR #11
- **Rama:** `docs/fase-09-contenido-lanzamiento`
- **Criterios de aceptación:**
  - [x] Artículo 1 (borrador ES+EN): Formularios Blade accesibles
  - [x] Artículo 2 (borrador ES+EN): Mensajes de error accesibles
  - [x] Artículo 3 (borrador ES+EN): Teclado en Livewire
  - [x] Artículo 4 (borrador ES+EN): Pruebas con axe-core
  - [ ] Revisión técnica y de lenguaje completada
  - [ ] SEO y accesibilidad de artículos verificados
  - [ ] Release v1.0 documentado
  - [ ] Autorización del propietario antes de publicar
- **Commits:** `docs: seed 4 bilingual draft articles with image and video embeds`
- **PR:** #11 (borradores)
- **Merge por propietario:** 2026-10-26 (borradores); lanzamiento pendiente

---

## Mejora — Modo claro/oscuro

- **Rama:** `feature/modo-claro-oscuro`
- **Estado:** Completada — PR #13 mergeado
- **Alcance:**
  - [x] Botón luna/sol en layout público y panel admin (`aria-pressed`, etiqueta traducida)
  - [x] Preferencia en `localStorage`, fallback a `prefers-color-scheme`, sin FOUC (script inline en `<head>`)
  - [x] Paleta invertida en modo oscuro; prose, botón copiar y resaltado de código adaptados
  - [x] Pest: botón y script presentes en ES/EN
  - [x] Playwright: `theme.spec.js` (alternancia, teclado, persistencia, preferencia del sistema) y proyecto `chromium-dark` que repite las auditorías axe
- **Incidencias conocidas (previas, también en `main`):** el detalle de artículo falla axe por el iframe de YouTube (terceros) y `scrollable-region-focusable` en `<pre>`.
- **PR:** #13

---

## Mejora — Herramientas de accesibilidad

- **Rama:** `feature/herramientas-accesibilidad`
- **Estado:** Completada — PR #14 mergeado
- **Alcance:**
  - [x] Panel "Accesibilidad" en layout público y admin (disclosure accesible por teclado)
  - [x] Zoom de texto 100–200 % persistido
  - [x] Modos de color: esquema del sitio, invertir brillo, invertir colores, amarillo sobre negro, tinte azul, blanco y negro, blanco sobre negro
  - [x] Pest: panel traducido y script previo al pintado en ES/EN
  - [x] Playwright: `accessibility-tools.spec.js` (teclado, zoom, persistencia, axe en modos de alto contraste)
- **PR:** #14

---

## Mejora — Página Acerca de

- **Rama:** `feature/pagina-acerca-de`
- **Estado:** Implementada — pendiente de push/PR
- **Alcance:**
  - [x] Rutas `/es/acerca-de` (`es.about`) y `/en/about` (`en.about`), con canonical, hreflang y selector de idioma hacia la página equivalente
  - [x] Enlace en el menú principal (con `aria-current="page"`) y en el footer
  - [x] Vista semántica (`<article>`, un `<h1>`, `<p>`) dentro del `<main>` del layout; textos ES/EN en `lang/*/ui.php`, nombres en español marcados con `lang="es"` en la versión inglesa
  - [x] Ambas URLs en `sitemap.xml`
  - [x] Pest: `AboutPageTest.php` (200 y título, 404 con locale cruzado, enlaces en nav y footer, estado actual, selector de idioma, sitemap)
  - [x] Playwright: axe en ES/EN (claro y `chromium-dark`) y sin scroll horizontal a 320 CSS px
- **PR:** pendiente

---

## Mejora — Menú responsive

- **Rama:** `feature/menu-responsive` (creada sobre `feature/pagina-acerca-de`, porque el menú incluye el enlace "Acerca de")
- **Estado:** Implementada — pendiente de push/PR, después del merge de `feature/pagina-acerca-de`
- **Alcance:**
  - [x] Por debajo de `lg` (móvil y tableta): botón ☰/✕ con `aria-expanded` y panel lateral que se desliza desde la derecha, bajo el header
  - [x] Botones de tema y de accesibilidad siempre visibles; solo un panel abierto a la vez
  - [x] Cierre con el botón, con Escape (devuelve el foco) y al pulsar fuera; sin animación con `prefers-reduced-motion`
  - [x] Etiquetas "Menú" y "Accesibilidad" solo para lectores de pantalla por debajo de `sm`
  - [x] Encabezado de la portada más grande y centrado
  - [x] Playwright: `navigation.spec.js` (320/375/768/1280 px, teclado, sin solapes, panel dentro de la pantalla, texto al 200 %, axe con el menú abierto)
- **PR:** pendiente

---

## Registro de merges en main

| Fecha | Fase | PR | Merged por |
|-------|------|----|------------|
| 2026-10-26 | 7 — Auditoría WCAG 2.2 AA | PR #9 | Propietario |
| 2026-10-26 | 8 — Seguridad y preparación de producción | PR #10 | Propietario |
| 2026-10-26 | 9 — Contenido y lanzamiento (borradores) | PR #11 | Propietario |
| 2026-10-27 | Mantenimiento — skill `laravel-best-practices` | PR #12 | Propietario |
| 2026-10-27 | Mejora — Modo claro/oscuro | PR #13 | Propietario |
| 2026-10-27 | Mejora — Herramientas de accesibilidad | PR #14 | Propietario |
