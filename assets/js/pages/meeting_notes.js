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

jQuery(function(){
    $('select[name=customer_id]').on("change",function(){
        let elemName = $('select[name=customer_id] option:selected').text();
        console.log("Name:",elemName)
        if($(this).val()=='other'){
            $('input[name=customer_name]').closest(".form-group").removeClass("d-none");
            $('input[name=customer_name]').trigger('select');
        }else{
            $('input[name=customer_name]').closest(".form-group").addClass("d-none");
            $('input[name=customer_name]').val(elemName);
        }
    })
})