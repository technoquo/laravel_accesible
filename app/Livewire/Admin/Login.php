<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Acceso al panel')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public function mount(): void
    {
        if (Auth::check() && Auth::user()->is_admin) {
            $this->redirect(route('admin.posts.index'));
        }
    }

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $rateLimiterKey = 'admin-login:'.$this->email.'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimiterKey, 5)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => RateLimiter::availableIn($rateLimiterKey),
                    'minutes' => ceil(RateLimiter::availableIn($rateLimiterKey) / 60),
                ]),
            ]);
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::hit($rateLimiterKey, 60);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! Auth::user()->is_admin) {
            Auth::logout();
            RateLimiter::hit($rateLimiterKey, 60);

            throw ValidationException::withMessages([
                'email' => 'No tienes acceso al panel de administración.',
            ]);
        }

        RateLimiter::clear($rateLimiterKey);
        session()->regenerate();

        $this->redirect(route('admin.posts.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}
