(function () {
  'use strict';
  var mobileButton = document.querySelector('.nav-toggle');
  var navigation = document.getElementById('primary-navigation');
  var categoryButton = document.querySelector('.category-toggle');
  var categoryPanel = document.getElementById('shop-category-panel');
  var searchInput = document.getElementById('header-search-input');
  var backButton = document.querySelector('.back-to-top');

  function setMobileMenu(open) {
    if (!mobileButton || !navigation) return;
    navigation.classList.toggle('is-open', open);
    mobileButton.setAttribute('aria-expanded', String(open));
    mobileButton.setAttribute('aria-label', open ? 'بستن فهرست' : 'نمایش فهرست');
    mobileButton.textContent = open ? '×' : '☰';
  }
  function setCategories(open) {
    if (!categoryButton || !categoryPanel) return;
    categoryPanel.hidden = !open;
    categoryButton.setAttribute('aria-expanded', String(open));
  }
  if (mobileButton && navigation) {
    mobileButton.addEventListener('click', function () {
      var open = mobileButton.getAttribute('aria-expanded') !== 'true';
      setMobileMenu(open);
      if (!open) setCategories(false);
    });
  }
  if (categoryButton && categoryPanel) {
    categoryButton.addEventListener('click', function () {
      setCategories(categoryButton.getAttribute('aria-expanded') !== 'true');
    });
  }
  document.addEventListener('click', function (event) {
    if (categoryButton && categoryPanel && !categoryButton.contains(event.target) && !categoryPanel.contains(event.target)) setCategories(false);
    if (mobileButton && navigation && navigation.classList.contains('is-open') && !navigation.contains(event.target) && !mobileButton.contains(event.target)) setMobileMenu(false);
  });
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      if (categoryButton && categoryButton.getAttribute('aria-expanded') === 'true') {
        setCategories(false); categoryButton.focus();
      } else if (mobileButton && mobileButton.getAttribute('aria-expanded') === 'true') {
        setMobileMenu(false); mobileButton.focus();
      }
    }
    if (event.key === '/' && searchInput && !event.ctrlKey && !event.altKey && !event.metaKey && !(event.target instanceof HTMLInputElement) && !(event.target instanceof HTMLTextAreaElement) && !event.target.isContentEditable) {
      event.preventDefault(); searchInput.focus();
    }
  });
  window.addEventListener('resize', function () { if (window.innerWidth > 820) setMobileMenu(false); });
  if (backButton) backButton.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
  });
}());
