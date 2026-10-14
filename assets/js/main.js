(function () {
  'use strict';
  var button = document.querySelector('.nav-toggle');
  var navigation = document.getElementById('primary-navigation');
  if (!button || !navigation) return;
  button.addEventListener('click', function () {
    var opened = navigation.classList.toggle('is-open');
    button.setAttribute('aria-expanded', opened ? 'true' : 'false');
    button.setAttribute('aria-label', opened ? 'بستن فهرست' : 'نمایش فهرست');
    button.textContent = opened ? '×' : '☰';
  });
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
      navigation.classList.remove('is-open');
      button.setAttribute('aria-expanded', 'false');
      button.textContent = '☰';
      button.focus();
    }
  });
}());
