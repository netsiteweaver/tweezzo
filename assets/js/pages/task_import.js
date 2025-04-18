jQuery(function(){

    $('#fields_match').on('change', '.column_select', function(){
        let elem = $(this).val();
        if(elem=='manual'){
            $(this).closest('td').find('input').removeClass('d-none');
        }
        else{
            $(this).closest('td').find('input').addClass('d-none');
        }
    })

    $('#proceed').on('click', function(){
        let data = {};
        let error = false;
        $('#fields_match').find('.column_select').each(function(){
            let elem = $(this).val();
            if(elem=='manual'){
                let manual_val = $(this).closest('td').find('input').val();
                if(manual_val==''){
                    error = true;
                    return false;
                }
                data[$(this).attr('name')] = manual_val;
            }
            else{
                data[$(this).attr('name')] = elem;
            }
        })
        if(error){
            alert('Please fill all the fields');
            return;
        }
        $.ajax({
            url: '/tasks/import',
            type: 'POST',
            data: data,
            success: function(response){
                if(response.status=='success'){
                    window.location.href = '/tasks';
                }
                else{
                    alert(response.message);
                }
            }
        })
    })
})
