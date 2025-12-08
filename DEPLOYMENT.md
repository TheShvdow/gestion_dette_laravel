# 🚀 Guide de Déploiement sur Render.com

## État actuel du projet

Votre application Laravel est **prête pour le déploiement** sur Render.com. Tous les fichiers de configuration nécessaires sont en place.

## 📋 Fichiers de configuration

### 1. [render.yaml](render.yaml)
Fichier de configuration Blueprint pour Render.com :
- Service web configuré avec Docker
- Base de données PostgreSQL (plan gratuit)
- Health check endpoint : `/api/health`
- Variables d'environnement pré-configurées

### 2. [Dockerfile](Dockerfile)
Build multi-stage optimisé :
- Compilation des assets Vue.js/Vite
- Runtime PHP 8.2 avec PHP-FPM
- Nginx comme serveur web
- Extensions PHP nécessaires (PostgreSQL, GD, etc.)

### 3. [docker/start.sh](docker/start.sh)
Script de démarrage qui :
- Configure Nginx sur le port dynamique de Render
- Attend que la base de données soit prête
- Exécute les migrations automatiquement
- Installe Laravel Passport
- Configure les caches Laravel
- Démarre Supervisor (Nginx + PHP-FPM)

### 4. Routes configurées
- **API Health Check** : [routes/api.php:23-29](routes/api.php#L23-L29)
- **Routes Web** : [routes/web.php](routes/web.php)

## 🎯 Étapes de déploiement

### Option A : Déploiement via Blueprint (recommandé)

1. **Connectez-vous à Render.com**
   - Allez sur https://render.com
   - Connectez votre compte GitHub

2. **Créez un nouveau Blueprint**
   - Cliquez sur "New" → "Blueprint"
   - Sélectionnez ce repository
   - Render détectera automatiquement le fichier `render.yaml`

3. **Configurez les variables d'environnement sensibles**

   Ajoutez ces variables dans le dashboard Render :

   ```
   CLOUDINARY_CLOUD_NAME=votre_cloud_name
   CLOUDINARY_API_KEY=votre_api_key
   CLOUDINARY_API_SECRET=votre_api_secret
   ```

4. **Déployez**
   - Cliquez sur "Apply" pour créer les ressources
   - Render créera automatiquement :
     - Le service web
     - La base de données PostgreSQL
     - Les connexions entre les services

### Option B : Déploiement manuel

Si vous préférez créer les ressources manuellement :

#### 1. Créer la base de données PostgreSQL

```bash
Nom : gestion-dette-db
Plan : Free
Database : gestion_dette
User : dette_user
```

#### 2. Créer le Web Service

```bash
Name : gestion-dette-app
Runtime : Docker
Branch : main
Dockerfile Path : ./Dockerfile
Health Check Path : /api/health
Auto-Deploy : Yes
```

#### 3. Configurer les variables d'environnement

Ajoutez toutes les variables du fichier [render.yaml:12-65](render.yaml#L12-L65) dans le dashboard.

## ✅ Vérification du déploiement

Une fois déployé, votre application sera accessible à :
```
https://gestion-dette-app.onrender.com
```

### Tests de santé

1. **Health Check**
   ```bash
   curl https://gestion-dette-app.onrender.com/api/health
   ```

   Réponse attendue :
   ```json
   {
     "status": "ok",
     "service": "gestion-dette-app",
     "timestamp": "2025-01-27T..."
   }
   ```

2. **Page de connexion**
   - Accédez à : `https://gestion-dette-app.onrender.com`
   - Vous devriez être redirigé vers `/login`

3. **Connexion test**
   - Login : `cyundt`
   - Mot de passe : `password`
   - Voir [CONNEXION.md](CONNEXION.md) pour plus de détails

## 🔧 Configuration post-déploiement

### 1. Créer le premier utilisateur

Si vous partez d'une base vide, connectez-vous via le shell Render :

```bash
php artisan tinker
```

Puis créez un utilisateur :

```php
$user = new \App\Models\User();
$user->login = 'admin';
$user->password = bcrypt('password');
$user->role = 'Admin';
$user->save();
```

### 2. Passport OAuth2

Laravel Passport est installé automatiquement au démarrage. Les clés sont générées dans `/var/www/html/storage`.

## 📊 Surveillance

### Logs

Consultez les logs dans le dashboard Render :
- Logs de démarrage (migrations, Passport, etc.)
- Logs Nginx
- Logs PHP-FPM
- Logs Laravel

### Métriques

Render fournit automatiquement :
- CPU usage
- Memory usage
- Request metrics
- Health check status

## 🔄 Mises à jour

### Déploiement automatique

Le déploiement automatique est activé (`autoDeploy: true`). Chaque push sur la branche `main` déclenchera un nouveau déploiement.

### Déploiement manuel

Dans le dashboard Render :
1. Allez sur votre service
2. Cliquez sur "Manual Deploy"
3. Sélectionnez la branche à déployer

## 🐛 Dépannage

### Le health check échoue

1. Vérifiez les logs de démarrage
2. Assurez-vous que le port est bien configuré (variable `PORT`)
3. Vérifiez que la route `/api/health` est bien définie

### Erreurs de base de données

1. Vérifiez que la base de données est bien connectée au service web
2. Consultez les logs de migration
3. Vérifiez les variables d'environnement `DB_*`

### Assets manquants (CSS/JS)

1. Vérifiez que le build Vite s'est bien terminé (logs)
2. Assurez-vous que `public/build` existe dans le container
3. Vérifiez les permissions sur `/var/www/html/public`

### Erreurs Passport

Si Laravel Passport ne s'installe pas :
1. Vérifiez les logs de `passport:install`
2. Les clés doivent être dans `/var/www/html/storage`
3. Permissions : `www-data:www-data`

## 📝 Notes importantes

### Plan gratuit Render

Le plan gratuit a des limitations :
- Le service s'endort après 15 minutes d'inactivité
- Premier démarrage peut prendre 30-60 secondes
- 750 heures/mois (suffisant pour un projet de test)
- Base de données PostgreSQL 1 GB (limitée, expire après 90 jours)

### Migration vers plan payant

Si vous avez besoin de plus de ressources :
1. Allez dans les paramètres du service
2. Changez le plan (à partir de $7/mois)
3. Pour la base de données : à partir de $7/mois pour 256 MB

## 🔗 Ressources

- [Documentation Render](https://render.com/docs)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Laravel Passport](https://laravel.com/docs/passport)
- [Inertia.js](https://inertiajs.com)

## 📞 Support

Pour toute question ou problème :
1. Consultez les logs Render
2. Vérifiez ce guide de déploiement
3. Consultez [CONNEXION.md](CONNEXION.md) pour les infos de test local

---

**Dernière mise à jour** : 2025-01-27
**Statut** : Prêt pour le déploiement ✅
