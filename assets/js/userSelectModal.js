jQuery(function(){

    init();

    $('#openUserModal') .on('click', function() {
        $('#userSelectModal').modal('show');
    });

    $('#userList').on('click', '.select-user', function() {
        
        
    });

    $('body').on('keydown', function(e) {
		if(e.key == '@') {
            // console.log(e.originalEvent.target.className)
			if(e.originalEvent.target.className.includes('note-editable')){
                showUserSelectModal();
			}
		}
    })
})

function showUserSelectModal() {
    $('#userSelectModal').modal('show');
}

function init() {
    let url = "/ajax/Misc/getUsersByTaskUuid";
    const path = window.location.pathname;
    const pathSegments = path.split('/').filter(segment => segment !== '');
    if(pathSegments.indexOf('portal') == -1){
        url = '/portal/Misc/getUsersByTaskUuid';
    }

    // Get the full query string
    const queryString = window.location.search;

    // Parse it
    const urlParams = new URLSearchParams(queryString);

    const taskUuid = urlParams.get('task_uuid');

    // console.log(queryString,urlParams,taskUuid)
    $.ajax({
        url: url,
        data:{taskUuid:taskUuid},
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.result) {
                var userList = data.data;
                $('#userList').empty();
                // var userSelect = $('#userSelect');
                // userSelect.empty();
                $('#userList').append("<li class='list-title list-group-item list-group-item-dark'>Customer</li>");
                $.each(data.customer, function(index, user) {
                    var userItem = $('<li data-email = "'+user.email+'"data-name = "'+user.personName+'"class="list-group-item-action select-user cursor-pointer list-group-item"></li>').html("@"+user.personName + " &lt;" + user.email + "&gt;" );
                    $('#userList').append(userItem);
                    userItem.on('click', function() {
                        var userName = $(this).data('name');
                        var userEmail = $(this).data('email');
                        $('.summernote').summernote('pasteHTML', userName + ": ");
                        $('.summernote').summernote('focus');
                        $('#userSelectModal').modal('hide');
                    });
                });
                $('#userList').append("<li class='list-title list-group-item list-group-item-success'>Developers</li>");
                $.each(data.developers, function(index, user) {
                    var userItem = $('<li data-email = "'+user.email+'"data-name = "'+user.personName+'"class="list-group-item-action select-user cursor-pointer list-group-item"></li>').html("@"+user.personName + " &lt;" + user.email + "&gt;" );
                    $('#userList').append(userItem);
                    userItem.on('click', function() {
                        var userName = $(this).data('name');
                        var userEmail = $(this).data('email');
                        $('.summernote').summernote('pasteHTML', userName + ": ");
                        $('.summernote').summernote('focus');
                        $('#userSelectModal').modal('hide');
                    });
                });
                $('#userList').append("<li class='list-title list-group-item list-group-item-warning'>Admins</li>");
                $.each(data.admins, function(index, user) {
                    var userItem = $('<li data-email = "'+user.email+'"data-name = "'+user.personName+'"class="list-group-item-action select-user cursor-pointer list-group-item"></li>').html("@"+user.personName + " &lt;" + user.email + "&gt;" );
                    $('#userList').append(userItem);
                    userItem.on('click', function() {
                        var userName = $(this).data('name');
                        var userEmail = $(this).data('email');
                        $('.summernote').summernote('pasteHTML', userName + ": ");
                        $('.summernote').summernote('focus');
                        $('#userSelectModal').modal('hide');
                    });
                });
                
            } else {
                // alert('Error fetching user list');
            }
        },
        error: function() {
            alert('Error fetching user list');
        }
    });
}