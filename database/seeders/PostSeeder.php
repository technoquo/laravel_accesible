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

## El problema: placeholder como etiqueta

Un error muy común en Blade es confiar en el atributo `placeholder` como única referencia visual del campo:

```html
<!-- ❌ Incorrecto: placeholder no reemplaza a <label> -->
<input type="email" placeholder="tu@email.com" name="email">
```

El `placeholder` desaparece en cuanto se escribe, suele tener un contraste insuficiente y no es una etiqueta fiable: no todos los lectores de pantalla lo anuncian igual, y quien revisa el formulario antes de enviarlo ya no sabe qué pedía cada campo. Incumple el criterio **WCAG 2.2 — 3.3.2 Labels or Instructions (Nivel A)** y, con el gris por defecto, a menudo también **1.4.3 Contrast (Minimum)**.

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
- `role="alert"` en el párrafo de error: si el error se inserta sin recargar la página (por ejemplo, con Livewire), el lector lo anuncia sin mover el foco. Tras un envío clásico que recarga la página, ese anuncio no es fiable; lleva el foco al primer campo con error o a un resumen de errores al principio del formulario.

## Criterios WCAG 2.2 aplicables

| Criterio | Nivel | Qué exige |
|----------|-------|-----------|
| 1.3.1 Info and Relationships | A | La relación etiqueta-campo debe ser determinable por programa |
| 1.4.3 Contrast (Minimum) | AA | Etiquetas, pistas y errores con contraste mínimo de 4.5:1 |
| 3.3.1 Error Identification | A | El error debe identificarse y describirse en texto |
| 3.3.2 Labels or Instructions | A | Los campos que piden datos deben tener etiqueta o instrucciones |

## Video: introducción a la accesibilidad web

![Introducción a la accesibilidad web y los estándares W3C](https://www.youtube.com/watch?v=20SHvU2PKsM)

## Prueba manual recomendada

1. Navega el formulario solo con `Tab` — cada campo debe recibir foco y su etiqueta debe estar visible en pantalla.
2. Activa NVDA + Firefox o VoiceOver + Safari — al enfocar el campo vacío el lector debe leer "Correo electrónico, edición, requerido".
3. Envía con el campo vacío — tras la recarga, el foco debe llevarte al error (o a un resumen de errores) y el lector debe anunciar el campo como «no válido» junto con el mensaje.
4. Comprueba contraste de la etiqueta y del error con la herramienta de contraste de DevTools (mínimo 4.5:1).

## Conclusión

Un `<label for>` bien vinculado, un `aria-describedby` que encadena pistas y errores, y un `aria-invalid` dinámico son los tres pilares de los formularios Blade accesibles. No requieren paquetes externos ni JavaScript adicional.

**Referencias:** [W3C WAI — Tutorial de formularios](https://www.w3.org/WAI/tutorials/forms/) · [MDN: `<label>`](https://developer.mozilla.org/es/docs/Web/HTML/Reference/Elements/label) · [WCAG 2.2](https://www.w3.org/TR/WCAG22/)
MD,
                ],
                'en' => [
                    'title' => 'Accessible Blade Forms',
                    'slug' => 'accessible-blade-forms',
                    'excerpt' => 'Learn to build Blade forms with correct labels, linked error messages, and ARIA attributes that work with screen readers.',
                    'content' => <<<'MD'
## Why Accessible Forms Matter

Forms are the most frequent entry point between a user and an application. A field without a label, an error with no announcement, or a button with no accessible name can completely block users who rely on screen readers or keyboard-only navigation.

## The Problem: Placeholder as a Label

A very common mistake in Blade is relying on the `placeholder` attribute as the only visual reference for a field:

```html
<!-- ❌ Incorrect: placeholder does not replace <label> -->
<input type="email" placeholder="you@email.com" name="email">
```

The `placeholder` disappears as soon as you type, usually has insufficient contrast, and is not a reliable label: screen readers do not all announce it the same way, and anyone reviewing the form before submitting no longer knows what each field asked for. This fails **WCAG 2.2 — 3.3.2 Labels or Instructions (Level A)** and, with the default grey, often **1.4.3 Contrast (Minimum)** as well.

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
- `role="alert"` on the error paragraph: when the error is inserted without a page reload (for example, with Livewire), the screen reader announces it without moving focus. After a classic submission that reloads the page, that announcement is unreliable; move focus to the first invalid field or to an error summary at the top of the form.

## Applicable WCAG 2.2 Criteria

| Criterion | Level | What it requires |
|-----------|-------|-----------------|
| 1.3.1 Info and Relationships | A | Label-field relationship must be programmatically determinable |
| 1.4.3 Contrast (Minimum) | AA | Labels, hints and errors with a minimum contrast of 4.5:1 |
| 3.3.1 Error Identification | A | The error must be identified and described in text |
| 3.3.2 Labels or Instructions | A | Fields that request input must have a label or instructions |

## Video: Introduction to Web Accessibility

![Introduction to Web Accessibility and W3C Standards](https://www.youtube.com/watch?v=20SHvU2PKsM)

## Recommended Manual Test

1. Navigate the form using only `Tab` — every field must receive focus and its label must be visible on screen.
2. Enable NVDA + Firefox or VoiceOver + Safari — when focusing an empty field, the reader should announce "Email address, edit, required".
3. Submit with an empty field — after the reload, focus must take you to the error (or an error summary) and the reader must announce the field as "invalid" together with the message.
4. Check the contrast of the label and error with the DevTools contrast tool (minimum 4.5:1).

## Conclusion

A properly linked `<label for>`, an `aria-describedby` that chains hints and errors, and a dynamic `aria-invalid` are the three pillars of accessible Blade forms. They require no external packages or additional JavaScript.

**References:** [W3C WAI — Forms Tutorial](https://www.w3.org/WAI/tutorials/forms/) · [MDN: `<label>`](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/label) · [WCAG 2.2](https://www.w3.org/TR/WCAG22/)
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
<form x-on:submit.prevent="$wire.submit().then(() => $el.querySelector('[aria-invalid=true]')?.focus())">
    {{-- Región de estado global: existe siempre, solo cambia su contenido --}}
    <div role="status" aria-atomic="true" class="text-sm text-green-800">
        {{ $successMessage }}
    </div>

    <div>
        <label for="name">Nombre completo</label>
        <input
            type="text"
            id="name"
            wire:model.live.blur="name"
            @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
        >
        @error('name')
            <p id="name-error" role="alert" class="text-sm text-red-700">
                {{ $message }}
            </p>
        @enderror
    </div>

    <button type="submit">Enviar</button>
</form>
```

```php
// app/Livewire/ContactForm.php
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactForm extends Component
{
    #[Validate('required|string|min:2|max:100')]
    public string $name = '';

    public string $successMessage = '';

    public function submit(): void
    {
        $this->successMessage = '';
        $this->validate();

        // ... lógica de negocio

        $this->successMessage = 'Formulario enviado correctamente.';
        $this->reset('name');
    }
}
```

Los puntos clave:
- `role="alert"` en cada error individual hace que se anuncie en cuanto Livewire lo inserta en el DOM.
- La región `role="status"` (que ya implica `aria-live="polite"`) está siempre presente y visible: anuncia el éxito sin interrumpir y también lo ven quienes no usan lector de pantalla.
- `#[Validate]` junto con `wire:model.live.blur` valida cada campo al salir de él, no mientras se escribe.
- `aria-invalid` y `aria-describedby` solo aparecen cuando hay error, y se actualizan en cada re-render.
- Al enviar, la promesa de `$wire.submit()` se resuelve cuando Livewire ya actualizó el DOM; en ese momento se lleva el foco al primer campo no válido. Si varios `role="alert"` aparecen a la vez, los lectores de pantalla no siempre los anuncian todos, así que el foco es la garantía.

## Criterios WCAG 2.2 aplicables

| Criterio | Nivel | Qué exige |
|----------|-------|-----------|
| 3.3.1 Error Identification | A | Describir el error en texto |
| 3.3.3 Error Suggestion | AA | Sugerir cómo corregirlo cuando sea posible |
| 4.1.3 Status Messages | AA | Mensajes de estado programáticamente determinables |

## Video: Notificaciones y retroalimentación en interfaces web

![W3C WAI Perspectives: Notifications and Feedback](https://www.youtube.com/watch?v=E1fEv4Vpexg)

## Prueba manual recomendada

1. Con NVDA + Firefox: escribe un dato inválido y sal del campo con `Tab`. El lector debe anunciar el error sin que tengas que volver al campo.
2. Envía el formulario con datos inválidos: el foco debe ir al primer campo con error y el lector debe anunciarlo como «no válido» junto con su mensaje.
3. Corrige el error y sal del campo: el mensaje debe desaparecer y el lector ya no debe anunciar el campo como «no válido».
4. Tras el envío correcto, comprueba que el mensaje de confirmación se ve en pantalla y se anuncia automáticamente.

## Conclusión

`role="alert"` en errores individuales, una región `role="status"` siempre presente para las confirmaciones y el foco en el primer campo no válido al enviar bastan para que un formulario Livewire comunique su estado a todas las personas, sin paquetes adicionales.

**Referencias:** [WAI-ARIA — Live Regions](https://www.w3.org/TR/wai-aria-1.2/#live_region_roles) · [WCAG 2.2 — 3.3.1](https://www.w3.org/TR/WCAG22/#error-identification) · [Livewire 4 — Validación](https://livewire.laravel.com/docs/4.x/validation)
MD,
                ],
                'en' => [
                    'title' => 'Accessible Error Messages in Livewire',
                    'slug' => 'accessible-error-messages-livewire',
                    'excerpt' => 'Techniques for communicating validation errors in Livewire components using ARIA live regions, role="alert", and focus management.',
                    'content' => <<<'MD'
## The Problem of Silent Errors

When Livewire updates the DOM after a failed validation, screen readers do not automatically detect the change unless the developer explicitly signals it. The result is a form that seems broken: the user sees errors on screen but the screen reader announces nothing.

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
<form x-on:submit.prevent="$wire.submit().then(() => $el.querySelector('[aria-invalid=true]')?.focus())">
    {{-- Global status region: always present, only its content changes --}}
    <div role="status" aria-atomic="true" class="text-sm text-green-800">
        {{ $successMessage }}
    </div>

    <div>
        <label for="name">Full name</label>
        <input
            type="text"
            id="name"
            wire:model.live.blur="name"
            @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
        >
        @error('name')
            <p id="name-error" role="alert" class="text-sm text-red-700">
                {{ $message }}
            </p>
        @enderror
    </div>

    <button type="submit">Submit</button>
</form>
```

```php
// app/Livewire/ContactForm.php
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactForm extends Component
{
    #[Validate('required|string|min:2|max:100')]
    public string $name = '';

    public string $successMessage = '';

    public function submit(): void
    {
        $this->successMessage = '';
        $this->validate();

        // ... business logic

        $this->successMessage = 'Form submitted successfully.';
        $this->reset('name');
    }
}
```

Key points:
- `role="alert"` on each individual error makes it announced as soon as Livewire inserts it into the DOM.
- The `role="status"` region (which already implies `aria-live="polite"`) is always present and visible: it announces success without interrupting, and people who do not use a screen reader see it too.
- `#[Validate]` together with `wire:model.live.blur` validates each field when you leave it, not while you type.
- `aria-invalid` and `aria-describedby` only appear when there is an error, and update on every re-render.
- On submit, the `$wire.submit()` promise resolves once Livewire has updated the DOM; at that point focus moves to the first invalid field. When several `role="alert"` elements appear at once, screen readers do not always announce them all, so focus is the safety net.

## Applicable WCAG 2.2 Criteria

| Criterion | Level | What it requires |
|-----------|-------|-----------------|
| 3.3.1 Error Identification | A | Describe the error in text |
| 3.3.3 Error Suggestion | AA | Suggest how to fix it when possible |
| 4.1.3 Status Messages | AA | Status messages must be programmatically determinable |

## Video: Notifications and Feedback in Web Interfaces

![W3C WAI Perspectives: Notifications and Feedback](https://www.youtube.com/watch?v=E1fEv4Vpexg)

## Recommended Manual Test

1. With NVDA + Firefox: type invalid data and leave the field with `Tab`. The reader must announce the error without you returning to the field.
2. Submit the form with invalid data: focus must move to the first invalid field and the reader must announce it as "invalid" together with its message.
3. Fix the error and leave the field: the message must disappear and the reader must no longer announce the field as "invalid".
4. After a successful submission, check that the confirmation message is visible on screen and announced automatically.

## Conclusion

`role="alert"` on individual errors, an always-present `role="status"` region for confirmations, and focus on the first invalid field on submit are enough for a Livewire form to communicate its state to everyone, with no extra packages.

**References:** [WAI-ARIA — Live Regions](https://www.w3.org/TR/wai-aria-1.2/#live_region_roles) · [WCAG 2.2 — 3.3.1](https://www.w3.org/TR/WCAG22/#error-identification) · [Livewire 4 — Validation](https://livewire.laravel.com/docs/4.x/validation)
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
                    'excerpt' => 'Cómo conservar y mover el foco tras las actualizaciones de Livewire y la navegación con wire:navigate para que nadie que use teclado se pierda.',
                    'content' => <<<'MD'
## Por qué el teclado es fundamental

Muchas personas navegan solo con teclado o con dispositivos que lo emulan: personas con discapacidad motora, usuarios de lectores de pantalla en escritorio, de conmutadores o de control por voz. Un componente Livewire que pierde el foco tras una actualización, o lo deja en un lugar inesperado, obliga a esas personas a volver a recorrer la página desde el principio.

## El problema: el elemento enfocado desaparece

Livewire compara el HTML nuevo con el actual y solo modifica lo que cambia, así que un elemento que sigue en el DOM conserva el foco. El problema aparece cuando se elimina el propio elemento enfocado, por ejemplo al sustituir un botón por un texto de carga:

```blade
{{-- ❌ Problemático: el botón enfocado se elimina mientras carga --}}
<div>
    @if ($loading)
        <span>Cargando...</span>
    @else
        <button wire:click="loadMore">Ver más</button>
    @endif
</div>
```

Cuando el botón desaparece, el foco vuelve a `<body>`: quien usa teclado regresa al principio de la página y el lector de pantalla no anuncia nada.

## Solución 1: no eliminar el elemento enfocado

Mantén el botón en el DOM y anuncia el estado de carga en una región aparte:

```blade
{{-- ✅ El botón permanece; la carga se anuncia sin mover el foco --}}
<button type="button" wire:click="loadMore">
    Ver más
</button>

<p role="status" class="sr-only">
    <span wire:loading wire:target="loadMore">Cargando más resultados…</span>
</p>
```

En listas generadas con `@foreach`, añade `wire:key` a cada elemento para que Livewire no confunda unos con otros al insertar o reordenar, y el foco se quede en el elemento correcto.

## Solución 2: mover el foco de forma intencionada

Al cargar nuevos resultados, lleva el foco al primero de ellos. La promesa que devuelve una acción de `$wire` se resuelve cuando Livewire ya actualizó el DOM, así que el elemento nuevo ya existe:

```php
// app/Livewire/PostList.php
public int $perPage = 10;

public ?int $firstNewIndex = null;

public function loadMore(): void
{
    $this->firstNewIndex = $this->perPage;
    $this->perPage += 10;
}

public function render(): View
{
    return view('livewire.post-list', [
        'posts' => Post::latest()->take($this->perPage)->get(),
    ]);
}
```

```blade
{{-- resources/views/livewire/post-list.blade.php --}}
<div>
    @foreach ($posts as $post)
        <article
            wire:key="post-{{ $post->id }}"
            tabindex="-1"
            @if ($loop->index === $firstNewIndex) data-first-new @endif
        >
            <h3>{{ $post->title }}</h3>
        </article>
    @endforeach

    <button
        type="button"
        x-on:click="$wire.loadMore().then(() => document.querySelector('[data-first-new]')?.focus())"
    >
        Ver más
    </button>
</div>
```

`tabindex="-1"` permite que el elemento reciba el foco por programa sin añadirlo al orden de tabulación.

## Solución 3: foco tras `wire:navigate`

Con `wire:navigate` la página cambia sin recarga completa, y el foco puede quedarse en un enlace que ya no existe. Lleva el foco al contenido principal después de cada navegación. El evento `livewire:navigated` también se dispara en la carga inicial, así que solo se mueve el foco cuando antes hubo una navegación:

```js
// resources/js/app.js
let navigatedWithLivewire = false;

document.addEventListener('livewire:navigate', () => {
    navigatedWithLivewire = true;
});

document.addEventListener('livewire:navigated', () => {
    if (navigatedWithLivewire) {
        document.getElementById('main-content')?.focus();
    }
});
```

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
| 2.4.3 Focus Order | A | El orden del foco debe preservar el significado y la operabilidad |
| 2.4.7 Focus Visible | AA | El indicador de foco debe ser visible |
| 2.4.11 Focus Not Obscured (Minimum) | AA | El elemento enfocado no puede quedar totalmente tapado por otro contenido |
| 4.1.3 Status Messages | AA | Mensajes como «Cargando…» deben anunciarse sin mover el foco |

## Video: compatibilidad con teclado

![W3C WAI Perspectives: Keyboard Compatibility](https://www.youtube.com/watch?v=93UgG72os8M)

## Prueba manual recomendada

1. Abre el componente y navega solo con `Tab`, `Shift+Tab`, `Enter` y `Espacio`.
2. Activa una acción Livewire (botón, búsqueda, paginación) — comprueba que el foco no vuelve al principio de la página y que el lector anuncia el cambio.
3. Pulsa «Ver más» — el foco debe pasar al primer resultado nuevo.
4. Sigue un enlace con `wire:navigate` — el foco debe quedar en el contenido principal de la nueva página.
5. Zoom al 200 % — los indicadores de foco deben verse y no quedar tapados por cabeceras fijas.

## Conclusión

No eliminar el elemento enfocado, mover el foco de forma intencionada cuando aparece contenido nuevo y recolocarlo tras cada `wire:navigate` son las tres reglas que mantienen navegables por teclado los componentes Livewire.

**Referencias:** [Livewire 4 — Navigate](https://livewire.laravel.com/docs/4.x/navigate) · [Livewire 4 — wire:loading](https://livewire.laravel.com/docs/4.x/wire-loading) · [WCAG 2.2 — 2.4.3](https://www.w3.org/TR/WCAG22/#focus-order) · [WAI — SCR26](https://www.w3.org/WAI/WCAG22/Techniques/client-side-script/SCR26)
MD,
                ],
                'en' => [
                    'title' => 'Keyboard Navigation in Livewire Components',
                    'slug' => 'keyboard-navigation-livewire',
                    'excerpt' => 'How to keep and move focus after Livewire updates and wire:navigate visits so keyboard users never get lost.',
                    'content' => <<<'MD'
## Why the Keyboard is Fundamental

Many people navigate using only a keyboard or devices that emulate one: people with motor disabilities, desktop screen reader users, switch users and voice control users. A Livewire component that loses focus after an update, or leaves it somewhere unexpected, forces those people to work through the page again from the top.

## The Problem: the Focused Element Disappears

Livewire compares the new HTML with the current one and only changes what differs, so an element that stays in the DOM keeps its focus. The problem appears when the focused element itself is removed, for example when a button is swapped for a loading text:

```blade
{{-- ❌ Problematic: the focused button is removed while loading --}}
<div>
    @if ($loading)
        <span>Loading...</span>
    @else
        <button wire:click="loadMore">Load more</button>
    @endif
</div>
```

When the button disappears, focus falls back to `<body>`: keyboard users are sent back to the top of the page and the screen reader announces nothing.

## Solution 1: Do Not Remove the Focused Element

Keep the button in the DOM and announce the loading state in a separate region:

```blade
{{-- ✅ The button stays; loading is announced without moving focus --}}
<button type="button" wire:click="loadMore">
    Load more
</button>

<p role="status" class="sr-only">
    <span wire:loading wire:target="loadMore">Loading more results…</span>
</p>
```

In lists rendered with `@foreach`, add `wire:key` to each item so Livewire does not mix them up when inserting or reordering, and focus stays on the right element.

## Solution 2: Move Focus Intentionally

When new results load, move focus to the first one. The promise returned by a `$wire` action resolves once Livewire has updated the DOM, so the new element already exists:

```php
// app/Livewire/PostList.php
public int $perPage = 10;

public ?int $firstNewIndex = null;

public function loadMore(): void
{
    $this->firstNewIndex = $this->perPage;
    $this->perPage += 10;
}

public function render(): View
{
    return view('livewire.post-list', [
        'posts' => Post::latest()->take($this->perPage)->get(),
    ]);
}
```

```blade
{{-- resources/views/livewire/post-list.blade.php --}}
<div>
    @foreach ($posts as $post)
        <article
            wire:key="post-{{ $post->id }}"
            tabindex="-1"
            @if ($loop->index === $firstNewIndex) data-first-new @endif
        >
            <h3>{{ $post->title }}</h3>
        </article>
    @endforeach

    <button
        type="button"
        x-on:click="$wire.loadMore().then(() => document.querySelector('[data-first-new]')?.focus())"
    >
        Load more
    </button>
</div>
```

`tabindex="-1"` lets the element receive focus programmatically without adding it to the tab order.

## Solution 3: Focus After `wire:navigate`

With `wire:navigate` the page changes without a full reload, and focus can be left on a link that no longer exists. Move focus to the main content after each navigation. The `livewire:navigated` event also fires on the initial page load, so focus only moves when a navigation happened first:

```js
// resources/js/app.js
let navigatedWithLivewire = false;

document.addEventListener('livewire:navigate', () => {
    navigatedWithLivewire = true;
});

document.addEventListener('livewire:navigated', () => {
    if (navigatedWithLivewire) {
        document.getElementById('main-content')?.focus();
    }
});
```

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
| 2.4.3 Focus Order | A | Focus order must preserve meaning and operability |
| 2.4.7 Focus Visible | AA | Focus indicator must be visible |
| 2.4.11 Focus Not Obscured (Minimum) | AA | The focused element must not be entirely hidden by other content |
| 4.1.3 Status Messages | AA | Messages such as "Loading…" must be announced without moving focus |

## Video: Keyboard Compatibility

![W3C WAI Perspectives: Keyboard Compatibility](https://www.youtube.com/watch?v=93UgG72os8M)

## Recommended Manual Test

1. Open the component and navigate using only `Tab`, `Shift+Tab`, `Enter`, and `Space`.
2. Trigger a Livewire action (button, search, pagination) — check that focus does not jump back to the top of the page and that the reader announces the change.
3. Press "Load more" — focus must move to the first new result.
4. Follow a `wire:navigate` link — focus must land on the main content of the new page.
5. Zoom to 200% — focus indicators must be visible and not hidden behind sticky headers.

## Conclusion

Not removing the focused element, moving focus intentionally when new content appears, and placing it again after each `wire:navigate` are the three rules that keep Livewire components keyboard navigable.

**References:** [Livewire 4 — Navigate](https://livewire.laravel.com/docs/4.x/navigate) · [Livewire 4 — wire:loading](https://livewire.laravel.com/docs/4.x/wire-loading) · [WCAG 2.2 — 2.4.3](https://www.w3.org/TR/WCAG22/#focus-order) · [WAI — SCR26](https://www.w3.org/WAI/WCAG22/Techniques/client-side-script/SCR26)
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

Las auditorías manuales son imprescindibles, pero lentas y difíciles de escalar. axe-core detecta de forma automática los problemas más mecánicos y repetibles —contraste, nombres accesibles, atributos ARIA no válidos— y te deja tiempo para las comprobaciones que requieren criterio humano.

## Instalación

```bash
npm install --save-dev @axe-core/playwright
```

Configura `playwright.config.js` para que apunte a tu sitio local y, en CI, levante el servidor de Laravel por sí mismo:

```js
// playwright.config.js
import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    use: {
        baseURL: process.env.BASE_URL ?? 'https://laravel-accesible.test',
        ignoreHTTPSErrors: true,
    },
    webServer: process.env.CI
        ? { command: 'php artisan serve --port=8000', url: 'http://127.0.0.1:8000' }
        : undefined,
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
            .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
            .exclude('#herd-ai-host')   // distintivo de Laravel Herd, solo en desarrollo
            .analyze();

        expect(results.violations).toEqual([]);
    });
});
```

`.withTags()` limita el análisis a las reglas de los niveles A y AA. axe etiqueta cada regla con la versión de WCAG que la introdujo: si solo pides `wcag2a` y `wcag2aa`, quedan fuera las reglas de WCAG 2.1 (por ejemplo, `autocomplete-valid`), así que incluye las cinco etiquetas.
`.exclude()` descarta elementos fuera de tu control (distintivos de desarrollo, widgets de terceros).

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
- Si el texto alternativo de una imagen es descriptivo y correcto (solo comprueba que exista).
- Si el orden de lectura tiene sentido semántico.
- Si la navegación por teclado es lógica e intuitiva.
- Errores que dependen de contenido dinámico no renderizado en el momento del análisis.

**Las pruebas automáticas no reemplazan la revisión manual.** Son la primera línea de defensa, no la única.

## Criterios WCAG 2.2 cubiertos por axe

axe comprueba automáticamente, entre otros: contraste de texto (1.4.3), presencia de texto alternativo (1.1.1), etiquetas de formulario y nombres accesibles (4.1.2), idioma de la página (3.1.1), propósito de los campos con `autocomplete` (1.3.5), tamaño de los objetivos (2.5.8, con la etiqueta `wcag22aa`) y atributos ARIA válidos (4.1.2). Las reglas sobre orden de encabezados y landmarks son buenas prácticas de axe (etiqueta `best-practice`), no criterios WCAG, y no se ejecutan con las etiquetas anteriores.

## Integración en CI/CD

En GitHub Actions, prepara Laravel, compila los assets, instala el navegador y lanza Playwright. Con la configuración anterior, Playwright levanta `php artisan serve` por sí mismo:

```yaml
- name: Preparar Laravel
  run: |
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed --force
- name: Compilar assets
  run: npm ci && npm run build
- name: Instalar Chromium
  run: npx playwright install --with-deps chromium
- name: Pruebas de accesibilidad
  run: npx playwright test tests/e2e/a11y/
  env:
    BASE_URL: http://127.0.0.1:8000
- name: Guardar el reporte
  if: failure()
  uses: actions/upload-artifact@v4
  with:
    name: playwright-report
    path: playwright-report/
```

Ajusta la base de datos de `.env` a la que use tu CI (por ejemplo, SQLite). El reporte HTML guardado como artefacto permite revisar cada violación con su selector y su HTML.

## Conclusión

Instalar `@axe-core/playwright`, usar `.withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])` y ejecutar las pruebas en cada PR es el paso mínimo para prevenir regresiones de accesibilidad. Combínalo con revisiones manuales periódicas para cubrir lo que ninguna herramienta puede detectar.

**Referencias:** [axe-core en GitHub](https://github.com/dequelabs/axe-core) · [@axe-core/playwright](https://github.com/dequelabs/axe-core-npm/tree/develop/packages/playwright) · [Playwright Docs](https://playwright.dev/docs/accessibility-testing)
MD,
                ],
                'en' => [
                    'title' => 'Accessibility Testing with axe-core and Playwright',
                    'slug' => 'accessibility-testing-axe-core-playwright',
                    'excerpt' => 'Set up @axe-core/playwright in your Laravel test suite to automatically detect WCAG 2.2 violations on every deployment.',
                    'content' => <<<'MD'
## Why You Need Automated Accessibility Tests

Manual audits are indispensable, but slow and hard to scale. axe-core automatically detects the most mechanical and repeatable issues — contrast, accessible names, invalid ARIA attributes — leaving you time for the checks that require human judgment.

## Installation

```bash
npm install --save-dev @axe-core/playwright
```

Configure `playwright.config.js` to point to your local site and, in CI, to start the Laravel server by itself:

```js
// playwright.config.js
import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    use: {
        baseURL: process.env.BASE_URL ?? 'https://laravel-accesible.test',
        ignoreHTTPSErrors: true,
    },
    webServer: process.env.CI
        ? { command: 'php artisan serve --port=8000', url: 'http://127.0.0.1:8000' }
        : undefined,
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
            .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
            .exclude('#herd-ai-host')   // Laravel Herd badge, development only
            .analyze();

        expect(results.violations).toEqual([]);
    });
});
```

`.withTags()` limits analysis to Level A and AA rules. axe tags each rule with the WCAG version that introduced it: if you only ask for `wcag2a` and `wcag2aa`, WCAG 2.1 rules (such as `autocomplete-valid`) are left out, so include all five tags.
`.exclude()` discards elements outside your control (development badges, third-party widgets).

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
- Whether the alternative text of an image is descriptive and accurate (it only checks that it exists).
- Whether the reading order makes semantic sense.
- Whether keyboard navigation is logical and intuitive.
- Errors that depend on dynamic content not rendered at analysis time.

**Automated tests do not replace manual review.** They are the first line of defense, not the only one.

## WCAG 2.2 Criteria Covered by axe

axe automatically checks, among others: text contrast (1.4.3), presence of alternative text (1.1.1), form labels and accessible names (4.1.2), page language (3.1.1), input purpose with `autocomplete` (1.3.5), target size (2.5.8, with the `wcag22aa` tag), and valid ARIA attributes (4.1.2). The heading order and landmark rules are axe best practices (`best-practice` tag), not WCAG criteria, and do not run with the tags above.

## CI/CD Integration

In GitHub Actions, prepare Laravel, build the assets, install the browser and run Playwright. With the configuration above, Playwright starts `php artisan serve` by itself:

```yaml
- name: Prepare Laravel
  run: |
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed --force
- name: Build assets
  run: npm ci && npm run build
- name: Install Chromium
  run: npx playwright install --with-deps chromium
- name: Accessibility tests
  run: npx playwright test tests/e2e/a11y/
  env:
    BASE_URL: http://127.0.0.1:8000
- name: Save the report
  if: failure()
  uses: actions/upload-artifact@v4
  with:
    name: playwright-report
    path: playwright-report/
```

Point the database in `.env` to the one your CI uses (for example, SQLite). The HTML report saved as an artifact lets you review each violation with its selector and HTML.

## Conclusion

Installing `@axe-core/playwright`, using `.withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])`, and running the tests on every PR is the minimum step to prevent accessibility regressions. Combine it with periodic manual reviews to cover what no tool can detect.

**References:** [axe-core on GitHub](https://github.com/dequelabs/axe-core) · [@axe-core/playwright](https://github.com/dequelabs/axe-core-npm/tree/develop/packages/playwright) · [Playwright Docs](https://playwright.dev/docs/accessibility-testing)
MD,
                ],
            ],
        ];
    }
}
