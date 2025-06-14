#!/bin/bash
set -euo pipefail

# Usage: GEMINI_API_KEY=your_api_key ./gemini_request.sh

# Use the endpoint from GEMINI_API_ENDPOINT when available
API_URL="${GEMINI_API_ENDPOINT:-https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent}"

# Allow the variable name GeminiAPI as an alternative
if [ -z "$GEMINI_API_KEY" ]; then
  GEMINI_API_KEY="${GeminiAPI:-}"
fi

if [ -z "$GEMINI_API_KEY" ]; then
  echo "GEMINI_API_KEY or GeminiAPI environment variable not set" >&2
  exit 1
fi

curl "${API_URL}?key=${GEMINI_API_KEY}" \
  -H 'Content-Type: application/json' \
  -X POST \
  -d '{
    "contents": [
      {
        "parts": [
          {
            "text": "Explain how AI works in a few words"
          }
        ]
      }
    ]
  }'
