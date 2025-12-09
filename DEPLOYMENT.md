# 🚀 Guide de Déploiement sur Render

## Prérequis

- Un compte Render (gratuit) : https://render.com
- Git repository connecté à GitHub/GitLab
- Dockerfile configuré ✅
- render.yaml configuré ✅

## Architecture de Déploiement

Votre application sera déployée avec :
- **Service Web** : Laravel API (Docker) sur port 8080
- **Base de données** : PostgreSQL 15 (Frankfurt)
- **Healthcheck** : `/api/health`
- **Plan** : Starter (gratuit pendant 90 jours)

## Étape 1 : Préparer le repository

### 1.1 Vérifier les fichiers nécessaires

```bash
# Ces fichiers doivent être présents
ls -la Dockerfile
ls -la render.yaml
ls -la docker/nginx.conf
ls -la docker/supervisord.conf
ls -la docker/start.sh
```

### 1.2 Commit et push sur GitHub/GitLab

```bash
git add .
git commit -m "chore: Add Render deployment configuration"
git push origin main
```

## Étape 2 : Créer le projet sur Render

### 2.1 Via le Dashboard (Recommandé)

1. **Connectez-vous à Render** : https://dashboard.render.com
2. **Cliquez sur "New +"** → "Blueprint"
3. **Connectez votre repository Git**
4. **Render détectera automatiquement le fichier `render.yaml`**
5. **Cliquez sur "Apply"**

### 2.2 Via le fichier render.yaml (Automatique)

Render créera automatiquement :
- Base de données PostgreSQL : `gestion-dette-db`
- Service Web : `gestion-dette-api`
- Variables d'environnement configurées

## Étape 3 : Variables d'environnement supplémentaires

Après le déploiement initial, ajoutez ces variables dans le Dashboard :

1. Allez dans **Environment** de votre service
2. Ajoutez les variables suivantes :

### Variables optionnelles mais recommandées

```env
# CORS - Mettez l'URL de votre frontend
CORS_ALLOWED_ORIGINS=https://votre-frontend.vercel.app,https://votre-frontend.netlify.app

# Mail (si vous utilisez les emails)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@gestion-dette.com
MAIL_FROM_NAME="${APP_NAME}"

# Cloudinary (si vous utilisez le stockage d'images)
CLOUDINARY_URL=cloudinary://your_key:your_secret@your_cloud_name
CLOUDINARY_UPLOAD_PRESET=your_preset
```

## Étape 4 : Vérifier le déploiement

### 4.1 Attendre le build

Le build prendra environ **5-10 minutes** la première fois :

1. **Build de l'image Docker** (3-5 min)
2. **Démarrage de la base de données** (1-2 min)
3. **Migrations** (30 sec)
4. **Installation de Passport** (1 min)

### 4.2 Vérifier les logs

Dans le Dashboard → **Logs**, vous devriez voir :

```
==========================================
Starting Laravel Application
==========================================
Configuring Nginx to listen on port 10000...
Waiting for database connection...
Database connection established!
Running database migrations...
Installing Laravel Passport...
Optimizing application...
Checking /health route...
Testing /health endpoint...
==========================================
Application ready! Starting services...
==========================================
```

### 4.3 Tester l'API

```bash
# Remplacez par votre URL Render
export API_URL="https://gestion-dette-api.onrender.com"

# Test du health endpoint
curl $API_URL/api/health

# Réponse attendue :
# {"status":"ok","service":"gestion-dette-app","timestamp":"2024-01-..."}

# Test de l'authentification
curl -X POST $API_URL/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "login": "admin",
    "password": "password"
  }'
```

## Étape 5 : Configuration du Frontend

Mettez à jour votre frontend pour pointer vers l'API Render :

```javascript
// .env ou .env.production
VITE_API_BASE_URL=https://gestion-dette-api.onrender.com/api/v1
```

## 📊 Monitoring et Maintenance

### Logs en temps réel

```bash
# Dans le Dashboard Render
Events → Live Logs
```

### Base de données

1. **Accéder à la base de données** :
   - Dashboard → Database → Connect
   - Utilisez le **Internal Database URL** pour les connexions internes
   - Utilisez le **External Database URL** pour les clients SQL externes

2. **Connexion via psql** :
```bash
psql postgres://user:password@host:5432/database_name
```

### Scaling

Pour augmenter les performances :
1. Dashboard → Service → Settings
2. **Instance Type** : Passer de Starter à Standard
3. **Auto-scaling** : Configurer les règles

## 🔧 Troubleshooting

### Erreur : "Health check failed"

```bash
# Vérifiez que la route /health existe
grep -r "health" routes/api.php

# Vérifiez les logs de Nginx
# Dashboard → Logs → Filter "nginx"
```

### Erreur : "Database connection failed"

```bash
# Vérifiez les variables d'environnement
# Dashboard → Environment → Vérifier DB_HOST, DB_PORT, etc.

# Testez la connexion manuellement
php artisan migrate:status
```

### Erreur : "Passport keys not found"

```bash
# Les clés Passport sont générées automatiquement au démarrage
# Vérifiez les logs :
grep -i "passport" logs

# Si nécessaire, redéployez :
# Dashboard → Manual Deploy → Deploy latest commit
```

### Erreur : "500 Internal Server Error"

```bash
# Activez temporairement le debug
# Environment → APP_DEBUG=true
# Puis redéployez

# Consultez les logs Laravel
# Dashboard → Logs → Filter "error"
```

## 🔄 Redéploiement

### Redéploiement automatique

Chaque fois que vous poussez sur la branche `main`, Render redéploie automatiquement.

```bash
git add .
git commit -m "feat: nouvelle fonctionnalité"
git push origin main
# Render détecte le push et redéploie automatiquement
```

### Redéploiement manuel

1. Dashboard → Service → Manual Deploy
2. Cliquez sur **"Deploy latest commit"**
3. Ou sélectionnez un commit spécifique

### Rollback

1. Dashboard → Service → Deploys
2. Trouvez le déploiement fonctionnel
3. Cliquez sur **"Rollback to this deploy"**

## 💰 Coûts

### Plan Starter (Gratuit 90 jours)
- ✅ 512 MB RAM
- ✅ 0.5 CPU
- ✅ PostgreSQL inclus (1 GB)
- ❌ Sleep après 15 min d'inactivité

### Plan Standard ($7/mois)
- ✅ 2 GB RAM
- ✅ 1 CPU
- ✅ Pas de sleep
- ✅ Auto-scaling

### Optimisations pour rester gratuit

1. **Utiliser le plan Free** pour le développement
2. **Passer à Starter** pour la production
3. **Utiliser un cron externe** pour garder l'app éveillée :

```bash
# Créer un cron job sur https://cron-job.org
# URL : https://gestion-dette-api.onrender.com/api/health
# Fréquence : Toutes les 10 minutes
```

## 🔐 Sécurité

### Variables sensibles

❌ **Ne jamais commit** :
- `.env`
- Clés Passport
- Tokens API

✅ **Utilisez Render Secrets** :
1. Dashboard → Environment
2. Cochez **"Secret"** pour les variables sensibles

### CORS

Configurez CORS pour n'autoriser que votre frontend :

```env
CORS_ALLOWED_ORIGINS=https://votre-frontend.com
```

### HTTPS

Render fournit automatiquement un certificat SSL gratuit.

## 📚 Ressources

- [Documentation Render](https://docs.render.com)
- [Laravel Deployment Guide](https://laravel.com/docs/10.x/deployment)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)

## ✅ Checklist de déploiement

- [ ] Dockerfile configuré et testé localement
- [ ] render.yaml configuré avec toutes les variables
- [ ] Code poussé sur GitHub/GitLab
- [ ] Service créé sur Render
- [ ] Base de données créée
- [ ] Variables d'environnement configurées
- [ ] Health check passe (200 OK)
- [ ] Migrations exécutées avec succès
- [ ] Passport installé correctement
- [ ] API testée (login, endpoints)
- [ ] Frontend connecté à l'API
- [ ] CORS configuré correctement
- [ ] Monitoring configuré

---

**Support** : Si vous rencontrez des problèmes, consultez les logs dans le Dashboard Render ou contactez le support.
