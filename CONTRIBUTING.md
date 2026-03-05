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

- **PHP Lint** - Syntax validation
- **Moodle Code Checker** - PSR-2 + Moodle-specific standards
- **PHPDoc Checker** - Documentation compliance
- **Mustache Lint** - Template syntax validation
- **Upgrade Savepoints** - Database upgrade validation

All code must pass these checks before merging.

---

## Pre-Commit Setup

### 1. Install Pre-Commit

```bash
# Using pip (recommended)
pip install pre-commit

# Using Homebrew (macOS)
brew install pre-commit

# Using apt (Debian/Ubuntu)
apt-get install pre-commit
```

### 2. Install Git Hooks

From the plugin root directory:

```bash
pre-commit install
```

This will set up Git hooks that run automatically on each commit.

### 3. Make Hook Scripts Executable

```bash
chmod +x .githooks/*.sh
```

### 4. Verify Installation

```bash
pre-commit run --all-files
```

You should see output showing all hooks being checked.

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

#### PHP Lint
```bash
php -l path/to/file.php
```

#### Moodle Code Checker (requires moodle-plugin-ci)
```bash
moodle-plugin-ci codechecker
```

#### PHPDoc Checker
```bash
moodle-plugin-ci phpdoc
```

#### Validation
```bash
moodle-plugin-ci validate
```

#### Upgrade Savepoints
```bash
moodle-plugin-ci savepoints
```

#### Mustache Templates
```bash
moodle-plugin-ci mustache
```

### Setting Up Full moodle-plugin-ci

See [Moodle Plugin CI Documentation](https://github.com/moodlehq/moodle-plugin-ci) for complete setup.

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
# Reinstall pre-commit
pre-commit clean
pre-commit install

# Make scripts executable
chmod +x .githooks/*.sh
```

**Issue**: Mustache lint fails with false positives

**Solution**: The basic lint checks for balanced braces. Complex templates should be verified in Moodle.

**Issue**: Code Checker fails but passes locally

**Solution**: Ensure you're using the same PHP version and Moodle standard:
```bash
php --version
moodle-plugin-ci --version
```

### Debugging Failed Checks

1. **Run the specific hook**:
   ```bash
   bash .githooks/moodle-codechecker.sh path/to/file.php
   ```

2. **Check hook configuration**:
   ```bash
   cat .pre-commit-config.yaml
   ```

3. **View pre-commit logs**:
   ```bash
   pre-commit run --all-files --verbose
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
