var zoomOverlayActive = false;

jQuery(function(){
    /* CUSTOM IMAGE ZOOM 
	*
	* To use this function, set class zoomImage to img tag. 
	* [OPTIONAL] Put some content in the alt
	* as this will display underneath the image if available.
	* 
    * @author Reeaz Ramoly 
	*/
	$('img').css("cursor","zoom-in");

	$('body').on('click', 'img', function () {
		if(zoomOverlayActive) return false;
		if($(this).hasClass("no-zoom")) return false;

		zoomOverlayActive = true;
		let modal = "<div id='imageModal' class='modal fade' style='z-index:99999;'>";
            // modal += '<span class="close-modal" style="color:#ccc;cursor:pointer; position: absolute;right: 0;top: 0;font-size: 24px;font-weight:bold;z-index: 999;padding: 1px 4px;background-color: #000000;margin: 0;border: 1px solid #ccc;box-shadow: -4px 4px 3px #4c4c4c;">X</span>';
            modal += "<div class='modal-dialog' role='document'>"
            modal += "<img class='modal-content img-thumbnail' id='img'>";
            modal += "<div id='caption'></caption>";
            modal += "</div></div>";
        $('body #imageModal').remove();
        $('body .modal-backdrop').remove();
        $('body').prepend(modal);

		let src = $(this).attr('src');
		let caption = $(this).attr('alt');
		if (typeof caption === 'undefined') caption = '';
		caption = (caption.length > 0) ? caption.replace(/<\/?[^>]+(>|$)/g, "") : '';
        $('#imageModal').modal("show");
		$('#img').attr('src', src);
		$('#caption').text(caption);
		$('#imageModal').css("cursor","zoom-out");
		$('#imageModal img').css("cursor","not-allowed");
	})

	$('body').on('click', '.close-modal', function () {
		closeImageModal();
	})

	$('body').on('click', '#imageModal img', function () {
		return false;
	})

	$('body').on('click','.modal-backdrop', function(){
		return false;
	})

	$('body').on('click', '#imageModal', function () {
		closeImageModal();
	})    
})

function closeImageModal()
{
	zoomOverlayActive = false;
	$('#imageModal').remove();
	$('body .modal-backdrop').remove();
}

document.onkeydown = function(evt) {
    evt = evt || window.event;
    var isEscape = false;
    if ("key" in evt) {
        isEscape = (evt.key === "Escape" || evt.key === "Esc");
    } else {
        isEscape = (evt.keyCode === 27);
    }
    if (isEscape) {
		if(zoomOverlayActive) {
			closeImageModal();
		}
    }
};