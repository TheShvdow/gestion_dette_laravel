# 🗃️ Initialisation des Données

Guide pour créer les données initiales après le déploiement sur Render.

## 📝 Accéder au Shell Render

1. Allez sur https://dashboard.render.com
2. Sélectionnez votre service **gestion-dette-api**
3. Cliquez sur **Shell** dans le menu de gauche
4. Une console s'ouvre directement dans le container

## 1️⃣ Créer les Rôles (Obligatoire)

Les rôles doivent exister avant de créer des utilisateurs.

```php
php artisan tinker

// Dans tinker
use App\Models\Role;

// Créer le rôle Admin
$admin = new Role();
$admin->libelle = 'Admin';
$admin->save();

// Créer le rôle Boutiquier
$boutiquier = new Role();
$boutiquier->libelle = 'Boutiquier';
$boutiquier->save();

// Créer le rôle Client
$client = new Role();
$client->libelle = 'Client';
$client->save();

// Vérifier
Role::all();

exit
```

## 2️⃣ Créer un Utilisateur Admin

```php
php artisan tinker

use App\Models\User;
use App\Models\Role;

// Récupérer le rôle Admin
$adminRole = Role::where('libelle', 'Admin')->first();

// Créer l'utilisateur admin
$admin = new User();
$admin->nom = 'Super';
$admin->prenom = 'Admin';
$admin->login = 'admin';
$admin->email = 'admin@gestion-dette.com';
$admin->password = bcrypt('Admin@2024');
$admin->role_id = $adminRole->id;
$admin->active = true;
$admin->save();

exit
```

## 3️⃣ Créer un Boutiquier

```php
php artisan tinker

use App\Models\User;
use App\Models\Role;

$boutiquierRole = Role::where('libelle', 'Boutiquier')->first();

$boutiquier = new User();
$boutiquier->nom = 'Diallo';
$boutiquier->prenom = 'Amadou';
$boutiquier->login = 'boutiquier';
$boutiquier->email = 'boutiquier@gestion-dette.com';
$boutiquier->password = bcrypt('Boutiquier@2024');
$boutiquier->role_id = $boutiquierRole->id;
$boutiquier->active = true;
$boutiquier->save();

exit
```

## 4️⃣ Créer des Clients de Test

```php
php artisan tinker

use App\Models\Client;
use App\Models\User;
use App\Models\Role;

$clientRole = Role::where('libelle', 'Client')->first();

// Client 1 : avec compte utilisateur
$user1 = new User();
$user1->nom = 'Ndiaye';
$user1->prenom = 'Fatou';
$user1->login = 'fatou.ndiaye';
$user1->email = 'fatou@example.com';
$user1->password = bcrypt('Client@2024');
$user1->role_id = $clientRole->id;
$user1->active = true;
$user1->save();

$client1 = new Client();
$client1->surname = 'Fatou Ndiaye';
$client1->telephone = '771234567';
$client1->adresse = 'Dakar, Parcelles Assainies';
$client1->user_id = $user1->id;
$client1->save();

// Client 2 : sans compte utilisateur
$client2 = new Client();
$client2->surname = 'Moussa Sow';
$client2->telephone = '775678901';
$client2->adresse = 'Thiès, Cité Malick Sy';
$client2->save();

// Client 3 : avec compte utilisateur
$user3 = new User();
$user3->nom = 'Fall';
$user3->prenom = 'Awa';
$user3->login = 'awa.fall';
$user3->email = 'awa@example.com';
$user3->password = bcrypt('Client@2024');
$user3->role_id = $clientRole->id;
$user3->active = true;
$user3->save();

$client3 = new Client();
$client3->surname = 'Awa Fall';
$client3->telephone = '779876543';
$client3->adresse = 'Saint-Louis, Sor';
$client3->user_id = $user3->id;
$client3->save();

exit
```

## 5️⃣ Créer des Articles

```php
php artisan tinker

use App\Models\Article;

// Article 1
$article1 = new Article();
$article1->libelle = 'Riz brisé 50kg';
$article1->prix = 25000;
$article1->quantite = 100;
$article1->save();

// Article 2
$article2 = new Article();
$article2->libelle = 'Huile 5L';
$article2->prix = 8500;
$article2->quantite = 50;
$article2->save();

// Article 3
$article3 = new Article();
$article3->libelle = 'Sucre 1kg';
$article3->prix = 850;
$article3->quantite = 200;
$article3->save();

// Article 4
$article4 = new Article();
$article4->libelle = 'Lait en poudre';
$article4->prix = 3500;
$article4->quantite = 30;
$article4->save();

// Article 5
$article5 = new Article();
$article5->libelle = 'Café Touba';
$article5->prix = 2000;
$article5->quantite = 0; // Rupture de stock
$article5->save();

exit
```

## 6️⃣ Créer des Dettes de Test

```php
php artisan tinker

use App\Models\Dette;
use App\Models\Client;
use App\Models\Article;

// Récupérer les clients et articles
$client1 = Client::first();
$riz = Article::where('libelle', 'LIKE', '%Riz%')->first();
$huile = Article::where('libelle', 'LIKE', '%Huile%')->first();
$sucre = Article::where('libelle', 'LIKE', '%Sucre%')->first();

// Dette 1 : Non soldée
$dette1 = new Dette();
$dette1->montant = 50000;
$dette1->montantDu = 50000;
$dette1->montantRestant = 50000;
$dette1->client_id = $client1->id;
$dette1->save();

// Attacher les articles
$dette1->articles()->attach($riz->id, [
    'quantite' => 2,
    'prix' => $riz->prix
]);
$dette1->articles()->attach($huile->id, [
    'quantite' => 1,
    'prix' => $huile->prix
]);

// Dette 2 : Partiellement payée
$client2 = Client::skip(1)->first();
$dette2 = new Dette();
$dette2->montant = 35000;
$dette2->montantDu = 35000;
$dette2->montantRestant = 15000; // 20000 déjà payés
$dette2->client_id = $client2->id;
$dette2->save();

$dette2->articles()->attach($riz->id, [
    'quantite' => 1,
    'prix' => $riz->prix
]);
$dette2->articles()->attach($sucre->id, [
    'quantite' => 10,
    'prix' => $sucre->prix
]);

exit
```

## 7️⃣ Vérifier les Données

```php
php artisan tinker

use App\Models\{Role, User, Client, Article, Dette};

// Compter les enregistrements
echo "Rôles: " . Role::count() . "\n";
echo "Utilisateurs: " . User::count() . "\n";
echo "Clients: " . Client::count() . "\n";
echo "Articles: " . Article::count() . "\n";
echo "Dettes: " . Dette::count() . "\n";

// Vérifier un utilisateur
$admin = User::where('login', 'admin')->first();
echo "Admin : " . $admin->nom . " " . $admin->prenom . "\n";
echo "Rôle : " . $admin->role->libelle . "\n";

exit
```

## 🧪 Tester l'API

### 1. Login Admin

```bash
export API_URL="https://gestion-dette-api.onrender.com"

curl -X POST $API_URL/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "login": "admin",
    "password": "Admin@2024"
  }'
```

**Réponse attendue** :
```json
{
  "status": 200,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "nom": "Super",
      "prenom": "Admin",
      "login": "admin",
      "role": {
        "libelle": "Admin"
      }
    }
  },
  "message": "Connexion réussie"
}
```

### 2. Récupérer le Token

```bash
# Copier le token de la réponse
export TOKEN="eyJ0eXAiOiJKV1QiLCJhbGc..."
```

### 3. Tester les Endpoints

```bash
# Liste des clients (Boutiquier)
curl $API_URL/api/v1/clients \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

# Liste des articles
curl $API_URL/api/v1/articles \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

# Liste des dettes
curl $API_URL/api/v1/dettes \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

# Dashboard
curl $API_URL/api/v1/dashboard \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

## 📋 Script Complet (Seeder)

Pour automatiser, créez un seeder :

```bash
php artisan make:seeder InitialDataSeeder
```

Puis dans `database/seeders/InitialDataSeeder.php` :

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Role, User, Client, Article, Dette};
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles
        $roles = [
            ['libelle' => 'Admin'],
            ['libelle' => 'Boutiquier'],
            ['libelle' => 'Client'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }

        // Créer l'admin
        $adminRole = Role::where('libelle', 'Admin')->first();
        User::firstOrCreate(
            ['login' => 'admin'],
            [
                'nom' => 'Super',
                'prenom' => 'Admin',
                'email' => 'admin@gestion-dette.com',
                'password' => Hash::make('Admin@2024'),
                'role_id' => $adminRole->id,
                'active' => true,
            ]
        );

        // Créer le boutiquier
        $boutiquierRole = Role::where('libelle', 'Boutiquier')->first();
        User::firstOrCreate(
            ['login' => 'boutiquier'],
            [
                'nom' => 'Diallo',
                'prenom' => 'Amadou',
                'email' => 'boutiquier@gestion-dette.com',
                'password' => Hash::make('Boutiquier@2024'),
                'role_id' => $boutiquierRole->id,
                'active' => true,
            ]
        );

        // Créer des articles
        $articles = [
            ['libelle' => 'Riz brisé 50kg', 'prix' => 25000, 'quantite' => 100],
            ['libelle' => 'Huile 5L', 'prix' => 8500, 'quantite' => 50],
            ['libelle' => 'Sucre 1kg', 'prix' => 850, 'quantite' => 200],
            ['libelle' => 'Lait en poudre', 'prix' => 3500, 'quantite' => 30],
            ['libelle' => 'Café Touba', 'prix' => 2000, 'quantite' => 0],
        ];

        foreach ($articles as $article) {
            Article::firstOrCreate(['libelle' => $article['libelle']], $article);
        }

        echo "✅ Données initiales créées avec succès!\n";
    }
}
```

Puis exécuter :

```bash
php artisan db:seed --class=InitialDataSeeder
```

## 🔐 Informations de Connexion

Après l'initialisation, voici les comptes disponibles :

### Admin
- **Login** : `admin`
- **Password** : `Admin@2024`
- **Accès** : Tous les endpoints

### Boutiquier
- **Login** : `boutiquier`
- **Password** : `Boutiquier@2024`
- **Accès** : Clients, Articles, Dettes

### Clients (exemples)
- **Login** : `fatou.ndiaye`
- **Password** : `Client@2024`

- **Login** : `awa.fall`
- **Password** : `Client@2024`

---

**⚠️ IMPORTANT** : Changez ces mots de passe en production !
