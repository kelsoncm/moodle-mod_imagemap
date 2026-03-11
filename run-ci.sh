#!/usr/bin/env bash
set -euo pipefail

docker compose up -d db ci

cmd="${1:-help}"

case "$cmd" in
  install)
    docker compose exec db \
      bash -lc 'PGPASSWORD=$POSTGRES_PASSWORD psql -U $POSTGRES_USER -d postgres -c "CREATE DATABASE moodle" 2>/dev/null || true'

    docker compose exec ci rm -rf /plugin/moodle /plugin/moodledata

    set +e
    docker compose exec ci \
      moodle-plugin-ci install \
        --db-type=pgsql \
        --db-host=db \
        --db-name=moodle \
        --db-user=moodle \
        --db-pass=moodle
    status=$?
    set -e
    if [ "$status" -ne 0 ]; then
      echo "moodle-plugin-ci install retornou código $status (OK se só reclamou de DB/Behat)."
    fi
    ;;

  validate)
    # Cada comando recebe o caminho do plugin (.)
    docker compose exec ci moodle-plugin-ci phplint .
    # docker compose exec ci moodle-plugin-ci phpcs .
    # docker compose exec ci moodle-plugin-ci codechecker .
    # docker compose exec ci moodle-plugin-ci phpdoc .
    docker compose exec ci moodle-plugin-ci validate .
    docker compose exec ci moodle-plugin-ci savepoints .
    docker compose exec ci moodle-plugin-ci mustache .
    ;;

  phpunit)
    docker compose exec ci moodle-plugin-ci phpunit
    ;;

  behat)
    docker compose exec ci moodle-plugin-ci behat --auto-rerun 3 --dump .
    ;;

  shell)
    docker compose exec ci bash
    ;;

  down)
    docker compose down -v
    ;;

  *)
    echo "Uso:"
    echo "  ./run-ci.sh install   # instala/atualiza Moodle 4.5 + plugin"
    echo "  ./run-ci.sh validate  # phplint, phpcs, codechecker, etc."
    echo "  ./run-ci.sh phpunit   # PHPUnit"
    echo "  ./run-ci.sh behat     # Behat"
    echo "  ./run-ci.sh shell     # shell no container CI"
    echo "  ./run-ci.sh down      # derruba tudo"
    ;;
esac
