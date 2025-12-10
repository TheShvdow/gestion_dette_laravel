#!/bin/bash

# Script de déploiement pour Koyeb
# Usage: bash deploy-koyeb.sh "Message de commit"

set -e

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║           🚀 Déploiement sur Koyeb (Gratuit)                ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Message de commit
COMMIT_MESSAGE="${1:-Update application for Koyeb}"

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
echo "║                  ✅ CODE POUSSÉ !                            ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "Prochaines étapes :"
echo ""
echo "1️⃣  Aller sur https://app.koyeb.com"
echo "2️⃣  Créer un nouveau Web Service"
echo "3️⃣  Connecter votre repo GitHub"
echo "4️⃣  Sélectionner Docker comme builder"
echo "5️⃣  Configurer les variables d'environnement"
echo "6️⃣  Déployer !"
echo ""
echo "📚 Guide complet : DEPLOIEMENT_KOYEB.md"
echo ""
echo "💰 Plan : 100% GRATUIT (pas de CB requise)"
echo ""
