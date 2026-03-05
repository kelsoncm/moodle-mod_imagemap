#!/bin/bash
# Quick setup script for pre-commit hooks and development dependencies
# Usage: bash setup-hooks.sh

set -e

echo "🚀 Setting up development environment for mod_imagemap..."
echo ""

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed."
    echo ""
    echo "Install it with one of these commands:"
    echo "  • brew install composer (macOS)"
    echo "  • sudo apt-get install composer (Debian/Ubuntu)"
    echo "  • Visit: https://getcomposer.org/download/"
    echo ""
    exit 1
fi

echo "✓ Composer found: $(composer --version)"

# Optional: moodle-plugin-ci is used in CI for full static analysis.
echo ""
echo "Bootstrapping local moodle-plugin-ci (^4)..."
echo "  This keeps local checks aligned with CI tooling"
if [ ! -x ".moodle-plugin-ci/bin/moodle-plugin-ci" ]; then
    composer create-project -n --no-dev --prefer-dist moodlehq/moodle-plugin-ci .moodle-plugin-ci "^4"
fi
echo "✓ moodle-plugin-ci local: $(.moodle-plugin-ci/bin/moodle-plugin-ci --version)"

# Check if pre-commit is installed
if ! command -v pre-commit &> /dev/null; then
    echo ""
    echo "❌ pre-commit is not installed."
    echo ""
    echo "Install it with one of these commands:"
    echo "  • pip install pre-commit"
    echo "  • brew install pre-commit (macOS)"
    echo "  • apt-get install pre-commit (Debian/Ubuntu)"
    echo ""
    exit 1
fi

echo ""
echo "✓ pre-commit found: $(pre-commit --version)"

# Ensure hook scripts are executable.
chmod +x .githooks/run-moodle-plugin-ci-v4.sh

# Install git hooks
echo ""
echo "Installing git hooks..."
pre-commit install

# Run test
echo ""
echo "Running test on all files..."
if pre-commit run --all-files; then
    echo ""
    echo "✅ Setup complete! Development environment is ready."
    echo ""
    echo "Next steps:"
    echo "  1. Make a change to a file"
    echo "  2. Run: git add <file>"
    echo "  3. Run: git commit -m 'Your message'"
    echo "     Pre-commit hooks will run automatically!"
    echo ""
    echo "Manual checks:"
    echo "  • ./.moodle-plugin-ci/bin/moodle-plugin-ci codechecker ."
    echo "  • ./.moodle-plugin-ci/bin/moodle-plugin-ci phpmd ."
    echo "  • ./.moodle-plugin-ci/bin/moodle-plugin-ci validate ."
    echo ""
    echo "For more info, see: CONTRIBUTING.md"
else
    echo ""
    echo "⚠️  Some hooks failed. Fix the issues and try again:"
    echo "  git add <file>"
    echo "  git commit -m 'Your message'"
    echo ""
    echo "Or run manually to debug:"
    echo "  ~/.pyenv/versions/precommit/bin/pre-commit run --all-files"
    echo ""
    exit 1
fi
