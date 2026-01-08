<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('telephone', 'like', "%{$search}%");
        }

        $clients = $query->orderBy('nom')->orderBy('prenom')->get();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÀ-ÿ\'\- ]+$/'],
            'prenom' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÀ-ÿ\'\- ]+$/'],
            'telephone' => ['required', 'string', 'max:20', 'unique:clients,telephone', 'regex:/^\+?\d+$/'],
            'adresse' => 'nullable|string|max:255',
        ]);

        $data = $request->only('nom', 'prenom', 'telephone', 'adresse');
        $data['nom'] = strtoupper($data['nom']);
        $data['prenom'] = ucfirst(strtolower($data['prenom']));

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès !');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÀ-ÿ\'\- ]+$/'],
            'prenom' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÀ-ÿ\'\- ]+$/'],
            'telephone' => ['required', 'string', 'max:20', 'unique:clients,telephone,' . $client->id, 'regex:/^\+?\d+$/'],
            'adresse' => 'nullable|string|max:255',
        ]);

        $data = $request->only('nom', 'prenom', 'telephone', 'adresse');
        $data['nom'] = strtoupper($data['nom']);
        $data['prenom'] = ucfirst(strtolower($data['prenom']));

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Client modifié avec succès !');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client supprimé !');
    }
}
