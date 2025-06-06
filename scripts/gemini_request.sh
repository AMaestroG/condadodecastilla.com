#!/bin/bash

# Usage: GEMINI_API_KEY=your_api_key ./gemini_request.sh

MODEL="${GEMINI_MODEL:-gemini-2.5-pro}"
API_URL="https://generativelanguage.googleapis.com/v1beta/models/${MODEL}:generateContent"

if [ -z "$GEMINI_API_KEY" ]; then
  echo "GEMINI_API_KEY environment variable not set" >&2
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
