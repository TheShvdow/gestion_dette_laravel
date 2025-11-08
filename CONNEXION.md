# 🔐 Informations de Connexion

## Serveur Laravel
**URL:** http://localhost:8000

## Utilisateur de Test

### Boutiquier
- **Login:** `cyundt`
- **Mot de passe:** `password`
- **Rôle:** Boutiquier

---

## 🚀 Démarrage de l'application

### Option 1 : Mode Production (recommandé pour tester)
```bash
# 1. Compiler les assets
npm run build

# 2. Démarrer Laravel
php artisan serve
```

Puis visitez : http://localhost:8000

### Option 2 : Mode Développement (avec hot reload)
```bash
# Terminal 1 - Laravel
php artisan serve

# Terminal 2 - Vite (dans un autre terminal)
npm run dev
```

---

## ❌ Résolution des problèmes

### Page non stylisée
- Assurez-vous d'avoir compilé les assets : `npm run build`
- Rafraîchissez avec Ctrl+Shift+R (hard refresh)
- Videz le cache du navigateur

### Erreur "route is not defined"
- C'est normal en mode production, Ziggy est configuré
- Rafraîchissez la page

### Login ne fonctionne pas
- Utilisez le **login** (ex: `cyundt`) et NON un email
- Mot de passe : `password`

---

## 📝 Autres utilisateurs disponibles

Pour créer d'autres utilisateurs de test :

```bash
php artisan tinker
```

Puis :
```php
$user = \App\Models\User::find(2); // Choisir un autre ID
$user->password = bcrypt('password');
$user->save();
echo "Login: " . $user->login;
```
