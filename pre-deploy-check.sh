#!/bin/bash

# Script de vérification avant déploiement sur Render
# Utilisation : bash pre-deploy-check.sh

set -e

echo "======================================"
echo "🔍 Vérification Pré-Déploiement Render"
echo "======================================"
echo ""

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Compteurs
ERRORS=0
WARNINGS=0

# Fonction de vérification
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} $1 MANQUANT"
        ((ERRORS++))
        return 1
    fi
}

check_executable() {
    if [ -x "$1" ]; then
        echo -e "${GREEN}✓${NC} $1 (exécutable)"
        return 0
    else
        echo -e "${YELLOW}⚠${NC} $1 (non exécutable - sera corrigé dans Docker)"
        ((WARNINGS++))
        return 1
    fi
}

# 1. Fichiers essentiels
echo "1️⃣  Fichiers de configuration Docker"
echo "-----------------------------------"
check_file "Dockerfile"
check_file "docker/nginx.conf"
check_file "docker/supervisord.conf"
check_file "docker/start.sh"
check_executable "docker/start.sh"
echo ""

# 2. Fichier Render
echo "2️⃣  Configuration Render"
echo "----------------------"
check_file "render.yaml"
echo ""

# 3. Fichiers Laravel essentiels
echo "3️⃣  Fichiers Laravel"
echo "------------------"
check_file "composer.json"
check_file "composer.lock"
check_file "artisan"
check_file ".env.example"
echo ""

# 4. Routes API
echo "4️⃣  Routes API"
echo "-------------"
check_file "routes/api.php"

# Vérifier que /health existe
if grep -q "/health" routes/api.php; then
    echo -e "${GREEN}✓${NC} Route /api/health trouvée"
else
    echo -e "${RED}✗${NC} Route /api/health MANQUANTE"
    ((ERRORS++))
fi
echo ""

# 5. Controllers
echo "5️⃣  Controllers essentiels"
echo "-------------------------"
check_file "app/Http/Controllers/AuthController.php"
check_file "app/Http/Controllers/ClientController.php"
check_file "app/Http/Controllers/ArticleController.php"
check_file "app/Http/Controllers/DetteController.php"
echo ""

# 6. Vérifier .dockerignore
echo "6️⃣  Optimisation Build"
echo "--------------------"
check_file ".dockerignore"
echo ""

# 7. Vérifier que vendor n'est pas commité
echo "7️⃣  Vérification Git"
echo "-------------------"
if [ -d "vendor" ] && git ls-files --error-unmatch vendor/ &>/dev/null; then
    echo -e "${YELLOW}⚠${NC} Le dossier vendor est commité (non recommandé)"
    echo "   Ajoutez /vendor dans .gitignore"
    ((WARNINGS++))
else
    echo -e "${GREEN}✓${NC} vendor/ non commité (bon)"
fi

if [ -f ".env" ] && git ls-files --error-unmatch .env &>/dev/null; then
    echo -e "${RED}✗${NC} Le fichier .env est commité (DANGER!)"
    echo "   Supprimez .env du Git immédiatement!"
    ((ERRORS++))
else
    echo -e "${GREEN}✓${NC} .env non commité (bon)"
fi
echo ""

# 8. Vérifier les dépendances Composer
echo "8️⃣  Dépendances"
echo "--------------"
if [ -f "composer.lock" ]; then
    if grep -q "laravel/passport" composer.lock; then
        echo -e "${GREEN}✓${NC} Laravel Passport installé"
    else
        echo -e "${RED}✗${NC} Laravel Passport MANQUANT"
        ((ERRORS++))
    fi

    if grep -q "laravel/framework" composer.lock; then
        echo -e "${GREEN}✓${NC} Laravel Framework installé"
    fi
fi
echo ""

# 9. Vérifier la structure des migrations
echo "9️⃣  Migrations"
echo "------------"
if [ -d "database/migrations" ]; then
    MIGRATION_COUNT=$(find database/migrations -name "*.php" | wc -l)
    if [ "$MIGRATION_COUNT" -gt 0 ]; then
        echo -e "${GREEN}✓${NC} $MIGRATION_COUNT migrations trouvées"
    else
        echo -e "${YELLOW}⚠${NC} Aucune migration trouvée"
        ((WARNINGS++))
    fi
else
    echo -e "${RED}✗${NC} Dossier database/migrations manquant"
    ((ERRORS++))
fi
echo ""

# 10. Vérifier le Dockerfile
echo "🔟 Analyse Dockerfile"
echo "-------------------"
if grep -q "FROM php:8.2" Dockerfile; then
    echo -e "${GREEN}✓${NC} PHP 8.2 configuré"
else
    echo -e "${YELLOW}⚠${NC} Version PHP différente de 8.2"
    ((WARNINGS++))
fi

if grep -q "EXPOSE 8080" Dockerfile; then
    echo -e "${GREEN}✓${NC} Port 8080 exposé"
else
    echo -e "${RED}✗${NC} Port 8080 non exposé (Render attend le port 8080)"
    ((ERRORS++))
fi

if grep -q "pdo_pgsql" Dockerfile; then
    echo -e "${GREEN}✓${NC} Extension PostgreSQL installée"
else
    echo -e "${RED}✗${NC} Extension PostgreSQL manquante"
    ((ERRORS++))
fi
echo ""

# 11. Vérifier render.yaml
echo "1️⃣1️⃣ Analyse render.yaml"
echo "---------------------"
if grep -q "healthCheckPath: /api/health" render.yaml; then
    echo -e "${GREEN}✓${NC} Health check configuré"
else
    echo -e "${YELLOW}⚠${NC} Health check non configuré"
    ((WARNINGS++))
fi

if grep -q "dockerfilePath: ./Dockerfile" render.yaml; then
    echo -e "${GREEN}✓${NC} Chemin Dockerfile correct"
else
    echo -e "${RED}✗${NC} Chemin Dockerfile incorrect"
    ((ERRORS++))
fi

if grep -q "type: postgres" render.yaml || grep -q "engine: postgres" render.yaml; then
    echo -e "${GREEN}✓${NC} Base de données PostgreSQL configurée"
else
    echo -e "${RED}✗${NC} Base de données PostgreSQL manquante"
    ((ERRORS++))
fi
echo ""

# 12. Git status
echo "1️⃣2️⃣ Status Git"
echo "-------------"
if git rev-parse --git-dir > /dev/null 2>&1; then
    BRANCH=$(git branch --show-current)
    echo -e "${GREEN}✓${NC} Branche actuelle : $BRANCH"

    if [ -z "$(git status --porcelain)" ]; then
        echo -e "${GREEN}✓${NC} Aucune modification non commitée"
    else
        echo -e "${YELLOW}⚠${NC} Modifications non commitées trouvées:"
        git status --short
        ((WARNINGS++))
    fi

    # Vérifier si origin existe
    if git remote get-url origin > /dev/null 2>&1; then
        ORIGIN=$(git remote get-url origin)
        echo -e "${GREEN}✓${NC} Remote origin : $ORIGIN"
    else
        echo -e "${RED}✗${NC} Remote origin non configuré"
        ((ERRORS++))
    fi
else
    echo -e "${RED}✗${NC} Pas un repository Git"
    ((ERRORS++))
fi
echo ""

# Résumé
echo "======================================"
echo "📊 RÉSUMÉ"
echo "======================================"

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✅ Tout est prêt pour le déploiement !${NC}"
    echo ""
    echo "Prochaines étapes :"
    echo "1. git add ."
    echo "2. git commit -m 'chore: Ready for Render deployment'"
    echo "3. git push origin main"
    echo "4. Créez le Blueprint sur https://dashboard.render.com"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️  $WARNINGS avertissement(s) trouvé(s)${NC}"
    echo ""
    echo "Vous pouvez déployer, mais vérifiez les avertissements ci-dessus."
    exit 0
else
    echo -e "${RED}❌ $ERRORS erreur(s) trouvée(s)${NC}"
    if [ $WARNINGS -gt 0 ]; then
        echo -e "${YELLOW}⚠️  $WARNINGS avertissement(s) trouvé(s)${NC}"
    fi
    echo ""
    echo "Corrigez les erreurs avant de déployer."
    exit 1
fi
