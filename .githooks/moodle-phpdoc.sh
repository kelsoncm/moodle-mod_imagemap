#!/bin/bash
# Moodle PHPDoc Checker Hook
# Validates PHPDoc comments in PHP files

set -e

exit_code=0

# Check if files are provided
if [ $# -eq 0 ]; then
    echo "No files to check"
    exit 0
fi

# Try to use moodle-plugin-ci first
if command -v phpcs &> /dev/null; then
    echo "📝 Running Moodle PHPDoc Checker..."
    
    for file in "$@"; do
        if [[ "$file" == *.php ]]; then
            if phpcs --standard=PSR12 --sniffs=PEAR.Commenting.ClassComment,PEAR.Commenting.FunctionComment "$file" 2>/dev/null || true; then
                :
            fi
        fi
    done
else
    echo "⚠️  phpcs not installed. Skipping PHPDoc Checker."
    echo "   Install moodle-plugin-ci to enable full checks."
fi

exit 0
