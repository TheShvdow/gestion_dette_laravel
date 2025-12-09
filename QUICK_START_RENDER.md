# ⚡ Déploiement Rapide sur Render

Guide en 5 étapes pour déployer votre API Laravel sur Render.

## 🎯 Avant de commencer

Vous avez déjà tout ce qu'il faut :
- ✅ Dockerfile configuré
- ✅ render.yaml prêt
- ✅ Configuration Docker (nginx, supervisor, start script)

## 📋 Étapes de Déploiement

### 1️⃣ Push sur GitHub/GitLab (2 min)

```bash
cd /home/deriss/Documents/developpements/gestion_dette_laravel

# Vérifier le status
git status

# Ajouter tous les fichiers
git add .

# Commit
git commit -m "feat: Add Render deployment configuration"

# Push (remplacez 'main' par votre branche si différent)
git push origin main
```

### 2️⃣ Créer le Service sur Render (3 min)

1. **Ouvrez** : https://dashboard.render.com
2. **Connectez-vous** ou créez un compte (gratuit)
3. **Cliquez** sur le bouton **"New +"** en haut à droite
4. **Sélectionnez** : **"Blueprint"**
5. **Connectez votre repository** Git (GitHub/GitLab)
6. **Render détecte automatiquement** le fichier `render.yaml`
7. **Cliquez** sur **"Apply"**

### 3️⃣ Configuration Automatique (1 min)

Render va automatiquement créer :

📦 **Base de données PostgreSQL**
- Nom : `gestion-dette-db`
- Version : PostgreSQL 15
- Région : Frankfurt
- Plan : Starter (gratuit 90 jours)

🌐 **Service Web**
- Nom : `gestion-dette-api`
- Type : Docker
- Région : Frankfurt
- Port : 8080
- Health check : `/api/health`

### 4️⃣ Attendre le Build (5-10 min)

Le déploiement se fait en plusieurs étapes :

```
[1/6] 🏗️  Building Docker image...           (3-5 min)
[2/6] 🗄️  Starting PostgreSQL database...     (1-2 min)
[3/6] 🔄  Running database migrations...      (30 sec)
[4/6] 🔐  Installing Laravel Passport...      (1 min)
[5/6] ⚡  Optimizing application...           (30 sec)
[6/6] ✅  Starting Nginx + PHP-FPM...         (10 sec)
```

**Suivez les logs** dans le Dashboard → Events → Live Logs

### 5️⃣ Tester l'API (1 min)

Une fois le déploiement terminé, vous verrez :

```
✓ Deploy live!
https://gestion-dette-api.onrender.com
```

**Testez immédiatement** :

```bash
# Remplacez par votre URL Render
export API_URL="https://gestion-dette-api.onrender.com"

# 1. Test du health endpoint
curl $API_URL/api/health

# Réponse attendue :
# {"status":"ok","service":"gestion-dette-app","timestamp":"2024-..."}

# 2. Test de login (créez d'abord un utilisateur)
curl -X POST $API_URL/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "login": "admin",
    "password": "password"
  }'
```

## 🎉 C'est terminé !

Votre API est maintenant en ligne et accessible depuis n'importe où.

### URL de votre API

```
https://gestion-dette-api.onrender.com
```

### Prochaines étapes

1. **Configurez votre frontend** :
   ```javascript
   // .env.production
   VITE_API_BASE_URL=https://gestion-dette-api.onrender.com/api/v1
   ```

2. **Créez votre premier utilisateur** :
   ```bash
   # Via Render Shell (Dashboard → Shell)
   php artisan tinker

   # Dans tinker
   $user = new App\Models\User();
   $user->login = 'admin';
   $user->email = 'admin@example.com';
   $user->password = bcrypt('password');
   $user->nom = 'Admin';
   $user->prenom = 'System';
   $user->role_id = 1; // Admin
   $user->active = true;
   $user->save();
   ```

3. **Configurez CORS** pour votre frontend :
   - Dashboard → Environment → Modifier `CORS_ALLOWED_ORIGINS`
   - Mettez l'URL de votre frontend (ex: `https://votre-app.vercel.app`)

## 🔧 Commandes Utiles

### Accéder aux logs

```
Dashboard → Service → Events → Live Logs
```

### Accéder au Shell

```
Dashboard → Service → Shell
```

### Redéployer

```bash
# Option 1 : Push un nouveau commit
git add .
git commit -m "update: ..."
git push origin main
# Render redéploie automatiquement

# Option 2 : Redéploiement manuel
# Dashboard → Manual Deploy → Deploy latest commit
```

### Gérer la base de données

```
Dashboard → Database → Connect

# Connection string interne (pour l'app)
postgresql://user:pass@host:5432/db

# Connection string externe (pour pgAdmin, etc.)
postgresql://user:pass@external-host:5432/db
```

## ⚠️ Points Importants

### 1. Sleep Mode (Plan Free/Starter)

Les instances gratuites dorment après **15 minutes d'inactivité**.

**Solution** : Utilisez un service de ping gratuit
- https://cron-job.org
- URL à pinger : `https://gestion-dette-api.onrender.com/api/health`
- Fréquence : Toutes les 10 minutes

### 2. Variables d'environnement

**APP_KEY** sera généré automatiquement par Render.

Pour les autres variables, allez dans :
```
Dashboard → Environment → Add Environment Variable
```

### 3. HTTPS Automatique

Render fournit un certificat SSL gratuit automatiquement.
Votre API est accessible en **HTTPS uniquement**.

### 4. Logs

Les logs sont conservés pendant **7 jours** sur le plan gratuit.

Pour des logs permanents :
```
Dashboard → Settings → Logging
```

## 🐛 Dépannage Rapide

### ❌ Health check échoue

```bash
# Vérifiez les logs
Dashboard → Logs

# Recherchez "error" ou "failed"
# Vérifiez que le port 8080 est bien exposé
grep "8080" logs
```

### ❌ Erreur de connexion base de données

```bash
# Vérifiez les variables d'environnement
Dashboard → Environment

# Assurez-vous que DB_HOST, DB_PORT, etc. sont bien configurés
```

### ❌ Passport keys not found

```bash
# Normal lors du premier déploiement
# Les clés sont générées automatiquement par start.sh
# Si le problème persiste : redéployer manuellement
```

### ❌ 500 Internal Server Error

```bash
# Activez temporairement le debug
Environment → APP_DEBUG=true
Environment → LOG_LEVEL=debug

# Consultez les logs pour voir l'erreur exacte
```

## 📚 Documentation Complète

Pour plus de détails, consultez :
- [DEPLOYMENT.md](DEPLOYMENT.md) - Guide complet de déploiement
- [Render Docs](https://docs.render.com)
- [Laravel Deployment](https://laravel.com/docs/10.x/deployment)

## 💰 Coûts

- **90 jours gratuits** avec le plan Starter
- Après : **$7/mois** pour le plan Standard
- Base de données incluse

## ✅ Checklist

- [ ] Code poussé sur GitHub/GitLab
- [ ] Service créé sur Render (Blueprint)
- [ ] Build terminé avec succès (vert)
- [ ] Health check passe (200 OK)
- [ ] API testée avec curl
- [ ] Utilisateur admin créé
- [ ] CORS configuré pour le frontend
- [ ] Frontend connecté à l'API

---

**🎊 Félicitations ! Votre API Laravel est maintenant en production !**
