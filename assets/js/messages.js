jQuery(function(){
    window.setInterval(function(){
        $.ajax({
            url: base_url + "messages/get",
            method:"GET",
            data:{id:1},
            dataType: "JSON",
            success: function(response) {
                let messages = $("#messages-dropdown-block .navbar-badge").text()
                if(messages !== "") messages = parseInt(messages);
                if(response.result){
                    if(response.rows>0){
                        $('#messages-dropdown-block .badge').html(response.rows)
                        $('#messages-dropdown-block .dropdown-header').html(response.rows + " Message" + ( (response.rows>1)?'s':'' ) );
                        $('.messages-block').empty();
                        $(response.messages).each(function(i,j){
                            let html = '<div class="dropdown-divider"></div>';
                            html += '<a href="'+base_url+'messages/view/'+j.uuid+'" class="dropdown-item">';
                            html += '<i class="fas fa-envelope mr-2"></i> '+j.subject;
                            html += '</a>';
                            $('.messages-block').append(html);
                        })
                        $('#messages-dropdown-block').removeClass("d-none");
                        if(messages != response.messages.length) {
                            ringBell();
                            $('.main-header').addClass("header-orange");
                            $('#messages-dropdown-block .far.fa-bell').addClass("fa-2x")
                            window.setTimeout(function(){
                                $('.main-header').removeClass("header-orange");
                                $('#messages-dropdown-block .far.fa-bell').removeClass("fa-2x");
                            },10000)
                        }
                    }else{
                        $('#messages-dropdown-block .badge').html('0')
                        $('#messages-dropdown-block .dropdown-header').html("0 Message");
                        $('#messages-dropdown-block').addClass("d-none");
                    }
                }
            }
        })
    },1000)

    if(typeof JsBarcode !== 'undefined') JsBarcode(".barcode").init();
})