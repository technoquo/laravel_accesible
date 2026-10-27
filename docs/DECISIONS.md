# Decisiones de arquitectura y tecnología

> Proyecto: Laravel Accesible
> Última actualización: 2026-10-26

Este documento registra las decisiones técnicas relevantes, el contexto que las motivó y las alternativas descartadas. Se actualiza al final de cada fase.

---

## Plantilla de decisión

```
### DEC-XXX — Título breve
- **Fecha:** YYYY-MM-DD
- **Fase:** N
- **Estado:** Propuesta | Aceptada | Rechazada | Reemplazada por DEC-YYY
- **Contexto:** Por qué se tomó esta decisión.
- **Decisión:** Qué se decidió exactamente.
- **Alternativas descartadas:** Qué más se evaluó y por qué no se eligió.
- **Consecuencias:** Implicaciones positivas y negativas.
```

---

## Decisiones registradas

### DEC-001 — Stack tecnológico principal
- **Fecha:** 2026-10-26
- **Fase:** 0 (planificación)
- **Estado:** Aceptada
- **Contexto:** Se necesita un stack estable, mantenible y con soporte activo que permita construir un blog técnico accesible y bilingüe.
- **Decisión:** Laravel 13, PHP >=8.3 (preferentemente 8.5), Livewire 4, Blade, Tailwind CSS, Alpine.js (integrado vía Livewire), MySQL 8+, Pest para tests.
- **Alternativas descartadas:**
  - Inertia.js/Vue: aumenta complejidad para contenido SEO-first; Blade renderizado en servidor es suficiente.
  - Next.js/Nuxt: stack diferente al del dominio del proyecto (Laravel).
  - Vue/React standalone: innecesario para un blog; Livewire cubre la interactividad requerida.
- **Consecuencias:** Alpine.js no debe duplicarse (Livewire lo incluye); cuidar versiones compatibles entre Livewire 4 y Tailwind.

### DEC-002 — Arquitectura de traducciones en base de datos
- **Fecha:** 2026-10-26
- **Fase:** 0 (planificación)
- **Estado:** Aceptada
- **Contexto:** El blog es bilingüe (es/en). Los artículos, categorías y etiquetas tienen títulos, slugs y metadatos distintos por idioma.
- **Decisión:** Tablas de traducción separadas (`post_translations`, `category_translations`, `tag_translations`) con restricciones UNIQUE(entity_id, locale) y UNIQUE(locale, slug).
- **Alternativas descartadas:**
  - Columnas JSON por idioma en la misma tabla: dificulta índices, validación y consultas eficientes.
  - Paquete `spatie/laravel-translatable`: añade dependencia; el modelo propietario es más explícito y controlable para este caso.
- **Consecuencias:** Visibilidad de artículo = post publicado + traducción existente + fecha válida; no asumir que ambas traducciones existen.

### DEC-003 — Rutas prefijadas por locale
- **Fecha:** 2026-10-26
- **Fase:** 0 (planificación)
- **Estado:** Aceptada
- **Contexto:** SEO requiere URLs canónicas por idioma; hreflang necesita rutas recíprocas explícitas.
- **Decisión:** Prefijo de ruta `/es` y `/en`; solo estos dos valores son válidos; `/` redirige a `/es`; middleware de locale con validación estricta.
- **Alternativas descartadas:**
  - Subdominio por idioma (es.site.com): mayor complejidad de hosting y certificados SSL.
  - Negociación por `Accept-Language` sin prefijo en URL: dificulta canonicalización, caché y hreflang.
- **Consecuencias:** Todos los helpers de generación de URL deben incluir el locale; nunca construir URLs por concatenación sin validación/codificación.

### DEC-004 — Panel de administración (Filament vs. alternativa)
- **Fecha:** 2026-10-26
- **Fase:** 4 (confirmada)
- **Estado:** Aceptada
- **Contexto:** Filament v4 requiere Livewire 3; el proyecto usa Livewire 4.4. Instalar Filament exigiría un downgrade de Livewire, rompiendo el stack acordado en DEC-001 y DEC-006.
- **Decisión:** Panel artesanal con Blade + Livewire 4 (componentes class-based). Sin dependencias adicionales.
- **Alternativas descartadas:**
  - Filament v4: incompatible con Livewire 4 (requiere Livewire 3).
  - Laravel Nova: pago; no justificado para el MVP.
- **Consecuencias:** Control total de accesibilidad en el panel; sin limitaciones de terceros. Mayor código a mantener, compensado por la simplicidad del dominio (editorial CRUD).

### DEC-005 — Renderizado de Markdown
- **Fecha:** 2026-10-26
- **Fase:** 4 (confirmada)
- **Estado:** Aceptada
- **Contexto:** El contenido de artículos se almacena en Markdown (`markdown_content`) y debe renderizarse a HTML de forma segura, sin XSS.
- **Decisión:** `league/commonmark` v2 con `GithubFlavoredMarkdownExtension`; opciones `html_input: strip` y `allow_unsafe_links: false`. Sin `spatie/laravel-markdown` (capa innecesaria). `MarkdownService` registrado como singleton.
- **Alternativas descartadas:**
  - `Parsedown`: menor mantenimiento activo.
  - `spatie/laravel-markdown`: wrapper sobre CommonMark; añade configuración sin beneficio real.
  - Renderizado en cliente (JS): el contenido publicado debe ser legible sin JavaScript (SEO + accesibilidad).
- **Consecuencias:** Toda la sanitización ocurre en `MarkdownService::toHtml()`; el HTML resultante es seguro para `{!! !!}` solo en el panel privado. En páginas públicas (Fase 5), usar el mismo servicio.

### DEC-006 — Livewire v4.4.8 con Alpine auto-inyectado
- **Fecha:** 2026-10-26
- **Fase:** 1 (bootstrap)
- **Estado:** Aceptada
- **Contexto:** El plan requiere Livewire 4 integrado con Alpine sin duplicación. Livewire 4 ofrece dos modos: auto-inyección (por defecto) y bundling manual en `app.js`.
- **Decisión:** Usar la auto-inyección predeterminada. No importar Alpine por separado en `app.js`.
- **Alternativas descartadas:** Bundling manual — innecesario para el MVP; añade complejidad sin beneficio actual.
- **Consecuencias:** Si en Fases posteriores se necesitan plugins Alpine personalizados, se deberá migrar al modo bundling manual importando desde `vendor/livewire/livewire/dist/livewire.esm`.

### DEC-007 — APP_LOCALE=es como locale predeterminado desde el bootstrap
- **Fecha:** 2026-10-26
- **Fase:** 1 (bootstrap)
- **Estado:** Aceptada
- **Contexto:** El sitio es español de forma predeterminada; el inglés es idioma secundario.
- **Decisión:** `APP_LOCALE=es`, `APP_FALLBACK_LOCALE=en`, `APP_FAKER_LOCALE=es_ES` en `.env` y `.env.example`.
- **Alternativas descartadas:** `APP_LOCALE=en` — contradice la especificación del plan maestro.
- **Consecuencias:** Los archivos de traducción de interfaz deben existir en `lang/es/` como locale primario.

### DEC-008 — Rutas localizadas con segmentos distintos por idioma
- **Fecha:** 2026-10-26
- **Fase:** 5 (páginas públicas)
- **Estado:** Aceptada
- **Contexto:** El plan maestro especifica URLs distintas por idioma (`/es/articulos` vs. `/en/articles`). Laravel no tiene soporte nativo para segmentos de ruta localizados sin paquetes externos.
- **Decisión:** Rutas explícitas dentro del grupo `{locale}` con restricción `.where('locale', 'es|en')`. Nombres prefijados por locale (`es.articles.index`, `en.articles.index`). Helper `locale_route()` en `app/helpers.php` para generación de URLs.
- **Alternativas descartadas:**
  - `mcamara/laravel-localization`: añade dependencia; el proyecto tiene una lógica sencilla que no justifica un paquete.
  - Segmentos iguales para ambos locales (solo slug diferente): más simple pero no cumple las URLs del plan.
- **Consecuencias:** Los métodos `show(string $locale, string $slug)` deben declarar `$locale` primero; Laravel inyecta parámetros de ruta posicionalmente en métodos de controlador.

### DEC-009 — Resaltado de código vía highlight.js CDN
- **Fecha:** 2026-10-26
- **Fase:** 5 (páginas públicas)
- **Estado:** Aceptada
- **Contexto:** Los artículos contienen bloques de código que deben resaltarse semánticamente. El contenido debe ser legible sin JS.
- **Decisión:** highlight.js v11 cargado desde cdnjs.cloudflare.com solo en la vista de detalle de artículo (`@push('scripts')`). Sin paquete PHP adicional.
- **Alternativas descartadas:**
  - `spatie/commonmark-shiki-highlighter`: PHP server-side, pero requiere Shiki y añade complejidad de build.
  - Prism.js: alternativa válida; highlight.js tiene mejor cobertura de lenguajes y auto-detección.
- **Consecuencias:** El resaltado es una mejora progresiva; sin JS el código es legible con estilos base en `app.css`. El botón copiar también depende de JS (clipboard API) con misma consideración.

### DEC-010 — Modo claro/oscuro por inversión de paleta
- **Fecha:** 2026-10-27
- **Rama:** `feature/modo-claro-oscuro`
- **Estado:** Aceptada
- **Contexto:** Se pide un selector claro/oscuro (iconos luna/sol) en todo el sitio, público y panel. El plan exige que sea accesible, con preferencia persistida y sin FOUC excesivo.
- **Decisión:**
  - Clase `dark` en `<html>` (`@custom-variant dark` de Tailwind v4). Un script inline en `<head>` (`<x-theme-init />`) la aplica antes del primer pintado: preferencia guardada en `localStorage` o, si no hay, `prefers-color-scheme`.
  - En modo oscuro se redefinen las variables de paleta (`--color-white`, `--color-gray-*`, `red`, `green`, `blue`, `yellow`) invirtiendo la escala, así las utilidades existentes (`bg-white`, `text-gray-600`…) se adaptan sin añadir `dark:` a cada clase. Los tonos 500/600 se aclaran para mantener contraste AA.
  - Botón `<x-theme-toggle />` con etiqueta fija ("Modo oscuro"/"Dark mode") y `aria-pressed`; iconos SVG `aria-hidden`. Oculto sin JS.
  - Los colores de highlight.js pasan a `app.css` (paletas GitHub Primer claro/oscuro vía variables `--hl-*`); se elimina la hoja CSS del CDN.
- **Alternativas descartadas:**
  - Añadir `dark:` a ~230 clases: más propenso a olvidos y a regresiones de contraste.
  - Alpine para el botón: innecesario para 40 líneas de JS sin estado reactivo.
- **Consecuencias:** Colores nuevos deben usar la paleta del tema (o variables) para heredar el modo oscuro; un color literal (hex) no se invierte. Sin JS el sitio se muestra en claro.

---

## Pendiente de decisión

- Estrategia de caché (Laravel Cache con tags vs. sin tags según driver disponible).
- Motor de búsqueda (MySQL FULLTEXT vs. Laravel Scout + driver local).
- Almacenamiento de imágenes (local vs. S3-compatible).
- Dominio, hosting y proveedor de deploy (Laravel Cloud u otro).
