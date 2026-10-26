<div>
    <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $postId ? 'Editar post' : 'Nuevo post' }}
        </h1>
        <a href="{{ route('admin.posts.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900 hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
            ← Volver a posts
        </a>
    </div>

    <form wire:submit="save" class="space-y-8" novalidate>

        {{-- Locale tabs --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div role="tablist" aria-label="Idioma" class="flex border-b border-gray-200">
                @foreach (['es' => 'Español', 'en' => 'English'] as $locale => $label)
                    <button type="button"
                        role="tab"
                        id="tab-{{ $locale }}"
                        aria-controls="tabpanel-{{ $locale }}"
                        aria-selected="{{ $activeLocale === $locale ? 'true' : 'false' }}"
                        wire:click="$set('activeLocale', '{{ $locale }}')"
                        class="px-5 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-900
                            {{ $activeLocale === $locale
                                ? 'border-b-2 border-gray-900 text-gray-900'
                                : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                        @if ($activeLocale !== $locale && $errors->hasAny(['title'.ucfirst($locale), 'slug'.ucfirst($locale), 'markdown'.ucfirst($locale)]))
                            <span class="ml-1 inline-block w-2 h-2 rounded-full bg-red-500" aria-label="Errores de validación"></span>
                        @endif
                    </button>
                @endforeach
            </div>

            @foreach (['es' => 'es', 'en' => 'en'] as $locale => $_)
                @php $suffix = ucfirst($locale) @endphp
                <div id="tabpanel-{{ $locale }}"
                    role="tabpanel"
                    aria-labelledby="tab-{{ $locale }}"
                    class="{{ $activeLocale === $locale ? 'block' : 'hidden' }} p-6 space-y-5">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="title{{ $suffix }}" class="block text-sm font-medium text-gray-700 mb-1">
                                Título <span aria-hidden="true" class="text-red-500">*</span>
                            </label>
                            <input id="title{{ $suffix }}"
                                type="text"
                                wire:model.live.debounce.500ms="title{{ $suffix }}"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('title'.$suffix) border-red-500 @enderror"
                                @if($errors->has('title'.$suffix)) aria-describedby="title{{ $suffix }}-error" aria-invalid="true" @endif />
                            @error('title'.$suffix)
                                <p id="title{{ $suffix }}-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="slug{{ $suffix }}" class="block text-sm font-medium text-gray-700 mb-1">
                                Slug <span aria-hidden="true" class="text-red-500">*</span>
                            </label>
                            <input id="slug{{ $suffix }}"
                                type="text"
                                wire:model="slug{{ $suffix }}"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900 @error('slug'.$suffix) border-red-500 @enderror"
                                @if($errors->has('slug'.$suffix)) aria-describedby="slug{{ $suffix }}-error" aria-invalid="true" @endif />
                            @error('slug'.$suffix)
                                <p id="slug{{ $suffix }}-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="excerpt{{ $suffix }}" class="block text-sm font-medium text-gray-700 mb-1">
                            Extracto
                        </label>
                        <textarea id="excerpt{{ $suffix }}"
                            wire:model="excerpt{{ $suffix }}"
                            rows="2"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('excerpt'.$suffix) border-red-500 @enderror"
                            @if($errors->has('excerpt'.$suffix)) aria-describedby="excerpt{{ $suffix }}-error" aria-invalid="true" @endif></textarea>
                        @error('excerpt'.$suffix)
                            <p id="excerpt{{ $suffix }}-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="markdown{{ $suffix }}" class="block text-sm font-medium text-gray-700">
                                Contenido (Markdown) <span aria-hidden="true" class="text-red-500">*</span>
                            </label>
                            @if ($activeLocale === $locale)
                                <button type="button"
                                    wire:click="togglePreview"
                                    class="text-xs text-gray-500 hover:text-gray-900 hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                                    {{ $showPreview ? 'Editar' : 'Vista previa' }}
                                </button>
                            @endif
                        </div>

                        @if ($showPreview && $activeLocale === $locale)
                            <div class="prose prose-sm max-w-none border border-gray-200 rounded p-4 bg-gray-50 min-h-[200px]">
                                {!! $this->previewHtml !!}
                            </div>
                        @else
                            <textarea id="markdown{{ $suffix }}"
                                wire:model="markdown{{ $suffix }}"
                                rows="16"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900 @error('markdown'.$suffix) border-red-500 @enderror"
                                @if($errors->has('markdown'.$suffix)) aria-describedby="markdown{{ $suffix }}-error" aria-invalid="true" @endif></textarea>
                            @error('markdown'.$suffix)
                                <p id="markdown{{ $suffix }}-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <details class="group">
                        <summary class="cursor-pointer text-sm font-medium text-gray-600 hover:text-gray-900 select-none list-none flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                            <span class="group-open:hidden">▶</span>
                            <span class="hidden group-open:inline">▼</span>
                            SEO (meta título y descripción)
                        </summary>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="metaTitle{{ $suffix }}" class="block text-sm font-medium text-gray-700 mb-1">
                                    Meta título <span class="text-xs text-gray-400">(máx. 70 caracteres)</span>
                                </label>
                                <input id="metaTitle{{ $suffix }}"
                                    type="text"
                                    wire:model="metaTitle{{ $suffix }}"
                                    maxlength="70"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('metaTitle'.$suffix) border-red-500 @enderror" />
                                @error('metaTitle'.$suffix)
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="metaDescription{{ $suffix }}" class="block text-sm font-medium text-gray-700 mb-1">
                                    Meta descripción <span class="text-xs text-gray-400">(máx. 160 caracteres)</span>
                                </label>
                                <textarea id="metaDescription{{ $suffix }}"
                                    wire:model="metaDescription{{ $suffix }}"
                                    rows="2"
                                    maxlength="160"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('metaDescription'.$suffix) border-red-500 @enderror"></textarea>
                                @error('metaDescription'.$suffix)
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </details>
                </div>
            @endforeach
        </div>

        {{-- Sidebar-style fields --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Publish settings --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Publicación</h2>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="status"
                        wire:model.live="status"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="draft">Borrador</option>
                        <option value="published">Publicado</option>
                    </select>
                </div>

                @if ($status === 'published')
                    <div>
                        <label for="publishedAt" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de publicación
                        </label>
                        <input id="publishedAt"
                            type="datetime-local"
                            wire:model="publishedAt"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('publishedAt') border-red-500 @enderror" />
                        @error('publishedAt')
                            <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            {{-- Category --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Categoría</h2>

                <div>
                    <label for="categoryId" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select id="categoryId"
                        wire:model="categoryId"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('categoryId') border-red-500 @enderror">
                        <option value="">Sin categoría</option>
                        @foreach ($this->categories as $category)
                            @php $catName = $category->translationForLocale('es')?->name ?? $category->stable_key @endphp
                            <option value="{{ $category->id }}">{{ $catName }}</option>
                        @endforeach
                    </select>
                    @error('categoryId')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tags --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Etiquetas</h2>

                <fieldset>
                    <legend class="sr-only">Seleccionar etiquetas</legend>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        @forelse ($this->tags as $tag)
                            @php $tagName = $tag->translationForLocale('es')?->name ?? $tag->stable_key @endphp
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox"
                                    wire:model="tagIds"
                                    value="{{ $tag->id }}"
                                    class="rounded border-gray-300 text-gray-900 focus:ring-2 focus:ring-gray-900" />
                                {{ $tagName }}
                            </label>
                        @empty
                            <p class="text-sm text-gray-400">No hay etiquetas.</p>
                        @endforelse
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('admin.posts.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                Cancelar
            </a>
            <button type="submit"
                class="bg-gray-900 text-white rounded px-5 py-2 text-sm font-semibold hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                <span wire:loading.remove wire:target="save">
                    {{ $postId ? 'Guardar cambios' : 'Crear post' }}
                </span>
                <span wire:loading wire:target="save">Guardando…</span>
            </button>
        </div>
    </form>
</div>
