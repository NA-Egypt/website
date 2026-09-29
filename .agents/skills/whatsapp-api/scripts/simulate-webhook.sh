#!/usr/bin/env bash
# ==============================================================================
# NA-Egypt WhatsApp Inbound Webhook Simulator
# ==============================================================================
set -e

TARGET="local"
MESSAGE="1"
PHONE="201012345678"
NAME="Test Member"
SECRET=""

while [[ $# -gt 0 ]]; do
  case $1 in
    --target)
      TARGET="$2"
      shift 2
      ;;
    --message)
      MESSAGE="$2"
      shift 2
      ;;
    --phone)
      PHONE="$2"
      shift 2
      ;;
    --name)
      NAME="$2"
      shift 2
      ;;
    --secret)
      SECRET="$2"
      shift 2
      ;;
    -h|--help)
      echo "Usage: $0 [options]"
      echo ""
      echo "Options:"
      echo "  --target   Target environment: 'local' (default), 'production', 'staging', or full URL"
      echo "  --message  Simulated message body (default: '1')"
      echo "  --phone    Simulated sender phone (default: '201012345678')"
      echo "  --name     Simulated sender push name (default: 'Test Member')"
      echo "  --secret   Optional webhook shared secret"
      echo ""
      echo "Examples:"
      echo "  $0 --message '1'                          # Test JFT reading locally"
      echo "  $0 --message 'helpline'                   # Test Helpline hotline locally"
      echo "  $0 --target production --message 'jft'    # Test production webhook"
      exit 0
      ;;
    *)
      echo "Unknown option: $1"
      exit 1
      ;;
  esac
done

# Resolve Destination URL
case "$TARGET" in
  local)
    ENDPOINT="http://127.0.0.1:8000/api/v1/whatsapp/webhook"
    ;;
  production)
    ENDPOINT="https://naegypt.org/api/v1/whatsapp/webhook"
    ;;
  staging)
    ENDPOINT="https://egyptna.org/api/v1/whatsapp/webhook"
    ;;
  http*://*)
    ENDPOINT="${TARGET%/}/api/v1/whatsapp/webhook"
    ;;
  *)
    ENDPOINT="http://${TARGET}/api/v1/whatsapp/webhook"
    ;;
esac

TIMESTAMP=$(date +%s)
MSG_ID="SIM_$(date +%s)_$RANDOM"

PAYLOAD=$(cat <<EOF
{
  "event": "message",
  "device_id": "default",
  "secret": "${SECRET}",
  "payload": {
    "id": "${MSG_ID}",
    "from": "${PHONE}@s.whatsapp.net",
    "push_name": "${NAME}",
    "body": "${MESSAGE}",
    "timestamp": ${TIMESTAMP},
    "is_from_me": false
  }
}
EOF
)

echo "Dispatching simulated WhatsApp message to: $ENDPOINT"
echo "Sender: $NAME ($PHONE)"
echo "Message: '$MESSAGE'"
echo "---"

HEADERS=(-H "Content-Type: application/json" -H "Accept: application/json")
if [ -n "$SECRET" ]; then
    HEADERS+=(-H "X-Webhook-Secret: $SECRET")
fi

HTTP_RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "${HEADERS[@]}" -d "$PAYLOAD" "$ENDPOINT")
BODY=$(echo "$HTTP_RESPONSE" | sed '$d')
CODE=$(echo "$HTTP_RESPONSE" | tail -n1)

echo "HTTP Status: $CODE"
echo "Response Body: $BODY"

if [ "$CODE" = "200" ] || [ "$CODE" = "201" ]; then
    echo "Result: Success! Webhook accepted and queued."
else
    echo "Result: Webhook call failed with status $CODE."
    exit 1
fi
