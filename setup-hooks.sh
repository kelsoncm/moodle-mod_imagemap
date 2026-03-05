#!/bin/bash
# Quick setup script for pre-commit hooks
# Usage: bash setup-hooks.sh

set -e

echo "🚀 Setting up pre-commit hooks for mod_imagemap..."

# Check if pre-commit is installed
if ! command -v pre-commit &> /dev/null; then
    echo "❌ pre-commit is not installed."
    echo ""
    echo "Install it with one of these commands:"
    echo "  • pip install pre-commit"
    echo "  • brew install pre-commit (macOS)"
    echo "  • apt-get install pre-commit (Debian/Ubuntu)"
    echo ""
    exit 1
fi

echo "✓ pre-commit found: $(pre-commit --version)"

# Install git hooks
echo ""
echo "Installing git hooks..."
pre-commit install

# Make hook scripts executable
echo ""
echo "Making hook scripts executable..."
chmod +x .githooks/*.sh

# Run test
echo ""
echo "Running test on all files..."
if pre-commit run --all-files; then
    echo ""
    echo "✅ Setup complete! Pre-commit hooks are ready."
    echo ""
    echo "Next steps:"
    echo "  1. Make a change to a file"
    echo "  2. Run: git add <file>"
    echo "  3. Run: git commit -m 'Your message'"
    echo "     Hooks will run automatically!"
    echo ""
    echo "For more info, see: CONTRIBUTING.md"
else
    echo ""
    echo "⚠️  Some hooks failed. Fix the issues and try again:"
    echo "  git add <file>"
    echo "  git commit -m 'Your message'"
    echo ""
    exit 1
fi
