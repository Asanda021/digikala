(function () {
  'use strict';
  if (!window.bazarnamaDiscovery) return;
  var cfg = window.bazarnamaDiscovery, labels = cfg.labels;
  var keys = { wishlist: 'bazarnama-wishlist-v1', compare: 'bazarnama-compare-v1' };
  var limits = { wishlist: 40, compare: 4 }, memory = { wishlist: [], compare: [] };
  var persistent = { wishlist: true, compare: true };
  function read(type) {
    if (!persistent[type]) return memory[type].slice();
    try {
      var items = JSON.parse(localStorage.getItem(keys[type]) || '[]');
      if (!Array.isArray(items)) return [];
      return Array.from(new Set(items.map(Number).filter(function (id) {
        return Number.isSafeInteger(id) && id > 0;
      }))).slice(0, limits[type]);
    } catch (err) { persistent[type] = false; return memory[type].slice(); }
  }
  function save(type, ids) {
    memory[type] = ids.slice();
    try { localStorage.setItem(keys[type], JSON.stringify(ids)); }
    catch (err) { persistent[type] = false; }
  }
  function el(tag, css, value) {
    var node = document.createElement(tag);
    if (css) node.className = css;
    if (value !== undefined) node.textContent = String(value);
    return node;
  }
  function notify(value) {
    var node = document.getElementById('bn-collection-notice') || document.getElementById('bn-discovery-alert');
    if (!node) {
      node = el('div'); node.id = 'bn-discovery-alert';
      node.setAttribute('role', 'status'); node.setAttribute('aria-live', 'polite');
      document.body.appendChild(node);
    }
    node.textContent = value;
  }
  function syncButtons() {
    document.querySelectorAll('[data-bn-collection][data-product-id]').forEach(function (button) {
      var type = button.getAttribute('data-bn-collection'), id = Number(button.getAttribute('data-product-id'));
      if (!keys[type]) return;
      var active = read(type).indexOf(id) !== -1;
      button.setAttribute('aria-pressed', String(active));
      button.classList.toggle('is-active', active);
      button.textContent = (active ? '✓ ' : '+ ') + labels[type];
    });
  }
  function send(action, body, signal) {
    var form = new URLSearchParams();
    form.set('action', action); form.set('nonce', cfg.nonce);
    Object.keys(body).forEach(function (key) {
      if (Array.isArray(body[key])) body[key].forEach(function (id) { form.append(key + '[]', String(id)); });
      else form.set(key, String(body[key]));
    });
    return fetch(cfg.ajax, { method: 'POST', credentials: 'same-origin', body: form, signal: signal })
      .then(function (response) { if (!response.ok) throw Error('Server error'); return response.json(); })
      .then(function (result) { if (!result.success) throw Error('Server error'); return result.data; });
  }
  function productLink(product) {
    var link = el('a', 'bn-result-card'), image = el('img'), text = el('span', 'bn-result-body');
    link.href = product.url;
    image.src = product.image; image.alt = ''; image.loading = 'lazy';
    text.appendChild(el('strong', '', product.title));
    text.appendChild(el('span', 'bn-result-price', product.price || ''));
    link.appendChild(image); link.appendChild(text);
    return link;
  }
  var search = document.getElementById('header-search-input');
  var suggestions = document.getElementById('bn-search-suggestions');
  var controller = null, timer = null, searchGeneration = 0;
  if (search && suggestions) {
    search.setAttribute('aria-controls', 'bn-search-suggestions');
    search.addEventListener('input', function () {
      clearTimeout(timer);
      if (controller) controller.abort();
      var generation = ++searchGeneration;
      var term = search.value.trim();
      if (term.length < 2) { suggestions.hidden = true; suggestions.replaceChildren(); return; }
      timer = setTimeout(function () {
        controller = new AbortController();
        send('bazarnama_search', { term: term }, controller.signal).then(function (data) {
          if (generation !== searchGeneration || search.value.trim() !== term) return;
          suggestions.replaceChildren();
          (data.products || []).forEach(function (product) { suggestions.appendChild(productLink(product)); });
          if (!(data.products || []).length) suggestions.appendChild(el('p', 'bn-empty-small', labels.noResults));
          suggestions.hidden = false;
        }).catch(function (err) {
          if (generation === searchGeneration && err.name !== 'AbortError' && search.value.trim() === term) suggestions.hidden = true;
        });
      }, 250);
    });
    search.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        searchGeneration++;
        clearTimeout(timer);
        if (controller) controller.abort();
        suggestions.hidden = true;
        suggestions.replaceChildren();
      }
    });
    document.addEventListener('click', function (event) {
      if (!suggestions.contains(event.target) && event.target !== search) {
        searchGeneration++;
        clearTimeout(timer);
        if (controller) controller.abort();
        suggestions.hidden = true;
      }
    });
  }
  var page = document.querySelector('[data-bn-page]');
  function removeButton(type, id) {
    var button = el('button', 'bn-remove-btn', 'حذف');
    button.type = 'button';
    button.setAttribute('data-bn-collection', type);
    button.setAttribute('data-product-id', String(id));
    return button;
  }
  function renderTable(products, target) {
    var wrap = el('div', 'bn-compare-scroll'), table = el('table', 'bn-compare-table');
    var headings = [ ['price', 'قیمت'], ['stock', 'موجودی'], ['sku', 'شناسه کالا'] ];
    var attrs = Object.create(null);
    products.forEach(function (product) {
      (product.attributes || []).forEach(function (attribute) { attrs[attribute.name] = true; });
    });
    Object.keys(attrs).forEach(function (name) { headings.push(['attr', name]); });
    var top = el('tr');
    top.appendChild(el('th', '', 'کالا'));
    products.forEach(function (product) {
      var th = el('th'); th.appendChild(productLink(product)); th.appendChild(removeButton('compare', product.id)); top.appendChild(th);
    });
    table.appendChild(top);
    headings.forEach(function (heading) {
      var tr = el('tr'); tr.appendChild(el('th', '', heading[1]));
      products.forEach(function (p) {
        var value = heading[0] === 'attr'
          ? ((p.attributes || []).find(function (a) { return a.name === heading[1]; }) || {}).value
          : p[heading[0]];
        tr.appendChild(el('td', '', value || '—'));
      });
      table.appendChild(tr);
    });
    wrap.appendChild(table); target.appendChild(wrap);
  }
  var collectionRequestVersion = 0;
  function renderPage() {
    if (!page) return;
    var requestVersion = ++collectionRequestVersion;
    var type = page.getAttribute('data-bn-page'), target = document.getElementById('bn-collection-results');
    var ids = read(type); target.replaceChildren();
    if (!ids.length) {
      target.appendChild(el('p', 'bn-collection-empty', labels[type === 'compare' ? 'emptyCompare' : 'emptyWishlist']));
      return;
    }
    target.appendChild(el('p', 'bn-collection-loading', 'در حال دریافت اطلاعات...'));
    send('bazarnama_products', { ids: ids }).then(function (data) {
      if (requestVersion !== collectionRequestVersion) return;
      target.replaceChildren();
      var products = data.products || [], valid = products.map(function (p) { return Number(p.id); });
      if (JSON.stringify(ids) !== JSON.stringify(valid)) { save(type, valid); syncButtons(); }
      if (!products.length) {
        target.appendChild(el('p', 'bn-collection-empty', labels[type === 'compare' ? 'emptyCompare' : 'emptyWishlist']));
      } else if (type === 'compare') {
        renderTable(products, target);
      } else {
        var grid = el('div', 'bn-saved-grid');
        products.forEach(function (product) {
          var card = el('article', 'bn-saved-product');
          card.appendChild(productLink(product));
          card.appendChild(el('p', 'bn-saved-meta', product.stock));
          card.appendChild(removeButton(type, product.id));
          grid.appendChild(card);
        });
        target.appendChild(grid);
      }
    }).catch(function () {
      if (requestVersion !== collectionRequestVersion) return;
      target.replaceChildren(el('p', 'bn-collection-empty', labels.error));
    });
  }
  document.addEventListener('click', function (event) {
    var button = event.target && typeof event.target.closest === 'function'
      ? event.target.closest('[data-bn-collection][data-product-id]') : null;
    if (!button) return;
    var type = button.getAttribute('data-bn-collection');
    var id = Number(button.getAttribute('data-product-id'));
    if (!keys[type] || !Number.isSafeInteger(id) || id <= 0) return;
    var ids = read(type), index = ids.indexOf(id);
    if (index >= 0) ids.splice(index, 1);
    else if (ids.length >= limits[type]) { notify(labels[type === 'compare' ? 'limitCompare' : 'limitWishlist']); return; }
    else ids.push(id);
    save(type, ids); syncButtons(); renderPage();
    if (!persistent[type]) notify('ذخیره‌سازی مرورگر غیرفعال است؛ فهرست دائمی نخواهد بود.');
  });
  window.addEventListener('storage', function (event) {
    if (event.key === keys.wishlist || event.key === keys.compare) { syncButtons(); renderPage(); }
  });
  syncButtons(); renderPage();
}());
