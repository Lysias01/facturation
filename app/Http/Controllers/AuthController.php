<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Traite la connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Compte désactivé']);
            }

            // Enregistrer l'activité de connexion
            ActivityLogger::login();

            // Redirect based on role
            if (Auth::user()->isAdmin()) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('dashboard.employe');
            }
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect'
        ]);
    }


    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        // Enregistrer l'activité de déconnexion
        ActivityLogger::logout();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
