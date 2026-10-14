#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$root"
"$root/scripts/verify.sh"
for exe in zip unzip; do command -v "$exe" >/dev/null 2>&1 || { echo "Missing $exe" >&2; exit 2; }; done
tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT
mkdir -p "$tmp/bazarnama" "$root/dist"
for file in style.css functions.php header.php footer.php front-page.php index.php page.php single.php search.php searchform.php 404.php comments.php woocommerce.php README.md; do
  cp "$file" "$tmp/bazarnama/$file"
done
for folder in inc assets templates woocommerce; do cp -R "$folder" "$tmp/bazarnama/$folder"; done
dest="$root/dist/bazarnama-1.4.0.zip"
rm -f "$dest"
( cd "$tmp" && zip -X -qr "$dest" bazarnama )
unzip -t "$dest" >/dev/null
if command -v sha256sum >/dev/null 2>&1; then
  ( cd "$root/dist" && sha256sum bazarnama-1.4.0.zip > SHA256SUMS )
fi
echo "ZIP candidate: $dest"
echo "NOT production approved. Requires actual WooCommerce end-to-end testing."
