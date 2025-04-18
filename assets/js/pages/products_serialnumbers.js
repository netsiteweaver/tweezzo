jQuery(function(){

    $('.delete-serialnumber').on("click",function(){
        if(!confirm("Are you sure you want to delete this record?")) {
            return false;
        }
    })
    
})
