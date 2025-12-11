#!/bin/bash

echo "=== Génération des clés Passport pour l'environnement Cloud ==="
echo ""

# Vérifier si les clés existent déjà
if [ -f "storage/oauth-private.key" ] && [ -f "storage/oauth-public.key" ]; then
    echo "✓ Les clés Passport existent déjà dans storage/"
    echo ""
    echo "Voulez-vous les régénérer ? (y/N)"
    read -r response
    if [[ ! "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        echo "Opération annulée."
        exit 0
    fi
fi

# Installer Passport si nécessaire
echo "1. Installation de Passport..."
php artisan passport:install --force

echo ""
echo "2. Lecture des clés générées..."

# Lire les clés et les encoder en base64
PRIVATE_KEY=$(cat storage/oauth-private.key | base64 -w 0)
PUBLIC_KEY=$(cat storage/oauth-public.key | base64 -w 0)

# Obtenir les IDs du client personnel
PERSONAL_CLIENT_ID=$(php artisan tinker --execute="echo DB::table('oauth_clients')->where('personal_access_client', 1)->value('id');")
PERSONAL_CLIENT_SECRET=$(php artisan tinker --execute="echo DB::table('oauth_clients')->where('personal_access_client', 1)->value('secret');")

echo ""
echo "=== Variables d'environnement à ajouter sur Laravel Cloud ==="
echo ""
echo "Copiez ces valeurs dans les variables d'environnement de votre projet Laravel Cloud :"
echo ""
echo "PASSPORT_PRIVATE_KEY=\"$PRIVATE_KEY\""
echo ""
echo "PASSPORT_PUBLIC_KEY=\"$PUBLIC_KEY\""
echo ""
echo "PASSPORT_PERSONAL_ACCESS_CLIENT_ID=\"$PERSONAL_CLIENT_ID\""
echo ""
echo "PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=\"$PERSONAL_CLIENT_SECRET\""
echo ""
echo "=== Instructions ==="
echo "1. Connectez-vous à Laravel Cloud"
echo "2. Allez dans votre projet > Settings > Environment Variables"
echo "3. Ajoutez les 4 variables ci-dessus"
echo "4. Redéployez votre application"
echo ""
echo "Une fois fait, vous n'aurez plus besoin de lancer passport:install !"
