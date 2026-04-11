/**
 * Admin back office: toggle AdminLTE dark mode (body.dark-mode), persist preference.
 */
(function ($) {
  'use strict';

  var STORAGE_KEY = 'tweezzo-admin-theme';

  function setChrome(dark) {
    document.documentElement.classList.toggle('admin-dark', dark);
    var meta = document.querySelector('meta[name="theme-color"]');
    if (meta) {
      meta.setAttribute('content', dark ? '#343a40' : '#ffffff');
    }
  }

  function syncToggleButton(dark) {
    var $btn = $('#admin-theme-toggle');
    if (!$btn.length) {
      return;
    }
    var $icon = $btn.find('i').first();
    if ($icon.length) {
      $icon.attr('class', dark ? 'fas fa-sun' : 'fas fa-moon');
    }
    $btn.attr('title', dark ? 'Switch to light theme' : 'Switch to dark theme');
    $btn.attr('aria-pressed', dark ? 'true' : 'false');
  }

  function applyDarkMode(dark) {
    document.body.classList.toggle('dark-mode', !!dark);
    setChrome(!!dark);
    syncToggleButton(!!dark);
  }

  $(function () {
    var dark = document.body.classList.contains('dark-mode');
    syncToggleButton(dark);
    setChrome(dark);
  });

  $(document).on('click', '#admin-theme-toggle', function (e) {
    e.preventDefault();
    var dark = !document.body.classList.contains('dark-mode');
    try {
      localStorage.setItem(STORAGE_KEY, dark ? 'dark' : 'light');
    } catch (err) {}
    applyDarkMode(dark);
  });

  $(window).on('storage', function (e) {
    var ev = e.originalEvent;
    if (!ev || ev.key !== STORAGE_KEY) {
      return;
    }
    if (ev.newValue === 'dark') {
      applyDarkMode(true);
    } else if (ev.newValue === 'light') {
      applyDarkMode(false);
    }
  });
})(jQuery);
