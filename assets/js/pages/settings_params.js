var saved_smtp_settings = {};
jQuery(function(){
	//save state of SMTP settings
	$('.save-state').each(function(i,row){
		let name = $(row).attr("name");
		name = name.substring(14,name.length-1);

		saved_smtp_settings[name] = $(row).val();
	})

	$('.sidebar_collapse').on('click',function(){
    	
    	var status = $(this).val();
    	if(status==1){
    		$('body').addClass('sidebar-collapse')
    	}else{
    		$('body').removeClass('sidebar-collapse')
    	}
    })

    $('select[name=theme_color]').on('change',function(){
    	var theme = $(this).val();
    	$('body').removeClass('skin-red skin-purple skin-blue skin-yellow').addClass('skin-'+theme);
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

function sendTestEmail()
{
	$('#test-result').html("Sending test email ...");
	$('.test-email').addClass('running');
	$.ajax({
		url: base_url + 'settings/test_email',
		dataType:'JSON',
		method:'GET',
		success: function(response){
			if(response.result){
				$('#test-result').html('An email has been sent to <b>'+response.email+'</b>');
			}else{
				$('#test-result').html("An error occurred");
			}
		},
		complete: function() {
			$('.test-email').removeClass('running');
		}

	})
}