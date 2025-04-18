$(document).ready(function () {

    var nom="";

    tableSort("#reorder-menu","menu");
    
    $('.icons > .fa').on('click', function () {

        $('.icons > .fa').removeClass("selected_icon");
        $(this).addClass("selected_icon");
        //get the classes used to the chosen icon
        var icon = ($(this).data('classname'));
        $('.preview_container').html('<span class="fa fa-2x '+icon+'"></span> [<span class="icon_name">'+icon+'</span>]');

        $('#icon').val(icon);
        $('.preview_container').attr('title',icon);

    })
    $("input[name=visible]").on('change', function(){
        var visible = $(this).val();
        if(visible == 1){

            $('#parent_menu').parent().parent().removeClass('hide');
            $('#parent_menu').addClass('required');

            $('.choose-icon').removeClass('hidden');

            $('input[name=display_order]').parent().parent().removeClass('hide');
            $('input[name=display_order]').addClass('required');

            

        }else{

            $('#parent_menu').parent().parent().addClass('hide');
            $('#parent_menu').removeClass('required');

            $('.choose-icon').addClass('hidden');

            $('input[name=display_order]').parent().parent().addClass('hide');
            $('input[name=display_order]').removeClass('required');

            
        }
        
    });

    $('input[name=action]').on('blur',function(){
        let elem = $(this).val();
        elem = elem.toLowerCase().trim().replace(/\s/g,'_');
        let icon = "";
        if( (elem=='listing') || (elem=='add') ){
            if(elem=='listing') icon = 'fa-list';
            if(elem=='add') icon = 'fa-plus-square-o';
            $('input[name=icon]').val(icon);
            $('.preview_container').html('<span class="fa fa-2x '+icon+'"></span> [<span class="icon_name">'+icon+'</span>]');
            $('.selected_icon').removeClass('selected_icon')
            $('.icons .'+icon).addClass('selected_icon')
        }
    })

    $('#type').on('change',function(){
        var type = $(this).val();
        
        if(type=='menu'){

            if($('input[name=nom]').val() == 'divider') $('input[name=nom]').val(nom);

            $('#visible_yes').prop('checked',true);

            $('input[name=nom]').focus();
            $('input[name=nom]').removeAttr('readonly');
            $('input[name=nom]').parent().parent().find('label').text('Display Name');
            $('input[name=nom]').addClass('required');

            $('input[name=visible]').parent().parent().removeClass('hide');

            $('input[name=machine_name]').parent().parent().addClass('hide');
            $('input[name=machine_name]').removeClass('required');

            $('#controller, #action').parent().parent().removeClass('hide');
            $('#controller, #action').addClass('required');

            $('#parent_menu').parent().parent().removeClass('hide');
            $('#parent_menu').addClass('required');

            $('input[name=action]').removeAttr('readonly');
            $('input[name=action]').addClass('required');

            $('.choose-icon').show();

            $("#parent_menu").prepend('<option value="0">Root</option>').val(0);
            $('#parent_menu').addClass('required');

        }else if(type=='divider'){

            nom = $('input[name=nom]').val();
            $('input[name=nom]').val('divider');
            $('input[name=nom]').attr('readonly', 'readonly');
            $('input[name=nom]').parent().parent().find('label').text('Display Name');
            // $('input[name=nom]').removeClass('required');

            $('input[name=visible]').parent().parent().addClass('hide');

            $('input[name=machine_name]').parent().parent().addClass('hide');
            $('input[name=machine_name]').removeClass('required');

            $('#controller, #action').parent().parent().addClass('hide');
            $('#controller, #action').removeClass('required');

            $('#parent_menu').parent().parent().removeClass('hide');
            $('#parent_menu').removeClass('required');

            $('input[name=action]').val('');
            $('input[name=action]').attr('readonly', 'readonly');
            $('input[name=action]').removeClass('required');

            $('.choose-icon').hide();

            $("#parent_menu option[value='0']").remove();

        }else if(type=='section'){

            // $('input[name=nom]').val('').focus();
            if($('input[name=nom]').val() == 'divider') $('input[name=nom]').val(nom);
            $('input[name=nom]').focus();
            $('input[name=nom]').parent().parent().find('label').text('Name');
            $('input[name=nom]').removeAttr('readonly');

            $('input[name=machine_name]').parent().parent().removeClass('hide');
            $('input[name=machine_name]').addClass('required');

            $('#controller, #action').parent().parent().addClass('hide');
            $('#controller, #action').removeClass('required');

            $('#parent_menu').parent().parent().addClass('hide');
            $('input[name=action]').removeAttr('readonly');
            $('.choose-icon').hide();
            $("#parent_menu option[value='0']").remove();

        }else{
            return;
        }

    })

    $(".hide-non-visible-items").on("click",function(){
        var elem = $(this).is(":checked");
        if(elem){
            $("tr.grey").addClass("hidden");
            $.cookie('hide-non-visible-items',true,{expires:365});
        }else{
            $("tr.grey").removeClass("hidden");
            $.removeCookie('hide-non-visible-items');
        }
    })

    var HideNonVisibleItems = $.cookie('hide-non-visible-items');
    if(HideNonVisibleItems){
        $("tr.grey").addClass("hidden");
        $(".hide-non-visible-items").prop('checked',true);
    }

})