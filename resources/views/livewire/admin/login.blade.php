<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm">
        <h1 class="text-2xl font-bold text-center mb-8 text-gray-900">Panel de administración</h1>

        <form wire:submit="login" class="bg-white shadow-sm border border-gray-200 rounded-lg p-8 space-y-5" novalidate>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Correo electrónico
                </label>
                <input id="email"
                    type="email"
                    wire:model="email"
                    autocomplete="email"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('email') border-red-500 @enderror"
                    @if($errors->has('email')) aria-describedby="email-error" aria-invalid="true" @endif />
                @error('email')
                    <p id="email-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Contraseña
                </label>
                <input id="password"
                    type="password"
                    wire:model="password"
                    autocomplete="current-password"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('password') border-red-500 @enderror"
                    @if($errors->has('password')) aria-describedby="password-error" aria-invalid="true" @endif />
                @error('password')
                    <p id="password-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-gray-900 text-white rounded px-4 py-2 text-sm font-semibold hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                <span wire:loading.remove wire:target="login">Entrar</span>
                <span wire:loading wire:target="login">Entrando…</span>
            </button>
        </form>
    </div>
</div>
