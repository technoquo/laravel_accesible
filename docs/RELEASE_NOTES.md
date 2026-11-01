# Notas de versión — Laravel Accesible

## v1.0 — Lanzamiento

- **Estado:** Preparada — pendiente de autorización del propietario para publicar y desplegar
- **Rama de preparación:** `docs/fase-09-lanzamiento`

### Funcionalidades

- Blog bilingüe (`/es`, `/en`) con selector de idioma hacia la página equivalente y `hreflang` solo para traducciones publicadas.
- Portada, índice paginado, detalle de artículo, categorías, etiquetas, búsqueda y página 404 accesible.
- Páginas "Acerca de" (con foto del autor) y "Contacto" (enlace `mailto:` configurable con `MAIL_CONTACT_ADDRESS`).
- Panel de administración privado: CRUD de artículos, categorías y etiquetas con traducciones ES/EN, editor Markdown con vista previa y sanitización (`league/commonmark`, `html_input: strip`).
- Bloques de código con resaltado, botón de copiar con aviso accesible y desplazamiento por teclado.
- Modo claro/oscuro y panel de accesibilidad (zoom de texto 100–200 % y modos de color de alto contraste).
- Menú responsive con panel lateral por debajo de `lg`.
- SEO: canonical, Open Graph, `sitemap.xml`, `robots.txt` y caché del sitemap con invalidación al publicar.
- Seguridad: cabeceras HTTP (`SecurityHeaders`), HTTPS forzado en producción, rate limiting en el login.

### Contenido de lanzamiento

Cuatro artículos originales en español con traducción revisada al inglés:

| Artículo (ES) | Article (EN) |
|---------------|--------------|
| Formularios Blade accesibles | Accessible Blade Forms |
| Mensajes de error accesibles en Livewire | Accessible Error Messages in Livewire |
| Navegación por teclado en componentes Livewire | Keyboard Navigation in Livewire Components |
| Pruebas de accesibilidad con axe-core y Playwright | Accessibility Testing with axe-core and Playwright |

Revisión técnica y de lenguaje (2026-11-01):

- Se corrigieron ejemplos de código que no funcionaban (propiedad duplicada, `#[Rule]` obsoleto en lugar de `#[Validate]`, paginador guardado como propiedad pública, variable sin definir).
- Se eliminaron afirmaciones sin fuente o incorrectas (porcentajes de uso del teclado y de cobertura de axe, gestión automática del foco con `wire:navigate`, criterios WCAG mal asignados).
- Las etiquetas de axe del artículo 4 coinciden ahora con las de las pruebas del proyecto (`wcag2a`, `wcag2aa`, `wcag21a`, `wcag21aa`, `wcag22aa`).
- Se retiraron las 4 imágenes de picsum.photos: eran fotos aleatorias cuyo texto alternativo no describía su contenido (WCAG 1.1.1). Los artículos no llevan imágenes; no hay licencias de imagen que gestionar.
- Vídeos: los 3 vídeos de W3C WAI se verificaron por oEmbed (ID, título y autor). Se retiró el del artículo 4, que no correspondía al tema.
- Todos los enlaces de referencia responden 200; los de MDN se actualizaron a su URL actual.

### Calidad

- Pest: 153/153 tests en verde (2026-11-01).
- Playwright + axe-core (WCAG 2.2 AA, claro y `chromium-dark`): 80/80 en `tests/e2e/a11y/`, sin violaciones en el detalle de los 4 artículos. En la ejecución completa, 5 tests ajenos a los artículos (menú, login del panel, panel de accesibilidad, portada EN) agotaron el tiempo de 30 s y pasaron al repetirlos: son intermitentes bajo carga y conviene vigilarlos.
- `composer audit` y `npm audit` sin vulnerabilidades en la Fase 8.

### Pendiente antes o después del lanzamiento

- [ ] Autorización del propietario para publicar los artículos y desplegar (ver `docs/DEPLOYMENT.md`).
- [ ] Pruebas manuales WCAG del propietario: teclado, lector de pantalla, zoom 200/400 %, reflow a 320 CSS px, espaciado de texto y movimiento reducido (ver `docs/ACCESSIBILITY_CHECKLIST.md`).
- [ ] CSP completa con nonces para Livewire (trabajo posterior al lanzamiento, ver `docs/DEPLOYMENT.md`).
- [ ] Licencia del proyecto pendiente de definir (README).
