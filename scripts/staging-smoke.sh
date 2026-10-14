#!/usr/bin/env bash
set -euo pipefail
command -v wp >/dev/null 2>&1 || { echo "Requires WP CLI" >&2; exit 2; }
wp core is-installed
wp theme is-installed bazarnama
wp theme is-active bazarnama
wp plugin is-active woocommerce
for key in woocommerce_shop_page_id woocommerce_cart_page_id woocommerce_checkout_page_id woocommerce_myaccount_page_id; do
  id="$(wp option get "$key" --format=plaintext)"
  [[ "$id" =~ ^[1-9][0-9]*$ ]] || { echo "Invalid $key" >&2; exit 1; }
  status="$(wp post get "$id" --field=post_status)"
  [[ "$status" == publish ]] || { echo "Unpublished $key" >&2; exit 1; }
  echo "OK $key"
done
echo "Read-only staging preflight PASS (not a purchase test)."
