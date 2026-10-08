#!/bin/bash
set -eu
regression_dir="$(cd "$(dirname "$0")/.." && pwd)"
source_dir="${1:-$regression_dir/../..}"
php_bin="$(command -v php || true)"
if [ -z "$php_bin" ]; then
  for candidate in /opt/homebrew/bin/php /opt/homebrew/opt/php@8.2/bin/php /usr/local/bin/php; do
    if [ -x "$candidate" ]; then php_bin="$candidate"; break; fi
  done
fi
[ -n "$php_bin" ] || { echo 'Critical backend tests need PHP with SQLite; PHP could not be found.'; exit 1; }
[ -f "$source_dir/vendor/autoload.php" ] || { echo 'Install Composer dependencies in the isolated site-source checkout first.'; exit 1; }
[ ! -f "$source_dir/bootstrap/cache/config.php" ] || { echo 'Remove cached configuration in the isolated checkout before testing.'; exit 1; }
"$regression_dir/backend/prepare-current-source.sh" "$source_dir"
mkdir -p "$regression_dir/reports"
export CRITICAL_EVIDENCE_DIR="$regression_dir/reports/critical-backend-evidence"
cd "$source_dir"
exec "$php_bin" vendor/bin/phpunit -c phpunit.critical.xml --log-junit "$regression_dir/reports/critical-backend.xml"
