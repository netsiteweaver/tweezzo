$(function() {
    var x = document.getElementById('background-video');

    let username = localStorage.getItem("username");
    let password = localStorage.getItem("password");
    let rememberMe = localStorage.getItem("rememberMe");

    if(typeof username !== 'undefined') $("input[name=inputEmail").val(username);
    if(typeof password !== 'undefined') $("input[name=inputPassword").val(password);
    if(rememberMe == '1') $('#remember-me').attr("checked",true);

    $('#controls div').on('click', function() {
        let elem = $(this);
        let btn = $(this).find('i');

        if ($(btn).hasClass("fa-play")) {
            $('#controls div.btn').eq(0).removeClass('btn-info').addClass("btn-outline-info");
            $('#controls div.btn').eq(1).addClass('btn-info').removeClass("btn-outline-info");
            x.play()
        }
        if ($(btn).hasClass("fa-pause")) {
            $('#controls div.btn').eq(0).addClass('btn-info').removeClass("btn-outline-info");
            $('#controls div.btn').eq(1).removeClass('btn-info').addClass("btn-outline-info")
            x.pause();
        }
    })

    $('.form-control').keypress(function(e) {
        var c = String.fromCharCode(e.which);
        if (c.toUpperCase() === c && c.toLowerCase() !== c && !e.shiftKey) {
            $('#message').show();
        } else {
            $('#message').hide();
        }
    });

    $("#signin").on("click", function() {
        if ($(this).hasClass("processing")) return false;

        $(this).addClass("processing");

        var valid = true;
        $(".required1").closest(".form-group").find("p").html("");
        $("#result").removeClass("error").html("");

        $(".required1").each(function() {
            var elem = $(this).val();
            var name = $(this).attr("data-name");
            if (elem.length == 0) {
                valid = false;
                $(this).closest(".form-group").find("p.error").html(
                    "<b>" + name + "</b> is required")
            }
        })

        if (valid) {
            $("#signin").addClass("d-none");
            $.ajax({
                url: base_url + "users/authenticate",
                type: "POST",
                dataType: "JSON",
                data: {
                    inputEmail: $("input[name=inputEmail").val(),
                    inputPassword: $("input[name=inputPassword").val(),
                },
                success: function(response) {
                    if (response.result == false) {
                        window.setTimeout(()=>{
                            $("#signin").removeClass("d-none");
                            $("#result").addClass("error").html(response
                                .reason);
                        },500);
                        playLoginSound("failed");
                    } else {
                        let rememberMe = $('#remember-me').is(":checked");
                        console.log(rememberMe)
                        if(rememberMe){
                            localStorage.setItem("username",$("input[name=inputEmail").val());
                            localStorage.setItem("password",$("input[name=inputPassword").val())
                            localStorage.setItem("rememberMe",1)
                        }else{
                            localStorage.removeItem("username");
                            localStorage.removeItem("password");
                            localStorage.setItem("rememberMe",0)
                        }
                        console.log(response)
                        if(response.expired_url){
                            window.location = base_url + response.expired_url;
                        }else{
                            window.location = base_url + "dashboard/index"
                        }
                    }
                },
                error: function() {
                    $("#result").addClass("alert alert-danger").text(
                        "1200: An error has occurred");
                    $("#signin").removeClass("d-none");
                },
                complete: function() {
                    $('#signin').removeClass("processing");
                }
            })
        } else {
            $("#signin").removeClass("processing")
        }

        return false;
    })
    $("#forget_password").on("click", function() {
        let username = $('input[name=inputEmail]').val();
        $(".login-container").addClass("d-none")
        $(".forgetpassword-container").removeClass("d-none")
        $("#email_to_reset").focus();
        $('#username_to_reset').val(username)
        return false;
    })
    $("#back_to_signin").on("click", function() {
        $(".login-container").removeClass("d-none")
        $(".forgetpassword-container").addClass("d-none")
        return false;
    })

    $('body').on("click",".overlay", function(){
        $('.overlay').remove();
    })

    $('#reset_password').on("click", function() {
        $('.error').text("")
        var username = $('#username_to_reset').val();
        if (username.length == 0) return false;

        $.ajax({
            url: base_url + "users/check_user_level",
            data:{username:username},
            method:"POST",
            dataType:"JSON",
            async:false,
            success:function(response) {
                if(response.result == false){
                    $('.error').text(response.reason);
                    valid = false;
                }else{
                    if(response.user_level == "Normal"){
                        $('body').prepend("<div class='overlay'><div class='normal-user-message'>An email will be sent to administrators who will contact you to proceed further. Click to close this message</div></div>");
                        window.setTimeout(function(){
                            $('.overlay').remove();
                            return true;
                        },5000)
                    }else{
                        $('body').prepend("<div class='overlay'><div class='normal-user-message'>An email has been sent. Click to close this message</div></div>");
                        window.setTimeout(function(){
                            $('.overlay').remove();
                            return true;
                        },5000)
                    }
                }
            }
        })
        return false;
    })
});


function playLoginSound(state)
{	
    //var soundFiles = ['success.wav','failed.wav'];
    var states = ['success','failed'];
    if(!states.includes(state)) state = 'success';
    // var index = states.indexOf(state);
    console.log(state)
	if(document.getElementById(state) !== null) document.getElementById(state).play()
}
