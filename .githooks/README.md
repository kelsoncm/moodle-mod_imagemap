# Pre-Commit Hooks for mod_imagemap

⚠️ **DEPRECATED**: The individual hook scripts in this directory are no longer used.

See [CONTRIBUTING.md](../CONTRIBUTING.md) for updated setup instructions using the official `moodlehq/moodle-plugin-ci` tool.

---

## Current Setup

Pre-commit hooks now use `moodle-plugin-ci` for consistent static analysis:

- **Code Checker** - Moodle Coding Standard compliance
- **PHPMD** - Code quality issues
- **Mustache Lint** - Template syntax validation
- **PHP Lint** - Syntax verification

## Installation

Follow the steps in [CONTRIBUTING.md](../CONTRIBUTING.md):

1. Install Composer globally
2. Run `composer install` in plugin directory
3. Install pre-commit (pip/brew/apt)
4. Run `bash setup-hooks.sh`

All checks are run automatically on `git commit`.

---

## Legacy Scripts (Archive)

The following scripts have been superseded by `moodle-plugin-ci`:

- `moodle-codechecker.sh` → `php ./vendor/bin/moodle-plugin-ci.phar codechecker`
- `moodle-phpdoc.sh` → *integrated in codechecker*
- `moodle-validate.sh` → `php ./vendor/bin/moodle-plugin-ci.phar validate`
- `moodle-savepoints.sh` → *integrated in codechecker*
- `mustache-lint.sh` → `php ./vendor/bin/moodle-plugin-ci.phar mustachelint`

These are kept for reference only and should not be invoked directly.

---

**Documentation**: See [CONTRIBUTING.md](../CONTRIBUTING.md) for current development guidelines.
