<div>
    <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-900">Posts</h1>
        <a href="{{ route('admin.posts.create') }}"
           class="inline-flex items-center bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900">
            Nuevo post
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-300 text-green-800 rounded text-sm" role="alert" aria-live="polite">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-wrap gap-3 mb-4">
        <div>
            <label for="search" class="sr-only">Buscar posts</label>
            <input id="search"
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Buscar posts…"
                class="border border-gray-300 rounded px-3 py-2 text-sm min-w-[200px] focus:outline-none focus:ring-2 focus:ring-gray-900" />
        </div>
        <div>
            <label for="status-filter" class="sr-only">Filtrar por estado</label>
            <select id="status-filter"
                wire:model.live="statusFilter"
                class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">Todos los estados</option>
                <option value="draft">Borrador</option>
                <option value="published">Publicado</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Título (ES)</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Publicado</th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <span class="sr-only">Acciones</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($this->posts as $post)
                    @php $esTranslation = $post->translationForLocale('es') @endphp
                    <tr wire:key="post-{{ $post->id }}" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                            {{ $esTranslation?->title ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if ($post->status === \App\PostStatus::Published)
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Publicado</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Borrador</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $post->published_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right space-x-4 whitespace-nowrap">
                            <a href="{{ route('admin.posts.edit', $post) }}"
                               class="text-gray-600 hover:text-gray-900 hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                                Editar
                            </a>
                            <button wire:click="delete({{ $post->id }})"
                                wire:confirm="¿Eliminar este post? Esta acción no se puede deshacer."
                                class="text-red-600 hover:text-red-800 hover:underline focus:outline-none focus:ring-2 focus:ring-red-600 rounded">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500">
                            @if ($search)
                                No hay posts que coincidan con «{{ $search }}».
                            @else
                                Aún no hay posts. <a href="{{ route('admin.posts.create') }}" class="text-gray-900 font-medium hover:underline">Crear el primero</a>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->posts->links() }}
    </div>
</div>
