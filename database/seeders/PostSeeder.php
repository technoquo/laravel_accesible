<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Tag;
use App\Models\User;
use App\PostStatus;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_admin', true)->firstOrFail();

        foreach ($this->articles() as $article) {
            $this->upsertArticle($admin, $article);
        }
    }

    /** @param array<string, mixed> $article */
    private function upsertArticle(User $admin, array $article): void
    {
        $category = Category::where('stable_key', $article['category'])->first();
        $tagIds = Tag::whereHas(
            'translations',
            fn ($q) => $q->whereIn('slug', $article['tags'])
        )->pluck('id');

        $existing = PostTranslation::where('locale', 'es')
            ->where('slug', $article['es']['slug'])
            ->first();

        $post = $existing
            ? $existing->post
            : Post::create([
                'author_id' => $admin->id,
                'status' => PostStatus::Draft,
                'published_at' => null,
                'category_id' => $category?->id,
            ]);

        $post->update(['category_id' => $category?->id]);

        foreach (['es', 'en'] as $locale) {
            PostTranslation::updateOrCreate(
                ['post_id' => $post->id, 'locale' => $locale],
                [
                    'title' => $article[$locale]['title'],
                    'slug' => $article[$locale]['slug'],
                    'excerpt' => $article[$locale]['excerpt'],
                    'markdown_content' => $article[$locale]['content'],
                ]
            );
        }

        $post->tags()->sync($tagIds);
    }

    /** @return array<int, array<string, mixed>> */
    private function articles(): array
    {
        return [
            // ── Artículo 1: Formularios Blade accesibles ────────────────────
            [
                'category' => 'formularios',
                'tags' => ['wcag', 'aria', 'blade'],
                'es' => [
                    'title' => 'Formularios Blade accesibles',
                    'slug' => 'formularios-blade-accesibles',
                    'excerpt' => 'Aprende a construir formularios en Blade con etiquetas correctas, mensajes de error vinculados y atributos ARIA que funcionan con lectores de pantalla.',
                    'content' => <<<'MD'
## Por qué los formularios accesibles importan

Los formularios son el punto de entrada más frecuente entre el usuario y la aplicación. Un campo sin etiqueta, un error sin anuncio o un botón sin nombre accesible puede bloquear completamente a personas que usan lectores de pantalla o navegan solo con teclado.

![Persona trabajando en una laptop con código visible en la pantalla](https://picsum.photos/seed/forms-a11y/800/450 "Formularios accesibles en aplicaciones Laravel")

## El problema: placeholder como etiqueta

Un error muy común en Blade es confiar en el atributo `placeholder` como única referencia visual del campo:

```html
<!-- ❌ Incorrecto: placeholder no reemplaza a <label> -->
<input type="email" placeholder="tu@email.com" name="email">
```

El `placeholder` desaparece al escribir, tiene bajo contraste por defecto y no es anunciado de forma consistente por todos los lectores de pantalla. Incumple el criterio **WCAG 2.2 — 1.3.1 Info and Relationships (Nivel A)**.

## La solución: `<label>` vinculado y `aria-describedby`

```blade
{{-- ✅ Correcto --}}
<div>
    <label for="email">
        Correo electrónico
        <span aria-hidden="true" class="text-red-500">*</span>
    </label>

    <input
        type="email"
        id="email"
        name="email"
        autocomplete="email"
        required
        aria-describedby="email-hint{{ $errors->has('email') ? ' email-error' : '' }}"
        aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
        value="{{ old('email') }}"
    >

    <p id="email-hint" class="text-sm text-gray-600">
        Solo usaremos este correo para la confirmación.
    </p>

    @error('email')
        <p id="email-error" role="alert" class="text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>
```

Los puntos clave:
- `for` en `<label>` vincula la etiqueta al campo por `id`.
- `aria-describedby` encadena el `id` de la pista y del error cuando existe.
- `aria-invalid="true"` avisa al lector de pantalla que el campo contiene un error.
- `role="alert"` en el párrafo de error fuerza el anuncio inmediato sin necesitar foco.

## Criterios WCAG 2.2 aplicables

| Criterio | Nivel | Qué exige |
|----------|-------|-----------|
| 1.3.1 Info and Relationships | A | La relación etiqueta-campo debe ser determinable por programa |
| 3.3.1 Error Identification | A | El error debe describirse en texto |
| 3.3.2 Labels or Instructions | A | Los campos con entrada requerida deben tener etiqueta |
| 4.1.3 Status Messages | AA | Los mensajes de estado deben poder ser anunciados sin recibir foco |

## Video: introducción a la accesibilidad web

![Introducción a la accesibilidad web y los estándares W3C](https://www.youtube.com/watch?v=20SHvU2PKsM)

## Prueba manual recomendada

1. Navega el formulario solo con `Tab` — cada campo debe recibir foco y su etiqueta debe estar visible en pantalla.
2. Activa NVDA + Firefox o VoiceOver + Safari — al enfocar el campo vacío el lector debe leer "Correo electrónico, edición, requerido".
3. Envía con el campo vacío — el mensaje de error debe anunciarse automáticamente sin recargar la página.
4. Comprueba contraste de la etiqueta y del error con la herramienta de contraste de DevTools (mínimo 4.5:1).

## Conclusión

Un `<label for>` bien vinculado, un `aria-describedby` que encadena pistas y errores, y un `aria-invalid` dinámico son los tres pilares de los formularios Blade accesibles. No requieren paquetes externos ni JavaScript adicional.

**Referencias:** [W3C WAI — Tutorial de formularios](https://www.w3.org/WAI/tutorials/forms/) · [MDN: `<label>`](https://developer.mozilla.org/es/docs/Web/HTML/Element/label) · [WCAG 2.2](https://www.w3.org/TR/WCAG22/)
MD,
                ],
                'en' => [
                    'title' => 'Accessible Blade Forms',
                    'slug' => 'accessible-blade-forms',
                    'excerpt' => 'Learn to build Blade forms with correct labels, linked error messages, and ARIA attributes that work with screen readers.',
                    'content' => <<<'MD'
## Why Accessible Forms Matter

Forms are the most frequent entry point between a user and an application. A field without a label, an error with no announcement, or a button with no accessible name can completely block users who rely on screen readers or keyboard-only navigation.

![Person working on a laptop with code visible on screen](https://picsum.photos/seed/forms-a11y/800/450 "Accessible forms in Laravel applications")

## The Problem: Placeholder as a Label

A very common mistake in Blade is relying on the `placeholder` attribute as the only visual reference for a field:

```html
<!-- ❌ Incorrect: placeholder does not replace <label> -->
<input type="email" placeholder="you@email.com" name="email">
```

The `placeholder` disappears on input, has low contrast by default, and is not consistently announced by all screen readers. This fails **WCAG 2.2 — 1.3.1 Info and Relationships (Level A)**.

## The Solution: Linked `<label>` and `aria-describedby`

```blade
{{-- ✅ Correct --}}
<div>
    <label for="email">
        Email address
        <span aria-hidden="true" class="text-red-500">*</span>
    </label>

    <input
        type="email"
        id="email"
        name="email"
        autocomplete="email"
        required
        aria-describedby="email-hint{{ $errors->has('email') ? ' email-error' : '' }}"
        aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
        value="{{ old('email') }}"
    >

    <p id="email-hint" class="text-sm text-gray-600">
        We will only use this email for the confirmation.
    </p>

    @error('email')
        <p id="email-error" role="alert" class="text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>
```

Key points:
- `for` on `<label>` links it to the field by `id`.
- `aria-describedby` chains the hint `id` and the error `id` when it exists.
- `aria-invalid="true"` tells the screen reader the field contains an error.
- `role="alert"` on the error paragraph forces an immediate announcement without requiring focus.

## Applicable WCAG 2.2 Criteria

| Criterion | Level | What it requires |
|-----------|-------|-----------------|
| 1.3.1 Info and Relationships | A | Label-field relationship must be programmatically determinable |
| 3.3.1 Error Identification | A | The error must be described in text |
| 3.3.2 Labels or Instructions | A | Fields with required input must have a label |
| 4.1.3 Status Messages | AA | Status messages must be announceable without receiving focus |

## Video: Introduction to Web Accessibility

![Introduction to Web Accessibility and W3C Standards](https://www.youtube.com/watch?v=20SHvU2PKsM)

## Recommended Manual Test

1. Navigate the form using only `Tab` — every field must receive focus and its label must be visible on screen.
2. Enable NVDA + Firefox or VoiceOver + Safari — when focusing an empty field, the reader should announce "Email address, edit, required".
3. Submit with an empty field — the error message must be announced automatically without reloading the page.
4. Check the contrast of the label and error with the DevTools contrast tool (minimum 4.5:1).

## Conclusion

A properly linked `<label for>`, an `aria-describedby` that chains hints and errors, and a dynamic `aria-invalid` are the three pillars of accessible Blade forms. They require no external packages or additional JavaScript.

**References:** [W3C WAI — Forms Tutorial](https://www.w3.org/WAI/tutorials/forms/) · [MDN: `<label>`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/label) · [WCAG 2.2](https://www.w3.org/TR/WCAG22/)
MD,
                ],
            ],

            // ── Artículo 2: Mensajes de error accesibles ────────────────────
            [
                'category' => 'formularios',
                'tags' => ['wcag', 'aria', 'livewire'],
                'es' => [
                    'title' => 'Mensajes de error accesibles en Livewire',
                    'slug' => 'mensajes-de-error-accesibles-livewire',
                    'excerpt' => 'Técnicas para comunicar errores de validación en componentes Livewire usando ARIA live regions, role="alert" y gestión del foco.',
                    'content' => <<<'MD'
## El problema de los errores silenciosos

Cuando Livewire actualiza el DOM después de una validación fallida, los lectores de pantalla no detectan el cambio automáticamente a menos que el desarrollador lo indique de forma explícita. El resultado es un formulario que parece roto: el usuario ve errores en pantalla pero el lector de pantalla no anuncia nada.

![Pantalla de ordenador mostrando un formulario con mensajes de error resaltados](https://picsum.photos/seed/errors-a11y/800/450 "Mensajes de error accesibles en componentes Livewire")

## Cómo funcionan las ARIA live regions

Las live regions son áreas del DOM que el navegador vigila. Cuando su contenido cambia, el lector de pantalla lo anuncia sin que el usuario necesite enfocar el elemento.

```html
<!-- aria-live="polite": espera a que el usuario termine de leer antes de anunciar -->
<div aria-live="polite" aria-atomic="true">
    Mensaje no urgente
</div>

<!-- role="alert" equivale a aria-live="assertive": interrumpe inmediatamente -->
<div role="alert">
    Error crítico
</div>
```

## Patrón recomendado en un componente Livewire

```blade
{{-- resources/views/livewire/contact-form.blade.php --}}
<form wire:submit="submit">
    {{-- Región de estado global --}}
    <div
        role="status"
        aria-live="polite"
        aria-atomic="true"
        class="sr-only"
    >
        @if ($successMessage)
            {{ $successMessage }}
        @endif
    </div>

    <div>
        <label for="name">Nombre completo</label>
        <input
            type="text"
            id="name"
            wire:model.blur="name"
            aria-describedby="{{ $errors->has('name') ? 'name-error' : '' }}"
            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
        >
        @error('name')
            <p id="name-error" role="alert" class="text-red-600 text-sm">
                {{ $message }}
            </p>
        @enderror
    </div>

    <button type="submit">Enviar</button>
</form>
```

```php
// app/Livewire/ContactForm.php
class ContactForm extends Component
{
    public string $name = '';
    public string $successMessage = '';

    #[Rule(['required', 'string', 'min:2', 'max:100'])]
    public string $name = '';

    public function submit(): void
    {
        $this->validate();
        // ... lógica de negocio
        $this->successMessage = 'Formulario enviado correctamente.';
        $this->reset('name');
    }
}
```

Los puntos clave:
- `role="alert"` en cada error individual hace el anuncio inmediato en cuanto Livewire re-renderiza.
- Un `role="status"` global con `aria-live="polite"` anuncia el éxito sin interrumpir al usuario.
- `wire:model.blur` valida al perder el foco, evitando anuncios mientras el usuario escribe.
- `aria-invalid` y `aria-describedby` se actualizan reactivamente en cada re-render.

## Criterios WCAG 2.2 aplicables

| Criterio | Nivel | Qué exige |
|----------|-------|-----------|
| 3.3.1 Error Identification | A | Describir el error en texto |
| 3.3.3 Error Suggestion | AA | Sugerir cómo corregirlo cuando sea posible |
| 4.1.3 Status Messages | AA | Mensajes de estado programáticamente determinables |

## Video: Notificaciones y retroalimentación en interfaces web

![W3C WAI Perspectives: Notifications and Feedback](https://www.youtube.com/watch?v=E1fEv4Vpexg)

## Prueba manual recomendada

1. Con NVDA + Firefox: completa y envía el formulario con datos inválidos. El lector debe anunciar cada error sin que el usuario mueva el foco.
2. Verifica que al corregir un error y salir del campo, el anuncio de error desaparece.
3. Tras el envío exitoso, comprueba que el mensaje de confirmación se anuncia automáticamente.

## Conclusión

`role="alert"` en errores individuales y `role="status"` en confirmaciones globales son el dúo mínimo para que Livewire comunique el estado del formulario a todos los usuarios. El coste de implementación es una clase CSS y dos atributos ARIA.

**Referencias:** [WAI-ARIA — Live Regions](https://www.w3.org/TR/wai-aria-1.2/#live_region_roles) · [WCAG 2.2 — 3.3.1](https://www.w3.org/TR/WCAG22/#error-identification) · [Livewire Docs](https://livewire.laravel.com/docs)
MD,
                ],
                'en' => [
                    'title' => 'Accessible Error Messages in Livewire',
                    'slug' => 'accessible-error-messages-livewire',
                    'excerpt' => 'Techniques for communicating validation errors in Livewire components using ARIA live regions, role="alert", and focus management.',
                    'content' => <<<'MD'
## The Problem of Silent Errors

When Livewire updates the DOM after a failed validation, screen readers do not automatically detect the change unless the developer explicitly signals it. The result is a form that seems broken: the user sees errors on screen but the screen reader announces nothing.

![Computer screen showing a form with highlighted error messages](https://picsum.photos/seed/errors-a11y/800/450 "Accessible error messages in Livewire components")

## How ARIA Live Regions Work

Live regions are areas of the DOM that the browser monitors. When their content changes, the screen reader announces it without the user needing to focus the element.

```html
<!-- aria-live="polite": waits until the user finishes reading before announcing -->
<div aria-live="polite" aria-atomic="true">
    Non-urgent message
</div>

<!-- role="alert" equals aria-live="assertive": interrupts immediately -->
<div role="alert">
    Critical error
</div>
```

## Recommended Pattern in a Livewire Component

```blade
{{-- resources/views/livewire/contact-form.blade.php --}}
<form wire:submit="submit">
    {{-- Global status region --}}
    <div
        role="status"
        aria-live="polite"
        aria-atomic="true"
        class="sr-only"
    >
        @if ($successMessage)
            {{ $successMessage }}
        @endif
    </div>

    <div>
        <label for="name">Full name</label>
        <input
            type="text"
            id="name"
            wire:model.blur="name"
            aria-describedby="{{ $errors->has('name') ? 'name-error' : '' }}"
            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
        >
        @error('name')
            <p id="name-error" role="alert" class="text-red-600 text-sm">
                {{ $message }}
            </p>
        @enderror
    </div>

    <button type="submit">Submit</button>
</form>
```

Key points:
- `role="alert"` on each individual error triggers an immediate announcement when Livewire re-renders.
- A global `role="status"` with `aria-live="polite"` announces success without interrupting the user.
- `wire:model.blur` validates on blur, preventing announcements while the user is typing.
- `aria-invalid` and `aria-describedby` update reactively on every re-render.

## Applicable WCAG 2.2 Criteria

| Criterion | Level | What it requires |
|-----------|-------|-----------------|
| 3.3.1 Error Identification | A | Describe the error in text |
| 3.3.3 Error Suggestion | AA | Suggest how to fix it when possible |
| 4.1.3 Status Messages | AA | Status messages must be programmatically determinable |

## Video: Notifications and Feedback in Web Interfaces

![W3C WAI Perspectives: Notifications and Feedback](https://www.youtube.com/watch?v=E1fEv4Vpexg)

## Recommended Manual Test

1. With NVDA + Firefox: complete and submit the form with invalid data. The reader must announce each error without the user moving focus.
2. Verify that when correcting an error and leaving the field, the error announcement disappears.
3. After a successful submission, check that the confirmation message is automatically announced.

## Conclusion

`role="alert"` on individual errors and `role="status"` on global confirmations are the minimum duo for Livewire to communicate form state to all users. The implementation cost is one CSS class and two ARIA attributes.

**References:** [WAI-ARIA — Live Regions](https://www.w3.org/TR/wai-aria-1.2/#live_region_roles) · [WCAG 2.2 — 3.3.1](https://www.w3.org/TR/WCAG22/#error-identification) · [Livewire Docs](https://livewire.laravel.com/docs)
MD,
                ],
            ],

            // ── Artículo 3: Teclado en Livewire ────────────────────────────
            [
                'category' => 'teclado',
                'tags' => ['wcag', 'livewire'],
                'es' => [
                    'title' => 'Navegación por teclado en componentes Livewire',
                    'slug' => 'navegacion-teclado-livewire',
                    'excerpt' => 'Cómo gestionar el foco después de actualizaciones de Livewire, evitar trampas de foco y garantizar la navegación completa por teclado.',
                    'content' => <<<'MD'
## Por qué el teclado es fundamental

Aproximadamente el 7 % de los usuarios con discapacidad motora navegan la web exclusivamente con teclado. Además, todos los usuarios de lectores de pantalla dependen de él. Un componente Livewire que mueve el foco al lugar incorrecto tras una actualización puede desorientar completamente al usuario.

![Teclado mecánico iluminado sobre un escritorio oscuro](https://picsum.photos/seed/keyboard-a11y/800/450 "Navegación por teclado en interfaces Livewire")

## El problema: foco perdido tras re-render

Cuando Livewire reemplaza un fragmento del DOM, el navegador pierde el elemento que tenía foco si ese elemento fue destruido o recreado. El usuario queda en un estado indeterminado.

```blade
{{-- ❌ Problemático: el botón se destruye y recrea en cada render --}}
<div>
    @if ($loading)
        <span>Cargando...</span>
    @else
        <button wire:click="loadMore">Ver más</button>
    @endif
</div>
```

## Solución 1: `wire:key` para preservar identidad de elementos

```blade
{{-- ✅ wire:key le indica a Livewire que es el mismo elemento --}}
<button wire:key="load-more-btn" wire:click="loadMore">
    Ver más
</button>
```

Con `wire:key`, Livewire actualiza el elemento en lugar de destruirlo y recrearlo, preservando el foco.

## Solución 2: gestión explícita del foco con `$this->js()`

Cuando es necesario mover el foco intencionalmente (por ejemplo, al abrir un modal o cargar nuevos resultados), usa `$this->js()` desde el componente PHP:

```php
public function loadMore(): void
{
    $this->posts = Post::published()->paginate(10);

    // Mover el foco al primer nuevo resultado
    $this->js("
        const firstNew = document.querySelector('[data-new-result]');
        firstNew?.focus();
    ");
}
```

```blade
@foreach ($posts->slice($previousCount) as $post)
    <article data-new-result tabindex="-1">
        <h3>{{ $post->title }}</h3>
    </article>
@endforeach
```

`tabindex="-1"` permite que el elemento reciba foco por programa sin añadirlo al orden de tabulación natural.

## Solución 3: `wire:navigate` y scroll suave

Si usas `wire:navigate` para navegación SPA, Livewire 4 gestiona el foco automáticamente al principio de la nueva página. Asegúrate de que `<main id="main-content">` exista para que el foco aterrice en el lugar correcto.

```blade
{{-- En el layout --}}
<main id="main-content" tabindex="-1">
    {{ $slot }}
</main>
```

## Criterios WCAG 2.2 aplicables

| Criterio | Nivel | Qué exige |
|----------|-------|-----------|
| 2.1.1 Keyboard | A | Toda funcionalidad debe ser operable por teclado |
| 2.4.3 Focus Order | A | El orden de foco debe ser lógico y predecible |
| 2.4.7 Focus Visible | AA | El indicador de foco debe ser visible |
| 3.2.2 On Input | A | Cambiar un componente no debe provocar cambios de contexto inesperados |

## Video: compatibilidad con teclado

![W3C WAI Perspectives: Keyboard Compatibility](https://www.youtube.com/watch?v=93UgG72os8M)

## Prueba manual recomendada

1. Abre el componente y navega solo con `Tab`, `Shift+Tab`, `Enter` y `Espacio`.
2. Activa una acción Livewire (clic en botón, búsqueda, paginación) — verifica que el foco permanece en una posición lógica.
3. Abre y cierra cualquier modal o panel desplegable — el foco debe volver al elemento que lo abrió.
4. Zoom al 200 % — los indicadores de foco deben ser visibles y no estar cortados.

## Conclusión

`wire:key` evita la pérdida de foco por recreación innecesaria de elementos. `$this->js()` permite moverlo intencionalmente cuando el flujo lo requiere. Con estas dos herramientas y un `tabindex="-1"` bien colocado, los componentes Livewire son plenamente navegables por teclado.

**Referencias:** [Livewire 4 — JavaScript](https://livewire.laravel.com/docs/javascript) · [WCAG 2.2 — 2.1.1](https://www.w3.org/TR/WCAG22/#keyboard) · [WAI — Managing Focus](https://www.w3.org/WAI/WCAG22/Techniques/client-side-script/SCR26)
MD,
                ],
                'en' => [
                    'title' => 'Keyboard Navigation in Livewire Components',
                    'slug' => 'keyboard-navigation-livewire',
                    'excerpt' => 'How to manage focus after Livewire updates, avoid focus traps, and ensure complete keyboard navigation.',
                    'content' => <<<'MD'
## Why the Keyboard is Fundamental

Approximately 7% of users with motor disabilities navigate the web exclusively by keyboard. Additionally, all screen reader users depend on it. A Livewire component that moves focus to the wrong place after an update can completely disorient the user.

![Illuminated mechanical keyboard on a dark desk](https://picsum.photos/seed/keyboard-a11y/800/450 "Keyboard navigation in Livewire interfaces")

## The Problem: Focus Lost After Re-render

When Livewire replaces a DOM fragment, the browser loses the element that had focus if that element was destroyed or recreated. The user is left in an indeterminate state.

```blade
{{-- ❌ Problematic: the button is destroyed and recreated on every render --}}
<div>
    @if ($loading)
        <span>Loading...</span>
    @else
        <button wire:click="loadMore">Load more</button>
    @endif
</div>
```

## Solution 1: `wire:key` to Preserve Element Identity

```blade
{{-- ✅ wire:key tells Livewire it's the same element --}}
<button wire:key="load-more-btn" wire:click="loadMore">
    Load more
</button>
```

With `wire:key`, Livewire updates the element instead of destroying and recreating it, preserving focus.

## Solution 2: Explicit Focus Management with `$this->js()`

When it is necessary to intentionally move focus (for example, when opening a modal or loading new results), use `$this->js()` from the PHP component:

```php
public function loadMore(): void
{
    $this->posts = Post::published()->paginate(10);

    // Move focus to the first new result
    $this->js("
        const firstNew = document.querySelector('[data-new-result]');
        firstNew?.focus();
    ");
}
```

```blade
@foreach ($posts->slice($previousCount) as $post)
    <article data-new-result tabindex="-1">
        <h3>{{ $post->title }}</h3>
    </article>
@endforeach
```

`tabindex="-1"` allows the element to receive focus programmatically without adding it to the natural tab order.

## Solution 3: `wire:navigate` and Smooth Scroll

If you use `wire:navigate` for SPA navigation, Livewire 4 automatically manages focus at the top of the new page. Make sure `<main id="main-content">` exists so focus lands in the right place.

```blade
{{-- In the layout --}}
<main id="main-content" tabindex="-1">
    {{ $slot }}
</main>
```

## Applicable WCAG 2.2 Criteria

| Criterion | Level | What it requires |
|-----------|-------|-----------------|
| 2.1.1 Keyboard | A | All functionality must be operable by keyboard |
| 2.4.3 Focus Order | A | Focus order must be logical and predictable |
| 2.4.7 Focus Visible | AA | Focus indicator must be visible |
| 3.2.2 On Input | A | Changing a component must not cause unexpected context changes |

## Video: Keyboard Compatibility

![W3C WAI Perspectives: Keyboard Compatibility](https://www.youtube.com/watch?v=93UgG72os8M)

## Recommended Manual Test

1. Open the component and navigate using only `Tab`, `Shift+Tab`, `Enter`, and `Space`.
2. Trigger a Livewire action (button click, search, pagination) — verify that focus stays in a logical position.
3. Open and close any modal or collapsible panel — focus must return to the element that opened it.
4. Zoom to 200% — focus indicators must be visible and not clipped.

## Conclusion

`wire:key` prevents focus loss from unnecessary element recreation. `$this->js()` allows intentional focus movement when the flow requires it. With these two tools and a well-placed `tabindex="-1"`, Livewire components are fully navigable by keyboard.

**References:** [Livewire 4 — JavaScript](https://livewire.laravel.com/docs/javascript) · [WCAG 2.2 — 2.1.1](https://www.w3.org/TR/WCAG22/#keyboard) · [WAI — Managing Focus](https://www.w3.org/WAI/WCAG22/Techniques/client-side-script/SCR26)
MD,
                ],
            ],

            // ── Artículo 4: Pruebas con axe-core ───────────────────────────
            [
                'category' => 'pruebas',
                'tags' => ['wcag', 'axe-core'],
                'es' => [
                    'title' => 'Pruebas de accesibilidad con axe-core y Playwright',
                    'slug' => 'pruebas-accesibilidad-axe-core-playwright',
                    'excerpt' => 'Configura @axe-core/playwright en tu suite de pruebas Laravel para detectar automáticamente violaciones WCAG 2.2 en cada despliegue.',
                    'content' => <<<'MD'
## Por qué necesitas pruebas automáticas de accesibilidad

Las auditorías manuales son imprescindibles pero lentas y difícilmente escalables. axe-core puede detectar automáticamente entre el 30 % y el 40 % de los problemas de accesibilidad —los más mecánicos y repetibles— dejándote tiempo para las comprobaciones que requieren criterio humano.

![Pantalla con código de pruebas y resultados de accesibilidad en terminal](https://picsum.photos/seed/testing-a11y/800/450 "Pruebas automatizadas de accesibilidad con axe-core")

## Instalación

```bash
npm install --save-dev @axe-core/playwright
```

Verifica que tu `playwright.config.js` apunte a la URL correcta del entorno de pruebas:

```js
// playwright.config.js
import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    use: {
        baseURL: 'https://laravel-accesible.test',
        ignoreHTTPSErrors: true,
    },
});
```

## Anatomía de un test axe con Playwright

```js
// tests/e2e/a11y/home.spec.js
import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test.describe('Portada — WCAG 2.2 AA', () => {
    test('no debe tener violaciones de accesibilidad', async ({ page }) => {
        await page.goto('/es');

        const results = await new AxeBuilder({ page })
            .withTags(['wcag2a', 'wcag2aa', 'wcag22aa'])
            .exclude('#herd-ai-host')   // excluir herramientas de desarrollo
            .analyze();

        expect(results.violations).toEqual([]);
    });
});
```

`.withTags()` limita el análisis a los criterios WCAG que te interesan.
`.exclude()` descarta elementos fuera de tu control (badges de desarrollo, chatbots de terceros).

## Interpretar los resultados

Cuando hay una violación, axe devuelve un objeto con:

```json
{
    "id": "color-contrast",
    "impact": "serious",
    "description": "Ensures the contrast between foreground and background colors meets WCAG 2 AA contrast ratio thresholds",
    "nodes": [{
        "html": "<p class=\"text-gray-400\">Texto de ejemplo</p>",
        "failureSummary": "Fix any of the following: Element has insufficient color contrast..."
    }]
}
```

Los campos más útiles son `id` (regla infractora), `impact` (`critical`, `serious`, `moderate`, `minor`) y `nodes[].html` para localizar el elemento en la vista.

## Estructura recomendada de tests

```
tests/e2e/a11y/
├── public.spec.js    # Portada, índice, detalle, búsqueda, 404
└── admin.spec.js     # Login, panel, formularios de edición
```

Organiza los tests por área funcional, no por componente. Así un test fallido señala directamente la página afectada.

## Lo que axe NO detecta

axe detecta violaciones estructurales y de contraste, pero no puede evaluar:
- Si el texto alternativo de una imagen es descriptivo y correcto.
- Si el orden de lectura tiene sentido semántico.
- Si la navegación por teclado es lógica e intuitiva.
- Errores que dependen de contenido dinámico no renderizado en el momento del análisis.

**Las pruebas automáticas no reemplazan la revisión manual.** Son la primera línea de defensa, no la única.

## Criterios WCAG 2.2 cubiertos por axe

axe cubre automáticamente criterios como: contraste de color (1.4.3, 1.4.11), etiquetas de formulario (1.3.1, 3.3.2), texto alternativo (1.1.1), estructura de encabezados (1.3.1), landmarks (1.3.6), atributos ARIA válidos (4.1.2) y varios más.

## Video: Notificaciones y retroalimentación accesibles

![W3C WAI Perspectives: Notifications and Feedback](https://www.youtube.com/watch?v=spxT2CmHoPk)

## Integración en CI/CD

Para ejecutar los tests en GitHub Actions, añade un job que levante el servidor, ejecute las migraciones y lance Playwright:

```yaml
- name: Run Playwright accessibility tests
  run: npx playwright test tests/e2e/a11y/
```

Guarda el reporte HTML (`playwright-report/`) como artefacto para inspeccionar las violaciones visualmente.

## Conclusión

Instalar `@axe-core/playwright`, añadir `.withTags(['wcag2a', 'wcag2aa', 'wcag22aa'])` y ejecutar los tests en cada PR es el paso mínimo para prevenir regresiones de accesibilidad. Combínalo con revisiones manuales periódicas para cubrir lo que ninguna herramienta puede detectar.

**Referencias:** [axe-core en GitHub](https://github.com/dequelabs/axe-core) · [@axe-core/playwright](https://github.com/dequelabs/axe-core-npm/tree/develop/packages/playwright) · [Playwright Docs](https://playwright.dev/docs/accessibility-testing)
MD,
                ],
                'en' => [
                    'title' => 'Accessibility Testing with axe-core and Playwright',
                    'slug' => 'accessibility-testing-axe-core-playwright',
                    'excerpt' => 'Set up @axe-core/playwright in your Laravel test suite to automatically detect WCAG 2.2 violations on every deployment.',
                    'content' => <<<'MD'
## Why You Need Automated Accessibility Tests

Manual audits are indispensable but slow and hard to scale. axe-core can automatically detect between 30% and 40% of accessibility issues — the most mechanical and repeatable ones — leaving you time for checks that require human judgment.

![Screen with test code and accessibility results in terminal](https://picsum.photos/seed/testing-a11y/800/450 "Automated accessibility testing with axe-core")

## Installation

```bash
npm install --save-dev @axe-core/playwright
```

Verify that your `playwright.config.js` points to the correct test environment URL:

```js
// playwright.config.js
import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    use: {
        baseURL: 'https://laravel-accesible.test',
        ignoreHTTPSErrors: true,
    },
});
```

## Anatomy of an axe Test with Playwright

```js
// tests/e2e/a11y/home.spec.js
import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test.describe('Homepage — WCAG 2.2 AA', () => {
    test('should have no accessibility violations', async ({ page }) => {
        await page.goto('/en');

        const results = await new AxeBuilder({ page })
            .withTags(['wcag2a', 'wcag2aa', 'wcag22aa'])
            .exclude('#herd-ai-host')   // exclude dev tools
            .analyze();

        expect(results.violations).toEqual([]);
    });
});
```

`.withTags()` limits analysis to the WCAG criteria you care about.
`.exclude()` discards elements outside your control (dev badges, third-party chatbots).

## Interpreting the Results

When there is a violation, axe returns an object with:

```json
{
    "id": "color-contrast",
    "impact": "serious",
    "description": "Ensures the contrast between foreground and background colors meets WCAG 2 AA contrast ratio thresholds",
    "nodes": [{
        "html": "<p class=\"text-gray-400\">Example text</p>",
        "failureSummary": "Fix any of the following: Element has insufficient color contrast..."
    }]
}
```

The most useful fields are `id` (the failing rule), `impact` (`critical`, `serious`, `moderate`, `minor`), and `nodes[].html` to locate the element in the view.

## Recommended Test Structure

```
tests/e2e/a11y/
├── public.spec.js    # Homepage, index, detail, search, 404
└── admin.spec.js     # Login, panel, edit forms
```

Organize tests by functional area, not by component. That way a failing test directly points to the affected page.

## What axe Does NOT Detect

axe detects structural and contrast violations, but cannot evaluate:
- Whether the alternative text of an image is descriptive and accurate.
- Whether the reading order makes semantic sense.
- Whether keyboard navigation is logical and intuitive.
- Errors that depend on dynamic content not rendered at analysis time.

**Automated tests do not replace manual review.** They are the first line of defense, not the only one.

## WCAG 2.2 Criteria Covered by axe

axe automatically covers criteria such as: color contrast (1.4.3, 1.4.11), form labels (1.3.1, 3.3.2), alternative text (1.1.1), heading structure (1.3.1), landmarks (1.3.6), valid ARIA attributes (4.1.2), and several more.

## Video: Accessible Notifications and Feedback

![W3C WAI Perspectives: Notifications and Feedback](https://www.youtube.com/watch?v=spxT2CmHoPk)

## CI/CD Integration

To run tests in GitHub Actions, add a job that starts the server, runs migrations, and launches Playwright:

```yaml
- name: Run Playwright accessibility tests
  run: npx playwright test tests/e2e/a11y/
```

Save the HTML report (`playwright-report/`) as an artifact to visually inspect violations.

## Conclusion

Installing `@axe-core/playwright`, adding `.withTags(['wcag2a', 'wcag2aa', 'wcag22aa'])`, and running tests on every PR is the minimum step to prevent accessibility regressions. Combine it with periodic manual reviews to cover what no tool can detect.

**References:** [axe-core on GitHub](https://github.com/dequelabs/axe-core) · [@axe-core/playwright](https://github.com/dequelabs/axe-core-npm/tree/develop/packages/playwright) · [Playwright Docs](https://playwright.dev/docs/accessibility-testing)
MD,
                ],
            ],
        ];
    }
}
