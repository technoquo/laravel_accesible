# Laravel Accesible — Plan maestro para Codex CLI o Claude Code

## Misión
Construir desde cero un blog técnico bilingüe (es/en), orientado a desarrolladores Laravel, para enseñar accesibilidad web con ejemplos reproducibles y referencias WCAG 2.2. El sitio mismo debe aspirar a WCAG 2.2 AA, sujeto a auditoría manual y automática. Español predeterminado.

## Instrucciones obligatorias al agente
1. Trabaja **una fase por vez**. Antes de modificar archivos, inspecciona el repositorio y explica qué harás. Espera confirmación para iniciar cada fase; no continúes automáticamente.
2. Respeta las versiones realmente instaladas y consulta la documentación oficial vigente de Laravel, Livewire, Filament y dependencias. No inventes APIs ni compatibilidades.
3. Usa Laravel 13, PHP >=8.3 (preferentemente 8.5), Livewire 4, Blade, Tailwind, Alpine integrado en Livewire, MySQL 8+ o MariaDB compatible, Pest, Playwright y axe-core. Filament solo si su versión compatible se verifica.
4. Prefiere Blade renderizado en servidor para contenido SEO y Livewire solo donde la interactividad lo justifique. Evita duplicar Alpine.
5. No introduzcas paquetes sin explicar la necesidad y comprobar mantenimiento, licencia y compatibilidad.
6. TDD cuando sea razonable. Después de cada fase: ejecutar pruebas relevantes, Pint, compilación de assets y reportar fallos. No declarar WCAG AA solo por pasar axe.
7. Seguridad: validación, autorización, CSRF, escaping, sanitización del Markdown/HTML, subida segura de archivos, rate limiting, credenciales fuera del repositorio, no registro público.
8. Accesibilidad: HTML semántico, idioma correcto, enlace saltar al contenido, foco visible, navegación por teclado, etiquetas y errores accesibles, contraste, zoom/reflow, reduced motion, textos alternativos, no interacciones solo por color, no ARIA innecesario. Evaluar WCAG 2.2 A/AA.
9. Traducciones: rutas /es y /en; interfaz mediante archivos lang; artículos y SEO traducidos en BD. No asumir que ambas traducciones están publicadas. Evitar 404 silencioso y enlaces hreflang a traducciones inexistentes.
10. Al final de cada fase entrega: archivos cambiados, decisiones, comandos ejecutados, pruebas con resultado, riesgos, rama, estado del Pull Request y siguiente fase. No hacer deploy, push, borrar datos ni migraciones destructivas sin permiso.
11. Crear y mantener `docs/DECISIONS.md`, `docs/PROGRESS.md`, `docs/ACCESSIBILITY_CHECKLIST.md`, `docs/DEPLOYMENT.md` y README.
12. Los textos del sitio y tutoriales serán originales; no generar artículos ficticios como publicados. Seeds solo en local/testing.
13. Git: `main` está protegida. Prohibido hacer commit, push directo, merge o force push en `main`. Cada fase requiere rama independiente y Pull Request; solo el propietario realiza el merge manualmente. Ver protocolo Git más abajo.

## Protocolo Git obligatorio: una rama y un Pull Request por fase

**La rama `main` está protegida mediante Branch Protection Rules.** Estas reglas se aplican tanto a Codex CLI como a Claude Code y prevalecen sobre cualquier instrucción posterior que sugiera fusionar automáticamente.

### Prohibiciones
- Nunca realizar commits directamente en `main`, ni hacer `git push origin main`, `git merge` hacia `main`, `git push --force` o reescribir su historial.
- Nunca cambiar, desactivar o eludir la protección de ramas; no utilizar privilegios de administrador para saltársela.
- Nunca ejecutar el merge de un Pull Request, ni activar auto-merge. **El propietario revisa y fusiona manualmente.**
- No ejecutar `git reset --hard`, `git clean -fd`, rebase destructivo ni descartar cambios existentes sin permiso expreso.
- No hacer push ni crear Pull Request sin autorización previa. Se permite preparar commits locales de la rama de fase una vez autorizada la implementación.

### Nombres de ramas por fase (según la numeración de este plan)
| Fase | Rama |
|---|---|
| 0 — Auditoría | `chore/fase-00-auditoria` (solo si se autorizan cambios documentales) |
| 1 — Bootstrap | `feature/fase-01-bootstrap` |
| 2 — Internacionalización | `feature/fase-02-internacionalizacion` |
| 3 — Datos y modelos | `feature/fase-03-datos-modelos` |
| 4 — Panel privado | `feature/fase-04-panel-privado` |
| 5 — Páginas públicas | `feature/fase-05-paginas-publicas` |
| 6 — SEO y rendimiento | `feature/fase-06-seo-rendimiento` |
| 7 — Accesibilidad | `feature/fase-07-accesibilidad` |
| 8 — Seguridad y producción | `chore/fase-08-seguridad-produccion` |
| 9 — Contenido y lanzamiento | `docs/fase-09-contenido-lanzamiento` |

### Secuencia obligatoria
1. Al iniciar, ejecutar `git status --short --branch`, identificar la rama activa y verificar que no existan cambios sin guardar. Si hay cambios, detenerse y consultar; no sobrescribirlos.
2. **Fase 0:** solo inspeccionar, sin modificar ni crear ramas hasta obtener autorización. Si el repositorio todavía no existe, preparar el plan de inicialización sin asumir que ya existe `main` remoto.
3. Una vez autorizada una fase y confirmado que la anterior fue fusionada por el propietario, actualizar `main` mediante un avance rápido seguro (`git pull --ff-only origin main`) cuando el árbol esté limpio. Si no se puede avanzar limpiamente, detenerse y solicitar instrucciones.
4. Crear una rama nueva desde `main` con el nombre de la tabla. Verificar que no exista ya una rama con ese nombre antes de crearla; si existe, inspeccionarla y pedir instrucciones.
5. Implementar solo la fase autorizada; ejecutar pruebas, Pint, build y comprobaciones de accesibilidad pertinentes. Actualizar documentación y registrar commits descriptivos **solo en la rama de fase**.
6. Mostrar el resumen de cambios y pruebas; solicitar permiso antes de `git push -u origin <rama>` y antes de abrir el Pull Request con base `main`.
7. Presentar el enlace del Pull Request si fue creado, y detenerse. No fusionarlo ni empezar otra fase.
8. Esperar la confirmación explícita del propietario de que el Pull Request fue fusionado manualmente. Solo entonces actualizar `main` y preparar la siguiente rama.

### Informe al finalizar cada fase
Indicar rama activa, commits, archivos modificados, resultados de pruebas, comprobaciones WCAG, incidencias, estado/enlace del Pull Request y confirmación pendiente del merge manual. No declarar una fase integrada en `main` sin verificarlo.

## Producto MVP
- Portada con artículos recientes, categorías y búsqueda.
- Índice paginado y detalle de artículos con tabla de contenidos opcional, bloques de código accesibles y botón copiar accesible.
- Categorías, etiquetas, SEO, sitemap, robots, canonical, hreflang y feed RSS opcional.
- Selector de idioma que conserva la página equivalente cuando existe; fallback informado a índice del otro idioma cuando no existe.
- Panel de administración privado: login, roles mínimos, CRUD de posts, traducciones, categorías, etiquetas, borrador, vista previa autorizada y publicación.
- Editor Markdown con previsualización segura y sintaxis de código resaltada, sin dependencia de JavaScript para leer contenido publicado.
- Página Acerca de, política de privacidad si se recogen datos, 404 accesible.
- Sin comentarios, formulario de contacto, cuentas públicas, analítica invasiva ni newsletter en MVP.

## Modelo de datos propuesto
- `users`: autenticación y atributo `is_admin` o política/rol mínimo.
- `posts`: id, category_id nullable, author_id, status (draft/published), published_at nullable, timestamps, soft deletes opcional.
- `post_translations`: id, post_id, locale (es/en), title, slug, excerpt, markdown_content, meta_title, meta_description, timestamps; UNIQUE(post_id, locale), UNIQUE(locale, slug).
- `categories`: id, stable_key/slug, sort_order, timestamps.
- `category_translations`: category_id, locale, name, slug, description; UNIQUE(category_id, locale), UNIQUE(locale, slug).
- `tags`: id, stable_key/slug, timestamps.
- `tag_translations`: tag_id, locale, name, slug; restricciones equivalentes.
- `post_tag`: post_id, tag_id, UNIQUE(post_id, tag_id).
- Opcional después: `media` con metadatos, texto alternativo por idioma y rutas de almacenamiento seguras.
- Índices para status+published_at, claves foráneas, integridad referencial y consultas eficientes. La visibilidad de un artículo depende de post publicado + traducción existente + fecha válida.

## Diseño de rutas
- `/` redirige a `/es` (o negociación controlada y persistente más adelante).
- `/es`, `/en` portadas.
- `/es/articulos`, `/en/articles` índices.
- `/es/articulos/{slug}`, `/en/articles/{slug}` detalles.
- `/es/categorias/{slug}`, `/en/categories/{slug}` categorías.
- `/es/etiquetas/{slug}`, `/en/tags/{slug}` etiquetas (si se implementan).
- `/es/acerca-de`, `/en/about`.
- `/admin` protegido; nombres de rutas estables, middleware de locale y canonicalización segura.
- Solo `es` y `en` válidos; no construir URLs mediante concatenación sin codificación/validación.

## Fases y criterios de aceptación

### Fase 0 — Auditoría del entorno y planificación
- Comprobar `php -v`, `composer -V`, `node -v`, `npm -v`, `git --version`, extensiones PHP, DB y directorio de trabajo.
- Revisar existencia de repositorio y no sobrescribir nada.
- Consultar https://laravel.com/for/agents y documentación oficial de paquetes.
- Confirmar editor Markdown, dominio pendiente, hosting y repositorio local.
- Entregar matriz de compatibilidad y riesgos. **No instalar todavía**.

### Fase 1 — Bootstrap
- Crear Laravel 13, configurar `.env.example`, MySQL local y Pest.
- Instalar Livewire 4 y Tailwind según versiones soportadas; verificar assets y que Alpine no se duplique.
- Configurar Pint, `.editorconfig`, `.gitignore`, README, documentación de decisiones.
- Prueba de smoke para `/`, migraciones y compilación.
- Commit: `chore: bootstrap laravel accesible`.

### Fase 2 — Internacionalización y layout
- Middleware de locale con validación estricta y traducciones de interfaz.
- Rutas `/es`, `/en`, layout Blade semántico, `<html lang>`, skip link, header, main, footer, navegación, selector de idioma.
- Diseño responsive, focus-visible, modo claro/oscuro solo si accesible y preferencia persistida sin FOUC excesivo.
- Tests de rutas, idiomas, navegación y teclado manual.
- Commit: `feat: add bilingual accessible shell`.

### Fase 3 — Datos y modelos
- Migraciones del modelo propuesto, factories, seeders locales y relaciones Eloquent.
- Scopes para artículos visibles por idioma; slug único por locale; protección frente a consultas N+1.
- Tests de integridad, publicaciones futuras, borradores, traducciones faltantes, colisiones de slug.
- Commit: `feat: add localized content schema`.

### Fase 4 — Panel privado
- Verificar versión compatible de Filament. Si no hay versión compatible, proponer alternativa antes de instalar.
- Autenticación segura sin registro público; políticas para admin; panel CRUD de posts, categorías, etiquetas y traducciones.
- Editor Markdown, vista previa privada, borradores, publicación y validación por idioma.
- Sanitizar Markdown a HTML con esquema allowlist; impedir XSS y enlaces peligrosos; archivos subidos con validación.
- Probar permisos, CSRF, validación, edición y publicación; auditoría accesible del panel y documentar limitaciones de componentes terceros.
- Commit: `feat: add secure editorial admin`.

### Fase 5 — Páginas públicas
- Portada, listado paginado, detalle, categorías, etiquetas, búsqueda en título/resumen por locale.
- Markdown renderizado de forma segura, código resaltado con etiquetas semánticas, copiar código con feedback accesible y fallback manual.
- Estados vacíos, 404, carga y errores comprensibles. Ningún contenido draft visible.
- Tests de endpoints, filtros, paginación, visibilidad, búsqueda y XSS.
- Commit: `feat: publish bilingual article pages`.

### Fase 6 — SEO y rendimiento
- Title/description por idioma, canonical, Open Graph, hreflang recíproco solo para traducciones publicadas, sitemap XML, robots.txt.
- Metadatos correctos en paginación, caché con invalidación al publicar, consultas eficientes, imágenes responsive y lazy loading cuando corresponda.
- Tests de canonical/hreflang/sitemap, tiempos y consultas principales.
- Commit: `feat: add localized SEO and performance`.

### Fase 7 — Auditoría WCAG 2.2 AA
- Añadir Playwright + @axe-core/playwright en entorno de pruebas reproducible.
- Pruebas automáticas en portada, índices, artículos, búsqueda, cambio de idioma, formularios admin y errores.
- Pruebas manuales: solo teclado, NVDA/Firefox o Chrome, zoom 200/400%, reflow 320 CSS px, contrastes, reduced motion, foco tras Livewire, mensajes de estado, formularios, idioma.
- Registrar hallazgos con severidad, WCAG, pasos, solución y evidencia. Corregir problemas y repetir pruebas.
- No afirmar certificación ni conformidad total por resultados automáticos.
- Commit: `test: audit and improve accessibility`.

### Fase 8 — Seguridad y preparación de producción
- Revisión de dependencias, authorization, XSS, CSRF, CSP compatible, cookies, sesiones, rate limiting, backups, logs sin datos sensibles.
- `.env` producción, `APP_DEBUG=false`, HTTPS, workers/cron solo si necesarios, almacenamiento y permisos.
- Comprobar PHP >=8.3 en hosting, extensiones, acceso SSH, Composer, Node/build, webroot `/public` y migraciones reversibles.
- Preparar `docs/DEPLOYMENT.md`, rollback y checklist; **no desplegar sin permiso**.
- Commit: `chore: prepare secure deployment`.

### Fase 9 — Contenido y lanzamiento
- Crear como borradores 4 artículos originales en español y traducciones revisadas en inglés: formularios Blade; mensajes de error; teclado en Livewire; pruebas con axe-core.
- Cada artículo: problema, ejemplo incorrecto, solución, WCAG aplicable, prueba manual/automática, conclusión y enlaces a fuentes oficiales.
- Revisar exactitud técnica, lenguaje, SEO, accesibilidad y licencia de imágenes.
- Validar todo, documentar release v1.0 y solicitar autorización antes de publicar.
- Commit: `docs: prepare bilingual launch content`.

## Mejoras posteriores al MVP

### Mejora — Página Acerca de
- **Rama:** `feature/pagina-acerca-de` (desde `main` actualizado, según el protocolo Git).
- **Rutas:** `/es/acerca-de` (nombre `es.about`) y `/en/about` (nombre `en.about`), dentro del grupo `{locale}` con `SetLocale`, restringidas con `->where('locale', ...)` como las rutas públicas existentes. Se enlazan con `locale_route('about')`.
- **Selector de idioma:** pasar `$alternateUrls` a la vista para que ES ↔ EN apunte a la página equivalente (`/es/acerca-de` ↔ `/en/about`) en vez de a la portada.
- **Navegación:** enlace "Acerca de" / "About" en el menú principal (`<nav aria-label="{{ __('ui.nav_main') }}">` de `layouts/app.blade.php`), con estado activo (`font-semibold underline` y `aria-current="page"`), y enlace en el `<footer>`.
- **Vista:** `resources/views/about.blade.php` que extiende `layouts.app`. El `<main id="main-content">` lo aporta el layout, así que la vista no debe añadir un segundo `<main>`: usa un `<article>` o `<section>` con un único `<h1>` y párrafos `<p>`. Título y meta description traducidos por idioma.
- **Textos:** todos los textos en `lang/es/ui.php` y `lang/en/ui.php` (o en `about.*`), sin texto fijo en Blade. Contenido:

  **ES**
  > **Acerca de**
  >
  > Laravel Accesible nació para compartir las experiencias de personas usuarias, con o sin discapacidad, que se benefician de la accesibilidad. El proyecto se enfoca en Laravel para que las personas desarrolladoras puedan construir aplicaciones con igualdad de acceso y sin barreras.
  >
  > Soy Leonel López, una persona sorda, graduado de la Universidad Latinoamericana de Ciencia y Tecnología (ULACIT) de Costa Rica. He participado en varias actividades sobre accesibilidad web para personas sordas, como WordCamp, el Congreso Accesible América, DeafTech y el Zero Project en Austria. También participé en Laracon EU 2026 en Ámsterdam. Fui emprendedor con Acceso Visual. Soy profesor de lengua de señas y desarrollador Laravel desde hace más de 4 años, y llevo años luchando por el derecho de acceso de las personas sordas.
  >
  > El objetivo de este blog es compartir conocimiento con quienes desarrollan en Laravel y con cualquier persona interesada en la accesibilidad. Es un proyecto voluntario, y espero que compartas nuestra experiencia.

  **EN**
  > **About**
  >
  > Laravel Accesible was created to share the experiences of users, with or without disabilities, who benefit from accessibility. The project focuses on Laravel so that developers can build applications with equal access and without barriers.
  >
  > I am Leonel López, a Deaf person and a graduate of the Latin American University of Science and Technology (ULACIT) in Costa Rica. I have taken part in several events on web accessibility for Deaf people, such as WordCamp, Congreso Accesible América, DeafTech and the Zero Project in Austria. I also attended Laracon EU 2026 in Amsterdam. I was an entrepreneur with Acceso Visual. I am a sign language teacher and have been a Laravel developer for more than 4 years, and I have spent years fighting for Deaf people's right to access.
  >
  > The goal of this blog is to share knowledge with Laravel developers and with anyone interested in accessibility. It is a volunteer project, and I hope you will share our experience.

- **Contenido en otro idioma:** los nombres propios en español dentro del texto inglés (Congreso Accesible América, Acceso Visual) se marcan con `lang="es"` para que los lectores de pantalla los pronuncien bien (WCAG 3.1.2).
- **Accesibilidad y diseño:** contraste mínimo AA (4.5:1 texto normal, 3:1 texto grande) en modo claro, modo oscuro y los modos de color del panel de accesibilidad; ancho de lectura limitado (`max-w-prose`), tipografía relativa que respeta el zoom de texto, reflow sin scroll horizontal a 320 CSS px, foco visible en los nuevos enlaces.
- **Tests Pest** (`tests/Feature/AboutPageTest.php`):
  - `GET /es/acerca-de` responde 200 y muestra el título "Acerca de".
  - `GET /en/about` responde 200 y muestra el título "About".
  - El menú principal y el footer contienen el enlace a la página en cada idioma.
  - `/en/acerca-de` y `/es/about` responden 404.
- **Playwright:** añadir `/es/acerca-de` y `/en/about` a las auditorías axe existentes (claro y `chromium-dark`).
- **SEO:** incluir ambas URLs en `sitemap.xml` con hreflang recíproco.
- Actualizar `docs/PROGRESS.md` con la nueva mejora.
- Commit: `feat: add bilingual about page`.

## Definition of Done global
- Tests PHP verdes, build frontend verde, Pint sin diferencias, pruebas E2E esenciales verdes.
- No exposición de borradores, sin XSS conocido, permisos verificados.
- ES y EN navegables; selector coherente; rutas SEO correctas.
- Sin errores críticos/altos de accesibilidad abiertos, con revisión manual documentada.
- README, decisiones, progreso, checklist y deployment actualizados.
- Sin secretos en Git, sin despliegue no autorizado.

## Prompt inicial para Codex CLI / Claude Code

Actúa como arquitecto senior Laravel, especialista en accesibilidad WCAG 2.2 AA y desarrollador de pruebas. Lee íntegramente `LARAVEL_ACCESIBLE_PLAN.md` y síguelo como especificación del proyecto. Consulta las instrucciones oficiales de Laravel para agentes en https://laravel.com/for/agents y la documentación oficial de las versiones instaladas. Empieza exclusivamente con la Fase 0: inspecciona el entorno y repositorio, valida compatibilidad y propone los comandos exactos. No crees ni modifiques archivos del proyecto hasta que autorice la Fase 1. Al terminar, informa de resultados, riesgos y próximo paso. Nunca avances de fase sin mi confirmación. Respeta las Branch Protection Rules: nunca hagas commits ni merges en main, trabaja en una rama por fase, pide permiso antes del push/PR y espera a que yo fusione manualmente cada PR. Responde en español claro.

## Prompts para continuar
- `Continúa con la Fase 1 del plan. Antes de cambiar archivos, verifica main y crea la rama de fase correspondiente; explica el alcance, implementa, prueba, documenta y detente sin fusionar main.`
- Sustituir el número para las fases siguientes. Al reanudar sesión: `Lee LARAVEL_ACCESIBLE_PLAN.md y docs/PROGRESS.md, verifica Git y retoma solo la fase pendiente autorizada.`

## Referencias oficiales
- https://laravel.com/docs/13.x
- https://laravel.com/for/agents
- https://livewire.laravel.com/docs/4.x
- https://www.w3.org/TR/WCAG22/
- https://www.w3.org/WAI/test-evaluate/
- https://playwright.dev/
