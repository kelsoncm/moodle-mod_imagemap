# Migration Guide: Pre-Commit Hooks v2 (moodle-plugin-ci)

## What Changed?

The pre-commit configuration has been updated to use the official **`moodlehq/moodle-plugin-ci`** tool installed **globally** instead of custom bash scripts.

| Before | After |
|--------|-------|
| Individual `.githooks/*.sh` scripts | `moodle-plugin-ci` command (global) |
| Inconsistent with CI/CD | Consistent with GitHub Actions CI/CD |
| Potentially fragile/unmaintained | Official Moodle maintained tool |
| Manual script updates | Automatic via `composer global update` |

## Why the Change?

1. **Consistency**: Same tool runs in pre-commit and GitHub Actions CI/CD
2. **Reliability**: Official Moodle tool, not custom scripts
3. **Maintainability**: Updates via `composer global update`
4. **Developer Experience**: Developers see exact same errors as CI/CD
5. **Simplicity**: No complex bash scripts to maintain

## What You Need to Do

### Step 1: Install Composer Globally (if not already installed)

```bash
# macOS
brew install composer

# Ubuntu/Debian
sudo apt-get install composer

# Or download from https://getcomposer.org/download/
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Verify
composer --version
```

### Step 2: Install moodle-plugin-ci Globally

```bash
composer global require moodlehq/moodle-plugin-ci
```

Verify it works:
```bash
moodle-plugin-ci --version
```

If command not found, add Composer's bin directory to your PATH:
```bash
# Add to ~/.bashrc, ~/.zshrc, or equivalent
export PATH="$PATH:$HOME/.composer/vendor/bin"

# Then reload
source ~/.bashrc  # or ~/.zshrc
```

### Step 3: Install Pre-Commit Hooks

```bash
# One command setup (from plugin root)
bash setup-hooks.sh
```

Or manually:
```bash
pip install pre-commit
pre-commit install
```

### Step 4: Test

Make a commit and the hooks will run automatically:

```bash
git add some_file.php
git commit -m "My changes"
# Hooks run automatically
```

## Manual Testing

Run checks manually:

```bash
# Code checker
php ./vendor/bin/moodle-plugin-ci.phar codechecker

# Code quality
php ./vendor/bin/moodle-plugin-ci.phar phpmd

# Template syntax
php ./vendor/bin/moodle-plugin-ci.phar mustachelint

# All available commands
php ./vendor/bin/moodle-plugin-ci.phar --help
```

## Troubleshooting

### "PHP executable not found"

Make sure PHP is installed and in PATH:
```bash
php --version
```

### "vendor/bin/moodle-plugin-ci.phar not found"

Run `composer install` first:
```bash
composer install
```

### Hooks not running

Reinstall pre-commit:
```bash
pre-commit uninstall
pre-commit install
pre-commit run --all-files
```

## Legacy Scripts

The following scripts in `.githooks/` are **no longer used** but kept for reference:

- `moodle-codechecker.sh`
- `moodle-phpdoc.sh`
- `moodle-validate.sh`
- `moodle-savepoints.sh`
- `mustache-lint.sh`

They are archived in the directory but not invoked by anything. They can be safely deleted if you wish.

## Files Changed

- `.pre-commit-config.yaml` - Updated to use `moodle-plugin-ci.phar`
- `CONTRIBUTING.md` - Updated installation instructions
- `setup-hooks.sh` - Enhanced to install Composer dependencies
- `composer.json` - Created with `moodlehq/moodle-plugin-ci` dependency  
- `.githooks/README.md` - Marked scripts as deprecated

## Questions?

See [CONTRIBUTING.md](CONTRIBUTING.md) for complete development setup guide.

---

**Migration Version**: 2.0 (March 5, 2026)  
**Status**: ✅ Complete - All developers should follow new setup process
