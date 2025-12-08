# 🚂 Guide de Déploiement sur Railway.app

## Pourquoi Railway ?

Railway est la plateforme la plus simple pour déployer des applications Laravel :
- ✅ Configuration ultra-simple
- ✅ PostgreSQL en un clic
- ✅ Variables d'environnement faciles à gérer
- ✅ Déploiement automatique depuis GitHub
- ✅ Logs en temps réel
- ✅ Plan gratuit généreux ($5 de crédit/mois)

## 📋 Prérequis

- Compte GitHub avec ce repository
- Compte Railway (gratuit) : https://railway.app

## 🚀 Déploiement étape par étape

### Étape 1 : Créer un compte Railway

1. Allez sur https://railway.app
2. Cliquez sur "Start a New Project"
3. Connectez votre compte GitHub

### Étape 2 : Créer le projet

1. Cliquez sur "Deploy from GitHub repo"
2. Sélectionnez le repository `gestion_dette_laravel`
3. Railway détectera automatiquement le [Dockerfile](Dockerfile)

### Étape 3 : Ajouter PostgreSQL

1. Dans votre projet Railway, cliquez sur "+ New"
2. Sélectionnez "Database" → "PostgreSQL"
3. Railway créera automatiquement la base de données
4. Une variable `DATABASE_URL` sera automatiquement ajoutée à votre service

### Étape 4 : Configurer les variables d'environnement

Dans les paramètres de votre service, ajoutez ces variables :

#### Variables obligatoires

```bash
APP_NAME="Gestion Dette"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-app.up.railway.app

# Générer avec: php artisan key:generate --show
APP_KEY=base64:VOTRE_CLE_GENEREE_ICI

# Session & Cookie
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

# Logging
LOG_CHANNEL=stderr
LOG_LEVEL=info

# Database (Railway génère automatiquement DATABASE_URL)
# Mais on doit aussi spécifier DB_CONNECTION
DB_CONNECTION=pgsql
```

#### Variables optionnelles (Cloudinary)

Si vous utilisez Cloudinary pour le stockage d'images :

```bash
CLOUDINARY_CLOUD_NAME=votre_cloud_name
CLOUDINARY_API_KEY=votre_api_key
CLOUDINARY_API_SECRET=votre_api_secret
```

### Étape 5 : Générer APP_KEY localement

Sur votre machine locale, exécutez :

```bash
php artisan key:generate --show
```

Copiez la valeur générée (ex: `base64:xxxxx...`) et ajoutez-la comme variable `APP_KEY` sur Railway.

### Étape 6 : Configurer le domaine public

1. Dans les paramètres du service, section "Networking"
2. Cliquez sur "Generate Domain"
3. Railway créera un domaine comme `votre-app.up.railway.app`
4. Mettez à jour `APP_URL` avec ce domaine

### Étape 7 : Déployer

1. Railway déploiera automatiquement dès que vous ajoutez les variables
2. Suivez les logs en temps réel dans l'onglet "Deployments"
3. Le premier déploiement prend ~5-10 minutes

### Étape 8 : Vérifier le déploiement

Une fois déployé :

#### Test du health check

```bash
curl https://votre-app.up.railway.app/api/health
```

Réponse attendue :
```json
{
  "status": "ok",
  "service": "gestion-dette-app",
  "timestamp": "2025-11-28T..."
}
```

#### Test de la page d'accueil

Visitez `https://votre-app.up.railway.app`

Vous devriez être redirigé vers `/login`

#### Test de connexion

- Login : `cyundt`
- Mot de passe : `password`

(Voir [CONNEXION.md](CONNEXION.md) pour plus de détails)

## 🔧 Configuration avancée

### Automatiser les déploiements

Railway redéploie automatiquement à chaque push sur la branche `main`.

Pour désactiver les déploiements automatiques :
1. Settings → "Deploys"
2. Désactiver "Auto Deploy"

### Consulter les logs

Dans le dashboard Railway :
1. Sélectionnez votre service
2. Onglet "Deployments"
3. Cliquez sur un déploiement pour voir les logs

Logs utiles à surveiller :
- Build Docker (compilation des assets)
- Migrations de base de données
- Installation de Passport
- Démarrage Nginx/PHP-FPM

### Gérer la base de données

#### Accéder à la base de données

Dans le service PostgreSQL :
1. Onglet "Data"
2. Vous pouvez exécuter des requêtes SQL directement

#### Se connecter via psql (local)

Railway fournit les credentials dans les variables :

```bash
# Récupérer l'URL de connexion
# Dans Railway: PostgreSQL service → Connect → Connection URL

psql "postgresql://postgres:password@host:port/railway"
```

#### Créer un utilisateur admin

Si vous partez d'une base vide :

```bash
# Via Railway shell (dans le service web)
php artisan tinker
```

Puis :
```php
$user = new \App\Models\User();
$user->login = 'admin';
$user->password = bcrypt('password');
$user->role = 'Admin';
$user->save();
```

### Variables d'environnement Railway

Railway injecte automatiquement certaines variables :

- `PORT` - Port sur lequel l'app doit écouter (géré automatiquement par notre Dockerfile)
- `DATABASE_URL` - URL de connexion PostgreSQL (format: `postgresql://user:pass@host:port/db`)
- `RAILWAY_ENVIRONMENT` - Environnement (production, staging, etc.)

## 🐛 Dépannage

### Le build échoue

**Symptôme** : Le build Docker échoue pendant `npm run build`

**Solution** :
1. Vérifiez les logs de build
2. Assurez-vous que [package.json](package.json) est valide
3. Vérifiez que [vite.config.js](vite.config.js) est correct

### L'application crash au démarrage

**Symptôme** : Le service redémarre continuellement

**Causes probables** :

1. **APP_KEY manquant**
   - Générez avec `php artisan key:generate --show`
   - Ajoutez dans les variables Railway

2. **Base de données non connectée**
   - Vérifiez que PostgreSQL est créé
   - Vérifiez que `DATABASE_URL` existe

3. **Migrations échouent**
   - Consultez les logs
   - Vérifiez la connexion DB

### Le health check échoue

**Symptôme** : `/api/health` retourne 404 ou 500

**Solutions** :

1. Vérifiez que la route existe :
   ```bash
   # Dans Railway shell
   php artisan route:list | grep health
   ```

2. Vérifiez les logs Nginx
3. Assurez-vous que [HealthController.php](app/Http/Controllers/HealthController.php) existe

### Les assets CSS/JS ne chargent pas

**Symptôme** : La page s'affiche sans style

**Causes** :

1. **Vite manifest manquant**
   - Le build Vite a échoué
   - Vérifiez les logs de build

2. **APP_URL incorrect**
   - Mettez à jour avec le bon domaine Railway

3. **Mix public path**
   - Vérifiez [vite.config.js](vite.config.js)

## 📊 Surveillance et métriques

### Métriques Railway

Railway fournit automatiquement :
- Utilisation CPU
- Utilisation mémoire
- Bande passante réseau
- Nombre de requêtes

### Logs en temps réel

```bash
# Installer Railway CLI
npm i -g @railway/cli

# Login
railway login

# Voir les logs en temps réel
railway logs
```

### Alertes

Configurez des alertes dans Railway pour :
- Utilisation CPU > 80%
- Utilisation mémoire > 90%
- Crashs de l'application

## 💰 Tarification

### Plan gratuit

- $5 de crédit/mois
- Suffisant pour :
  - 1 service web (512 MB RAM)
  - 1 base PostgreSQL (256 MB)
  - ~500 heures d'exécution/mois

### Plan Pro ($20/mois)

- $20 de crédit inclus
- Support prioritaire
- Métriques avancées
- Snapshots de base de données

## 🔄 Workflow de développement

### Développement local

```bash
# 1. Démarrer l'application localement
php artisan serve

# 2. Compiler les assets
npm run dev
```

### Staging

1. Créez une branche `staging`
2. Créez un nouveau service Railway pour staging
3. Connectez-le à la branche `staging`

### Production

1. Push sur `main`
2. Railway déploie automatiquement
3. Surveillez les logs

## 📝 Checklist de déploiement

- [ ] Repository GitHub configuré
- [ ] Compte Railway créé
- [ ] Projet Railway créé depuis GitHub
- [ ] PostgreSQL ajouté au projet
- [ ] Variable `APP_KEY` générée et configurée
- [ ] Variable `APP_URL` configurée avec le domaine Railway
- [ ] Variables Cloudinary configurées (si nécessaire)
- [ ] Premier déploiement réussi
- [ ] Health check répond correctement
- [ ] Page de connexion accessible
- [ ] Connexion avec utilisateur test réussie

## 🔗 Ressources

- [Documentation Railway](https://docs.railway.app)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Docker Multi-stage Builds](https://docs.docker.com/build/building/multi-stage/)
- [PostgreSQL sur Railway](https://docs.railway.app/databases/postgresql)

## 📞 Support

### Railway

- Discord : https://discord.gg/railway
- Documentation : https://docs.railway.app
- Status : https://railway.statuspage.io

### Laravel

- Documentation : https://laravel.com/docs
- Forums : https://laracasts.com/discuss

---

**Dernière mise à jour** : 2025-11-28
**Statut** : Prêt pour le déploiement sur Railway ✅
