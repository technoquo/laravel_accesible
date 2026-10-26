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
- **Fase:** 0 (planificación)
- **Estado:** Propuesta — pendiente verificación de versión compatible en Fase 4
- **Contexto:** Se necesita un CRUD editorial privado. Filament es la opción preferida del ecosistema Laravel, pero su compatibilidad con Laravel 13 debe confirmarse.
- **Decisión provisional:** Usar Filament si la versión compatible está disponible; de lo contrario, proponer alternativa antes de instalar.
- **Alternativas a evaluar si Filament no es compatible:**
  - Panel Blade + Livewire artesanal: más trabajo, control total de accesibilidad.
  - Laravel Nova: pago; posible opción.
- **Consecuencias:** Documentar limitaciones de accesibilidad de componentes de terceros si se usa Filament.

### DEC-005 — Renderizado de Markdown
- **Fecha:** 2026-10-26
- **Fase:** 0 (planificación)
- **Estado:** Propuesta — confirmar paquete en Fase 4/5
- **Contexto:** El contenido de artículos se almacena en Markdown y debe renderizarse a HTML de forma segura, con resaltado de código semántico y sin XSS.
- **Decisión provisional:** Usar `league/commonmark` con configuración de allowlist HTML; `spatie/laravel-markdown` o similar para integración; resaltado con `highlight.php` o equivalente sin JS obligatorio.
- **Alternativas descartadas:**
  - `Parsedown`: menor mantenimiento activo.
  - Renderizado en cliente (JS): el contenido publicado debe ser legible sin JavaScript (SEO + accesibilidad).
- **Consecuencias:** Sanitizar siempre antes de persistir HTML; nunca confiar en el input del editor.

---

## Pendiente de decisión

- Editor Markdown en el panel admin (TipTap, CodeMirror, textarea simple).
- Estrategia de caché (Laravel Cache con tags vs. sin tags según driver disponible).
- Motor de búsqueda (MySQL FULLTEXT vs. Laravel Scout + driver local).
- Almacenamiento de imágenes (local vs. S3-compatible).
- Dominio, hosting y proveedor de deploy (Laravel Cloud u otro).
