<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $actif = $request->input('actif');

        $clients = Client::query()
            ->with('user.role')
            ->when($search, function ($query, $search) {
                $query->where('surname', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%")
                      ->orWhere('adresse', 'like', "%{$search}%");
            })
            ->when($actif !== null, function ($query) use ($actif) {
                $query->whereHas('user', function($q) use ($actif) {
                    $q->where('active', $actif);
                });
            })
            ->orderBy('surname')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Boutiquier/Clients/Index', [
            'clients' => $clients,
            'filters' => [
                'search' => $search,
                'actif' => $actif,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'surname' => 'required|string|max:255',
            'telephone' => 'required|string|max:20|unique:clients,telephone',
            'adresse' => 'nullable|string|max:500',
            'login' => 'required|string|max:255|unique:users,login',
            'password' => 'required|string|min:8',
        ], [
            'surname.required' => 'Le nom est obligatoire',
            'telephone.required' => 'Le téléphone est obligatoire',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé',
            'login.required' => 'Le login est obligatoire',
            'login.unique' => 'Ce login est déjà utilisé',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
        ]);

        DB::beginTransaction();
        try {
            // Get Client role
            $clientRole = Role::where('libelle', 'Client')->first();

            // Create user
            $user = User::create([
                'nom' => $validated['surname'],
                'prenom' => '',
                'login' => $validated['login'],
                'password' => Hash::make($validated['password']),
                'roleId' => $clientRole->id,
                'active' => 'oui',
            ]);

            // Create client
            Client::create([
                'surname' => $validated['surname'],
                'telephone' => $validated['telephone'],
                'adresse' => $validated['adresse'] ?? '',
                'user_id' => $user->id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Client créé avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la création du client');
        }
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'surname' => 'required|string|max:255',
            'telephone' => 'required|string|max:20|unique:clients,telephone,' . $client->id,
            'adresse' => 'nullable|string|max:500',
            'active' => 'required|boolean',
        ], [
            'surname.required' => 'Le nom est obligatoire',
            'telephone.required' => 'Le téléphone est obligatoire',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé',
            'active.required' => 'Le statut est obligatoire',
        ]);

        DB::beginTransaction();
        try {
            $client->update([
                'surname' => $validated['surname'],
                'telephone' => $validated['telephone'],
                'adresse' => $validated['adresse'] ?? '',
            ]);

            $client->user->update([
                'nom' => $validated['surname'],
                'active' => $validated['active'] ? 'oui' : 'non',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Client mis à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du client');
        }
    }

    public function show(Client $client)
    {
        $client->load(['user.role', 'dettes' => function($q) {
            $q->orderBy('date', 'desc');
        }, 'dettes.paiements']);

        $stats = [
            'totalDettes' => $client->dettes->sum('montant'),
            'totalPaye' => $client->dettes->sum('montantDu'),
            'totalRestant' => $client->dettes->sum('montantRestant'),
            'dettesEnCours' => $client->dettes->where('status', '!=', 'Solde')->count(),
            'dettesSoldees' => $client->dettes->where('status', 'Solde')->count(),
        ];

        return Inertia::render('Boutiquier/Clients/Show', [
            'client' => $client,
            'stats' => $stats,
        ]);
    }
}
