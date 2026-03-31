jQuery(function(){
    var quotes = [
        'Patience is bitter, but its fruit is sweet.',
        'The two most powerful warriors are patience and time.',
        'With time and patience, the mulberry leaf becomes silk.',
        'Be patient. Good things take time.',
        'Patience is the companion of wisdom.'
    ];

    let email = localStorage.getItem('email');
    if($('input[name=email]').val()== "") { 
        $('input[name=email]').val(email);
    }
    console.log(email)

    $('.forgot-password').on('click', function(e){
        e.preventDefault();
    })
    $('form').on('submit',function(e){
        e.preventDefault();
    })
    $('.login__submit').on("click", function() {
        if($(this).hasClass("running")) {
            let quote = Math.floor(Math.random() * quotes.length )
            alertify.warning(quotes[quote])
            return;
        }

        $(this).addClass("running");
        let email = $('input[name=email]').val();
        let password = $('input[name=password]').val();
        let customer_id = $("#customerSelectModal select#selectCustomer").val();
        let remember_me = $('input[name=remember_me]').is(":checked") ? 1 : 0;

        if(email == "" || password == "") {
            alertify.alert('Please fill in all fields')
            $('.login__submit').removeClass("running");
            return;
        }

        if(password.length < 4) {
            alertify.alert('Password must be at least 4 characters long')
            $('.login__submit').removeClass("running");
            return;
        }

        $.ajax({
            url: base_url + "portal/customers/authenticate",
            data: {email:email, password:password, customer_id:customer_id},
            method: "POST",
            dataType: "JSON",
            success: function(response) {
                if(response.result) {
                    if(remember_me) {
                        localStorage.setItem('email', email);
                    }else{
                        localStorage.removeItem('email');
                    }
                    var nextUrl = (response.redirect && response.redirect.length)
                        ? (response.redirect.indexOf('http') === 0 ? response.redirect : (base_url + response.redirect))
                        : (base_url + "portal/customers/dashboard");
                    window.location.href = nextUrl;
                }else{
                    console.log(response.customers);
                    if( (response.customers=="") && (response.users !== "") ){
                        $('.login__submit').removeClass("running");
                        alertify.alert('Authentication failed')
                    }else{
                        $('#customerSelectModal select#selectCustomer').empty();
                        let option = `<option value=''>Select a Customer</option>`;
                        $('#customerSelectModal select#selectCustomer').append(option)
                        $(response.customers).each(function(i,j){
                            let option = `<option value='${j.customer_id}'>${j.company_name}</option>`;
                            $('#customerSelectModal select#selectCustomer').append(option)
                        })
                        $('#customerSelectModal').modal("show");
                    }
                }
            }
        })
    })

    $(".signinForCustomer").on("click", function(){
        $("#customerSelectModal select#selectCustomer").removeClass("is-invalid")
        let customer_id = $("#customerSelectModal select#selectCustomer").val();

        if(customer_id=="") {
            $("#customerSelectModal select#selectCustomer").addClass("is-invalid")
            return;
        }

        $('.login__submit').removeClass("running");
        $(".login__submit").trigger("click");
    })

    $('.forgot-password').on('click', function(){
        let email = $('input[name=email]').val();
        if(email == "") {
            alertify.alert('Please enter your email address')
            return;
        }
        alertify.confirm('Forgot Password', `An email will be sent to <b>${email}</b> to initiate the forgot password process. Are you sure you want to continue ?`
            , function(){ 
                $.ajax({
                    url: base_url + "portal/customers/forgotPassword",
                    data: {email:email},
                    method: "POST",
                    dataType: "JSON",
                    success: function(response) {
                        if(response.result) {
                            alertify.alert('If email submitted is in our records you will soon receive an email. Please follow instructions to complete the process.')
                        }else{
                        //     alertify.alert('Authentication failed')
                        }
                    }
                })
            }
            , function(){ 
                // alertify.error('Cancel')
            }
        );
    })
})