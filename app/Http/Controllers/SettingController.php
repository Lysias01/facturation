<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    /**
     * Afficher la page de paramètres
     */
    public function edit()
    {
        $settings = Setting::first();
        $users = User::orderByDesc('created_at')->get();
        return view('settings.edit', compact('settings', 'users'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'nullable|email|max:255',
            'ifu' => 'nullable|string|max:100',
            'rccm' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'national_motto' => 'nullable|string|max:255',
        ]);

        $settings = Setting::first() ?? new Setting;
        
        $oldData = [
            'company_name' => $settings->company_name,
            'ifu' => $settings->ifu,
            'rccm' => $settings->rccm,
        ];

        $settings->company_name = $request->company_name;
        $settings->phone = $request->phone;
        $settings->address = $request->address;
        $settings->email = $request->email;
        $settings->ifu = $request->ifu;
        $settings->rccm = $request->rccm;
        $settings->country = $request->country;
        $settings->national_motto = $request->national_motto;

        // Upload du logo - on stocke directement dans public/logos pour éviter les problèmes de symlink
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo s'il existe
            if ($settings->logo) {
                $oldPath = public_path('logos/' . $settings->logo);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            // Stocker le nouveau logo dans public/logos/
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('logos'), $filename);
            $settings->logo = $filename;
        }

        $settings->save();

        Cache::forget('app_settings');

        // Enregistrer l'activité
        ActivityLogger::updated(
            $settings,
            "Mise à jour des paramètres de l'entreprise: {$request->company_name}",
            null,
            $oldData
        );

        return redirect()->route('settings.edit', ['tab' => 'company'])->with('success', 'Paramètres mis à jour avec succès !');
    }

    /**
     * reinitialiser les paramètre 
     */
    public function reset()
    {
        $settings = Setting::first();

        if ($settings) {
            // Supprimer le logo dans public/logos/
            if ($settings->logo) {
                $logoPath = public_path('logos/' . $settings->logo);
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }

            $settingsName = $settings->company_name;
            $settings->delete();
            
            // Enregistrer l'activité
            ActivityLogger::deleted(
                $settings,
                "Réinitialisation des paramètres de l'entreprise: {$settingsName}"
            );
        }

        Cache::forget('app_settings');

        return redirect()->route('settings.edit', ['tab' => 'company'])->with('success', 'Paramètres réinitialisés avec succès.');
    }

    /**
     * Ajouter un nouvel utilisateur
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,employe',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        // Enregistrer l'activité
        ActivityLogger::created(
            $user,
            "Création de l'utilisateur {$user->name} ( {$user->email} ) - Rôle: {$user->role}"
        );

        return redirect()->route('settings.edit', ['tab' => 'users'])->with('success', 'Utilisateur créé avec succès !');
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,employe',
        ]);

        $oldData = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Enregistrer l'activité
        ActivityLogger::updated(
            $user,
            "Modification de l'utilisateur {$user->name} ( {$user->email} )",
            null,
            $oldData
        );

        return redirect()->route('settings.edit', ['tab' => 'users'])->with('success', 'Utilisateur mis à jour avec succès !');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroyUser(User $user)
    {
        // Empêcher la suppression de soi-même
        if ($user->id === auth()->id()) {
            return redirect()->route('settings.edit', ['tab' => 'users'])->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        
        $user->delete();

        // Enregistrer l'activité
        ActivityLogger::deleted(
            $user,
            "Suppression de l'utilisateur {$userName} ( {$userEmail} )"
        );

        return redirect()->route('settings.edit', ['tab' => 'users'])->with('success', 'Utilisateur supprimé avec succès !');
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleUserStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('settings.edit', ['tab' => 'users'])->with('error', 'Vous ne pouvez pas modifier votre propre statut.');
        }

        $oldStatus = $user->is_active;
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activé' : 'désactivé';
        
        // Enregistrer l'activité
        ActivityLogger::updated(
            $user,
            "{$status} l'utilisateur {$user->name}",
            null,
            ['is_active' => $oldStatus]
        );

        return redirect()->route('settings.edit', ['tab' => 'users'])->with('success', "Utilisateur $status avec succès !");
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Enregistrer l'activité
        ActivityLogger::updated(
            $user,
            "Réinitialisation du mot de passe de l'utilisateur {$user->name}",
            null,
            ['password' => '***']
        );

        return redirect()->route('settings.edit', ['tab' => 'users'])->with('success', 'Mot de passe réinitialisé avec succès pour ' . $user->name . ' !');
    }
}
