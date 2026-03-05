# Pre-Commit Hooks for mod_imagemap

This directory contains Git pre-commit hook scripts that enforce code quality standards.

## Overview

Pre-commit hooks are scripts that run automatically before each Git commit. They catch issues early and ensure code quality.

## Available Hooks

### Moodle Code Checker (`moodle-codechecker.sh`)

Enforces Moodle coding standards on PHP files.

**Checks**:
- Array syntax: short `[]` required
- Spacing after control keywords
- Inline comment formatting
- Trailing commas in multi-line arrays
- End-of-file newlines

### Moodle PHPDoc Checker (`moodle-phpdoc.sh`)

Validates PHP documentation compliance.

**Checks**:
- Class docblocks
- Function docblocks
- Parameter documentation
- Return type documentation

### Moodle Plugin Validator (`moodle-validate.sh`)

Validates plugin structure and metadata.

**Checks**:
- Required files exist (version.php, lib.php, etc.)
- version.php syntax
- Plugin component definition

### Upgrade Savepoints Checker (`moodle-savepoints.sh`)

Ensures database upgrade scripts have proper savepoint declarations.

**Checks**:
- upgrade.php has savepoint calls
- Proper upgrade function structure

### Mustache Lint (`mustache-lint.sh`)

Validates Mustache template syntax.

**Checks**:
- Balanced braces: `{{` and `}}`
- Proper tag closure
- Basic template validity

## Quick Start

### Installation

```bash
# One-time setup
bash setup-hooks.sh
```

Or manually:

```bash
# Install pre-commit
pip install pre-commit

# Install hooks
pre-commit install

# Make scripts executable
chmod +x .githooks/*.sh
```

### Testing Hooks

```bash
# Test all files
pre-commit run --all-files

# Test specific file
pre-commit run --files path/to/file.php

# Test specific hook
pre-commit run moodle-codechecker --all-files
```

## Hook Behavior

### On Commit

By default, hooks run on:
- Staged files only
- Commit stage (can't run later)
- Block commit if any check fails

### Bypass (Not Recommended)

Skip hooks for emergency commits:

```bash
git commit --no-verify
```

⚠️ This should be rare and requires justification.

## Configuration

Hooks are configured in `.pre-commit-config.yaml`.

### Modifying Hooks

1. Edit `.pre-commit-config.yaml`
2. Update hook scripts in `.githooks/*.sh`
3. Reinstall: `pre-commit install`
4. Test: `pre-commit run --all-files`

### Adding New Hooks

1. Create script in `.githooks/`
2. Add entry to `.pre-commit-config.yaml`
3. Run: `pre-commit install`
4. Test: `pre-commit run --all-files`

## Troubleshooting

### Hooks Not Running

**Problem**: `pre-commit install` didn't work

**Solution**:
```bash
pre-commit uninstall
pre-commit install
```

### Permission Denied

**Problem**: `Permission denied` when running hooks

**Solution**:
```bash
chmod +x .githooks/*.sh
```

### Wrong PHP Version

**Problem**: Checks fail but pass locally

**Solution**:
```bash
# Verify PHP version
php --version

# Check configured PHP
which php

# Update .pre-commit-config.yaml if needed
```

### Slow Execution

**Problem**: Hooks take too long

**Solution**:
- Only relevant files are checked (not slow by design)
- First run downloads dependencies (subsequent runs are faster)
- Use `--verbose` to see where time is spent: `pre-commit run --all-files --verbose`

## Performance

Estimated execution time:

- **On small changes**: < 1 second
- **On large changesets**: 2-5 seconds
- **First run**: Longer (caches dependencies)

Hook execution is optimized to:
- Only check modified files
- Run in parallel when possible
- Use fast syntax checks

## Dependencies

Hooks may require:

- **PHP CLI** - For all PHP checks
- **phpcs** - For advanced code checking (optional)
- **moodle-plugin-ci** - For full Moodle checks (optional)

Basic checks work without external tools. Full suite requires moodle-plugin-ci.

## CI Integration

These same hooks run in GitHub Actions CI workflow:

- `.github/workflows/moodle-plugin-ci.yml`
- Runs on push and pull requests
- Uses same standards locally and in CI

## More Information

- **Contributing**: See [CONTRIBUTING.md](../CONTRIBUTING.md)
- **Moodle Standards**: [Moodle Coding Style](https://docs.moodle.org/dev/Coding_style)
- **Pre-Commit Framework**: [https://pre-commit.com](https://pre-commit.com)

---

**Keep code quality high from the start!** ✨
