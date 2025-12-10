# Guide de Test du Déploiement

## 1. Tester la connexion API

### A. Health Check
Testez d'abord que votre API répond:

```bash
# Remplacez <VOTRE_URL> par l'URL de votre application Laravel Cloud
curl https://<VOTRE_URL>/api/health
```

Vous devriez recevoir:
```json
{
  "status": "ok",
  "service": "gestion-dette-app",
  "timestamp": "2025-12-10T..."
}
```

### B. Test de connexion CORS depuis le frontend

Ouvrez la console développeur de votre navigateur et exécutez:

```javascript
fetch('https://<VOTRE_URL>/api/health')
  .then(res => res.json())
  .then(data => console.log('Success:', data))
  .catch(err => console.error('Error:', err));
```

## 2. Variables d'environnement requises sur Laravel Cloud

Assurez-vous que ces variables sont configurées sur Laravel Cloud:

### Configuration de base
```bash
APP_NAME="Gestion Dette"
APP_ENV=production
APP_DEBUG=false  # IMPORTANT: false en production
APP_KEY=<généré par Laravel>
APP_URL=https://<VOTRE_URL_LARAVEL_CLOUD>
```

### Base de données (PostgreSQL Neon)
```bash
DB_CONNECTION=pgsql
DATABASE_URL=postgresql://neondb_owner:npg_L2SzdOM3fRPG@ep-sparkling-dream-adoo4yue-pooler.c-2.us-east-1.aws.neon.tech/neondb?sslmode=require
DB_SSLMODE=require
```

### Frontend URL (si vous avez un frontend séparé)
```bash
FRONTEND_URL=https://<URL_DE_VOTRE_FRONTEND>
```

### Cloudinary (pour les images)
```bash
CLOUDINARY_CLOUD_NAME=dvlazryzt
CLOUDINARY_API_KEY=252382129819484
CLOUDINARY_API_SECRET=<votre_secret>
```

### Session et Cache
```bash
SESSION_DRIVER=database  # Recommandé pour production
CACHE_DRIVER=database    # Ou redis si disponible
QUEUE_CONNECTION=database
```

## 3. Problèmes courants et solutions

### Erreur CORS
**Symptôme**: `Access to fetch at '...' has been blocked by CORS policy`

**Solution**:
1. Vérifiez que `FRONTEND_URL` est défini dans Laravel Cloud
2. Vérifiez que votre domaine frontend correspond aux patterns CORS
3. Exécutez `php artisan config:cache` après modification

### Erreur 500 Internal Server Error
**Symptôme**: Le serveur répond avec une erreur 500

**Solution**:
1. Vérifiez les logs Laravel Cloud
2. Assurez-vous que `APP_DEBUG=false` en production
3. Vérifiez que toutes les variables d'environnement sont définies
4. Exécutez les commandes de cache:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Base de données non accessible
**Symptôme**: Timeout ou erreur de connexion base de données

**Solution**:
1. Vérifiez que `DATABASE_URL` est correctement défini
2. Vérifiez que `DB_SSLMODE=require` est défini
3. Testez la connexion depuis le terminal Laravel Cloud:
   ```bash
   php artisan db:show
   ```

### Authentification ne fonctionne pas
**Symptôme**: Token invalide ou erreurs d'authentification

**Solution**:
1. Vérifiez que `APP_KEY` est défini
2. Régénérez si nécessaire: `php artisan key:generate`
3. Videz le cache: `php artisan cache:clear`

## 4. Commandes utiles sur Laravel Cloud

```bash
# Voir les logs en temps réel
php artisan log:tail

# Vérifier la configuration
php artisan config:show

# Vérifier les routes API
php artisan route:list --path=api

# Tester la connexion base de données
php artisan db:show

# Exécuter les migrations
php artisan migrate --force

# Vider tous les caches
php artisan optimize:clear
```

## 5. Configuration de votre frontend

Dans votre application frontend, assurez-vous d'utiliser l'URL correcte:

```javascript
// .env ou config file du frontend
VITE_API_URL=https://<VOTRE_URL_LARAVEL_CLOUD>/api/v1
// ou
REACT_APP_API_URL=https://<VOTRE_URL_LARAVEL_CLOUD>/api/v1
// ou
NEXT_PUBLIC_API_URL=https://<VOTRE_URL_LARAVEL_CLOUD>/api/v1
```

Dans votre code:
```javascript
// Exemple avec axios
axios.defaults.baseURL = import.meta.env.VITE_API_URL;

// Exemple avec fetch
const API_URL = import.meta.env.VITE_API_URL;
fetch(`${API_URL}/login`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({ email, password })
})
```

## 6. Checklist de déploiement

- [ ] APP_URL configuré avec l'URL Laravel Cloud
- [ ] DATABASE_URL configuré et testé
- [ ] FRONTEND_URL configuré
- [ ] APP_DEBUG=false en production
- [ ] Migrations exécutées
- [ ] Caches générés (config, route, view)
- [ ] Health check répond correctement
- [ ] CORS configuré pour le frontend
- [ ] Frontend pointe vers la bonne URL API
- [ ] Authentification testée
