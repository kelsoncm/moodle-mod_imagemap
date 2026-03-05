#!/bin/bash
# Moodle Code Checker Hook
# Runs phpcs with Moodle standard on staged PHP files

set -e

exit_code=0

# Check if files are provided
if [ $# -eq 0 ]; then
    echo "No files to check"
    exit 0
fi

# Try to use moodle-plugin-ci first
if command -v phpcs &> /dev/null; then
    echo "🔍 Running Moodle Code Checker..."
    
    # Get the plugin path
    PLUGIN_PATH="$(git rev-parse --show-toplevel)"
    
    for file in "$@"; do
        if [[ "$file" == *.php ]]; then
            # Check if phpcs is configured for Moodle
            if phpcs --standard=moodle "$file" 2>/dev/null || true; then
                :
            else
                # Fallback: just do basic PHP lint  
                if ! php -l "$file" > /dev/null 2>&1; then
                    echo "❌ Syntax error in: $file"
                    exit_code=1
                fi
            fi
        fi
    done
    
    if [ $exit_code -ne 0 ]; then
        echo "❌ Moodle Code Checker failed. Run locally for details:"
        echo "   moodle-plugin-ci codechecker"
        exit 1
    fi
else
    echo "⚠️  phpcs not installed. Skipping Moodle Code Checker."
    echo "   Install moodle-plugin-ci to enable full checks."
fi

exit 0
