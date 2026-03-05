#!/usr/bin/env bash
set -euo pipefail

if command -v moodle-plugin-ci >/dev/null 2>&1; then
    exec moodle-plugin-ci "$@"
fi

if command -v composer >/dev/null 2>&1; then
    COMPOSER_BINDIR="$(composer global config bin-dir --absolute 2>/dev/null || true)"
    if [[ -n "${COMPOSER_BINDIR}" && -x "${COMPOSER_BINDIR}/moodle-plugin-ci" ]]; then
        exec "${COMPOSER_BINDIR}/moodle-plugin-ci" "$@"
    fi
fi

for CANDIDATE in \
    "$HOME/.config/composer/vendor/bin/moodle-plugin-ci" \
    "$HOME/.composer/vendor/bin/moodle-plugin-ci"
do
    if [[ -x "${CANDIDATE}" ]]; then
        exec "${CANDIDATE}" "$@"
    fi
done

echo "Executable moodle-plugin-ci not found." >&2
echo "Install with: composer global require moodlehq/moodle-plugin-ci" >&2
echo "Or ensure Composer global bin-dir is in PATH." >&2
exit 127
