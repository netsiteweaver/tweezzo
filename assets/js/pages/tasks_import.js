jQuery(function(){

    $('select[name=customer_id]').on('change', function(){
        let customer_id = $(this).val();

        if(customer_id == ''){
            $('select[name=project_id]').closest("div").addClass("d-none");
            $('select[name=sprint_id]').closest("div").addClass("d-none");
            showButton();
            return false;
        }

        $('select[name=project_id]').val("");
        $('select[name=project_id] option:not(:first)').attr("hidden",true);
        $('select[name=sprint_id]').closest("div").addClass("d-none");

        // $('select[name=sprint_id]').val("");
        // $('select[name=sprint_id] option:not(:first)').attr("hidden",true);

        $('select[name=project_id] option').each(function(i,j){
            if($(this).data("customer-id") == customer_id){
                $(this).removeAttr('hidden')
            }else{
                // $(this).attr("hidden",true)
            }
        })

        $('select[name=project_id]').closest("div").removeClass("d-none");
        let ct = $('select[name=project_id] option:not([hidden]').length;
        $('select[name=project_id]').closest("div").find("label span").text(ct - 1)

        showButton();
    })

    $('select[name=project_id]').on('change', function(){
        let project_id = $(this).val();

        if(project_id == ''){
            $('select[name=sprint_id]').closest("div").addClass("d-none");
            showButton();
            return false;
        }

        $('select[name=sprint_id]').val("");
        $('select[name=sprint_id] option:not(:first)').attr("hidden",true);

        $('select[name=sprint_id] option').each(function(i,j){
            if($(this).data("project-id") == project_id){
                $(this).removeAttr('hidden')
            }else{
                // $(this).attr("hidden",true)
            }
        })

        $('select[name=sprint_id]').closest("div").removeClass("d-none");
        let ct = $('select[name=sprint_id] option:not([hidden]').length;
        $('select[name=sprint_id]').closest("div").find("label span").text(ct - 1);

        showButton();
    })

    $('select[name=sprint_id]').on('change', function(){
        let sprint_id = $(this).val();
        $('input[name=selected_sprint_id]').val(sprint_id);

        showButton();
    })

    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
    
        var formData = new FormData(this);
        formData.append('file', $('#fileInput')[0].files[0]);
        formData.append('selected_sprint_id', $('input[name=selected_sprint_id]').val());

        var totalRows = 0;
        $.ajax({
            url: base_url + 'tasks/upload_file',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // console.log(response.data)
                let received = JSON.parse(response);
                console.log(received)
                $('#modalPreview table#preview_import tbody').empty();
                $(received.data).each(function(i,j){
                    totalRows++
                    let tr = "<tr>";
                    tr += `<td>${i+1}</td>`;
                    for(x = 0; x<6; x++)
                    {
                        tr += `<td>${j[x]}</td>`;
                    }
                    tr += `<td class='cursor-pointer deleteRow'><i class="fa fa-trash"></div></td>`
                    //<td>${j[0]}</td><td>${j[0]}</td><td>${j[0]}</td><td>${j[0]}</td><td>${j[0]}</td>`
                    tr += "</tr>";
                    $('#modalPreview table#preview_import tbody').append(tr);
                })
                $('#modalPreview tfoot tr th').text("Total rows = " + totalRows)
                $('#modalPreview').modal("show");
            },
            error: function() {
                $('#result').html("An error occurred.");
            }
        });
    });

    $("table#preview_import").on("click",".deleteRow", function(){
        $(this).closest("tr").remove();
    })

    $("#uploadAndImport").on("click", function() {
        let tableData = [];

        $('#preview_import tbody tr').each(function () {
            let rowData = [];
            $(this).find('td').each(function () {
                rowData.push($(this).text().trim());
            });

            // Push only if the row has data
            if (rowData.length > 0) {
                tableData.push({
                    task_name: rowData[1],
                    description: rowData[2],
                    section: rowData[3],
                    expected: rowData[4],
                    excluded: rowData[5],
                    completed: rowData[6]
                });
            }
        });

        let sprint_id = $('select[name=sprint_id]').val();
        let customer_id = $('select[name=customer_id]').val();
        let project_id = $('select[name=project_id]').val();
        $.ajax({
            url: 'tasks/process_import',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ tasks: tableData, sprintId:sprint_id }),
            success: function (response) {
                // alert("Import successful!");
                let url = `/tasks/listing?customer_id=${customer_id}&project_id=${project_id}&sprint_id=${sprint_id}`;
                window.location.href = base_url + url;

            },
            error: function (xhr) {
                alert("An error occurred during import.");
            }
        });
    })
    
})


function showButton()
{
    let customer_id = $('select[name=customer_id]').val();
    let project_id = $('select[name=project_id]').val();
    let sprint_id = $('select[name=sprint_id]').val();

    if( (customer_id == "") || (project_id == "") || (sprint_id == "") ) {
        $("#uploadBlock").addClass("d-none");
    }else{
        $("#uploadBlock").removeClass("d-none");
    }
    
}