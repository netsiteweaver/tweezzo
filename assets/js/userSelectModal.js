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
    $.ajax({
        url: '/ajax/misc/getCustomerByTaskUuid',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.result) {
                var userList = data.data;
                $('#userList').empty();
                // var userSelect = $('#userSelect');
                // userSelect.empty();
                $.each(userList, function(index, user) {
                    // userSelect.append($('<option></option>').val(user.id).text(user.name));
                    // console.log(user);

                    var userItem = $('<li data-email = "'+user.email+'"data-name = "'+user.personName+'"class="list-group-item-action select-user cursor-pointer list-group-item"></li>').html("@"+user.personName + " &lt;" + user.email + "&gt;");
                    $('#userList').append(userItem);
                    userItem.on('click', function() {
                        var userName = $(this).data('name');
                        var userEmail = $(this).data('email');
                        $('.summernote').summernote('pasteHTML', userEmail + ": ");
                        $('.summernote').summernote('focus');
                        $('#userSelectModal').modal('hide');
                    });
                });
            } else {
                alert('Error fetching user list');
            }
        },
        error: function() {
            alert('Error fetching user list');
        }
    });
}