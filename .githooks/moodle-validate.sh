#!/bin/bash
# Moodle Plugin Validation Hook
# Validates plugin structure and metadata

set -e

PLUGIN_PATH="$(git rev-parse --show-toplevel)"

echo "✅ Validating Moodle plugin structure..."

# Check required files
required_files=(
    "version.php"
    "lib.php"
    "db/install.xml"
    "db/access.php"
    "lang/en/imagemap.php"
)

exit_code=0

for file in "${required_files[@]}"; do
    if [ ! -f "$PLUGIN_PATH/$file" ]; then
        echo "❌ Missing required file: $file"
        exit_code=1
    fi
done

# Validate version.php syntax
if [ -f "$PLUGIN_PATH/version.php" ]; then
    if ! php -l "$PLUGIN_PATH/version.php" > /dev/null 2>&1; then
        echo "❌ Syntax error in version.php"
        exit_code=1
    fi
fi

# Check for valid plugin name in version.php
if grep -q '\$plugin->component' "$PLUGIN_PATH/version.php"; then
    echo "✓ Plugin component defined"
else
    echo "⚠️  Plugin component not found in version.php"
fi

if [ $exit_code -eq 0 ]; then
    echo "✓ Plugin structure validation passed"
fi

exit $exit_code
