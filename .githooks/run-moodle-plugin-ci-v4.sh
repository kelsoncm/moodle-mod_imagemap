#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CI_DIR="${ROOT_DIR}/.moodle-plugin-ci"
CI_BIN="${CI_DIR}/bin/moodle-plugin-ci"

if [[ ! -x "${CI_BIN}" ]]; then
    echo "Bootstrapping local moodle-plugin-ci (^4) in ${CI_DIR}..."
    composer create-project -n --no-dev --prefer-dist moodlehq/moodle-plugin-ci "${CI_DIR}" "^4"
fi

exec "${CI_BIN}" "$@"
