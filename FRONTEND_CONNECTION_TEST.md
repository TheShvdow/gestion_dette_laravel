# Test de Connexion Frontend → API Laravel Cloud

## 🎯 Objectif
Tester que votre frontend local peut se connecter à l'API déployée sur Laravel Cloud.

## 📍 URLs
- **API Backend**: `https://gestion-dette.laravel.cloud`
- **Frontend Local**: `http://localhost:5173`

## 🧪 Tests à effectuer

### Test 1: Health Check (sans authentification)

Ouvrez la console de votre navigateur (F12) et collez ceci:

```javascript
fetch('https://gestion-dette.laravel.cloud/api/health')
  .then(res => res.json())
  .then(data => console.log('✅ Health Check:', data))
  .catch(err => console.error('❌ Health Check Error:', err));
```

**Résultat attendu:**
```json
{
  "status": "ok",
  "service": "gestion-dette-app",
  "timestamp": "2025-12-10T..."
}
```

### Test 2: Login (avec authentification)

```javascript
fetch('https://gestion-dette.laravel.cloud/api/v1/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: JSON.stringify({
    login: 'boutiquier',  // Remplacez par vos identifiants
    password: 'passer123'  // Remplacez par votre mot de passe
  })
})
  .then(res => res.json())
  .then(data => {
    console.log('✅ Login Response:', data);
    if (data.status === 200) {
      console.log('🎉 Token obtenu:', data.data.token);
    }
  })
  .catch(err => console.error('❌ Login Error:', err));
```

**Résultat attendu (succès):**
```json
{
  "status": 200,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJS..."
  },
  "message": "Connexion reussie"
}
```

**Erreurs possibles:**

#### Erreur CORS
```
Access to fetch at 'https://gestion-dette.laravel.cloud/api/v1/login'
from origin 'http://localhost:5173' has been blocked by CORS policy
```

**Solution**:
- Ajoutez `FRONTEND_URL=http://localhost:5173` dans les variables d'environnement Laravel Cloud
- Exécutez `php artisan config:clear` sur le serveur

#### Erreur 401 Unauthorized
```json
{
  "status": 401,
  "data": null,
  "message": "Echec de l'authentification"
}
```

**Causes possibles:**
1. **Passport non configuré** → Suivez [LARAVEL_CLOUD_SETUP.md](LARAVEL_CLOUD_SETUP.md)
2. **Mauvais identifiants** → Vérifiez login/password
3. **Utilisateur n'existe pas** → Créez un utilisateur de test

#### Erreur 500 Internal Server Error
```json
{
  "message": "Server Error"
}
```

**Solution**:
- Vérifiez les logs Laravel Cloud: `php artisan log:tail`
- Assurez-vous que la base de données est accessible
- Vérifiez que `APP_KEY` est défini

### Test 3: Requête authentifiée (après login)

Après avoir obtenu un token, testez une requête protégée:

```javascript
const token = 'COLLEZ_VOTRE_TOKEN_ICI';

fetch('https://gestion-dette.laravel.cloud/api/v1/user', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
  }
})
  .then(res => res.json())
  .then(data => console.log('✅ User Data:', data))
  .catch(err => console.error('❌ Error:', err));
```

## 🔧 Configuration du Frontend

### Fichier: `.env` (dans gestion_dette_frontend)

```env
VITE_API_BASE_URL=https://gestion-dette.laravel.cloud/api/v1
```

### Vérifier la configuration

Dans votre code frontend, vérifiez que l'URL est correcte:

```javascript
// src/api/client.js
console.log('API URL:', import.meta.env.VITE_API_BASE_URL);
// Devrait afficher: https://gestion-dette.laravel.cloud/api/v1
```

## 🚀 Démarrer le frontend avec la bonne configuration

```bash
cd ../gestion_dette_frontend

# Vérifier le fichier .env
cat .env

# Devrait contenir:
# VITE_API_BASE_URL=https://gestion-dette.laravel.cloud/api/v1

# Démarrer le frontend
npm run dev
```

## 📊 Monitoring des requêtes

### Dans la console navigateur (Network Tab)

1. Ouvrez F12 → Network
2. Filtrez par "Fetch/XHR"
3. Tentez de vous connecter
4. Cliquez sur la requête `login`
5. Vérifiez:
   - **Request URL**: Doit être `https://gestion-dette.laravel.cloud/api/v1/login`
   - **Request Headers**: Doit contenir `Accept: application/json`
   - **Request Payload**: Doit contenir `{"login":"...","password":"..."}`
   - **Response Status**: 200 = succès, 401 = échec auth, 500 = erreur serveur
   - **Response Headers**: Doit contenir `Access-Control-Allow-Origin`

## 🎯 Checklist de test

### Frontend
- [ ] `.env` contient la bonne URL API
- [ ] `npm run dev` démarre sans erreur
- [ ] Console navigateur ne montre pas d'erreurs

### Backend (Laravel Cloud)
- [ ] Health check répond (status 200)
- [ ] Passport est installé (`php artisan passport:client --personal`)
- [ ] Migrations exécutées
- [ ] CORS configuré pour accepter `http://localhost:5173`

### Connexion
- [ ] Login retourne un token
- [ ] Token est stocké dans localStorage
- [ ] Requêtes suivantes incluent le header Authorization
- [ ] Requêtes protégées fonctionnent avec le token

## 🐛 Commandes de dépannage

### Sur Laravel Cloud (terminal)

```bash
# Voir les logs en temps réel
php artisan log:tail

# Vérifier la configuration
php artisan config:show auth
php artisan config:show cors

# Tester la base de données
php artisan db:show

# Vérifier les routes API
php artisan route:list --path=api
```

### Sur votre machine locale

```bash
# Frontend - Voir les variables d'env
cd gestion_dette_frontend
npm run dev -- --debug

# Backend - Tester localement
cd gestion_dette_laravel
php artisan serve
```

## 📞 Support

Si le problème persiste:

1. **Copiez les logs** du terminal Laravel Cloud
2. **Faites une capture d'écran** de la console navigateur (F12)
3. **Notez** le message d'erreur exact
4. **Vérifiez** que toutes les étapes de [LARAVEL_CLOUD_SETUP.md](LARAVEL_CLOUD_SETUP.md) sont complétées
