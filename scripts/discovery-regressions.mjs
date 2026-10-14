/**
 * Deterministic regression suite for overlapping AJAX and restricted browser storage.
 * This tests genuine discovery.js with a minimal DOM; not WooCommerce HTTP runtime.
 */
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';
const source = readFileSync(new URL('../assets/js/discovery.js', import.meta.url), 'utf8');

function node(tag) {
  return {
    tag, attrs: {}, children: [], textContent: '', hidden: false,
    setAttribute(key, value) { this.attrs[key] = value; },
    getAttribute(key) { return this.attrs[key]; },
    appendChild(child) { this.children.push(child); },
    replaceChildren(...children) { this.children = children; },
    classList: { toggle() {} },
  };
}
function prepare(readOnly = false) {
  const store = new Map([['bazarnama-compare-v1', '[1,2]']]);
  const listeners = {};
  const pending = [];
  const root = node('root');
  const localStorage = {
    getItem(key) { return store.get(key) || null; },
    setItem(key, val) {
      if (readOnly) throw new Error('Storage write disabled');
      store.set(key, val);
    },
  };
  const document = {
    createElement: node,
    getElementById(key) { return key === 'bn-collection-results' ? root : null; },
    querySelector(key) { return key === '[data-bn-page]' ? { getAttribute: () => 'compare' } : null; },
    querySelectorAll: () => [],
    addEventListener(key, callback) { listeners[key] = callback; },
    body: node('body'),
  };
  const window = {
    localStorage, addEventListener() {},
    bazarnamaDiscovery: {
      ajax: '/mock', nonce: 'test',
      labels: {
        wishlist: 'علاقه‌مندی', compare: 'مقایسه',
        emptyWishlist: 'بدون علاقه‌مندی', emptyCompare: 'بدون کالا',
        limitWishlist: 'حداکثر چهل', limitCompare: 'حداکثر چهار',
        error: 'خطا', noResults: 'بدون نتیجه',
      },
    },
  };
  const fetch = () => new Promise((resolve) => { pending.push(resolve); });
  vm.runInNewContext(source, { window, document, localStorage, fetch, URLSearchParams, AbortController }, { timeout: 2000 });
  function click(id) {
    listeners.click({
      target: {
        closest: () => ({
          getAttribute(key) { return key === 'data-bn-collection' ? 'compare' : String(id); },
          classList: { toggle() {} },
          setAttribute() {},
        }),
      },
    });
  }
  function respond(index, ids) {
    const products = ids.map(id => ({
      id, title: 'Product' + id, url: '/', image: '', price: '10',
      stock: 'موجود', sku: '', attributes: [],
    }));
    pending[index]({ ok: true, json: async () => ({ success: true, data: { products } }) });
  }
  const displayed = () => JSON.stringify(root).match(/Product[0-9]+/g) || [];
  return { root, store, pending, listeners, click, respond, displayed };
}
const flush = () => new Promise(resolve => setImmediate(resolve));

// Requests finish out of order: the old result must not overwrite the new one.
{
  const app = prepare();
  assert.equal(app.pending.length, 1, 'first render sends a request');
  app.click(1);
  assert.equal(app.pending.length, 2, 'removing item sends an updated request');
  app.respond(1, [2]);
  await flush();
  assert.deepEqual(app.displayed(), ['Product2']);
  app.respond(0, [1, 2]);
  await flush();
  assert.deepEqual(app.displayed(), ['Product2'], 'stale AJAX response ignored');
  console.log('PASS: stale AJAX cannot overwrite new collection');
}

// If reading localStorage works but writing fails, in-session state must work.
{
  const app = prepare(true);
  app.click(1);
  app.click(2);
  assert.equal(app.pending.length, 2, 'cleared in-memory collection must not refetch');
  assert.equal(app.root.children[0].textContent, 'بدون کالا');
  assert.equal(app.store.get('bazarnama-compare-v1'), '[1,2]', 'write failure not persisted');
  console.log('PASS: in-memory collection persists during read-only storage');
}

// Events from unusual targets must not trigger a TypeError.
{
  const app = prepare();
  assert.doesNotThrow(() => app.listeners.click({ target: { nodeType: 3 } }));
  console.log('PASS: non-element click target ignored');
}
console.log('PASS: all deterministic browser-state regressions');
