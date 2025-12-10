#!/bin/bash
set -e

echo "============================================"
echo "Setting up Laravel Passport"
echo "============================================"

# Run Passport migrations
echo "Running Passport migrations..."
php artisan migrate --force

# Install Passport (generates keys and creates clients)
echo "Installing Passport (generating OAuth keys)..."
php artisan passport:install --force

echo "Passport setup completed successfully!"
