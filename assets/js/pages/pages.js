jQuery(function(){

    // tableSort('#pages','pages','id','display_order');
    Search('searchText','pages');


    $(".deleteExisting").on("click", function () {
        var id = $(this).parent().find('img').data('id');
        $(this).parent().parent().addClass("hidden");
        $(this).parent().parent().find('.deleteImages').val(id);
        $('#image_block').removeClass('hidden')
    })

    $('#select_currency').on('change', function () {
        var elem = $('#select_currency').find('option:selected').val()
        var elemLabel = $('#select_currency').find('option:selected').text()

        if (elem.length == 0) {
            $('.foreign_label').text('Price foreign')
        } else {
            $('.foreign_label').text('Price in ' + elemLabel)
        }

    })

    $('textarea[name=description]').on('keyup',function(){
        let maxlength = $(this).attr('maxlength');
        let l = $(this).val().length;
        $('#meta-description').html( (maxlength - l) + ' characters left');
    })

    $('select[name=parent]').on('change', function () {
        let elem = $(this).val()
        if (elem == 'footer') {
            $('select[name=footer]').removeClass('hidden')
        } else {
            $('select[name=footer]').addClass('hidden')
        }
    })

    $('#select-page-type').on('change', function () {
        let selectedElem = $(this).val();
        if(selectedElem==""){
            window.location.href = base_url + "pages/listing";
            return;
        }
        window.location.href = base_url + "pages/listing?type="+selectedElem;
    })

    $('.page-name').on('blur', function () {
        let name = $(this).val();
        if (name.length == 0) return;
        let uuid = $('input[name=uuid').val();
        let slug = $(this).closest('div.tab-pane').find('.page-slug').val();
        if (slug.length == 0) {
            let generated_slug = string_to_slug(name);
            if (slugExists(generated_slug, uuid)) {
                generated_slug = generated_slug + '-1';
            }
            $(this).closest('div.tab-pane').find('.page-slug').val(generated_slug)
        }
    })

    $('ul.nav-tabs li').on('click', function () {
        let target = $(this).find('a').attr('href');
        setTimeout(function () {
            $(target).find('.page-name').select();
        }, 500)
        $
    })

    $('.page-slug').on('blur', function () {
        let that = $(this);
        let slug = $(this).val();
        if (slug.length == 0) return false;
        let uuid = $('input[name=uuid').val();

        if (slugExists(slug, uuid)) {
            $(that).val(slug + '-1');
        }
        // $.ajax({
        //     url: base_url + 'ajax/misc/slugExists',
        //     data: { slug: slug, uuid: uuid },
        //     method: 'post',
        //     dataType: 'JSON',
        //     success: function (response) {
        //         if (response.result) {
        //             $(that).val(slug + '-1');
        //         }
        //     }
        // })
    })

    $('#query').on('click', function () {
        $(this).addClass('btn-info').removeClass('btn-default');
        $('#form').removeClass('btn-info').addClass('btn-default');
        $('#form').removeClass('active');
        $('#query-block').removeClass("hidden");
        $('#form-block').addClass("hidden");
    })

    $('#form').on('click', function () {
        $(this).addClass('btn-info').removeClass('btn-default');
        $('#query').removeClass('btn-info').addClass('btn-default');
        $('#query').removeClass('active');
        $('#form-block').removeClass("hidden");
        $('#query-block').addClass("hidden");
    })

    $('body').on('click', '.add-block', function () {
        //clone the button and removes it
        let btn = $(this).closest('.col-sm-12').clone();
        $(this).closest('.col-sm-12').remove();

        let newOrderValue = getOrder();
        //clone the proto and append it
        let block = $('.proto').clone().removeClass('hidden proto');
        $('.block-container').append(block)

        //append the cloned button
        $('.block-container').append(btn)

        $('.block-group').last().find('.block_order').val(newOrderValue)
    })

    $('body').on('click', '.edit-block', function () {
        let pageID = $('input[name=id]').val();
        let pageBlockID = $(this).closest('.block-group').find('.page_block_id').val();
        //window.location.href = 
        let url = base_url + 'pages/blockcontent/' + pageBlockID;
        // let win = window.open(url, 'Edit Content', 'height=500,width=1000,menubar=no,toolbar=no,location=no');
        window.open(url, '_blank');

        // setTimeout(function () {
        //     let check = win.closed
        //     alert(check)
        // }, 3000)
        // alert('in progress')
        // console.log(pageID, pageBlockID)
    })

    $('body').on('click', '.delete-block', function () {
        // $('input[name=deletedBlocks]').val('100000')
        let blockID = $(this).closest('.block-group').data('page-block-id');
        // alert(blockID)

        let obj = $(this);
        bootbox.confirm({
            title: "Needs Confirmation",
            message: "Deleting a block from a page will also delete all its content (for this page only). Are you sure you want to proceed?",
            callback: function (e) {
                if (e) {
                    $(obj).closest('.block-group').remove();
                    let deletedBlocksJSON = $('input[name=deletedBlocks]').val();
                    let deletedBlocks = JSON.parse(deletedBlocksJSON);
                    deletedBlocks.push(blockID);
                    $('input[name=deletedBlocks]').val(JSON.stringify(deletedBlocks));
                }
            }
        })
    })

    // CKEDITOR.replace('editorFullDescription_en');
    // CKEDITOR.replace('editorShortDescription_en');
    // CKEDITOR.replace('editorFullDescription_fr');
    // CKEDITOR.replace('editorShortDescription_fr');

})

function string_to_slug(str) {
    str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();

    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to = "aaaaeeeeiiiioooouuuunc------";
    for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

    return str;
}

function slugExists(slug, uuid) {
    let result = false;
    $.ajax({
        url: base_url + 'ajax/misc/slugExists',
        data: { slug: slug, uuid: uuid },
        method: 'post',
        dataType: 'JSON',
        async: false,
        success: function (response) {
            result = response.result
        }
    })
    return result;
}

function getOrder()
{
    var orders = [];
    $('.block-group').each(function(i,j){
        if(!$(this).hasClass('proto')) {
            orders.push($(this).find('.block_order').val());
        }
    })
    return Math.max.apply(Math,orders)+10;
}