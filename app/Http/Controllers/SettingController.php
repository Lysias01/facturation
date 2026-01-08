<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Afficher la page de paramètres
     */
    public function edit()
    {
        $settings = Setting::first();
        return view('settings.edit', compact('settings'));
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

        $settings->company_name = $request->company_name;
        $settings->phone = $request->phone;
        $settings->address = $request->address;
        $settings->email = $request->email;
        $settings->ifu = $request->ifu;
        $settings->rccm = $request->rccm;
        $settings->country = $request->country;
        $settings->national_motto = $request->national_motto;

        // Upload du logo
        if ($request->hasFile('logo')) {
            if ($settings->logo && Storage::exists('public/'.$settings->logo)) {
                Storage::delete('public/'.$settings->logo);
            }
            $path = $request->file('logo')->store('settings', 'public');
            $settings->logo = $path;
        }

        $settings->save();

        Cache::forget('app_settings');

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès !');
    }

    /**
     * reinitialiser les paramètre 
     */
    public function reset()
    {
        $settings = Setting::first();

        if ($settings) {
            if ($settings->logo && Storage::exists('public/'.$settings->logo)) {
                Storage::delete('public/'.$settings->logo);
            }

            $settings->delete();
        }

        Cache::forget('app_settings');

        return redirect()->back()->with('success', 'Paramètres réinitialisés avec succès.');
    }


}
