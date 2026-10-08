#!/bin/bash
set -eu
regression_dir="$(cd "$(dirname "$0")/.." && pwd)"
source_dir="${1:-$regression_dir/../..}"
[ -f "$source_dir/artisan" ] || { echo 'Choose the downloaded Laravel source folder.'; exit 1; }
mkdir -p "$source_dir/tests/Regression"
cp "$regression_dir"/backend/*.php "$source_dir/tests/Regression/"
cp "$regression_dir/backend/phpunit.regression.xml" "$source_dir/phpunit.regression.xml"
cp "$regression_dir/backend/phpunit.critical.xml" "$source_dir/phpunit.critical.xml"
echo 'Backend regression copied into the isolated source checkout.'
echo 'Requires PHP, Composer dependencies and SQLite. Run using phpunit.regression.xml only.'
