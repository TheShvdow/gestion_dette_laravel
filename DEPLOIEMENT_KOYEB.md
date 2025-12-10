# 🚀 Déploiement sur Koyeb (100% Gratuit)

## Pourquoi Koyeb ?

- ✅ **100% Gratuit** - 1 service web gratuit à vie
- ✅ **Pas de carte bancaire** requise
- ✅ Support Docker natif
- ✅ Déploiement automatique depuis GitHub
- ✅ SSL/HTTPS automatique
- ✅ Compatible avec Neon Database

---

## 📋 Prérequis

- Compte GitHub
- Compte Koyeb : https://app.koyeb.com/auth/signup (gratuit, sans CB)
- Base de données Neon configurée

---

## 🎯 Étape 1 : Créer un Compte Koyeb

1. Aller sur https://app.koyeb.com/auth/signup
2. S'inscrire avec GitHub (recommandé) ou email
3. **Aucune carte bancaire requise !**

---

## 📤 Étape 2 : Pousser sur GitHub

```bash
git add .
git commit -m "feat: Configure for Koyeb deployment"
git push origin main
```

---

## 🌐 Étape 3 : Créer le Service sur Koyeb

### Via l'Interface Web (Recommandé)

1. **Aller sur** : https://app.koyeb.com

2. **Cliquer sur** : "Create Web Service"

3. **Source** :
   - Sélectionner "GitHub"
   - Autoriser Koyeb à accéder à vos repos
   - Choisir le repo `gestion_dette_laravel`
   - Branche : `main`

4. **Builder** :
   - Builder : Docker
   - Dockerfile path : `./Dockerfile`

5. **Instance** :
   - Type : `Nano` (gratuit)
   - Region : `Paris (par)` ou `Frankfurt (fra)`

6. **Ports** :
   - Port : `8080`
   - Protocol : `HTTP`

7. **Environment Variables** :

   Cliquer sur "Add variable" et ajouter :

   | Variable | Valeur |
   |----------|--------|
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `APP_KEY` | `base64:VOTRE_CLE` (générer avec `php artisan key:generate --show`) |
   | `DB_CONNECTION` | `pgsql` |
   | `DB_HOST` | `ep-sparkling-dream-adoo4yue-pooler.c-2.us-east-1.aws.neon.tech` |
   | `DB_PORT` | `5432` |
   | `DB_DATABASE` | `neondb` |
   | `DB_USERNAME` | `neondb_owner` |
   | `DB_PASSWORD` | `npg_L2SzdOM3fRPG` |
   | `DB_SSLMODE` | `require` |
   | `CACHE_DRIVER` | `file` |
   | `SESSION_DRIVER` | `file` |
   | `QUEUE_CONNECTION` | `sync` |

8. **Advanced** (Optionnel) :
   - Health check path : `/api/health`
   - Health check port : `8080`

9. **Cliquer sur** : "Deploy"

---

## ⏱️ Étape 4 : Attendre le Déploiement (5-10 min)

Koyeb va :
1. ✅ Cloner votre repo GitHub
2. ✅ Builder l'image Docker
3. ✅ Se connecter à Neon
4. ✅ Démarrer l'application sur le port 8080
5. ✅ Générer une URL HTTPS

---

## ✅ Étape 5 : Vérifier le Déploiement

### Votre URL

Koyeb vous donnera une URL du type :
```
https://gestion-dette-XXXXX.koyeb.app
```

### Test Health Check

```bash
curl https://votre-app.koyeb.app/api/health
```

**Attendu** :
```json
{
  "status": "ok",
  "service": "gestion-dette-app",
  "timestamp": "2025-12-10T..."
}
```

### Test Login

```bash
curl -X POST https://votre-app.koyeb.app/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"login":"admin","password":"Admin@2024"}'
```

**Attendu** :
```json
{
  "status": 200,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
  },
  "message": "Connexion reussie"
}
```

---

## 🔄 Redéploiement Automatique

Koyeb redéploiera **automatiquement** à chaque push sur `main` :

```bash
git add .
git commit -m "Update: nouvelle fonctionnalité"
git push origin main
```

---

## 📊 Monitoring

### Logs en Temps Réel

Dans le dashboard Koyeb :
1. Aller sur votre service
2. Onglet "Logs"
3. Voir les logs en temps réel

### Métriques

- CPU usage
- Memory usage
- Requêtes/seconde
- Temps de réponse

---

## 🔧 Configuration Avancée

### Variables d'Environnement Secrètes

Pour les variables sensibles (comme `DB_PASSWORD`) :

1. Dans Koyeb dashboard
2. Aller dans Settings → Environment
3. Marquer comme "Secret" ✓
4. La valeur sera masquée

### Domaine Personnalisé (Optionnel)

1. Aller dans Settings → Domains
2. Ajouter votre domaine
3. Configurer les DNS selon les instructions

---

## 💡 Astuces

### 1. Logs de Démarrage

Si l'app ne démarre pas, vérifier les logs :
- Connexion à Neon réussie ?
- Port 8080 bien exposé ?
- Variables d'environnement correctes ?

### 2. Sleep/Inactivité

Koyeb **ne met PAS en veille** les apps gratuites (contrairement à Render) !
Votre app reste **toujours active** 🎉

### 3. SSL Automatique

Koyeb génère automatiquement un certificat SSL.
Votre app est accessible en HTTPS par défaut.

---

## 🚨 Dépannage

### Erreur : "Application failed to start"

**Cause** : Port ou configuration incorrecte

**Solution** :
1. Vérifier que le Dockerfile expose bien le port 8080
2. Vérifier que `start.sh` configure Nginx sur le port 8080

### Erreur : "Database connection failed"

**Cause** : Problème avec Neon

**Solution** :
1. Vérifier les credentials Neon dans les variables d'environnement
2. Vérifier que `DB_SSLMODE=require` est présent
3. Tester la connexion depuis Neon Console

### Erreur : "Build failed"

**Cause** : Erreur dans le Dockerfile ou dépendances manquantes

**Solution** :
1. Vérifier les logs de build
2. Tester le build localement : `docker build -t test .`

---

## 📈 Limites du Plan Gratuit

| Ressource | Limite Gratuite |
|-----------|----------------|
| Services | 1 service web |
| Instances | 2 replicas max |
| CPU | 0.1 vCPU |
| RAM | 512 MB |
| Trafic | Illimité |
| Build time | 10 min max |
| Stockage | Éphémère (utiliser Neon pour la BD) |

**Suffisant pour** :
- Développement
- Démos
- Portfolio
- Petits projets

---

## 🆚 Koyeb vs Render

| Fonctionnalité | Koyeb Free | Render Free |
|----------------|------------|-------------|
| **Prix** | ✅ 0€ | ⚠️ 7$/mois (Starter) |
| **Sleep** | ✅ Jamais | ⚠️ Après 15 min |
| **Build time** | ✅ 10 min | ✅ Illimité |
| **SSL** | ✅ Auto | ✅ Auto |
| **Déploiement auto** | ✅ Oui | ✅ Oui |
| **Support Docker** | ✅ Oui | ✅ Oui |

**Verdict** : Koyeb est **meilleur** pour le gratuit ! 🏆

---

## 📞 Support

- **Dashboard** : https://app.koyeb.com
- **Docs** : https://www.koyeb.com/docs
- **Discord** : https://discord.gg/koyeb
- **Status** : https://status.koyeb.com

---

## ✅ Checklist de Déploiement

- [ ] Compte Koyeb créé
- [ ] Code poussé sur GitHub
- [ ] Service créé sur Koyeb
- [ ] Variables d'environnement configurées
- [ ] Build réussi
- [ ] Health check fonctionne (200 OK)
- [ ] Login fonctionne
- [ ] Dashboard accessible
- [ ] API testée

---

## 🎉 Félicitations !

Votre application est maintenant déployée **gratuitement** sur Koyeb !

**URL de l'API** : `https://votre-app.koyeb.app`

**Credentials** :
- Admin : `admin` / `Admin@2024`
- Boutiquier : `boutiquier` / `Boutiquier@2024`

⚠️ **Changez ces mots de passe en production !**

---

**Date** : 2025-12-10
**Plateforme** : Koyeb (Gratuit)
**Base de données** : Neon PostgreSQL
**Statut** : ✅ Prêt à déployer
