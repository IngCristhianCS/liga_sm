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
        $request->authenticate();

        $request->session()->regenerate();

        // Generar token Sanctum
        $user = Auth::user(); //Obtienes el usuario autenticado.
        $token = $user->createToken('api-token')->plainTextToken;

        // Almacenar el token en la sesión (opcional, pero útil)
        $request->session()->put('api_token', $token);

        // Puedes retornar el token en una vista o redirigir y usarlo desde la sesion.
        // Ejemplo de retornar en una vista
        // return view('dashboard', ['token' => $token]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        //Revocar los tokens de sanctum.
        $request->user()->tokens()->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}