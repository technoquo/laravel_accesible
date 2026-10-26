# Checklist de Accesibilidad — WCAG 2.2 AA

> Proyecto: Laravel Accesible
> Estándar: WCAG 2.2, niveles A y AA
> Última actualización: 2026-10-26

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
| 1.1.1 Contenido no textual | A | `[ ]` | Imágenes de artículos, iconos SVG, botones icono |

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
| 1.3.1 Información y relaciones | A | `[ ]` | Headings, listas, tablas semánticas |
| 1.3.2 Secuencia significativa | A | `[ ]` | Orden lógico en DOM |
| 1.3.3 Características sensoriales | A | `[ ]` | Sin instrucciones solo por color/posición |
| 1.3.4 Orientación | AA | `[ ]` | Contenido no bloqueado a orientación |
| 1.3.5 Identificación del propósito de entrada | AA | `[ ]` | autocomplete en formularios admin |

### 1.4 Distinguible
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 1.4.1 Uso del color | A | `[ ]` | Sin información solo por color |
| 1.4.2 Control del audio | A | `[N/A]` | MVP sin audio automático |
| 1.4.3 Contraste (mínimo) | AA | `[ ]` | Texto normal 4.5:1; grande 3:1 |
| 1.4.4 Cambio de tamaño del texto | AA | `[ ]` | Hasta 200% sin pérdida de contenido |
| 1.4.5 Imágenes de texto | AA | `[ ]` | Preferir texto real sobre imagen de texto |
| 1.4.10 Reajuste (reflow) | AA | `[ ]` | Sin scroll horizontal a 320 CSS px |
| 1.4.11 Contraste de componentes no textuales | AA | `[ ]` | Bordes de inputs, iconos funcionales |
| 1.4.12 Espaciado de texto | AA | `[ ]` | Sin pérdida al ajustar espaciado |
| 1.4.13 Contenido en hover o foco | AA | `[ ]` | Tooltips descartables, persistentes, hoverable |

---

## 2. Operable

### 2.1 Accesible por teclado
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 2.1.1 Teclado | A | `[ ]` | Toda funcionalidad accesible por teclado |
| 2.1.2 Sin trampa de teclado | A | `[ ]` | Foco no queda atrapado en componentes |
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
| 2.4.1 Evitar bloques | A | `[ ]` | Skip link implementado |
| 2.4.2 Titulado de páginas | A | `[ ]` | `<title>` único y descriptivo por página |
| 2.4.3 Orden del foco | A | `[ ]` | Orden lógico de tabulación |
| 2.4.4 Propósito de los enlaces (en contexto) | A | `[ ]` | Texto de enlace descriptivo |
| 2.4.5 Múltiples vías | AA | `[ ]` | Navegación + búsqueda disponibles |
| 2.4.6 Encabezados y etiquetas | AA | `[ ]` | Headings descriptivos; labels en formularios |
| 2.4.7 Foco visible | AA | `[ ]` | Indicador de foco visible en todos los elementos |
| 2.4.11 Apariencia del foco (mínimo) | AA | `[ ]` | WCAG 2.2 nuevo: área mínima de foco |
| 2.4.12 Apariencia del foco (mejorado) | AAA | `[ ]` | Referencia — no requerido en AA |
| 2.4.13 Apariencia del foco | AA | `[ ]` | WCAG 2.2: contraste del indicador de foco |

### 2.5 Modalidades de entrada
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 2.5.1 Gestos de puntero | A | `[ ]` | Sin gestos complejos obligatorios |
| 2.5.2 Cancelación de puntero | A | `[ ]` | Acciones en up-event; cancelable |
| 2.5.3 Etiqueta en el nombre | A | `[ ]` | Nombre accesible contiene texto visible |
| 2.5.4 Activación por movimiento | A | `[N/A]` | MVP sin activación por movimiento |
| 2.5.7 Arrastrar movimientos | AA | `[N/A]` | MVP sin drag-and-drop requerido |
| 2.5.8 Tamaño del objetivo (mínimo) | AA | `[ ]` | WCAG 2.2 nuevo: mínimo 24×24 CSS px |

---

## 3. Comprensible

### 3.1 Legible
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 3.1.1 Idioma de la página | A | `[ ]` | `<html lang="es">` o `lang="en"` correcto |
| 3.1.2 Idioma de las partes | AA | `[ ]` | Fragmentos en otro idioma marcados |

### 3.2 Predecible
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 3.2.1 Al recibir el foco | A | `[ ]` | Sin cambio de contexto al enfocar |
| 3.2.2 Al recibir entradas | A | `[ ]` | Sin envío automático de formularios |
| 3.2.3 Navegación coherente | AA | `[ ]` | Navegación consistente entre páginas |
| 3.2.4 Identificación coherente | AA | `[ ]` | Componentes iguales con mismo nombre accesible |
| 3.2.6 Ayuda coherente | AA | `[ ]` | WCAG 2.2 nuevo: mecanismos de ayuda consistentes |

### 3.3 Asistencia con la entrada de datos
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 3.3.1 Identificación de errores | A | `[ ]` | Errores descritos en texto; campo identificado |
| 3.3.2 Etiquetas o instrucciones | A | `[ ]` | Labels y hints en formularios |
| 3.3.3 Sugerencia tras error | AA | `[ ]` | Sugerencia de corrección cuando sea posible |
| 3.3.4 Prevención de errores (legales, financieros, datos) | AA | `[N/A]` | MVP sin transacciones financieras |
| 3.3.7 Autenticación redundante | AA | `[ ]` | WCAG 2.2 nuevo: login sin resolver puzzle cognitivo |
| 3.3.8 Autenticación accesible (mínimo) | AA | `[ ]` | WCAG 2.2 nuevo: alternativa al CAPTCHA cognitivo |

---

## 4. Robusto

### 4.1 Compatible
| Criterio | Nivel | Estado | Notas |
|----------|-------|--------|-------|
| 4.1.1 Interpretación | A | `[ ]` | HTML válido; IDs únicos |
| 4.1.2 Nombre, función, valor | A | `[ ]` | ARIA correcto; componentes con nombre/rol/valor |
| 4.1.3 Mensajes de estado | AA | `[ ]` | Errores Livewire anunciados sin cambio de foco |

---

## Hallazgos de auditoría

| ID | Página | Criterio WCAG | Nivel | Severidad | Descripción | Pasos para reproducir | Solución | Estado | Evidencia |
|----|--------|--------------|-------|-----------|-------------|----------------------|----------|--------|-----------|
| — | — | — | — | — | Pendiente Fase 7 | — | — | — | — |

---

## Pruebas manuales realizadas

| Fecha | Fase | Herramienta | Páginas | Hallazgos | Responsable |
|-------|------|-------------|---------|-----------|-------------|
| — | — | — | — | — | — |

---

## Notas importantes

- Los resultados de axe-core cubren aproximadamente el 30-40% de los criterios WCAG. La conformidad real requiere prueba manual.
- No declarar conformidad WCAG AA sin revisión manual documentada con lector de pantalla (NVDA + Firefox recomendado).
- Los criterios nuevos en WCAG 2.2 respecto a 2.1 son: 2.4.11, 2.4.12, 2.4.13, 2.5.7, 2.5.8, 3.2.6, 3.3.7, 3.3.8.
