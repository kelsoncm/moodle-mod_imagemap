# Final Setup Instructions - Pre-Commit Hooks (Global moodle-plugin-ci)

## Status Summary

✅ **moodle-plugin-ci** foi instalado globalmente com sucesso  
⚠️ Precisa configurar PATH e pre-commit environment

---

## Step 1: Add Composer's bin directory to PATH

O `moodle-plugin-ci` foi instalado em `~/.composer/vendor/bin/`, mas não está no seu PATH.

### Para Bash (Linux/macOS com bash)

Adicione ao final do `~/.bashrc`:

```bash
# Add Composer's global bin to PATH
export PATH="$PATH:$HOME/.composer/vendor/bin"
```

Depois reload:
```bash
source ~/.bashrc
```

### Para Zsh (macOS/Linux com zsh)

Adicione ao final do `~/.zshrc`:

```bash
# Add Composer's global bin to PATH
export PATH="$PATH:$HOME/.composer/vendor/bin"
```

Depois reload:
```bash
source ~/.zshrc
```

### Verificar

```bash
moodle-plugin-ci --version
# Deve retornar: 1.5.8 (ou versão similar)
```

---

## Step 2: Activate pre-commit from Correct Python Environment

Seu sistema tem `pre-commit` instalado em um virtualenv do pyenv chamado `precommit`.

### Option A: Use Global pre-commit (Recommended)

```bash
# Ativar environment
pyenv activate precommit

# Ou se usando direto com pyenv
~/.pyenv/versions/precommit/bin/pre-commit --version
```

### Option B: Create ~/.bashrc Alias (Easier)

Adicione ao seu `~/.bashrc` ou `~/.zshrc`:

```bash
# Use pre-commit from pyenv precommit environment
alias pre-commit="~/.pyenv/versions/precommit/bin/pre-commit"
```

Depois reload:
```bash
source ~/.bashrc  # ou ~/.zshrc
```

Verify:
```bash
pre-commit --version
```

---

## Step 3: Run Full Setup Again

Agora que PATH e pre-commit estão corretos:

```bash
cd /home/kelson/projetos/IFRN/ava/lms/mod_imagemap

# Re-run setup
bash setup-hooks.sh
```

Expected output:
```
🚀 Setting up development environment for mod_imagemap...

✓ Composer found: Composer version 2.7.1 ...
✓ moodle-plugin-ci found: 1.5.8   ← Deve aparecer agora
✓ pre-commit found: ...             ← Deve aparecer agora

Installing git hooks...
✓ Plugin structure validation passed
✓ Setup complete! Development environment is ready.
```

---

## Step 4: Test Pre-Commit Hooks

```bash
cd /home/kelson/projetos/IFRN/ava/lms/mod_imagemap

# Test all hooks
pre-commit run --all-files

# Expected: Hooks run without errors
```

---

## Complete Commands (Copy & Paste)

### For Bash users:

```bash
# 1. Add PATH
echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.bashrc
source ~/.bashrc

# 2. Add pre-commit alias
echo 'alias pre-commit="~/.pyenv/versions/precommit/bin/pre-commit"' >> ~/.bashrc
source ~/.bashrc

# 3. Run setup
cd /home/kelson/projetos/IFRN/ava/lms/mod_imagemap
bash setup-hooks.sh

# 4. Test
pre-commit run --all-files
```

### For Zsh users:

```bash
# 1. Add PATH
echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.zshrc
source ~/.zshrc

# 2. Add pre-commit alias
echo 'alias pre-commit="~/.pyenv/versions/precommit/bin/pre-commit"' >> ~/.zshrc
source ~/.zshrc

# 3. Run setup
cd /home/kelson/projetos/IFRN/ava/lms/mod_imagemap
bash setup-hooks.sh

# 4. Test
pre-commit run --all-files
```

---

## Troubleshooting

### moodle-plugin-ci still not found

```bash
# Verify it's installed
ls -la ~/.composer/vendor/bin/ | grep moodle

# Verify PATH is set
echo $PATH

# Manually test
~/.composer/vendor/bin/moodle-plugin-ci --version
```

### pre-commit still not found

```bash
# Verify alias is set
type pre-commit

# Manually test
~/.pyenv/versions/precommit/bin/pre-commit --version

# List pyenv versions
pyenv versions
```

---

## Next: Creating Your First Commit

Once hooks are running:

```bash
cd /home/kelson/projetos/IFRN/ava/lms/mod_imagemap

# Make a change
echo "# test" >> test-file.php

# Stage it
git add test-file.php

# Commit (hooks run automatically)
git commit -m "test: verify hooks are working"

# Hooks should run and either pass or show errors
```

---

## Documentation Links

- [CONTRIBUTING.md](CONTRIBUTING.md) - Full development setup guide
- [MIGRATION_HOOKS_v2.md](MIGRATION_HOOKS_v2.md) - Migration from old hooks
- [Moodle Plugin CI](https://github.com/moodlehq/moodle-plugin-ci) - Official tool

---

**Status**: ✅ Ready for final testing  
**Next Step**: Execute the "Complete Commands" section above for your shell (bash or zsh)
