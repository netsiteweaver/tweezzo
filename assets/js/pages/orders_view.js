jQuery(function(){
    $('.pdf').on("click",function(){
        $(this).addClass("hidden");
        $('#order_sheet_pdf').removeClass("p-40").addClass("p-10");
        $('.orderImagesTable, .togglePhotos').addClass("hidden");
        $('#order_sheet_pdf').addClass("pdf");
        // $('.expandable').removeClass("col-md-3").addClass("col-md-4");
        $('.to-add-pdf').removeClass("d-none");
        // $('.document-name').parent().removeClass("col-md-3 text-center").addClass("col-md-5 text-right");
        // $("#order_sheet_pdf.pdf .company-name").css("font-size","10px");
        const element = document.getElementById('order_sheet_pdf');
        const orderNumber = $('#order_sheet_pdf .order-number').text();
        html2pdf().from(element).save("Order-"+orderNumber+".pdf");
        window.setTimeout(function(){
            $('.pdf').removeClass("hidden");
            $('#order_sheet_pdf').removeClass("pdf");
            $('.orderImagesTable').removeClass("hidden");
            $('.orderImagesTable, .togglePhotos').removeClass("hidden");
            // $('.expandable').removeClass("col-md-4").addClass("col-md-3")
            $('#order_sheet_pdf').removeClass("p-10").addClass("p-40");
            $('.to-add-pdf').addClass("d-none");
        },1000)
    })

    $('.togglePhotos').on("click",function(){
        if($(this).hasClass("btn-warning")){
            $(this).removeClass("btn-warning").addClass("btn-info");
            $(this).closest('.row').find(".orderImagesTable").parent().removeClass("d-none");
            $(this).find(".show").addClass("hidden");
            $(this).find(".hide").removeClass("hidden");
        }else{
            $(this).addClass("btn-warning").removeClass("btn-info");
            $(this).closest('.row').find(".orderImagesTable").parent().addClass("d-none");
            $(this).find(".show").removeClass("hidden");
            $(this).find(".hide").addClass("hidden");
        }
    })
})