#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$root"
for exe in php node; do
  command -v "$exe" >/dev/null 2>&1 || { echo "Missing $exe" >&2; exit 2; }
done
grep -q 'Theme Name: Bazarnama' style.css
for essential in functions.php index.php woocommerce.php inc/discovery.php inc/customer-journey.php; do
  test -s "$essential"
done
while IFS= read -r -d '' file; do
  php -l "$file" >/dev/null
  echo "PHP OK $file"
done < <(find . -type f -name '*.php' -not -path './.git/*' -not -path './dist/*' -print0)
while IFS= read -r -d '' file; do
  node --check "$file"
  echo "JS OK $file"
done < <(find assets/js -type f -name '*.js' -print0)
node scripts/security-compat-check.mjs
echo "STATIC PASS ONLY. WordPress and checkout runtime tests are required."
