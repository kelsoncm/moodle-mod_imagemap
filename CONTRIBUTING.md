# Contributing to Image Map Module (mod_imagemap)

Thank you for your interest in contributing! This document provides guidelines and instructions for developers.

## Table of Contents

- [Code Standards](#code-standards)
- [Pre-Commit Setup](#pre-commit-setup)
- [Development Workflow](#development-workflow)
- [Testing](#testing)
- [Pull Request Process](#pull-request-process)

---

## Code Standards

This project follows **Moodle Coding Standards** and automatically enforces them through:

- **Moodle PHP Lint** - Syntax validation
- **Moodle Code Checker** - Moodle coding standards
- **Moodle PHPMD** - Code quality and best practices
- **Moodle Validate** - Plugin structure and metadata
- **Moodle Savepoints** - Upgrade savepoint checks
- **Moodle Mustache** - Template syntax validation

All code must pass these checks before committing.
Checks run locally using a project-local `moodle-plugin-ci` (`.moodle-plugin-ci/`, version `^4`) to match CI tooling.

---

## Pre-Commit Setup

### 1. Install Composer Globally

Ensure Composer is installed globally:

```bash
# macOS (using Homebrew)
brew install composer

# Ubuntu/Debian
sudo apt-get install composer

# Or install from https://getcomposer.org/download/
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Verify installation
composer --version
```

### 2. Bootstrap Local Moodle Plugin CI

From the plugin root directory:

```bash
composer create-project -n --no-dev --prefer-dist moodlehq/moodle-plugin-ci .moodle-plugin-ci "^4"
```

Verify local installation:

```bash
./.moodle-plugin-ci/bin/moodle-plugin-ci --version
```

### 3. Install Pre-Commit

```bash
# Using pip (recommended)
pip install pre-commit

# Using Homebrew (macOS)
brew install pre-commit

# Using apt (Debian/Ubuntu)
apt-get install pre-commit
```

### 4. Install Git Hooks

From the plugin root directory:

```bash
pre-commit install
```

This will set up Git hooks that run automatically on each commit.

### 5. Verify Installation

```bash
pre-commit run --all-files
```

You should see all hooks being executed with the globally installed `moodle-plugin-ci` tool.

---

## Development Workflow

### Creating a Feature Branch

```bash
git checkout -b feature/your-feature-name
```

Use descriptive branch names:
- `feature/` - New features
- `bugfix/` - Bug fixes
- `docs/` - Documentation updates
- `refactor/` - Code refactoring
- `test/` - Test additions

### Making Changes

1. **Edit files** as needed
2. **Stage changes**: `git add <files>`
3. **Pre-commit checks run automatically**
   - If checks fail, fix errors and try again
   - Use `pre-commit run --all-files` to test manually

### Running Pre-Commit Manually

Check specific files:
```bash
pre-commit run --files path/to/file.php
```

Check all files:
```bash
pre-commit run --all-files
```

Skip a specific hook (debugging only):
```bash
pre-commit run --all-files --hook-stage commit --exclude-hook <hook-id>
```

### Bypassing Pre-Commit (Not Recommended)

If you absolutely must skip checks:

```bash
git commit --no-verify
```

⚠️ **Warning**: This bypasses all quality checks. Use only for emergencies!

---

## Testing

### Full CI Pipeline

The GitHub Actions workflow tests against multiple configurations:

- **PHP Versions**: 8.1, 8.2, 8.3, 8.4
- **Moodle Versions**: 4.5, 5.0, 5.1
- **Databases**: PostgreSQL, MariaDB

Before creating a pull request, ensure all local checks pass.

### Manual Testing

Local checks using the same toolchain as CI:

#### PHP Lint
```bash
./.moodle-plugin-ci/bin/moodle-plugin-ci phplint .
```

#### Moodle Code Checker
```bash
./.moodle-plugin-ci/bin/moodle-plugin-ci codechecker .
```

#### Moodle PHPMD (Code Quality)
```bash
./.moodle-plugin-ci/bin/moodle-plugin-ci phpmd .
```

#### Plugin Validation
```bash
./.moodle-plugin-ci/bin/moodle-plugin-ci validate .
```

#### Upgrade Savepoints
```bash
./.moodle-plugin-ci/bin/moodle-plugin-ci savepoints .
```

#### Mustache Linter
```bash
./.moodle-plugin-ci/bin/moodle-plugin-ci mustache .
```

### Full moodle-plugin-ci Suite

For comprehensive test suite execution (including PHPUnit and Behat), see [Moodle Plugin CI Documentation](https://github.com/moodlehq/moodle-plugin-ci).

---

## Pull Request Process

### Before Creating a PR

1. **Ensure all checks pass**:
   ```bash
   pre-commit run --all-files
   ```

2. **Update documentation** if needed
   - Update `README.md` for user-facing changes
   - Update `docs/` for implementation details
   - Update `CHANGELOG.md` with your changes

3. **Test locally** in a Moodle instance

### Creating a Pull Request

1. Push your branch to the repository
2. Create PR with a clear title and description
3. Link any related issues: `Closes #123`
4. Wait for CI to pass (all workflow checks)
5. Request review from maintainers

### PR Requirements

- ✅ All GitHub Actions checks pass
- ✅ Pre-commit hooks pass
- ✅ Adequate test coverage
- ✅ Updated documentation
- ✅ CHANGELOG.md updated
- ✅ At least one approval from maintainers

---

## Code Review Guidelines

When reviewing code:

1. **Style**: Verify compliance with Moodle standards
2. **Functionality**: Test the feature locally
3. **Documentation**: Check for clarity and completeness
4. **Tests**: Ensure adequate coverage
5. **Security**: Look for potential vulnerabilities

---

## Common Issues & Fixes

### Pre-Commit Hook Failures

**Issue**: Hooks fail with "command not found"

**Solution**:
```bash
# Ensure moodle-plugin-ci is installed globally
composer global require moodlehq/moodle-plugin-ci

# Verify it's in your PATH
moodle-plugin-ci --version

# If not found, add to ~/.bashrc or ~/.zshrc
export PATH="$PATH:$HOME/.composer/vendor/bin"
```

**Issue**: moodle-plugin-ci command not found

**Solution**: Composer's global bin directory is not in PATH:
```bash
# Add to ~/.bashrc, ~/.zshrc, or equivalent
export PATH="$PATH:$HOME/.composer/vendor/bin"

# Reload shell
source ~/.bashrc  # or ~/.zshrc

# Verify
moodle-plugin-ci --version
```

**Issue**: Code Checker fails but passes locally

**Solution**: Ensure you have `moodle-plugin-ci` installed globally:
```bash
php --version
moodle-plugin-ci --version
```

### Debugging Failed Checks

1. **Run the specific hook manually**:
   ```bash
   moodle-plugin-ci codechecker
   moodle-plugin-ci phpmd
   moodle-plugin-ci mustachelint
   ```

2. **Check hook configuration**:
   ```bash
   cat .pre-commit-config.yaml
   ```

3. **View pre-commit logs**:
   ```bash
   pre-commit run --all-files --verbose
   ```

4. **Reinstall pre-commit hooks**:
   ```bash
   pre-commit uninstall
   pre-commit install
   pre-commit run --all-files
   ```

---

## Coding Standards Summary

### PHP Files

- Use **short array syntax** `[]` instead of `array()`
- Add space after control keywords: `if (`, `switch (`, `foreach (`
- End inline comments with proper punctuation: `.`, `!`, or `?`
- Include trailing comma in multi-line arrays
- Add newline at end of file
- Follow PSR-12 + Moodle standards

### Mustache Templates

- Use `{{#if}}...{{/if}}` for conditionals
- Use `{{#each}}...{{/each}}` for loops
- Always close tags properly
- Include `data-*` attributes for JavaScript binding

### Language Strings

- Key format: `$string['identifier']`
- Meaningful, descriptive identifiers
- Add to both `en/imagemap.php` and `pt_br/imagemap.php`

### Database Changes

- Always include `upgrade_plugin_savepoint()` calls
- Update `db/install.xml` for new tables/fields
- Create migration scripts in `db/upgrade.php`

---

## Questions?

- 📖 See [Architecture Guide](docs/IMPLEMENTATION.md)
- 🐛 Check [Known Issues](CHANGELOG.md)
- 📞 Open a discussion or issue on GitHub

---

**Thank you for contributing to improving Image Map Module!** 🎉
