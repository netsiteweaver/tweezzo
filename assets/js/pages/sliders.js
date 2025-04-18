/*sliders*/
$(document).ready(function(){
	$('input[name=slider_type]').on("click",function(){
		var sliderType = $('input[name=slider_type]:checked').val();
		if(sliderType=="photo"){
			$(".photo").removeClass("hidden");
			$(".youtube").addClass("hidden");
			$(".youtube").find('input').removeClass("required");
		}else{
			$(".youtube").removeClass("hidden");
			$(".youtube").find('input').addClass("required");
			$(".photo").addClass("hidden");
		}
	})

	$("input[type=file]").on('change',function(){
		var elem = $(this).val();
		$("input[name=photo_hidden]").val(elem);
	})

	$("#form").on("submit",function(){
		var error = false;

		var sliderType = $('input[name=slider_type]:checked').val();
		if(sliderType=="photo"){
			var photo_hidden = $("input[name=photo_hidden]").val();
			if(photo_hidden.length==0){
				error = true;
				alert("Please select an image to upload");
			}
		}
		$('.required').each(function(){
			var elem = $(this).val();
			if(elem.length==0){
				$(this).addClass("redBorder")
				error = true;
			}
		})

		if(error) return false;

	})

	var chk = $( "#sortable" );
	console.log(chk.length)
	if(chk.length>0){
		$( "#sortable" ).sortable({
			stop: function( event, ui ) {
				console.log(ui.item.index());
				reorder();
			}
		});
		$( "#sortable" ).disableSelection();
}

	function reorder() {
		var rank = 10;
		var slide_order = Array();
		$(".ui-state-default").each(function(){
			$(this).find('.display_order').text(rank);
			rank +=10;
			var id = $(this).attr('id');
			slide_order.push(id);
		})
		console.log(slide_order)
		$('.full-overlay').fadeIn(250);
		$.ajax({
			type:"POST",
			url:base_url+"sliders/reorder",
			data:{slide_order:slide_order}
		})
		.success(function( msg ) {
			console.log( msg );
			$('.full-overlay').fadeOut(250);
		});

	}	
})