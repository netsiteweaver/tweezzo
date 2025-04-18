$(document).ready(function(){

  // $('body').prepend('<a href="#" class="back-to-top"><i class=\'fa fa-chevron-up\'></i></a>');

  Search('searchText','tbl1');


  var amountScrolled = 100;

  $(window).scroll(function() {
    if ( $(window).scrollTop() > amountScrolled ) {
      $('a.back-to-top').fadeIn('slow');
    } else {
      $('a.back-to-top').fadeOut('slow');
    }
  });
  $('a.back-to-top').click(function() {
    $('html, body').animate({
      scrollTop: 0
    }, 700);
    return false;
  });

  $('document').ready(function(){

      $('.single-select').on("click",function(){
          let elem = $(this).is(":checked");
          let name = $(this).attr("name");
          if(elem){
              $('.single-select').prop("checked",false);
              $('input[name='+name+'].single-select').prop("checked",true);

              $('select[name=level]').val("Normal")
          }
      })

      $('select[name=level]').on("change", function() {
        if($(this).val() == 'Normal'){
          $('input[name=email]').removeClass("required")
        }else{
          $('input[name=email]').addClass("required")

        }
      })

      // $('select[name=level]').on('change',function(){
      //     let elem = $(this).val();
      //     if(elem != 'Normal'){
      //         toastr.info("Higher level users cannot be salesperson/delivery boy/store keeper");
      //         $('.single-select').prop("checked",false);
      //     }
      // })

    $('#text_to_filter').on('keyup',function(e){
      var text_to_filter = $(this).val().toLowerCase()
      if(text_to_filter.length == 0){
        $('table#permission_table tr').show();
      }else{
        $('table#permission_table tr').hide();
      }

      
      // console.log('==============================')
      console.log("Searching for "+text_to_filter)
      // console.log('==============================')

      $('table#permission_table tr').each(function(){

        var found = false;
        var row = $(this)

        $(this).find('.searchable').each(function(){
          // var that = $(this);
          // var found = false;
          var searchableText = $(this).text().toLowerCase();
          // console.log(searchableText);
          if(searchableText.indexOf(text_to_filter) >= 0){
            // console.log(">>>>>>>>>>",$(this).html())
            found = true;
          }else{
            // found = false;
          }
          

        })
        if(found){
          // console.log('found')
          $(this).show();
        } 
        // 

      })

    })

      $('.chkbox').on('click', function () {
          console.log('chkbox clicked');
          $('.update_permission').fadeIn();
          if ($(this).is(':checked')) {
              console.log('checked')
              if ($(this).hasClass('create')) {
                  $('.chkbox_create').prop('checked', true);
              }
              if ($(this).hasClass('read')) {
                  $('.chkbox_read').prop('checked', true);
              }
              if ($(this).hasClass('update')) {
                  $('.chkbox_update').prop('checked', true);
              }
              if ($(this).hasClass('remove')) {
                  $('.chkbox_delete').prop('checked', true);
              }

          } else {
              console.log('not checked')
              if ($(this).hasClass('create')) {
                  $('.chkbox_create').prop('checked', false);
              }
              if ($(this).hasClass('read')) {
                  $('.chkbox_read').prop('checked', false);
              }
              if ($(this).hasClass('update')) {
                  $('.chkbox_update').prop('checked', false);
              }
              if ($(this).hasClass('remove')) {
                  $('.chkbox_delete').prop('checked', false);
              }

          }

      })

    $('.update_permission').on('click', function () {

        var user_id = $('thead>tr').find('th').eq(0).attr('data-user-id');
        var total = $('.itemRow').length;
        var to_add = [];
        var to_update = [];

        $('.itemRow').each(function (x, y) {

            var permission_id = $(this).find('.info').attr('data-permission-id');
            var menu_id = $(this).find('.info').attr('data-menu-id');

            var cr = ($(this).find('.chkbox_create').is(':checked')) ? 1 : 0;
            var rd = ($(this).find('.chkbox_read').is(':checked')) ? 1 : 0;
            var up = ($(this).find('.chkbox_update').is(':checked')) ? 1 : 0;
            var de = ($(this).find('.chkbox_delete').is(':checked')) ? 1 : 0;

            if (permission_id.length > 0) {
                to_update.push({permission_id: permission_id, menu_id: menu_id, cr: cr, rd: rd, up: up, de: de})
            } else {
                to_add.push({user_id: user_id, menu_id: menu_id, cr: cr, rd: rd, up: up, de: de})
            }

        })

        var updateJson = JSON.stringify(to_update);
        var addJson = JSON.stringify(to_add);
        // console.log(addJson);return false;
        var url = base_url + 'users/save_permission/';
        $("#overlay-wrapper").removeClass("hidden");
        $.ajax({
            type: 'POST',
            url: url,
            beforeSend: function () {
                $('.full-overlay').fadeIn(300);
            },
            data: {
                updateJson: updateJson,
                addJson: addJson
            },
            success: function () {
                $('.full-overlay').fadeOut(300);
            },
            complete: function() {
                $('.update_permission').fadeOut();
                $("#overlay-wrapper").addClass("hidden");
                window.location.href=base_url+"users/listing";
            }
        })

    })


  })
  
	$("#save").on("click",function(){
    if($('#save').hasClass("running")) return false;

    var name = $("input[name=name]").val();
    var jobTitle = $("input[name=job_title]").val();
    var username = $("input[name=username]").val();
    var user_type = $("select[name=user_type]").val();
    var email = $("input[name=email]").val();
    var level = $("select[name=level]").val();
		var valid = true;
    var error_message = "<ul>";

    valid = formValidation("#add_user");
    if(!valid) return false;

    $('#save').addClass("running")
    Processing("#save","on");

    if(level == 'Normal'){
      $.ajax({
        method:"POST",
        url:base_url+"users/check_username_email",
        data:{username:username,email:email,level:'normal',user_type:'regular'},
        dataType:"JSON",
        success:function(response){
          if(response.username==false){
            error_message += "<li>Username already in use. Please select another one</li>";
            $('#save').removeClass("running");
            valid=false;
          }
          if(response.email==false){
            error_message += "<li>Email already in use. Please select another one</li>";
            $('#save').removeClass("running");
            valid=false;
          }
          if(!valid){
            Notify();
            bootbox.alert(error_message+= "</ul>");
            $('#save').removeClass("running");
            Processing("#save","off");
            return false;
          }else{
            $.ajax({
              url: base_url + "users/insert",
              method: "POST",
              data:$('#add_user').serialize(),
              dataType:'JSON',
              success:function(response){
                console.log(response)
                if(response.result){
                  if(user_type == 'regular') {
                    window.location.href = response.permissions_url;
                  }else{
                    window.location.href = "users/listing";
                  }
                  
                }else{
                  $('#save').removeClass("running")
                  bootbox.alert(response.reason); 
                }
              }
            })
          }
        },
        complete:function(){
          $('#update').removeClass("running");
          Processing("#save","off");
        }
      })
      
    }else{
      $.ajax({
        method:"POST",
        url:base_url+"users/check_username_email",
        data:{username:username,email:email,user_type:'regular'},
        dataType:"JSON",
        success:function(response){
          if(response.username==false){
            bootbox.alert("Username already in use. Please select another one")
            valid=false;
          }
          if(response.email==false){
            bootbox.alert("Email already in use. Please select another one")
            valid=false;
          }
          if(!valid){
            Notify();
            $('#update').removeClass("running");
            $('#save').removeClass("running");
            Processing("#save","off");
            return false;
          }else{
            $.ajax({
              url: base_url + "users/insert",
              method: "POST",
              data:$('#add_user').serialize(),
              dataType:'JSON',
              success:function(response){
                console.log(response)
                if(response.result){
                  window.location.href = response.permissions_url;
                }else{
                  $('#save').removeClass("running")
                  bootbox.alert(response.reason); 
                }
              }
            })
          }
        },
        complete:function(){
          $('#update').removeClass("running");
          Processing("#save","off");
        }
      })
    }

    return false;
	})


  $("#update").on("click",function(){
    if($('#update').hasClass("running")) return false;

    valid = formValidation("#edit_user");
    if(!valid) return false;

    // Processing("#update","on");
    $('#update').addClass("running")

    var id = $("input[name=id]").val();
    var username = $("input[name=username]").val();
    var email = $("input[name=email]").val();
    var level = $("select[name=level]").val();
    var valid = true;

    if( (username.length==0) && (email.length==0) ) return false;

    if(level == 'Normal'){
      var form = $('#edit_user')[0];
      var formData = new FormData(form);
      $.ajax({
        url: base_url + "users/update",
        method: "POST",
        processData: false,
        contentType: false,
        data: formData,
        dataType:'JSON',
        success:function(response){
          console.log(response)
          if(response.result){
            window.location.href = base_url + 'users/listing';
          }else{
            $('#update').removeClass("running")
            bootbox.alert(response.reason);
          }
        }
      })
    }else{
      $.ajax({
        method:"POST",
        url:base_url+"users/check_username_email",
        data:{username:username,email:email,id:id},
        dataType:"JSON",
        success:function(response){
          if(response.username==false){
            bootbox.alert("Username already in use. Please select another one")
            valid=false;
          }
          if(response.email==false){
            bootbox.alert("Email already in use. Please select another one")
            valid=false;
          }
          console.log(valid)
          if(!valid){
            $('#update').removeClass("running")
            Notify();
          }else{
            var form = $('#edit_user')[0];
            var formData = new FormData(form);
            $.ajax({
              url: base_url + "users/update",
              method: "POST",
              processData: false,
              contentType: false,
              data: formData,
              dataType:'JSON',
              success:function(response){
                console.log(response)
                if(response.result){
                  window.location.href = base_url + 'users/listing';
                }else{
                  $('#update').removeClass("running")
                  bootbox.alert(response.reason);
                }
              }
            })
          }
          
        },
        complete:function(){
          Processing("#save","off");
          $('#update').removeClass("running");
        }
      })
    }

    
    return valid;
  })

  $('input[name=generate_password]').on('click',function(){
    let elem = $(this).val();
    if(elem=='yes'){
      $('#password_block').addClass("hidden");
      $('#password_block input').removeClass("required");
    }else{
      $('#password_block').removeClass("hidden");
      $('#password_block input').addClass("required");
    }
  })

  $('.remove-photo').on("click",function(){
    $('input[name=delete_image]').val('1');
    $(this).closest(".row").remove();
  })

  $('body').on('click','.resetPassword',function(e){
    alert('Coming Soon');
    return false;
    e.preventDefault();
    var t = $(this);
    var message = $(this).data('message');
    var id = $(this).data('id');
    var url = $(this).data('url');

    if(message == null) message = "Are you sure you want to reset this user's password? A newly generated password will be sent to the user's email, so ensure that the email we have on record is correct before proceeding.";

      bootbox.confirm({
          message: message,
          buttons: {
              confirm: {
                  label: 'Yes',
                  className: 'btn-danger'
              },
              cancel: {
                  label: 'No',
                  className: 'btn-primary'
              }
          },
          callback: function (result) {
            if(result==true){
              $.ajax({
                url:url,
                type:"POST",
                dataType:"JSON",
                data:{id:id},
                success:function(d){
                  console.log(d)
                  t.closest("tr").remove();
                  toastr["warning"]("Record has been deleted.");
                }
              })
            }
          }
      });   
    return false;
  })

  $('.generate-password').on('click', function(){
    let newPassword = generatePassword(16);
    $('input[name=password]').val(newPassword).attr('type','text');
    $('input[name=pswd2]').val(newPassword).attr('type','text').attr('readonly',true);
    copyToClipboard(newPassword);
    toastr.success("The new password has been copied to your clipboard. Please use Ctrl+C if it does not work properly.")
    window.setTimeout(function(){
      $('input[name=password],input[name=pswd2]').attr('type','password');
      $('input[name=pswd2]').attr('readonly',false);
    },5000)
  })

})

function generatePassword(passwordLength) {
  var numberChars = "0123456789";
  var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  var lowerChars = "abcdefghijklmnopqrstuvwxyz";
  var specialChars = "#?!@$%^&*-";
  var allChars = numberChars + upperChars + lowerChars + specialChars;
  var randPasswordArray = Array(passwordLength);
  randPasswordArray[0] = numberChars;
  randPasswordArray[1] = upperChars;
  randPasswordArray[2] = lowerChars;
  randPasswordArray[3] = specialChars;
  randPasswordArray = randPasswordArray.fill(allChars, 4);
  return shuffleArray(randPasswordArray.map(function(x) { return x[Math.floor(Math.random() * x.length)] })).join('');
}

function shuffleArray(array) {
  for (var i = array.length - 1; i > 0; i--) {
    var j = Math.floor(Math.random() * (i + 1));
    var temp = array[i];
    array[i] = array[j];
    array[j] = temp;
  }
  return array;
}

function copyToClipboard(text) {
  if (window.clipboardData && window.clipboardData.setData) {
      // Internet Explorer-specific code path to prevent textarea being shown while dialog is visible.
      return window.clipboardData.setData("Text", text);

  }
  else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
      var textarea = document.createElement("textarea");
      textarea.textContent = text;
      textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in Microsoft Edge.
      document.body.appendChild(textarea);
      textarea.select();
      try {
          return document.execCommand("copy");  // Security exception may be thrown by some browsers.
      }
      catch (ex) {
          console.warn("Copy to clipboard failed.", ex);
          return prompt("Copy to clipboard: Ctrl+C, Enter", text);
      }
      finally {
          document.body.removeChild(textarea);
      }
  }
}