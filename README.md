# 💰 Gestion Dette - Backend API

API REST Laravel pour la gestion de dettes commerciales avec authentification Sanctum et PostgreSQL.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16.x-blue.svg)

---

## 🎯 Présentation

Backend API REST complet pour un système de gestion de dettes destiné aux boutiques. Gestion des clients, articles, dettes et paiements avec authentification sécurisée Laravel Sanctum.

---

## ✨ Fonctionnalités

### 🔐 Authentification
- ✅ Login/Logout avec Sanctum
- ✅ Tokens API sécurisés  
- ✅ Gestion des rôles (Admin, Boutiquier, Client)

### 👥 Clients
- ✅ CRUD complet
- ✅ Recherche et filtrage
- ✅ Association compte utilisateur

### 📦 Articles  
- ✅ Gestion du stock
- ✅ Mise à jour automatique

### 💳 Dettes
- ✅ Création multi-articles
- ✅ Paiement initial optionnel
- ✅ Validation du stock

### 💰 Paiements
- ✅ Historique complet
- ✅ Mise à jour automatique du solde

---

## 🛠 Stack

- **Framework:** Laravel 10.x
- **Base de données:** PostgreSQL 16 (Neon)
- **Auth:** Laravel Sanctum
- **Hébergement:** Laravel Cloud

---

## 🚀 Installation

```bash
# Cloner
git clone https://github.com/votre-username/gestion_dette_laravel.git
cd gestion_dette_laravel

# Installer
composer install
cp .env.example .env
php artisan key:generate

# Configurer DB dans .env
# DB_CONNECTION=pgsql
# DB_HOST=your-host.neon.tech

# Migrer
php artisan migrate
php artisan db:seed

# Démarrer
php artisan serve
```

---

## 📡 Endpoints

```http
POST   /api/v1/login
GET    /api/v1/clients
POST   /api/v1/dettes
GET    /api/v1/dashboard
```

Documentation complète: `https://gestion-dette.laravel.cloud/api/documentation`

---

## 🔐 Credentials

| Rôle | Login | Password |
|------|-------|----------|
| Boutiquier | boutiquier | passer123 |
| Admin | admin | Admin@2024 |

---

## 🌐 Déploiement

Laravel Cloud déploie automatiquement depuis `main`.

Variables d'environnement requises:
- `DB_*` (Neon Database)
- `APP_KEY`, `APP_URL`
- `FRONTEND_URL`

---

**Version:** 1.0.0 | **Auteur:** TheShvdow | **Licence:** MIT
