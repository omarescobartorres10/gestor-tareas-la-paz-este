<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Check if account is locked
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            $minutesLeft = now()->diffInMinutes($user->locked_until);

            // Only log if debug mode is enabled
            if (config('app.debug')) {
                \Log::warning('Login attempt on locked account', [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                ]);
            }

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ["Cuenta bloqueada por múltiples intentos fallidos. Inténtalo de nuevo en {$minutesLeft} minutos."],
            ]);
        }

        try {
            $request->authenticate();

            // Reset failed attempts on successful login
            if ($user) {
                $user->update([
                    'failed_login_attempts' => 0,
                    'locked_until' => null,
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip()
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Increment failed login attempts
            if ($user) {
                $attempts = $user->failed_login_attempts + 1;
                $updateData = ['failed_login_attempts' => $attempts];

                // Lock account after 5 failed attempts for 15 minutes
                if ($attempts >= 5) {
                    $updateData['locked_until'] = now()->addMinutes(15);

                    // Critical security log - account locked
                    \Log::warning('Account locked', [
                        'email' => $user->email,
                        'ip' => $request->ip()
                    ]);
                }

                $user->update($updateData);
            }

            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
