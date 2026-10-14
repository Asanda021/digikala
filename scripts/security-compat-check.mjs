/**
 * Repeatable, dependency-free storefront smoke checks.
 * Node.js 18+ is enough. WooCommerce / PHP integration is NOT mocked here.
 */
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';
const read = (file) => readFileSync(new URL('../' + file, import.meta.url), 'utf8');
const discovery = read('inc/discovery.php');
const functions = read('functions.php');
const customer = read('inc/customer-journey.php');
const js = read('assets/js/discovery.js');
const checks = [
  [discovery.includes('name="min_price"') && discovery.includes('name="max_price"'),
    'WooCommerce-native price filter inputs'],
  [!discovery.includes("'key' => '_price'"), 'No incompatible postmeta price filtering'],
  [discovery.includes('instanceof WC_Product_Attribute'), 'Attribute type guard'],
  [discovery.includes("! $product->is_type( 'variation' )"), 'Variation product exclusion'],
  [discovery.includes('is_scalar( $raw_id )'), 'Reject nested product IDs'],
  [discovery.includes("check_ajax_referer( 'bazarnama_discovery'"), 'AJAX nonce gate'],
  [discovery.includes("'publish' === $product->get_status()"), 'Public product visibility gate'],
  [functions.includes('wc_get_products( $query )') && !functions.includes('new WP_Query'),
    'Native WooCommerce product data query'],
  [customer.includes('$tracking_view ||'), 'Do not cache guest order tracking'],
];
let count = 0;
for (const [ok, name] of checks) {
  assert.ok(ok, name);
  count++;
  console.log('PASS contract:', name);
}
function mockBrowser(storageBlocked = false) {
  const eventHandlers = {};
  const saved = new Map();
  const localStorage = {
    getItem(key) {
      if (storageBlocked) throw new Error('disabled');
      return saved.get(key) || null;
    },
    setItem(key, value) {
      if (storageBlocked) throw new Error('disabled');
      saved.set(key, value);
    },
  };
  const alerts = [];
  const document = {
    querySelector: () => null,
    getElementById: () => null,
    querySelectorAll: () => [],
    addEventListener: (name, fn) => { eventHandlers[name] = fn; },
    createElement: () => ({
      classList: { toggle() {} },
      setAttribute() {},
      appendChild() {},
      textContent: '',
    }),
    body: {
      appendChild(node) { alerts.push(node); },
    },
  };
  const window = {
    localStorage,
    bazarnamaDiscovery: {
      nonce: 'test-only',
      ajax: '/not-called',
      labels: {
        wishlist: 'علاقه‌مندی',
        compare: 'مقایسه',
        limitCompare: 'بیش از چهار کالا نمی‌شود.',
        limitWishlist: 'بیش از چهل کالا نمی‌شود.',
      },
    },
    addEventListener() {},
  };
  vm.runInNewContext(js, {
    window, document, localStorage, URLSearchParams, AbortController,
    setTimeout, clearTimeout, fetch: () => { throw new Error('network unexpected'); },
  }, { timeout: 2000 });
  function click(type, id) {
    eventHandlers.click({
      target: {
        closest() {
          return { getAttribute: (name) => name === 'data-bn-collection' ? type : String(id) };
        },
      },
    });
  }
  function get(type) {
    return JSON.parse(saved.get('bazarnama-' + type + '-v1') || '[]');
  }
  return { click, get, alerts };
}
const app = mockBrowser();
for (let id = 1; id <= 5; id++) app.click('compare', id);
assert.equal(JSON.stringify(app.get('compare')), '[1,2,3,4]');
count++;
app.click('compare', 3);
assert.equal(JSON.stringify(app.get('compare')), '[1,2,4]');
count++;
app.click('compare', 5);
assert.equal(JSON.stringify(app.get('compare')), '[1,2,4,5]');
count++;
for (let id = 1; id <= 41; id++) app.click('wishlist', id);
assert.equal(app.get('wishlist').length, 40);
count++;
const blocked = mockBrowser(true);
blocked.click('wishlist', 17);
assert.ok(blocked.alerts.some((node) => node.textContent.includes('دائمی')));
count++;
console.log('PASS', count, 'static and simulated-browser checks.');
console.log('NOTE: Real PHP lint, staging WooCommerce, browsers, gateways and orders remain untested.');
