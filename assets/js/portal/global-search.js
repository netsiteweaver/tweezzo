/**
 * Portal global search: live typeahead for tasks (developer and customer portals).
 * Uses .portal-global-search-input and data-portal-search-url on the parent li.
 */
(function () {
  var DEBOUNCE_MS = 300;
  var MIN_LENGTH = 2;

  $(function () {
    $('.portal-global-search-input').each(function () {
      var $input = $(this);
      var $container = $input.closest('[data-portal-search-url]');
      var $results = $input.siblings('.portal-global-search-results');
      var searchUrl = $container.data('portal-search-url');
      if (!$results.length || !searchUrl) return;

      var timer = null;

      function renderResults(tasks) {
        $results.empty();
        if (!tasks || tasks.length === 0) {
          $results.append('<span class="dropdown-item text-muted">No tasks found</span>');
          $results.show();
          return;
        }
        var html = '';
        tasks.forEach(function (t) {
          var ref = (t.task_ref || t.task_number || '').toString();
          var name = (t.name || '').toString().replace(/</g, '&lt;').replace(/>/g, '&gt;');
          var project = (t.project_name || '').toString().replace(/</g, '&lt;').replace(/>/g, '&gt;');
          var sprint = (t.sprint_name || '').toString().replace(/</g, '&lt;').replace(/>/g, '&gt;');
          var url = (t.view_url || '').toString();
          html += '<a class="dropdown-item portal-search-item" href="' + url + '" data-view-url="' + url.replace(/"/g, '&quot;') + '">';
          html += '<div class="d-flex justify-content-between align-items-start">';
          html += '<span class="fw-bold">' + (ref ? ref + ' &middot; ' : '') + name + '</span>';
          html += '</div>';
          if (project || sprint) {
            html += '<small class="text-muted">' + [project, sprint].filter(Boolean).join(' / ') + '</small>';
          }
          html += '</a>';
        });
        $results.html(html).show();
      }

      function doSearch() {
        var q = ($input.val() || '').trim();
        if (q.length < MIN_LENGTH) {
          $results.hide().empty();
          return;
        }
        $.get(searchUrl, { q: q })
          .done(function (data) {
            if (data && data.result && Array.isArray(data.tasks)) {
              renderResults(data.tasks);
            } else {
              renderResults([]);
            }
          })
          .fail(function () {
            renderResults([]);
          });
      }

      $input.on('input', function () {
        if (timer) clearTimeout(timer);
        timer = setTimeout(doSearch, DEBOUNCE_MS);
      });
      $input.on('focus', function () {
        var q = ($input.val() || '').trim();
        if (q.length >= MIN_LENGTH) doSearch();
      });

      $(document).on('click', '.portal-search-item', function (e) {
        var url = $(this).data('view-url') || $(this).attr('href');
        if (url) {
          e.preventDefault();
          window.location.href = url;
        }
      });

      $input.on('keydown', function (e) {
        if (e.key === 'Escape') {
          $results.hide();
          $input.blur();
        }
      });
    });

    $(document).on('click', function (e) {
      if (!$(e.target).closest('.portal-global-search-input').length && !$(e.target).closest('.portal-global-search-results').length) {
        $('.portal-global-search-results').hide();
      }
    });
  });
})();
