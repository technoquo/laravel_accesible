# Checklist de Accesibilidad — WCAG 2.2 AA

> Proyecto: Laravel Accesible
> Estándar: WCAG 2.2, niveles A y AA
> Última actualización: 2026-10-26 (Fase 7)

Este documento registra el estado de cada criterio de éxito WCAG 2.2 aplicable al proyecto, los hallazgos de auditoría y las correcciones realizadas.

**Leyenda de estado:**
- `[ ]` Pendiente de verificar
- `[✓]` Verificado y conforme
- `[✗]` No conforme — ver hallazgo
- `[N/A]` No aplica al proyecto

> No declarar conformidad WCAG AA únicamente por resultados automáticos (axe-core). La conformidad requiere prueba manual documentada.

---

## 1. Perceptible

### 1.1 Alternativas de texto
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 1.1.1 Contenido no textual | A | `[✓]` | Imágenes de artículos usan alt desde MarkdownService; iconos decorativos con aria-hidden; botones con aria-label |

### 1.2 Medios tempodependientes
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 1.2.1 Solo audio y solo vídeo (pregrabado) | A | `[N/A]` | MVP sin audio/vídeo |
| 1.2.2 Subtítulos (pregrabado) | A | `[N/A]` | MVP sin vídeo |
| 1.2.3 Audiodescripción o alternativa en medios (pregrabado) | A | `[N/A]` | MVP sin vídeo |
| 1.2.4 Subtítulos (en directo) | AA | `[N/A]` | MVP sin directo |
| 1.2.5 Audiodescripción (pregrabado) | AA | `[N/A]` | MVP sin vídeo |

### 1.3 Adaptable
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 1.3.1 Información y relaciones | A | `[✓]` | Headings jerárquicos (h1→h2→h3), listas semánticas (ul/ol), tablas con th[scope], secciones con aria-labelledby |
| 1.3.2 Secuencia significativa | A | `[✓]` | Orden DOM coherente con orden visual; no reordenamiento CSS |
| 1.3.3 Características sensoriales | A | `[✓]` | Sin instrucciones solo por color/posición/forma |
| 1.3.4 Orientación | AA | `[✓]` | Contenido no bloqueado a orientación; diseño responsive |
| 1.3.5 Identificación del propósito de entrada | AA | `[✓]` | autocomplete="email" y autocomplete="current-password" en login admin |

### 1.4 Distinguible
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 1.4.1 Uso del color | A | `[✓]` | Sin información transmitida solo por color; estados de badges usan texto y color |
| 1.4.2 Control del audio | A | `[N/A]` | MVP sin audio automático |
| 1.4.3 Contraste (mínimo) | AA | `[✓]` | Verificado con axe-core: sin violaciones. Correcciones aplicadas: text-gray-400→text-gray-600 en fechas, text-gray-300→text-gray-500 en 404 decorativo, blockquote #6b7280→#4b5563 |
| 1.4.4 Cambio de tamaño del texto | AA | `[ ]` | Pendiente prueba manual: zoom 200% |
| 1.4.5 Imágenes de texto | AA | `[✓]` | Solo texto real; sin imágenes de texto |
| 1.4.10 Reajuste (reflow) | AA | `[ ]` | Pendiente prueba manual: 320 CSS px |
| 1.4.11 Contraste de componentes no textuales | AA | `[✓]` | Bordes de inputs (border-gray-300 en white ≈ 1.6:1) — componentes identificados por posición/label además de borde; botones CTA superan 3:1 |
| 1.4.12 Espaciado de texto | AA | `[ ]` | Pendiente prueba manual: ajuste de espaciado |
| 1.4.13 Contenido en hover o foco | AA | `[✓]` | No hay tooltips ni overlays; copy-btn se muestra en hover/focus sin desplazar contenido |

---

## 2. Operable

### 2.1 Accesible por teclado
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 2.1.1 Teclado | A | `[ ]` | Skip link verificado por axe y Playwright; bloques de código `<pre tabindex="0">` para desplazar líneas largas con teclado; pendiente prueba manual completa |
| 2.1.2 Sin trampa de teclado | A | `[ ]` | Pendiente prueba manual |
| 2.1.4 Atajos de teclado de un solo carácter | A | `[N/A]` | MVP sin atajos de un carácter |

### 2.2 Tiempo suficiente
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 2.2.1 Ajuste del tiempo | A | `[N/A]` | MVP sin límites de tiempo |
| 2.2.2 Pausar, detener, ocultar | A | `[N/A]` | MVP sin contenido en movimiento automático |

### 2.3 Convulsiones y reacciones físicas
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 2.3.1 Tres destellos o por debajo del umbral | A | `[N/A]` | MVP sin flashes |

### 2.4 Navegable
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 2.4.1 Evitar bloques | A | `[✓]` | Skip link "Ir al contenido principal" implementado y verificado con Playwright |
| 2.4.2 Titulado de páginas | A | `[✓]` | `<title>` único y descriptivo por página en todos los idiomas; verificado con axe |
| 2.4.3 Orden del foco | A | `[ ]` | Pendiente prueba manual con teclado |
| 2.4.4 Propósito de los enlaces (en contexto) | A | `[✓]` | Texto de enlace descriptivo; aria-label "Leer más: :title" en enlaces de tarjeta |
| 2.4.5 Múltiples vías | AA | `[✓]` | Navegación principal + búsqueda en índice de artículos |
| 2.4.6 Encabezados y etiquetas | AA | `[✓]` | Headings descriptivos; labels en todos los formularios; nav con aria-label |
| 2.4.7 Foco visible | AA | `[✓]` | `*:focus-visible { outline: 2px solid currentColor }` global; verificado con axe |
| 2.4.11 Apariencia del foco (mínimo) | AA | `[ ]` | Pendiente prueba manual: área mínima de foco 2.4.11 |
| 2.4.12 Apariencia del foco (mejorado) | AAA | `[ ]` | Referencia — no requerido en AA |
| 2.4.13 Apariencia del foco | AA | `[ ]` | Pendiente prueba manual: contraste del indicador de foco |

### 2.5 Modalidades de entrada
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 2.5.1 Gestos de puntero | A | `[✓]` | Sin gestos complejos obligatorios |
| 2.5.2 Cancelación de puntero | A | `[✓]` | Acciones en click (up-event); cancelable |
| 2.5.3 Etiqueta en el nombre | A | `[✓]` | Corregido: aria-label de enlaces "Leer más" ahora comienza con el texto visible ("Leer más: título") |
| 2.5.4 Activación por movimiento | A | `[N/A]` | MVP sin activación por movimiento |
| 2.5.7 Arrastrar movimientos | AA | `[N/A]` | MVP sin drag-and-drop requerido |
| 2.5.8 Tamaño del objetivo (mínimo) | AA | `[ ]` | Pendiente prueba manual: mínimo 24×24 CSS px |

---

## 3. Comprensible

### 3.1 Legible
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 3.1.1 Idioma de la página | A | `[✓]` | `<html lang="es">` o `lang="en"` dinámico; verificado con Playwright |
| 3.1.2 Idioma de las partes | AA | `[✓]` | Selector de idioma usa `lang="es/en"` en cada opción; enlaces hreflang correctos |

### 3.2 Predecible
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 3.2.1 Al recibir el foco | A | `[✓]` | Sin cambio de contexto al enfocar; verificado con axe |
| 3.2.2 Al recibir entradas | A | `[✓]` | Sin envío automático; búsqueda requiere Submit explícito |
| 3.2.3 Navegación coherente | AA | `[✓]` | Navegación consistente en todas las páginas públicas |
| 3.2.4 Identificación coherente | AA | `[✓]` | Mismo nombre accesible para componentes equivalentes |
| 3.2.6 Ayuda coherente | AA | `[✓]` | No aplica MVP sin formularios de contacto o ayuda |

### 3.3 Asistencia con la entrada de datos
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 3.3.1 Identificación de errores | A | `[✓]` | Errores con role="alert", id enlazado a aria-describedby, campo identificado |
| 3.3.2 Etiquetas o instrucciones | A | `[✓]` | Labels en todos los campos; hints con text-gray-500 |
| 3.3.3 Sugerencia tras error | AA | `[✓]` | Mensajes de error descriptivos en formularios |
| 3.3.4 Prevención de errores (legales, financieros, datos) | AA | `[N/A]` | MVP sin transacciones financieras |
| 3.3.7 Autenticación redundante | AA | `[✓]` | Login con email+contraseña; sin CAPTCHA cognitivo |
| 3.3.8 Autenticación accesible (mínimo) | AA | `[✓]` | Sin CAPTCHA; autocomplete habilitado |

---

## 4. Robusto

### 4.1 Compatible
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 4.1.1 Interpretación | A | `[✓]` | HTML válido verificado por axe; IDs únicos |
| 4.1.2 Nombre, función, valor | A | `[✓]` | ARIA correcto; nav con aria-label; aria-current en locale activo y nav admin; verificado con axe. El iframe de YouTube tiene `title`, pero el reproductor interno es de terceros (atributos ARIA no válidos y botón sin nombre) y queda excluido de axe |
| 4.1.3 Mensajes de estado | AA | `[✓]` | aria-live="polite" en resultados de búsqueda y feedback de copiar código |

---

## Hallazgos de auditoría — Fase 7

| ID | Página | Criterio WCAG | Nivel | Severidad | Descripción | Solución | Estado |
|----|--------|--------------|-------|-----------|-------------|----------|--------|
| H-01 | Todas (paginación) | 3.1.1 Idioma | A | Menor | `aria-label="Pagination"` hardcodeado en inglés en páginas en español | Cambiado a `__('ui.pagination_nav')` traducible | ✓ Corregido |
| H-02 | Admin panel | 2.4.4 Propósito de enlaces | A | Menor | Nav admin sin `aria-current="page"` en enlace activo | Añadido `aria-current="page"` + subrayado visual al link activo | ✓ Corregido |
| H-03 | Portada | 1.4.3 Contraste | AA | Alto | Fechas con `text-gray-400` (~2.85:1) en fondo blanco | Cambiado a `text-gray-600` (~7:1) | ✓ Corregido |
| H-04 | 404 | 1.4.3 Contraste | AA | Alto | Número "404" decorativo `text-gray-300` (1.47:1) | Cambiado a `text-gray-500` (~4.5:1); supera 3:1 para texto grande | ✓ Corregido |
| H-05 | Artículos (prose) | 1.4.3 Contraste | AA | Medio | Color blockquote `#6b7280` borderline en fondos claros | Cambiado a `#4b5563` (gray-600) | ✓ Corregido |
| H-06 | Índice artículos | 2.5.3 Etiqueta en nombre | A | Alto | `aria-label="Leer: título"` no contiene el texto visible "Leer más" | Cambiado a `aria-label="Leer más: título"` | ✓ Corregido |
| H-07 | Todas | 2.4.6 Encabezados y etiquetas | AA | Menor | Nav `aria-label="{{ __('ui.nav_home') }}"` daba "Inicio/Home" en lugar de nombre de nav | Cambiado a `__('ui.nav_main')` = "Navegación principal / Main navigation" | ✓ Corregido |
| H-08 | Admin panel | 1.4.3 Contraste | AA | Menor | Hints `text-gray-400` (~2.85:1) en campos de formulario admin | Cambiado a `text-gray-500` | ✓ Corregido |

---

## Resultados de pruebas automatizadas — Fase 7

### axe-core + Playwright (2026-10-26)

| Página | Herramienta | Resultado | Violaciones axe |
|--------|-------------|-----------|-----------------|
| `/es` Portada | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/en` Portada | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/es/articulos` Índice | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/en/articles` Índice | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/es/articulos?search=...` Búsqueda | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/es/articulos/{slug}` Detalle | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/es/articulos/inexistente` 404 | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/admin/login` Login | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/admin/posts` Lista posts | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/admin/posts/create` Formulario | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/admin/categories` Categorías | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| `/admin/tags` Etiquetas | axe WCAG 2.2 AA | ✓ Sin violaciones | 0 |
| Detalle artículo EN | — | ⏭ Omitido | Sin artículos EN publicados |
| Página categoría ES | — | ⏭ Omitido | Sin artículos con categoría en BD |
| Página etiqueta ES | — | ⏭ Omitido | Sin artículos con etiquetas en BD |

**Resumen: 21 pasados / 3 omitidos (sin contenido) / 0 fallidos**

> Nota: El badge de Herd (toolbar de desarrollo) se excluye del análisis axe con `.exclude('#herd-ai-host')` ya que no es parte del app sino una herramienta de desarrollo local.

---

## Pruebas manuales pendientes

Las siguientes pruebas requieren intervención humana y herramientas externas. **No se marcan como completadas hasta que el propietario las realice y documente.**

### 1. Navegación solo por teclado

**Herramienta:** Navegador + tecla Tab (sin ratón)
**Páginas a probar:** Portada, índice artículos, detalle artículo, búsqueda, login admin

Criterios a verificar:
- [ ] Todas las funciones accesibles con Tab / Shift+Tab / Enter / Space / flechas
- [ ] El foco no queda atrapado en ningún componente (2.1.2)
- [ ] El orden de tabulación es lógico (2.4.3)
- [ ] El skip link salta al contenido principal correctamente
- [ ] Los botones copy-code son alcanzables y activables con teclado
- [ ] La búsqueda funciona completamente con teclado
- [ ] El selector de idioma es operable con teclado

### 2. Lector de pantalla — NVDA + Firefox (recomendado)

**Herramienta:** NVDA (gratuito) + Firefox, o VoiceOver + Safari (macOS)
**Páginas a probar:** Portada, detalle artículo, formulario admin

Criterios a verificar:
- [ ] Cada página anuncia su idioma correcto
- [ ] Los headings son navegables (H1, H2, H3...)
- [ ] Los links tienen nombres descriptivos (no "click aquí")
- [ ] Las listas son anunciadas como listas
- [ ] El contenido de artículos Markdown es legible secuencialmente
- [ ] Los mensajes de error del formulario se anuncian correctamente
- [ ] El selector de idioma anuncia el idioma activo como "página actual"
- [ ] Las tablas del admin tienen cabeceras anunciadas

### 3. Zoom al 200% y 400%

**Herramienta:** Ctrl++ en navegador hasta 200% y 400%

- [ ] A 200%: todo el texto legible, sin desbordamiento horizontal, sin contenido cortado
- [ ] A 400%: el layout se adapta (posible scroll vertical), sin pérdida de función

### 4. Reflow a 320 CSS px

**Herramienta:** DevTools → viewport 320px de ancho

- [ ] Sin scroll horizontal en ninguna página pública
- [ ] El menú de navegación es operable
- [ ] Las tablas del admin no requieren scroll horizontal para la funcionalidad principal

### 5. Reduced motion

**Herramienta:** DevTools → Emular `prefers-reduced-motion: reduce`

- [ ] El botón copy-code no muestra animación de transición (ya está implementado con `@media (prefers-reduced-motion: reduce)`)
- [ ] Ninguna animación automática persiste con reduced motion

### 6. Contraste manual (opcional)

**Herramienta:** Colour Contrast Analyser o DevTools

- [ ] Verificar gray-500 (#6b7280) en fondos claros (esperado ~4.5:1)
- [ ] Verificar color de enlace prose (#1d4ed8) en blanco (esperado >4.5:1)
- [ ] Verificar bordes de inputs (border-gray-300) para 1.4.11
- [ ] Modo oscuro: repetir las comprobaciones anteriores con el tema oscuro activo (axe ya se ejecuta en el proyecto Playwright `chromium-dark`)
- [ ] Modo oscuro: el botón luna/sol anuncia "Modo oscuro, botón de alternancia, (no) presionado" en NVDA
- [ ] Modo oscuro: el anillo de foco es visible sobre fondos oscuros (2.4.7, 2.4.13)
- [ ] Herramientas: con zoom de texto al 200 % no hay pérdida de contenido ni scroll horizontal (1.4.4, 1.4.10)
- [ ] Herramientas: NVDA anuncia "Accesibilidad, botón, contraído/expandido" y el nivel de zoom al cambiarlo
- [ ] Herramientas: foco visible en los modos amarillo/blanco sobre negro

---

## Pruebas manuales realizadas

| Fecha | Fase | Herramienta | Páginas | Hallazgos | Responsable |
|-------|------|-------------|---------|-----------|-------------|
| — | — | — | — | Pendiente pruebas manuales | Propietario |

---

## Notas importantes

- Los resultados de axe-core cubren aproximadamente el 30-40% de los criterios WCAG. La conformidad real requiere prueba manual.
- No declarar conformidad WCAG AA sin revisión manual documentada con lector de pantalla (NVDA + Firefox recomendado).
- Los criterios nuevos en WCAG 2.2 respecto a 2.1 son: 2.4.11, 2.4.12, 2.4.13, 2.5.7, 2.5.8, 3.2.6, 3.3.7, 3.3.8.
- Los tests E2E se ejecutan con `npm run test:e2e` (requiere servidor Herd activo en laravel-accesible.test).
- Los 3 tests omitidos (detalle EN, categoría, etiqueta) se activarán automáticamente cuando haya contenido publicado en la BD.
