#!/bin/bash
set -u
project_dir="$(cd "$(dirname "$0")/.." && pwd)"
cd "$project_dir" || exit 1
node_bin="$(command -v node || true)"
if [ -z "$node_bin" ]; then
  node_bin="/Users/uche/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/bin/node"
fi
if [ ! -x "$node_bin" ]; then echo 'Node.js is missing. Install Node.js 22 or newer.'; exit 1; fi
export PATH="$(dirname "$node_bin"):$PATH"
if [ ! -f node_modules/@playwright/test/cli.js ]; then echo 'Install dependencies first: pnpm install'; exit 1; fi
mode="${1:-smoke}"
shift || true
# Listing scenarios must not overwrite saved run evidence.
if [[ "$mode" == "list" || " $* " == *" --list "* ]]; then set -- "$@" --reporter=list; fi
case "$mode" in
  full) export RUN_LIFECYCLE=true RUN_CRITICAL_STAGING=true RUN_SANDBOX_GATEWAY=true RUN_EMAIL_JOURNEYS=true RUN_SETTLEMENT_CHECK=true REPORT_DIR="reports/full-$(date +%Y%m%d-%H%M%S)"; exec "$node_bin" node_modules/@playwright/test/cli.js test --workers=1 "$@" ;;
  smoke) exec "$node_bin" node_modules/@playwright/test/cli.js test --grep @smoke --project=desktop "$@" ;;
  regression) exec "$node_bin" node_modules/@playwright/test/cli.js test --project=desktop "$@" ;;
  lifecycle) if [[ " $* " != *" --list "* ]]; then "$node_bin" scripts/prepare-report.mjs; fi; export RUN_LIFECYCLE=true; exec "$node_bin" node_modules/@playwright/test/cli.js test tests/lifecycle.spec.ts --project=desktop "$@" ;;
  critical-staging) export RUN_LIFECYCLE=true RUN_CRITICAL_STAGING=true REPORT_DIR="reports/critical-staging-$(date +%Y%m%d-%H%M%S)"; exec "$node_bin" node_modules/@playwright/test/cli.js test tests/critical-staging.spec.ts --project=desktop "$@" ;;
  cancellations) exec "$0" critical-staging --grep ' cancellation:' "$@" ;;
  returns) exec "$0" critical-staging --grep ' return:' "$@" ;;
  settlement) export RUN_SETTLEMENT_CHECK=true REPORT_DIR="reports/settlement-$(date +%Y%m%d-%H%M%S)"; exec "$node_bin" node_modules/@playwright/test/cli.js test tests/settlement-check.spec.ts --project=desktop "$@" ;;
  source) exec "$node_bin" node_modules/@playwright/test/cli.js test tests/source-api.spec.ts tests/source-journeys.spec.ts --project=desktop "$@" ;;
  mobile) exec "$node_bin" node_modules/@playwright/test/cli.js test --project=mobile "$@" ;;
  report) exec "$node_bin" node_modules/@playwright/test/cli.js show-report reports/html "$@" ;;
  check) exec "$node_bin" node_modules/typescript/bin/tsc --noEmit "$@" ;;
  list) exec "$node_bin" node_modules/@playwright/test/cli.js test --list --project=desktop "$@" ;;
  *) echo 'Choose full, smoke, regression, mobile, report, check, lifecycle, source, critical-staging, settlement or list.'; exit 2 ;;
esac
