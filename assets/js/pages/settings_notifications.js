var saved_smtp_settings = {};
jQuery(function(){
	
	$('.updatenotifications').on("click",function(){
		var notifications = [];
		var type = $(this).data("type");
		$('table#'+type+' tbody tr').each(function(i,j){
			let user = $(this).data('user');
			let checked =$(this).find('input[type=checkbox]').is(":checked")

			if (checked) notifications.push(user);
		})

		$.ajax({
			url: base_url + 'settings/updatenotifications',
			method: 'POST',
			dataType: 'JSON',
			data: {data:notifications, type:type},
			success: function(response) {
				window.location.reload();
			},
			complete: function() {

			}
		})
    })

	$('#updateAll').on("click", function(){
		var notifications = [];
		//get all notifications
		$('.updatenotifications').each(function(i,j){
			var type = $(this).data("type");
			$('table#'+type+' tbody tr').each(function(i,j){
				let user = $(this).data('user');
				let checked =$(this).find('input[type=checkbox]').is(":checked")
	
				if (checked) {
					notifications.push({type:type, user_id:user})
				}
			})
		})
		if(notifications.length==0) {
			bootbox.alert("Please select at least one user to update notifications");
			return false;
		}
		$.ajax({
			url: base_url + 'settings/updateAllNotifications',
			method: 'POST',
			dataType: 'JSON',
			data: {notifications},
			success: function(response) {
				window.location.reload();
			},
			complete: function() {

			}
		})
	})

	$('#update2').on("click", function(){
		var users = [];
		$('table#reset_password tr').each(function(i,j){
			if($(this).find('input[type=checkbox]').is(":checked")){
				users.push($(this).data('id'));
			}
		})

		$.ajax({
			url: base_url + 'settings/updatenotifications2',
			method: 'POST',
			dataType: 'JSON',
			data: {data:users},
			success: function(response) {
				window.location.reload();
			}
		})
	})

	$('#update3').on("click", function(){
		var workshop_department = $('select[name=workshop_department]').val();
		if(workshop_department=='') return false;

		$.ajax({
			url: base_url + 'settings/updatenotifications3',
			method: 'POST',
			dataType: 'JSON',
			data: {workshop_department:workshop_department},
			success: function(response) {
				window.location.reload();
			}
		})
	})
	

	$('.test-email').on('click', function(){

		if($(this).hasClass("running")) return false;

		if(confirm('By proceeding, an email will be sent to the email associated with your account. Would you like to proceed?'))
		{
			//check if smtp settings have changed
			let valid = true;
			$('.form-group.has-warning').removeClass('has-warning');
			$('.save-state').each(function(i,row){
				let name = $(row).attr("name");
				name = name.substring(14,name.length-1);
				if($(this).val() != saved_smtp_settings[name]) {
					$(this).closest('.form-group').addClass("has-warning");
					valid = false;
				}
			})

			if(!valid){
				if(confirm('Some of the settings have changed but has not yet been saved. Test Email will use the saved settings only. Would you still like to proceed without saving?')){
					sendTestEmail();
				}
			}else{
				sendTestEmail();
			}
			
		}
	})

})
