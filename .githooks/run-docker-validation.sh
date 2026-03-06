#!/usr/bin/env bash
# Run Moodle validation checks inside Docker container
# This is for checks that require a complete Moodle installation with config.php
#
# Usage:
#   ./.githooks/run-docker-validation.sh validate
#   ./.githooks/run-docker-validation.sh mustache
#
# Note: You must have a Docker container running with moodle-plugin-ci installed

set -euo pipefail

COMMAND="${1:?Usage: $0 <validate|mustache> [plugin-path]}"
PLUGIN_PATH="${2:-.}"
PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Detect Docker service name from docker-compose (if present)
# Fall back to running moodle-plugin-ci directly if available
CONTAINER_NAME="${MOODLE_CONTAINER:-moodle}"
COMPOSE_FILE="${COMPOSE_FILE:-../../../workspace/docker-compose.yml}"

if command -v docker &> /dev/null; then
    if docker ps --format "{{.Names}}" | grep -q "$CONTAINER_NAME"; then
        echo "Running 'moodle-plugin-ci $COMMAND' inside Docker container '$CONTAINER_NAME'..."
        exec docker exec -w /var/www/html/mod/imagemap "$CONTAINER_NAME" \
            moodle-plugin-ci "$COMMAND" "$PLUGIN_PATH"
    fi
fi

# If Docker is not available, try to run locally (will fail if config.php is missing)
if command -v moodle-plugin-ci &> /dev/null; then
    echo "Running 'moodle-plugin-ci $COMMAND' locally..."
    echo "⚠️  Warning: This requires a Moodle installation with config.php in the current directory"
    exec moodle-plugin-ci "$COMMAND" "$PLUGIN_PATH"
fi

echo "❌ Error: Neither Docker container '$CONTAINER_NAME' nor 'moodle-plugin-ci' command found"
echo ""
echo "To fix this:"
echo "  1. If using Docker: Ensure the Moodle container is running and reachable"
echo "  2. If using local install: Set up Moodle config.php in the parent directory"
echo "  3. Or install moodle-plugin-ci globally: ./setup-hooks.sh"
exit 1
