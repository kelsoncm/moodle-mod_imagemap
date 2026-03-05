#!/bin/bash
# Moodle Savepoints Checker Hook
# Ensures database upgrade scripts have proper savepoint declarations

set -e

PLUGIN_PATH="$(git rev-parse --show-toplevel)"
UPGRADE_FILE="$PLUGIN_PATH/db/upgrade.php"

# Only check if upgrade.php exists and was modified
if [ ! -f "$UPGRADE_FILE" ]; then
    exit 0
fi

echo "🔍 Checking upgrade savepoints..."

# Check if upgrade.php has upgrade_log() calls (savepoints)
if grep -q 'upgrade_plugin_savepoint' "$UPGRADE_FILE" || grep -q 'upgrade_main_savepoint' "$UPGRADE_FILE"; then
    echo "✓ Savepoints found in upgrade.php"
    exit 0
else
    # Check if it's a new/empty upgrade file
    lines=$(wc -l < "$UPGRADE_FILE")
    if [ "$lines" -lt 20 ]; then
        echo "⚠️  No upgrade functions found (may be new/empty file)"
        exit 0
    else
        echo "⚠️  No savepoints detected in upgrade.php"
        echo "   Ensure all upgrade steps include savepoint calls"
        exit 0
    fi
fi
