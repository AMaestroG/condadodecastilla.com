#!/bin/bash
# Start Flask API as a background service.
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LOG_DIR="$PROJECT_ROOT/logs"
mkdir -p "$LOG_DIR"
nohup python3 "$PROJECT_ROOT/flask_app.py" > "$LOG_DIR/flask_app.log" 2>&1 &
echo $! > "$LOG_DIR/flask_app.pid"
echo "Flask service started with PID $(cat "$LOG_DIR/flask_app.pid")"
