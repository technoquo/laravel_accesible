<div>
    <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-900">Etiquetas</h1>
        @if (! $isCreating && ! $editingId)
            <button wire:click="create"
                class="inline-flex items-center bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
                Nueva etiqueta
            </button>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-300 text-green-800 rounded text-sm" role="alert" aria-live="polite">
            {{ session('success') }}
        </div>
    @endif

    @if ($isCreating || $editingId)
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-5">
                {{ $editingId ? 'Editar etiqueta' : 'Nueva etiqueta' }}
            </h2>

            <form wire:submit="save" class="space-y-5" novalidate>
                <div>
                    <label for="stableKey" class="block text-sm font-medium text-gray-700 mb-1">
                        Clave estable <span aria-hidden="true" class="text-red-500">*</span>
                    </label>
                    <input id="stableKey" type="text" wire:model="stableKey"
                        class="w-full max-w-xs border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900 @error('stableKey') border-red-500 @enderror"
                        @if($errors->has('stableKey')) aria-describedby="stableKey-error" aria-invalid="true" @endif />
                    @error('stableKey')
                        <p id="stableKey-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach (['es' => 'Español', 'en' => 'English'] as $locale => $label)
                        @php $suffix = ucfirst($locale) @endphp
                        <fieldset class="border border-gray-200 rounded p-4">
                            <legend class="text-sm font-medium text-gray-700 px-1">{{ $label }}</legend>
                            <div class="space-y-4 mt-2">
                                <div>
                                    <label for="name{{ $suffix }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre <span aria-hidden="true" class="text-red-500">*</span>
                                    </label>
                                    <input id="name{{ $suffix }}" type="text"
                                        wire:model.live.debounce.500ms="name{{ $suffix }}"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('name'.$suffix) border-red-500 @enderror"
                                        @if($errors->has('name'.$suffix)) aria-describedby="name{{ $suffix }}-error" aria-invalid="true" @endif />
                                    @error('name'.$suffix)
                                        <p id="name{{ $suffix }}-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="slug{{ $suffix }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        Slug <span aria-hidden="true" class="text-red-500">*</span>
                                    </label>
                                    <input id="slug{{ $suffix }}" type="text" wire:model="slug{{ $suffix }}"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900 @error('slug'.$suffix) border-red-500 @enderror"
                                        @if($errors->has('slug'.$suffix)) aria-describedby="slug{{ $suffix }}-error" aria-invalid="true" @endif />
                                    @error('slug'.$suffix)
                                        <p id="slug{{ $suffix }}-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                        @enderror
                                </div>
                            </div>
                        </fieldset>
                    @endforeach
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <button type="submit"
                        class="bg-gray-900 text-white rounded px-4 py-2 text-sm font-semibold hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Guardar cambios' : 'Crear etiqueta' }}</span>
                        <span wire:loading wire:target="save">Guardando…</span>
                    </button>
                    <button type="button" wire:click="cancelEditing"
                        class="text-sm text-gray-600 hover:text-gray-900 hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre (ES)</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre (EN)</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Clave</th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <span class="sr-only">Acciones</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($this->tags as $tag)
                    <tr wire:key="tag-{{ $tag->id }}" class="{{ $editingId === $tag->id ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                            {{ $tag->translationForLocale('es')?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $tag->translationForLocale('en')?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $tag->stable_key }}</td>
                        <td class="px-4 py-3 text-sm text-right space-x-4">
                            <button wire:click="edit({{ $tag->id }})"
                                class="text-gray-600 hover:text-gray-900 hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                                Editar
                            </button>
                            <button wire:click="delete({{ $tag->id }})"
                                wire:confirm="¿Eliminar esta etiqueta? Se eliminará de todos los posts."
                                class="text-red-600 hover:text-red-800 hover:underline focus:outline-none focus:ring-2 focus:ring-red-600 rounded">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500">
                            No hay etiquetas aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
