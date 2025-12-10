# Configuration Laravel Cloud - Gestion Dette

## 🔴 Problème: Erreur 401 Unauthorized lors du login

### Cause
Votre application utilise **Laravel Passport** pour l'authentification OAuth2. L'erreur 401 survient car Passport n'est pas correctement configuré sur Laravel Cloud.

## ✅ Solution: Configuration Passport sur Laravel Cloud

### Étape 1: Se connecter au terminal Laravel Cloud

Dans votre dashboard Laravel Cloud, ouvrez le terminal de votre application.

### Étape 2: Exécuter les commandes de configuration Passport

Exécutez ces commandes **dans l'ordre**:

```bash
# 1. Exécuter toutes les migrations (incluant les tables OAuth)
php artisan migrate --force

# 2. Installer Passport (génère les clés OAuth et crée les clients)
php artisan passport:install --force

# 3. Vérifier que les clés ont été créées
ls -la storage/*.key

# 4. Vérifier que les tables OAuth existent
php artisan db:table oauth_clients --count
php artisan db:table oauth_access_tokens --count
```

### Étape 3: Vider les caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Étape 4: Tester l'API

```bash
# Test 1: Health check
curl https://gestion-dette.laravel.cloud/api/health

# Test 2: Login (remplacez les credentials)
curl -X POST https://gestion-dette.laravel.cloud/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"login":"boutiquier","password":"votre_password"}'
```

Si le login fonctionne, vous devriez recevoir:
```json
{
  "status": 200,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  },
  "message": "Connexion reussie"
}
```

## 🔧 Alternative: Utiliser un script automatisé

Si vous redéployez souvent, vous pouvez ajouter ces commandes au hook de déploiement Laravel Cloud:

### Dans le Dashboard Laravel Cloud > Deployment Hooks

Ajoutez ceci au **post-deployment hook**:

```bash
#!/bin/bash
set -e

# Run migrations
php artisan migrate --force

# Setup Passport if not already done
if [ ! -f storage/oauth-private.key ]; then
    echo "Setting up Passport..."
    php artisan passport:install --force
fi

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📋 Variables d'environnement requises

Assurez-vous que ces variables sont configurées sur Laravel Cloud:

```env
APP_NAME="Gestion Dette"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... # Généré automatiquement
APP_URL=https://gestion-dette.laravel.cloud

# Base de données PostgreSQL Neon
DB_CONNECTION=pgsql
DATABASE_URL=postgresql://neondb_owner:npg_L2SzdOM3fRPG@ep-sparkling-dream-adoo4yue-pooler.c-2.us-east-1.aws.neon.tech/neondb?sslmode=require
DB_SSLMODE=require

# Frontend CORS
FRONTEND_URL=http://localhost:5173  # Ou l'URL de votre frontend en production

# Cloudinary
CLOUDINARY_CLOUD_NAME=dvlazryzt
CLOUDINARY_API_KEY=252382129819484
CLOUDINARY_API_SECRET=XroXJXVLJ1uYApyjNT8JvVfLC9U

# Session & Cache (recommandé pour production)
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

## 🐛 Dépannage

### Erreur: "The MAC is invalid"
**Solution**: Exécutez `php artisan passport:install --force` à nouveau

### Erreur: "Client authentication failed"
**Solution**: Vérifiez que les migrations OAuth ont bien été exécutées:
```bash
php artisan db:table oauth_clients
```

### Les tokens expirent trop vite
**Solution**: Ajoutez dans `config/passport.php`:
```php
'token_expiration' => 365, // 1 an
'refresh_token_expiration' => 730, // 2 ans
```

### Les clés OAuth sont perdues après redéploiement
**Solution**: Configurez Laravel Cloud pour persister le dossier `storage`:
- Dans le dashboard Laravel Cloud, assurez-vous que `storage` est dans les volumes persistants

## 🧪 Test complet du frontend

Une fois Passport configuré:

1. **Ouvrez votre frontend** (localhost:5173)
2. **Essayez de vous connecter** avec vos identifiants
3. **Vérifiez la console** du navigateur:
   - Si vous voyez un token dans la réponse → ✅ Succès
   - Si vous voyez toujours 401 → Vérifiez les logs Laravel Cloud

## 📝 Logs utiles

Pour voir les logs en temps réel sur Laravel Cloud:

```bash
# Logs Laravel
php artisan log:tail

# Ou via Laravel Cloud dashboard:
# Dashboard > Votre App > Logs
```

## 🎯 Checklist de déploiement Passport

- [ ] Migrations Passport exécutées (`php artisan migrate --force`)
- [ ] Clés OAuth générées (`ls storage/*.key`)
- [ ] Clients OAuth créés (`php artisan db:table oauth_clients`)
- [ ] Caches vidés/régénérés
- [ ] Test de login réussi (via curl ou Postman)
- [ ] Frontend peut se connecter
- [ ] Refresh token fonctionne
