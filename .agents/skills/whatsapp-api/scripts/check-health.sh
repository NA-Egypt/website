#!/usr/bin/env bash
# ==============================================================================
# NA-Egypt WhatsApp Automation Microservice Health Checker
# ==============================================================================
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== NA-Egypt WhatsApp Health Checker ===${NC}"

# 1. Docker Daemon Check
echo -n "Checking Docker daemon... "
if docker info > /dev/null 2>&1; then
    echo -e "${GREEN}[OK] Running${NC}"
else
    echo -e "${RED}[FAIL] Docker daemon is not running or socket inaccessible${NC}"
    exit 1
fi

# 2. Container Status Check
echo -n "Checking naegypt_whatsapp container... "
CONTAINER_STATUS=$(docker inspect -f '{{.State.Status}}' naegypt_whatsapp 2>/dev/null || echo "missing")
if [ "$CONTAINER_STATUS" = "running" ]; then
    echo -e "${GREEN}[OK] Container is active and running${NC}"
else
    echo -e "${RED}[FAIL] Container status is: $CONTAINER_STATUS${NC}"
    echo "Run: docker compose -f docker-compose.whatsapp.yml up -d"
    exit 1
fi

# 3. HTTP Loopback Port 3000 Check
echo -n "Checking microservice HTTP binding (127.0.0.1:3000)... "
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:3000/devices || echo "000")
if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}[OK] HTTP 200 responded${NC}"
else
    echo -e "${RED}[FAIL] Received HTTP $HTTP_CODE${NC}"
    exit 1
fi

# 4. Device Registry & Status Check
ACTIVE_DEVICE=${1:-$(grep -E '^WHATSAPP_DEVICE_ID=' /var/www/new/.env 2>/dev/null | cut -d'=' -f2 | tr -d ' "\r' || echo "default")}
if [ -z "$ACTIVE_DEVICE" ]; then
    ACTIVE_DEVICE="default"
fi

echo -n "Checking device '${ACTIVE_DEVICE}' status... "
DEVICE_STATUS=$(curl -s http://127.0.0.1:3000/devices/${ACTIVE_DEVICE}/status || echo "{}")
IS_CONNECTED=$(echo "$DEVICE_STATUS" | grep -o '"is_connected":true' || true)
IS_LOGGED_IN=$(echo "$DEVICE_STATUS" | grep -o '"is_logged_in":true' || true)

if [ -n "$IS_CONNECTED" ] || [ -n "$IS_LOGGED_IN" ]; then
    echo -e "${GREEN}[OK] Device is CONNECTED to WhatsApp${NC}"
else
    echo -e "${YELLOW}[PENDING] Device is online but NOT PAIRED / DISCONNECTED${NC}"
    echo "  -> Visit https://naegypt.org/whatsapp/device to scan the pairing QR code."
fi

# 5. Device Webhook Registration Check
echo -n "Checking device webhook destination... "
WEBHOOK_RES=$(curl -s http://127.0.0.1:3000/devices/${ACTIVE_DEVICE}/webhook || echo "{}")
WEBHOOK_URL=$(echo "$WEBHOOK_RES" | grep -o '"webhook_url":"[^"]*"' | cut -d'"' -f4 || echo "")

if [ -n "$WEBHOOK_URL" ]; then
    echo -e "${GREEN}[OK] Webhook configured: $WEBHOOK_URL${NC}"
else
    echo -e "${YELLOW}[WARN] No webhook URL configured on device ${ACTIVE_DEVICE}${NC}"
fi

echo -e "\n${BLUE}All health checks finished.${NC}"
