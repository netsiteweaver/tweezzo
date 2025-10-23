/**
 * Online Users Tracking
 * Updates the online users list in real-time
 */

// Global variable to track the update interval
var onlineUsersInterval;

$(document).ready(function() {
    // Initialize online users if we're on a page that shows them
    if ($('#online-users-dropdown').length > 0 || $('#dashboard-online-count').length > 0) {
        updateOnlineUsers();
        
        // Update every 10 seconds
        onlineUsersInterval = setInterval(function() {
            updateOnlineUsers();
        }, 10000);
    }
});

/**
 * Fetch and update the online users list
 */
function updateOnlineUsers() {
    $.ajax({
        url: base_url + "ajax/misc/getOnlineUsers",
        method: "GET",
        dataType: "json",
        showLoader: false, // Don't show loader for this request
        success: function(response) {
            if (response.result) {
                updateOnlineUsersUI(response);
            }
        },
        error: function() {
            console.log('Failed to fetch online users');
        }
    });
}

/**
 * Update the UI with online users data
 */
function updateOnlineUsersUI(data) {
    // Update topbar badge count
    $('#online-users-count').text(data.total_count);
    
    // Update dashboard counts if on dashboard
    if ($('#dashboard-online-count').length > 0) {
        $('#dashboard-online-count').text(data.total_count);
        $('#admin-count').text(data.admin_count);
        $('#developer-count').text(data.developer_count);
        $('#customer-count').text(data.customer_count);
        
        // Update user lists on dashboard
        updateDashboardUserList('admin', data.grouped.admin);
        updateDashboardUserList('developer', data.grouped.developer);
        updateDashboardUserList('customer', data.grouped.customer);
    }
    
    // Update dropdown menu
    updateTopbarDropdown(data);
}

/**
 * Update the dashboard user lists
 */
function updateDashboardUserList(type, users) {
    var listId = '#' + type + '-list';
    var html = '';
    
    if (users.length === 0) {
        html = '<div class="text-center text-muted p-2"><small>No ' + type + 's online</small></div>';
    } else {
        users.forEach(function(user) {
            var lastActivity = moment(user.last_activity).fromNow();
            
            html += '<div class="online-user-item">';
            html += '  <div class="user-info">';
            html += '    <div class="user-name">' + user.name + '</div>';
            if (user.email) {
                html += '    <div class="user-email">' + user.email + '</div>';
            }
            html += '    <div class="user-status"><i class="fas fa-circle"></i> Active ' + lastActivity + '</div>';
            html += '  </div>';
            html += '</div>';
        });
    }
    
    $(listId).html(html);
}

/**
 * Update the topbar dropdown with online users
 */
function updateTopbarDropdown(data) {
    var html = '';
    
    if (data.total_count === 0) {
        html = '<div class="dropdown-item text-center text-muted">No users online</div>';
    } else {
        // Add admins
        if (data.grouped.admin.length > 0) {
            html += '<span class="dropdown-header"><i class="fas fa-user-shield text-info"></i> Admins (' + data.grouped.admin.length + ')</span>';
            data.grouped.admin.forEach(function(user) {
                html += createDropdownUserItem(user);
            });
            html += '<div class="dropdown-divider"></div>';
        }
        
        // Add developers
        if (data.grouped.developer.length > 0) {
            html += '<span class="dropdown-header"><i class="fas fa-code text-success"></i> Developers (' + data.grouped.developer.length + ')</span>';
            data.grouped.developer.forEach(function(user) {
                html += createDropdownUserItem(user);
            });
            html += '<div class="dropdown-divider"></div>';
        }
        
        // Add customers
        if (data.grouped.customer.length > 0) {
            html += '<span class="dropdown-header"><i class="fas fa-users text-warning"></i> Customers (' + data.grouped.customer.length + ')</span>';
            data.grouped.customer.forEach(function(user) {
                html += createDropdownUserItem(user);
            });
        }
    }
    
    $('#online-users-list').html(html);
}

/**
 * Create a dropdown user item
 */
function createDropdownUserItem(user) {
    var lastActivity = moment(user.last_activity).fromNow();
    
    var html = '';
    html += '<a href="#" class="dropdown-item">';
    html += '  <div class="d-flex justify-content-between align-items-start">';
    html += '    <div>';
    html += '      <h3 class="dropdown-item-title mb-1">' + user.name + '</h3>';
    if (user.email) {
        html += '      <p class="text-sm text-muted mb-1">' + user.email + '</p>';
    }
    html += '      <p class="text-sm text-muted mb-0"><i class="far fa-clock mr-1"></i> Active ' + lastActivity + '</p>';
    html += '    </div>';
    html += '    <span class="text-success"><i class="fas fa-circle"></i></span>';
    html += '  </div>';
    html += '</a>';
    
    return html;
}

