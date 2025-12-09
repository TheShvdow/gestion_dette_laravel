# 🔌 Gestion Dette - Backend API

Laravel REST API pour l'application de gestion de dettes.

## 🏗️ Architecture

Ce projet est le **backend API uniquement**. Le frontend est une application Vue.js séparée.

```
├── Backend API (ce projet)
│   ├── Laravel 10
│   ├── PostgreSQL
│   └── Laravel Passport (OAuth2)
│
└── Frontend SPA (projet séparé)
    ├── Vue 3 + Vite
    ├── TailwindCSS
    └── Chart.js
```

## 🚀 Démarrage rapide

### Prérequis

- PHP 8.2+
- PostgreSQL
- Composer

### Installation locale

```bash
# Cloner le repository
git clone <url>
cd gestion_dette_laravel

# Installer les dépendances
composer install

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Configurer la base de données dans .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gestion_dette
DB_USERNAME=votre_user
DB_PASSWORD=votre_password

# Exécuter les migrations
php artisan migrate

# Installer Laravel Passport
php artisan passport:install

# Démarrer le serveur
php artisan serve
```

L'API sera disponible sur http://localhost:8000

## 📡 Endpoints API

### Authentication

- `POST /api/v1/login` - Connexion (retourne access_token)
- `POST /api/v1/logout` - Déconnexion
- `POST /api/v1/register` - Inscription (Boutiquier uniquement)
- `POST /api/v1/refresh-token` - Rafraîchir le token

### Clients (Boutiquier)

- `GET /api/v1/clients` - Liste des clients
- `POST /api/v1/clients` - Créer un client
- `GET /api/v1/clients/{id}` - Détails d'un client
- `POST /api/v1/clients/{id}/dettes` - Dettes d'un client

### Articles (Boutiquier)

- `GET /api/v1/articles` - Liste des articles
- `POST /api/v1/articles` - Créer un article
- `GET /api/v1/articles/{id}` - Détails d'un article
- `PATCH /api/v1/articles/{id}` - Mettre à jour le stock
- `DELETE /api/v1/articles/{id}` - Supprimer un article

### Dettes

- `GET /api/v1/dettes` - Liste des dettes
- `POST /api/v1/dettes` - Créer une dette (Boutiquier)
- `GET /api/v1/dettes/{id}` - Détails d'une dette
- `GET /api/v1/dettes/{id}/paiements` - Paiements d'une dette
- `POST /api/v1/dettes/{id}/paiement` - Ajouter un paiement (Boutiquier)

### Utilisateurs (Admin)

- `GET /api/v1/users` - Liste des utilisateurs
- `POST /api/v1/users` - Créer un utilisateur

### Health Check

- `GET /api/health` - Vérifier l'état de l'API

## 🔐 Authentification

L'API utilise **Laravel Passport** (OAuth2).

### Flow d'authentification

1. **Login** : `POST /api/v1/login`
   ```json
   {
     "login": "username",
     "password": "password"
   }
   ```

   Réponse :
   ```json
   {
     "access_token": "eyJ0eXAiOiJKV1QiLCJh...",
     "token_type": "Bearer",
     "expires_in": 31536000
   }
   ```

2. **Utiliser le token** : Ajouter dans les headers
   ```
   Authorization: Bearer eyJ0eXAiOiJKV1QiLCJh...
   ```

3. **Rafraîchir le token** : `POST /api/v1/refresh-token`

## 🌍 CORS

Le backend accepte les requêtes depuis :

- `http://localhost:5173` (Vite dev)
- `http://localhost:3000` (dev alternatif)
- Votre frontend en production (configurer `FRONTEND_URL` dans `.env`)
- Déploiements Vercel (`*.vercel.app`)
- Déploiements Netlify (`*.netlify.app`)

Pour ajouter d'autres origines, modifiez `config/cors.php`.

## 🐳 Déploiement Docker

Le projet inclut un Dockerfile optimisé pour le déploiement.

### Variables d'environnement requises

```bash
APP_KEY=base64:xxx           # Générer avec: php artisan key:generate --show
APP_URL=https://api.example.com
DB_CONNECTION=pgsql
DATABASE_URL=postgresql://...  # Ou configurez DB_HOST, DB_DATABASE, etc.
FRONTEND_URL=https://app.example.com
```

---

**Version** : 1.0.0 | **Backend API uniquement**
