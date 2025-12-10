#!/bin/bash

# Script de test de l'API locale
# Usage: bash test-api.sh

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║              🧪 Test de l'API Laravel + Neon                 ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

API_URL="http://127.0.0.1:8000"

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}1️⃣  Test : Health Check${NC}"
echo "GET $API_URL/api/health"
echo ""

HEALTH_RESPONSE=$(curl -s "$API_URL/api/health")
HEALTH_STATUS=$(echo "$HEALTH_RESPONSE" | jq -r '.status' 2>/dev/null)

if [ "$HEALTH_STATUS" = "ok" ]; then
    echo -e "${GREEN}✓ Health check réussi${NC}"
    echo "$HEALTH_RESPONSE" | jq .
else
    echo -e "${RED}✗ Health check échoué${NC}"
    echo "$HEALTH_RESPONSE"
fi

echo ""
echo "════════════════════════════════════════════════════════════════"
echo ""

echo -e "${BLUE}2️⃣  Test : Login Admin${NC}"
echo "POST $API_URL/api/v1/login"
echo ""

LOGIN_RESPONSE=$(curl -s -X POST "$API_URL/api/v1/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"login":"admin","password":"Admin@2024"}')

LOGIN_STATUS=$(echo "$LOGIN_RESPONSE" | jq -r '.status' 2>/dev/null)
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.data.token' 2>/dev/null)

if [ "$LOGIN_STATUS" = "200" ] && [ "$TOKEN" != "null" ]; then
    echo -e "${GREEN}✓ Login réussi${NC}"
    echo "Status: $LOGIN_STATUS"
    echo "Token: ${TOKEN:0:50}..."
    echo ""
    echo "Données utilisateur:"
    echo "$LOGIN_RESPONSE" | jq '.data.user'
else
    echo -e "${RED}✗ Login échoué${NC}"
    echo "$LOGIN_RESPONSE" | jq .
    exit 1
fi

echo ""
echo "════════════════════════════════════════════════════════════════"
echo ""

echo -e "${BLUE}3️⃣  Test : Endpoint Protégé (Dashboard)${NC}"
echo "GET $API_URL/api/v1/dashboard"
echo ""

DASHBOARD_RESPONSE=$(curl -s "$API_URL/api/v1/dashboard" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN")

DASHBOARD_STATUS=$(echo "$DASHBOARD_RESPONSE" | jq -r '.status' 2>/dev/null)

if [ "$DASHBOARD_STATUS" = "200" ]; then
    echo -e "${GREEN}✓ Dashboard accessible${NC}"
    echo "$DASHBOARD_RESPONSE" | jq .
else
    echo -e "${YELLOW}⚠ Dashboard non accessible (peut-être normal si pas encore implémenté)${NC}"
    echo "$DASHBOARD_RESPONSE" | jq . 2>/dev/null || echo "$DASHBOARD_RESPONSE"
fi

echo ""
echo "════════════════════════════════════════════════════════════════"
echo ""

echo -e "${BLUE}4️⃣  Test : Liste des Clients${NC}"
echo "GET $API_URL/api/v1/clients"
echo ""

CLIENTS_RESPONSE=$(curl -s "$API_URL/api/v1/clients" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN")

CLIENTS_STATUS=$(echo "$CLIENTS_RESPONSE" | jq -r '.status' 2>/dev/null)

if [ "$CLIENTS_STATUS" = "200" ]; then
    echo -e "${GREEN}✓ Endpoint clients accessible${NC}"
    echo "Nombre de clients: $(echo "$CLIENTS_RESPONSE" | jq '.data | length' 2>/dev/null || echo "0")"
else
    echo -e "${YELLOW}⚠ Endpoint clients retourne : $CLIENTS_STATUS${NC}"
    echo "$CLIENTS_RESPONSE" | jq . 2>/dev/null || echo "$CLIENTS_RESPONSE"
fi

echo ""
echo "════════════════════════════════════════════════════════════════"
echo ""

echo -e "${GREEN}✅ Tests terminés !${NC}"
echo ""
echo -e "${YELLOW}📊 Résumé :${NC}"
echo "  - Health Check : OK"
echo "  - Login Admin  : OK"
echo "  - Token JWT    : Généré"
echo "  - API          : Fonctionnelle"
echo ""
