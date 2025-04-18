$(document).ready(function(){

  $('document').ready(function(){
  
  	$("#edit_user").on("submit",function(){
      var email = $("input[name=email]").val();
	  var user_level = $('input[name=user_level]').val();
      var id = $("input[name=id]").val();
  	  var valid = true;
	  var errorMessage = "";

      if( (user_level != 'Normal') && (email.length==0) ){
		errorMessage += "Email is mandatory for Admin Users<br>";
		valid = false;
	  }

      var pswd = $("input[name=pswd]").val();
      var pswd2 = $("input[name=pswd2]").val();

      if( (pswd.length>0) && (pswd!=pswd2) ){
		errorMessage += "New Password and New Password Confirmation must match exactly<br>";
        // bootbox.alert("New Password and New Password Confirmation must match exactly")
        valid = false;
      }

	  if(!valid){
		bootbox.alert(errorMessage)
	  }

  		$.ajax({
  			method:"POST",
  			url:base_url+"users/check_email",
  			data:{email:email,id:id},
  			dataType:"JSON",
  			async:false,
  			success:function(e){
  				console.log(e.result);
  				if(!e.result){
            Notify();
            toastr.clear();
  					bootbox.alert("Email is unique to each user and <strong>"+ email +"</strong> has already been used in another user's account.")
  				}
  				valid = e.result;
  			}
  		})
  		return valid;
  	})

    $("#close-modal").on("click",function(){
      $("#modal-email-required").removeClass("md-show")
    })

  })

})