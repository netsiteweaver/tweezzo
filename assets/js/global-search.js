/**
 * Global search in topbar: live typeahead for tasks (by task ref, name, etc.) and helpdesk tickets
 * (by ticket number, customer ref, subject, requester or company).
 */
(function () {
  var DEBOUNCE_MS = 300;
  var MIN_LENGTH = 2;
  var timer = null;
  var $input = $('#global-search-input');
  var $results = $('#global-search-results');

  if (!$input.length || !$results.length) return;

  function esc(v) {
    return (v == null ? '' : String(v)).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  var STATUS_BADGE = { 'new': 'primary', open: 'info', pending_customer: 'warning', on_hold: 'secondary', resolved: 'success', closed: 'dark' };

  function renderResults(tasks, tickets) {
    tasks = tasks || [];
    tickets = tickets || [];
    $results.empty();
    if (tasks.length === 0 && tickets.length === 0) {
      $results.append('<span class="dropdown-item text-muted">No tasks or tickets found</span>');
      $results.show();
      return;
    }
    var html = '';
    var both = tasks.length > 0 && tickets.length > 0;
    if (tasks.length) {
      if (both) html += '<h6 class="dropdown-header"><i class="fas fa-tasks mr-1"></i> Tasks</h6>';
      tasks.forEach(function (t) {
        var ref = esc(t.task_ref || t.task_number || '');
        var url = esc(t.view_url || '');
        var project = esc(t.project_name), sprint = esc(t.sprint_name);
        html += '<a class="dropdown-item global-search-item" href="' + url + '" data-view-url="' + url + '">';
        html += '<div class="d-flex justify-content-between align-items-start">';
        html += '<span class="font-weight-bold">' + (ref ? ref + ' &middot; ' : '') + esc(t.name) + '</span>';
        html += '</div>';
        if (project || sprint) {
          html += '<small class="text-muted">' + [project, sprint].filter(Boolean).join(' / ') + '</small>';
        }
        html += '</a>';
      });
    }
    if (tickets.length) {
      if (both) html += '<div class="dropdown-divider"></div>';
      html += '<h6 class="dropdown-header"><i class="fas fa-life-ring mr-1"></i> Tickets</h6>';
      tickets.forEach(function (t) {
        var url = esc(t.view_url || '');
        var badge = STATUS_BADGE[t.status] || 'secondary';
        html += '<a class="dropdown-item global-search-item" href="' + url + '" data-view-url="' + url + '">';
        html += '<div class="d-flex justify-content-between align-items-start">';
        html += '<span class="font-weight-bold">' + esc(t.ticket_number) + ' &middot; ' + esc(t.subject) + '</span>';
        html += '<span class="badge badge-' + badge + ' ml-2">' + esc(t.status_label) + '</span>';
        html += '</div>';
        html += '<small class="text-muted">' + esc(t.public_ref) + ' &middot; ' + esc(t.requester) + '</small>';
        html += '</a>';
      });
    }
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
        if (data && data.result) {
          renderResults(data.tasks, data.tickets);
        } else {
          renderResults([], []);
        }
      })
      .fail(function () {
        renderResults([], []);
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
