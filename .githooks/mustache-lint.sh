#!/bin/bash
# Mustache Lint Hook
# Validates Mustache template syntax

set -e

exit_code=0

# Check if files are provided
if [ $# -eq 0 ]; then
    echo "No files to check"
    exit 0
fi

echo "🎭 Checking Mustache templates..."

for file in "$@"; do
    if [[ "$file" == *.mustache ]]; then
        # Basic Mustache syntax check (matching braces)
        open_braces=$(grep -o '{{' "$file" | wc -l)
        close_braces=$(grep -o '}}' "$file" | wc -l)
        
        if [ "$open_braces" -ne "$close_braces" ]; then
            echo "❌ Mustache syntax error in: $file"
            echo "   Mismatched braces: {{ appears $open_braces times, }} appears $close_braces times"
            exit_code=1
        else
            echo "✓ $file: OK"
        fi
    fi
done

if [ $exit_code -ne 0 ]; then
    exit 1
fi

exit 0
