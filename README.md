# Gestion Dette - API Laravel

Application de gestion de dettes avec Laravel 10, PostgreSQL (Neon) et authentification JWT (Passport).

## 🚀 Stack Technique

- **Backend** : Laravel 10.x
- **Base de données** : PostgreSQL 16 (Neon Database)
- **Authentification** : Laravel Passport (OAuth2/JWT)
- **Déploiement** : Docker (Render)

---

## ⚙️ Installation Locale

### 1. Cloner et installer

```bash
git clone https://github.com/votre-username/gestion_dette_laravel.git
cd gestion_dette_laravel
composer install
```

### 2. Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Éditez `.env` avec vos credentials Neon :
```env
DB_CONNECTION=pgsql
DB_HOST=votre-host.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=votre_password
DB_SSLMODE=require
```

### 3. Base de données

```bash
php artisan migrate
php artisan passport:install
php artisan db:seed --class=InitialDataSeeder
```

### 4. Démarrer

```bash
php artisan serve
# API : http://127.0.0.1:8000
```

---

## 🧪 Tester l'API

```bash
bash test-api.sh
```

---

## 🔐 Credentials

**Admin** : `admin` / `Admin@2024`
**Boutiquier** : `boutiquier` / `Boutiquier@2024`

---

## 🚀 Déploiement

### Koyeb (Recommandé - 100% Gratuit)

```bash
bash deploy-koyeb.sh "Deploy to Koyeb"
```

- ✅ **100% Gratuit** (pas de CB)
- ✅ Pas de sleep
- ✅ SSL automatique

Documentation : [DEPLOIEMENT_KOYEB.md](DEPLOIEMENT_KOYEB.md)

### Render (Payant)

```bash
bash deploy.sh "Deploy to Render"
```

Documentation : [DEPLOIEMENT_RENDER.md](DEPLOIEMENT_RENDER.md)

---

## 📡 Endpoints Principaux

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | `/api/v1/login` | Connexion |
| GET | `/api/v1/dashboard` | Statistiques |
| GET | `/api/v1/clients` | Liste clients |
| GET | `/api/v1/articles` | Liste articles |
| GET | `/api/v1/dettes` | Liste dettes |

---

## 🔧 Dépannage

Voir [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

**Version** : 1.0.0
