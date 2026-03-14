/**
 * Global search in topbar: live typeahead for tasks (by task ref, name, etc.).
 */
(function () {
  var DEBOUNCE_MS = 300;
  var MIN_LENGTH = 2;
  var timer = null;
  var $input = $('#global-search-input');
  var $results = $('#global-search-results');

  if (!$input.length || !$results.length) return;

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
      html += '<a class="dropdown-item global-search-item" href="' + url + '" data-view-url="' + url.replace(/"/g, '&quot;') + '">';
      html += '<div class="d-flex justify-content-between align-items-start">';
      html += '<span class="font-weight-bold">' + (ref ? ref + ' &middot; ' : '') + name + '</span>';
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
    $.get(base_url + 'search/tasks', { q: q })
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

  function scheduleSearch() {
    if (timer) clearTimeout(timer);
    timer = setTimeout(doSearch, DEBOUNCE_MS);
  }

  $input.on('input', scheduleSearch);
  $input.on('focus', function () {
    var q = ($input.val() || '').trim();
    if (q.length >= MIN_LENGTH) doSearch();
  });

  $(document).on('click', '.global-search-item', function (e) {
    var url = $(this).data('view-url') || $(this).attr('href');
    if (url) {
      e.preventDefault();
      window.location.href = url;
    }
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('#navbar-global-search').length) {
      $results.hide();
    }
  });

  $input.on('keydown', function (e) {
    if (e.key === 'Escape') {
      $results.hide();
      $input.blur();
    }
  });
})();
