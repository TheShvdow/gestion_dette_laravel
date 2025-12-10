#!/bin/bash

# Script de déploiement rapide pour Render
# Usage: bash deploy.sh "Message de commit"

set -e

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║           🚀 Déploiement sur Render (Neon DB)               ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Message de commit
COMMIT_MESSAGE="${1:-Update application}"

echo "📝 Message : $COMMIT_MESSAGE"
echo ""

# Ajouter tous les fichiers
echo "➕ Ajout des fichiers..."
git add .
echo "✓ Fichiers ajoutés"
echo ""

# Créer le commit
echo "💾 Création du commit..."
git commit -m "$COMMIT_MESSAGE" || echo "⚠ Rien à commiter"
echo ""

# Branche actuelle
CURRENT_BRANCH=$(git branch --show-current)
echo "🌿 Branche : $CURRENT_BRANCH"
echo ""

# Push
echo "📤 Push vers GitHub..."
git push origin $CURRENT_BRANCH

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                  ✅ DÉPLOIEMENT LANCÉ !                      ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "Prochaines étapes :"
echo "1️⃣  Aller sur https://dashboard.render.com"
echo "2️⃣  Le service se redéploiera automatiquement"
echo "3️⃣  Suivre les logs de build"
echo "4️⃣  Tester l'API"
echo ""
