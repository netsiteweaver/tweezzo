document.querySelectorAll('.delete-note').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const noteId = this.dataset.id;
        if (confirm('Are you sure you want to delete this note?')) {
            fetch(`meeting_notes/delete/${noteId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('note-' + noteId).remove();
                } else {
                    alert('Failed to delete the note.');
                }
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.preview-image').forEach(function (img) {
    img.addEventListener('contextmenu', function (e) {
      e.preventDefault(); // prevent the default right-click menu

      if (confirm('Delete this image?')) {
        const imageId = img.getAttribute('data-id');

        fetch('/meeting_notes/delete_image/' + imageId, {
          method: 'DELETE',
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.result) {
                const row = img.closest('div');
                if (row) {
                    console.log('Deleting row:', row); // 👈 DEBUG
                    row.remove();
                }
            } else {
                alert('Failed to delete image.');
            }
        });
      }
    });
  });
});

function downloadPDF() {
    const element = document.getElementById('meeting-notes');
    const opt = {
        margin:       [10, 15, 10, 15], // top, left, bottom, right (in mm)
        // filename:     'Meeting-Minutes-2025-08-04.pdf',
        // image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 1, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf()
        .set(opt)
        .from(element)
        .toPdf()
        .get('pdf')
        .then(function(pdf){
            const totalPages = pdf.internal.getNumberOfPages();
            for (let i = 1; i <= totalPages; i++) {
                pdf.setPage(i);
                pdf.setFontSize(10);
                pdf.text(`Page ${i} of ${totalPages}`, 105, 290, { align: 'center' }); // footer center
            }
        })
        .save('Meeting-Minutes.pdf');
}

jQuery(function(){
    $('select[name=customer_id]').on("change",function(){
        let elemName = $('select[name=customer_id] option:selected').text();
        let customerId = $(this).val();
        console.log("Name:",elemName)
        if(customerId=='other'){
            $('input[name=customer_name]').closest(".form-group").removeClass("d-none");
            $('input[name=customer_name]').trigger('select');
        }else{
            $('input[name=customer_name]').closest(".form-group").addClass("d-none");
            $('input[name=customer_name]').val(elemName);
            
            // Auto-populate attendees from customer_access
            if(customerId && customerId !== '' && customerId !== 'other'){
                $.ajax({
                    url: base_url + "ajax/meeting_notes/getAttendeesByCustomerId",
                    data: {customer_id: customerId},
                    method: "POST",
                    dataType: "JSON",
                    success: function(response){
                        if(response.result && response.emails && response.emails.length > 0){
                            let attendeesTextarea = $('#attendees');
                            let currentValue = attendeesTextarea.val().trim();
                            let emailsToAdd = response.emails.join('\n');
                            
                            // Append emails, ensuring proper line breaks
                            if(currentValue){
                                // If there's existing content, add a newline before appending
                                attendeesTextarea.val(currentValue + '\n' + emailsToAdd);
                            }else{
                                // If empty, just set the emails
                                attendeesTextarea.val(emailsToAdd);
                            }
                        }
                    },
                    error: function(){
                        console.log("Error fetching attendees for customer");
                    }
                });
            }
        }
    })
})