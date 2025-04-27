$('ul.treeview-menu>li.active').closest('.treeview').addClass('menu-open');
$('ul.treeview-menu>li.active').closest('.treeview').addClass('active');
var GlobalFormStatus = true;
var latest_logins_id = 0;
var latest_orders_id = 0;
var deviceInfo = navigator;

function message(data)
{
	let str = '<a href="#" class="dropdown-item"><div class="media">';
	str += '<div class="media-body">';
	str += '<h3 class="dropdown-item-title">';
	str += data.document_number;
	// str += '<span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>';
	str += '</h3>';
	str += '<p class="text-sm">'+data.company_name+'</p>';
	str += '<p class="text-sm text-muted">'+data.deliverynote_date+'</p>';
	str += '</div>';
	str += '</div></a><div class="dropdown-divider"></div>';
	$('#search-sn-results').append(str)
}

$(document).ready(function(){

	// window.setTimeout(function(){
	// 	$('#search_serialnumber').trigger("focus")
	// },100)

	// const searchSerial = () => {
	// 	console.log('searching');
	// 	let text = $('#search_serialnumber').val();
	// 	console.log(text)
	// 	$('i#searching').removeClass("fa-search").addClass("fa-spin fa-spinner");
	// 	$('#search-sn-results').html("");
	// 	$.ajax({
	// 		url: base_url + "ajax/misc/searchSerialNumber",
	// 		data: {text:text},
	// 		method:"POST",
	// 		dataType:"JSON",
	// 		success:function(response){
	// 			$('i#searching').addClass("fa-search").removeClass("fa-spin fa-spinner");
	// 			$('#search-sn-result').removeClass("d-none");
	// 			$(response.rows).each(function(i,j){
	// 				message(j);
	// 				$('#search-sn-result .badge').text(response.rows.length)
	// 			})
	// 		}
	// 	})
	// }

	lightbox.option({
        'resizeDuration': 100,
        'fadeDuration': 100,
        'wrapAround': true,
        'positionFromTop': 50,
        'disableScrolling': true,
	})

	$("#exportToCsv").on("click", function(){
		let target = $(this).data('target');
		downloadTableAsCSV(target,"notes",{ skipColumns: [6] });
	})

	// const debouncedLog = debounce(searchSerial,300);

	// $('#search_serialnumber').on("keyup change",function(){
	// 	debouncedLog();
	// })
	$.fn.swapClass = function(classToRemove,classToAdd) {
		this.removeClass(classToRemove).addClass(classToAdd)
	};
	 
	$('footer.footer').html(deviceInfo.userAgent)

	$('body').on('keydown', function(e) {
		if(e.key == 'Escape') {
			if(e.originalEvent.target.className.includes('searchText')){
				$('.searchText').val('')
			}else if (e.originalEvent.target.className.includes('task-listing-search')) {
				$('#search_text').val("");
			}else if(e.originalEvent.target.className.includes('note-editable')){
                $('#userSelectModal').modal('show');
			}
		}
        if ((e.key == 'Enter') || (e.keyCode == 13)) {
			console.log(e.originalEvent.target.className)
            if (e.originalEvent.target.className.includes('message-to-send')) {
                // sendMokoze();
                return false;
			}else if (e.originalEvent.target.className.includes('message-to-send')) {
				return false;
			}else if(e.originalEvent.target.id.includes('search_serialnumber')){
				$("#topbar-search").trigger("click")
			}else if (e.originalEvent.target.className.includes('task-listing-search')) {
				Overlay("on");
        		$('.monitor').trigger("change");
			} else {
                // return false;
            }
        }
    })

	$('#version-history').on('click',function(){
		getVersionHistory();
	})

	$('#version-history-modal').on('click','.pagination a',function(e){
		e.preventDefault();
		let page = $(this).text();
		getVersionHistory(page)
	})

	$('input[type=number]').on("keydown",function(e){
		console.log($(this).val())
	})

	$('.print').on('click',function(){
		let url = $(this).data('url');
		console.log(url)
		window.print();
		if(typeof url !== 'undefined'){
			window.location.href = base_url + url;
		}
	})
	//Initialize Select2 Elements
	$('.select2').select2()      

	$('.summernote').summernote({
		// styleTags: [
		// 	'p',
		// 		{ title: 'Blockquote', tag: 'blockquote', className: 'blockquote', value: 'blockquote' },
		// 		'pre', 'h1', 'h2', 'h3', 'h5', 'h5', 'h6'
		// ],
		callbacks: {
			// callback for pasting text only (no formatting)
			onPaste: function (e) {
			  var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
			  console.log(bufferText)
			  e.preventDefault();
			  bufferText = bufferText.replace(/\r?\n/g, '<br>');
			  document.execCommand('insertHtml', false, bufferText);
			}
		},
		height: 150,
		tabsize: 4,
		placeholder: 'Enter text here ...',
		toolbar: [
		  // [groupName, [list of button]]
		  ['style', ['bold', 'italic', 'underline', 'clear']],
		  ['font', ['strikethrough', 'superscript', 'subscript']],
		  ['fontsize', ['fontsize']],
		  ['color', ['color']],
		  ['para', ['ul', 'ol', 'paragraph']],
		  ['height', ['height']],
		  ['view', ['fullscreen', 'codeview']],
		]
	});

	
	toastr.options = {
		"closeButton": false,
		"debug": false,
		"newestOnTop": true,
		"progressBar": true,
		"preventDuplicates": false,
		"onclick": null,
		"showDuration": "100",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut",
		"positionClass": "toast-bottom-right",
	}
	if(flashSuccess.length>0){
		toastr["success"](flashSuccess);
		window.setTimeout(function(){
			$('div.alert.alert-success').fadeOut(5000);
		},2500)
	}
	if(flashDanger.length>0){
		toastr["error"](flashDanger);
		window.setTimeout(function(){
			$('div.alert.alert-danger').fadeOut(5000);
		},2500)
	}

	$("input[type=number]").each(function(){
		var elem = $(this).val();
		var min = $(this).attr("min");

		if(!$(this).hasClass("text-right")) $(this).addClass("text-right")
		if(typeof min == 'undefined') min = 0;
		if(elem.length==0){
			$(this).val(min)
		}
    })
    
    $(".numeric").on('focus',function(){
        var elem = $(this).val().replace(/'/g,'');
        $(this).val(elem);
    })

    $(".numeric").on('blur',function(){
        var elem = $(this).val();
        $(this).val(numeral(elem).format('0,0.00'))
    })

	$('.required').each(function(){
        var elem = $(this).parent().find('label');
        // console.log(elem.length, $(this).attr('name'));
        if(elem.length == 0){
            $(this).parent().parent().find('label').addClass("asterisk");
        }else{
            $(this).parent().find('label').addClass("asterisk");
        }
		
	})

	$('form').submit(function(e){
		if($(this).hasClass("ignoreValidation")) return;
		$('.error').removeClass("error");
		$('p.error_notes').remove(); //removeClass("error_notes");
		var status = myCustomValidation();
		GlobalFormStatus = status;
		console.log(status,GlobalFormStatus)
		if(!status || !GlobalFormStatus){
			return false;
		}
	})

	/*$('#goFullScreen').on('click',function(){
		toggleFullScreen();
		if($(this).hasClass('expanded')){
			$(this).removeClass('expanded');
			$(this).find('i').removeClass('fa-compress').addClass('fa-expand')
		}else{
			$(this).addClass('expanded');
			$(this).find('i').removeClass('fa-expand').addClass('fa-compress')
		}
	})*/

    // $('#tbl1').DataTable({
    //   'paging'      : true,
    //   'lengthChange': false,
    //   'searching'   : false,
    //   'ordering'    : true,
    //   'info'        : true,
    //   'autoWidth'   : false,
    //   'pageLength'	: 25 
    // })

    // $('#tbl-no-search-no-paging').DataTable({
    //   'paging'      : false,
    //   'lengthChange': false,
    //   'searching'   : false,
    //   'ordering'    : true,
    //   'info'        : false,
    //   'autoWidth'   : false
    // })

    // $('#tbl-search').DataTable({
    //   'paging'      : true,
    //   'pageLength'	: 25,
    //   'lengthChange': false,
    //   'searching'   : true,
    //   'ordering'    : true,
    //   'info'        : true,
    //   'autoWidth'   : false
    // })

    $("button.delete, .delete2").on("click",function(e){

		e.preventDefault();
		var message = $(this).data('message');
		var url = $(this).data('url');
		if( (typeof url == 'undefined') || (url.length==0) ){
			var url = $(this).parent().attr("href");
		}
		Notify('error-sound');
		if(message == null) message = "Are you sure you want to delete this record?";

	    bootbox.confirm({
	        message: message,
	        buttons: {
	            confirm: {
	                label: 'Yes, Delete It !',
	                className: 'btn-danger'
	            },
	            cancel: {
	                label: 'No',
	                className: 'btn-primary'
	            }
	        },
	        callback: function (result) {
	        	if(result==true){
	        		//window.location.href = url;
	        	}
	            
	        }
	    });		
		return false;
	})

    $('body').on('click','.deleteAjax',function(e){
		e.preventDefault();
		var t = $(this);
		var message = $(this).data('message');
		var message_complete = $(this).data('message_complete');
		var id = $(this).data('id');
		var pk = $(this).data('pk');
		var uuid = $(this).data('uuid');
		var url = $(this).data('url');

		if(message == null) message = "Are you sure you want to delete this record?";
		if(message_complete == null) message_complete = "Record has been deleted.";
		Notify('error-sound');
	    bootbox.confirm({
	        message: message,
	        buttons: {
	            confirm: {
	                label: 'Yes',
	                className: 'btn-danger'
	            },
	            cancel: {
	                label: 'No',
	                className: 'btn-primary'
	            }
	        },
	        callback: function (result) {
	        	if(result==true){
	        		$.ajax({
	        			url:url,
	        			type:"POST",
	        			dataType:"JSON",
	        			data:{id:id,uuid:uuid,pk:pk},
	        			success:function(d){
	        				console.log(d)
	        				t.closest("tr").remove();
	        				toastr["warning"](message_complete);
	        			}
	        		})
	        	}else{
					toastr["info"]('Deletion Cancelled');
				}
	        }
	    });		
		return false;
	})

	$('body').on('click','.delete_row',function(e){
		e.preventDefault();
		var t = $(this);
		var message = $(this).data('message');
		var message_complete = $(this).data('message_complete');
		var id = $(this).data('id');
		var uuid = $(this).data('uuid');
		var pk = $(this).data('pk');
		var table = $(this).data('table');

		if(message == null) message = "Are you sure you want to delete this record?";
		if(message_complete == null) message_complete = "Record has been deleted.";
		Notify('error-sound');
	    bootbox.confirm({
	        message: message,
	        buttons: {
	            confirm: {
	                label: 'Yes',
	                className: 'btn-danger'
	            },
	            cancel: {
	                label: 'No',
	                className: 'btn-primary'
	            }
	        },
	        callback: function (result) {
	        	if(result==true){
	        		$.ajax({
	        			url: base_url + "ajax/misc/delete",
	        			type:"POST",
	        			dataType:"JSON",
	        			data:{id:id,uuid:uuid,table:table,pk:pk},
	        			success:function(d){
	        				console.log(d)
	        				t.closest("tr").remove();
	        				toastr["info"](message_complete);
	        			}
	        		})
	        	}else{
					toastr["info"]('Deletion Cancelled');
				}
	        }
	    });		
		return false;
	})

	$("button.deleteImage").on("click",function(e){

		$(this).parent().parent().find('.uploadImage').removeClass("hidden");
		$(this).parent().addClass("hidden");

		return false;
	})	

	$("#topbar-search").on("click", function() {
		let sn = $("#search_serialnumber").val();
		if(sn == '') return;

		$.ajax({
			url: base_url + "ajax/misc/searchSerialNumber",
			data: {text: sn},
			method: "POST",
			dataType: "JSON",
			success: function (response) {
				if(response.result){
					
					let data = "<table class='table table-bordered table-hover'>";
					data += "<tr>";
					data += "<th>TYPE</th>";
					data += "<th>COMPANY</th>";
					data += "<th>NUMBER</th>";
					data += "<th>PRODUCT</th>";
					data += "<th>S/N</th>";
					data += "<th>DATE</th>";
					data += "</tr>";
					$(response.rows).each(function(i,row){
						data += "<tr title='Click to View Document' class='cursor-pointer open-document' data-type='"+row.type+"' data-uuid='"+row.uuid+"'>";
						data += "<td>" + row.type + "</td>";
						data += "<td>" + row.company_name + "</td>";
						data += "<td>" + row.document_number + "</td>";
						data += "<td>" + row.stockref + "</td>";
						data += "<td>" + row.serial_number + "</td>";
						data += "<td>" + row.date + "</td>";
						data += "</tr>";
					})
					data += "</table>";
					$('#search-result-modal .modal-body').html(data);
					$('#search-result-modal').modal("show")
				}else{
					alert("Nothing found")
				}
				
			}
		})

		$("body").on("click",".open-document", function(){
			let type = $(this).data("type");
			let uuid = $(this).data("uuid");
			console.log(type, uuid)
			let uri = base_url + ( (type=='GR')?'goodsreceive':'deliverynotes' ) + '/serialnumbers/' + uuid;
			document.location.href = uri;
		})
	})

	function myCustomValidation()
	{
		
		var validationStatus = true;
		var errorMessages = "";
        var firstNonValidatedElement = false;

		$('.required').each(function(){
			var element = $(this).val()
			var elementName = $(this).data('name');
            var errorMessage = $(this).data('errormessage');
			$(this).parent().remove(".error_notes");
			if(elementName==null){
			// 	elementName = $(this).closest('div').find('label').text();
			// }else{
				elementName = $(this).attr('name');
				// elementName="This field";
			} 
			if(errorMessage==null) errorMessage="";

			if( (typeof element != 'undefined') && (element != null) ){
				if(element.length == 0){
					$(this).addClass("error");
					$(this).parent().append("<p class='error_notes'><b><u>"+elementName+"</u></b> is compulsory. "+errorMessage+"</p>");
					validationStatus = false;
					if(firstNonValidatedElement == false){
						firstNonValidatedElement = $(this);
					}
					errorMessages += errorMessages + elementName + ' is required.<br> ';
				}
			}
		})
        if(validationStatus == false){
            $('html, body').animate({ scrollTop: $(firstNonValidatedElement).offset().top-25})
			$(firstNonValidatedElement).focus();
			Notify();
			toastr.error('Please correct the errors highlighted and try again')
        }
		return validationStatus

	}

    $('.delete').on('click', function () {

    	alert('Function has been disabled');
    	return false;

        /*if ($(this).hasClass('resource')) {
            var url = base_url + 'menu/delete';
        } else if ($(this).hasClass('customerCategory')) {
            var url = base_url + 'en/customer_categories/delete';
        } else if ($(this).hasClass('userProfile')) {
            var url = base_url + 'en/user_profiles/delete';
        } else {
            return false;
        }

        var id = $(this).parent().parent().find('td').eq(0).text();

        $this_item = $(this);

		swal({
			title: 'WARNING',
			text: "Are you sure you want to delete this?",
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Yes',
			cancelButtonText: 'No'
		}).then((result) => {
			if (result.value) {
				$.post(
			        url,
			        {id: id},
			        function (response) {
			            console.log(response.result);
			            if (response.result == true) {
			                $this_item.parent().parent().remove();
			            }
			        },
			        'json'
			    )		  	
			}
		})*/
        /*BootstrapDialog.confirm({
            title: 'WARNING',
            message: 'Are you sure you want to delete this?',
            type: BootstrapDialog.TYPE_DANGER, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
            closable: true, // <-- Default value is false
            draggable: true, // <-- Default value is false
            //btnCancelLabel: 'Do not drop it!', // <-- Default value is 'Cancel',
            //btnOKLabel: 'Drop it!', // <-- Default value is 'OK',
            btnOKClass: 'btn-danger', // <-- If you didn't specify it, dialog type will be used,
            callback: function(result) {
                // result will be true if button was click, while it will be false if users close the dialog directly.
                if(result) {
                    $.post(
                        url,
                        {id: id},
                        function (response) {
                            console.log(response.result);
                            if (response.result == true) {
                                $this_item.parent().parent().remove();
                            }
                        },
                        'json'
                    )
                }
            }
        });   */     

        return false;
    })

	$(".onFocusInput").on("focus",function(){
		$(this).closest('.form-group').find('.onFocusNotes').removeClass("hidden");//.addClass("fadeIn").removeClass('fadeOut');
	})

	$(".onFocusInput").on("blur",function(){
		$(this).closest('.form-group').find('.onFocusNotes').addClass("hidden");//.removeClass("fadeIn").addClass('fadeOut');
    })

	$('#view-issues').on("click",function(){
		$('#issuesModal').modal("show");
	})

	$('#modal-signin').on("click",function(){
		let valid = true;
		let email = $('input[name=modal-email]').val();
		let pswd = $('input[name=modal-password]').val();
		$('.has-error').removeClass("has-error");
		if(email.length==0){
			$('input[name=modal-email]').closest('.form-group').addClass("has-error");
			valid = false;
		}
		if(pswd.length==0){
			$('input[name=modal-password]').closest('.form-group').addClass("has-error");
			valid = false;
		}

		if(!valid) return false;

		$.ajax({
			url:base_url+"users/authenticate",
			type:"POST",
			dataType:"JSON",
			data:{inputEmail:$('input[name=modal-email]').val(),inputPassword:$('input[name=modal-password]').val()},
			success:function(response){
			  if(response.result == false){
				$("#signin").removeClass("hidden");
				$("#result").addClass("alert alert-danger").text(response.reason);
			  }else{
				toastr.success("Success");
				$('#login-modal').modal("hide");
				setTimeout(function(){
					getVersionHistory();
				},500)
				
			  }
			},
			error:function(){
			  $("#result").addClass("alert alert-danger").text("1200: An error has occurred");
			}
		  })
	})
    
	$('table.table').each(function(i,j){
		let className ="";
		if(!$(this).hasClass("table-no-border")){
			// className += "table-bordered ";
		}
		if(!$(this).hasClass("table-not-condensed")){
			// className += "table-sm ";
		}
		if(!$(this).hasClass("table-not-striped")){
			// className += "table-striped ";
		}
		$(this).addClass(className);
	})
	// $('table.table').addClass("table-bordered table-condensed table-striped");
})

function getVersionHistory(page)
{
	if( (typeof(page)=='undefined') || (page==null) ) page = 1;
	$('#modal-overlay').removeClass("hidden");
	$('#version-history-modal').modal("show");
	$('#commit-history tbody').empty();
	$.ajax({
		url: base_url + "ajax/misc/getHistory/"+page,
		dataType:"JSON",
		success: function(response){
			if(response.result){
				$(response.commits).each(function(i,j){
					var line = "<tr>";
					line += "<td>"+j.details+"</td>";
					line += "<td>"+j.date+"</td>";
					if(typeof(j.author) != 'undefined') line += "<td>"+j.author+"</td>";
					line += "</tr>";
					$('#commit-history tbody').append(line);
				})
				$('.pagination').html(response.pagination);
				$('#modal-overlay').addClass("hidden");
			}else{
				if(response.reason=='login'){
					$('#version-history-modal').modal("hide");
					$('#login-modal').modal("show")
				}
			}
		}
	})
}

function toggleFullScreen() {
  if ((document.fullScreenElement && document.fullScreenElement !== null) ||    
   (!document.mozFullScreen && !document.webkitIsFullScreen)) {
    if (document.documentElement.requestFullScreen) {  
      document.documentElement.requestFullScreen();  
    } else if (document.documentElement.mozRequestFullScreen) {  
      document.documentElement.mozRequestFullScreen();  
    } else if (document.documentElement.webkitRequestFullScreen) {  
      document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);  
    }  
  } else {  
    if (document.cancelFullScreen) {  
      document.cancelFullScreen();  
    } else if (document.mozCancelFullScreen) {  
      document.mozCancelFullScreen();  
    } else if (document.webkitCancelFullScreen) {  
      document.webkitCancelFullScreen();  
    }  
  }  
}

function Notify(soundFile)
{	
	if( (soundFile==null) || (soundFile.length==0)) soundFile = 'error-sound'
	if(document.getElementById(soundFile) !== null) document.getElementById(soundFile).play()
}

function addCommas(nStr)
{
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

String.prototype.capitalize = function() {
  return this.charAt(0).toUpperCase() + this.slice(1)
}

String.prototype.ucwords = function() {
  str = this.toLowerCase();
  return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
  	function(s){
  	  return s.toUpperCase();
	});
};

/**
     * 
	 * @author Reeaz Ramoly <reeaz@ramoly.info>
	 * 
	 * This function makes sorting the rows of table much easier. It uses jquery-ui for cross
	 * browser compatibility. I have not tested on various environments but it should work, provided
	 * you have a well structured table, with tbody, as the function targets that tbody to sort each 
	 * tr inside it. Each tr must have attribute 'data-id' which contains the primary key value of 
	 * each row.
	 * 
	 * @param tableID The id of the table that needs to be re-ordered
	 * @param url [OPTIONAL] The controller/method that will save the new positions. if not set nothing
	 * will persists to the database, which means that you should other ways to do so
	 * @param primary_key The primary key as it is set in the specific table Defaults to 'id'
	 * @param display_order_field The field that stores the display order. Defaults to 'display_order'
	 * 
	 * 2021-11-07
	 * 
     */

 function tableSort(tableID, table_name, primary_key, display_order_field)
 {
	console.log('Sorting '+tableID+' Init...');
	var valid = true;
	if(typeof tableID === 'undefined') {
		valid = false;
	}
	if( (typeof table_name === 'undefined') || (table_name == '') ) {
		valid = false;
	}

	if(!valid) {
		alert("Error")
		return false;
	}
	if(typeof primary_key=== 'undefined') primary_key = 'id';
	if(typeof display_order_field=== 'undefined') display_order_field = 'display_order';

	$(tableID).addClass("cursor-sort");

	var fixHelper = function(e, ui) {  
		ui.children().each(function() {  
			$(this).width($(this).width());  
		});  
		return ui;  
	};

	$(tableID+" tbody").sortable({
		helper: fixHelper,
	}).disableSelection();

	$(tableID+" tbody").on('sortstop', function(ev,ui) {
		let data = [];
		let new_order = 0;
		$(tableID+" tbody" + ' tr').each(function(i,j){
			new_order = new_order + 1;
			data.push({
				[primary_key]:$(this).data('id'),
				[display_order_field]: new_order
			});
			$(this).find('.display_order').addClass('value_updated').text(new_order)
		})
		console.log(data);
		$.ajax({
			url: base_url + 'ajax/misc/reorder',
			method: "POST",
			data: {data:JSON.stringify(data),primary_key:primary_key,display_order_field:display_order_field, table_name:table_name},
			success: function(response) {
				toastr["info"]("Rows have bee successfully re-ordered.");
			},
			complete: function() {
				// window.setTimeout(function(){
				// 	$('.value_updated').removeClass("value_updated")
				// },10000)
			}
		})
	})
 }

 /**
  * Simple function to add filter possibility to table
  * @author Reeaz Ramoly <reeaz@ramoly.info>
  * 
  * @param {*} textToSearch 
  * @param {*} tableID 
  */
function Search(textToSearch,tableID)
{
	let elem = $('#'+textToSearch);
	if( (elem === null) || (elem.length == 0) ){
		let searchElements = "<div class='row'><div class='col-md-4'><input class='form-control searchText' placeholder='START TYPING...' id='searchText' autofocus></div></div>";
		$('#'+tableID).closest(".row").before(searchElements)
	}

	$('#'+tableID+' tr:gt(0)').addClass(function(i){
		return 'searchTR';
  	});
	
	$('body').on("keyup","#"+textToSearch, function() {
		var value = $(this).val().toLowerCase();
		console.log(value)
		$("#"+tableID+" tr.searchTR").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
}

function Overlay(option)
{
	if(typeof option == 'undefined') option = 'on';
	option = option.toLowerCase();
	let options = ['on','off'];
	if(!options.includes(option)) option = 'on';

	if(option == 'on') {
		$('#overlay').removeClass('hidden');
	}else{
		$('#overlay').addClass('hidden');
	}
}

function Processing(object,state,label,btnOff,btnOn)
{
	console.log()
	if(object==null) {
		console.log("no object")
		return false;
	}
	if(typeof state == 'undefined') {
		console.log("no state passed")
		return false;
	}
	if(typeof label == 'undefined') label = "Processing";
	if(typeof btnOff == 'undefined') btnOff = "<i class='fa fa-save'></i> Save";
	if(typeof btnOn == 'undefined') btnOn = "<i class='fa fa-spin fa-spinner'></i> "+label;

	let states = ['on','off'];
	state = state.trim().toLowerCase();
	if((states.indexOf(state))==-1) return false;
	
	if(state=="on"){
		$(object).html(btnOn)
	}else{
		$(object).html(btnOff)
	}
	
}

function formValidation(formID)
{
	var validationStatus = true;
	var errorMessages = "";
	var firstNonValidatedElement = false;

	
	$(formID).find('.required').each(function(){
		var element = $(this).val()
		var elementName = $(this).data('name');
		var errorMessage = $(this).data('errormessage');
		$(this).parent().remove(".error_notes");
		if(elementName==null){
			elementName = $(this).attr('name');
		} 
		if(errorMessage==null) errorMessage="";

		if( (typeof element != 'undefined') && (element != null) ){
			if(element.length == 0){
				$(this).addClass("error");
				$(this).parent().find('p.error_notes').remove();
				$(this).parent().append("<p class='error_notes'><b><u>"+elementName+"</u></b> is compulsory. "+errorMessage+"</p>");
				validationStatus = false;
				if(firstNonValidatedElement == false){
					firstNonValidatedElement = $(this);
				}
				errorMessages += errorMessages + elementName + ' is required.<br> ';
			}
		}
	})
	if(validationStatus == false){
		$('html, body').animate({ scrollTop: $(firstNonValidatedElement).offset().top-25})
		$(firstNonValidatedElement).focus();
		Notify();
		toastr.error('Please correct the errors highlighted and try again')
	}
	return validationStatus

}

function update_dashboard()
{
	$.ajax({
        url: base_url + "ajax/misc/dashboard_update",
        method:"GET",
        dataType:"JSON",
        success: function(data){
			if(data.result){

				$('#expenses').text("Rs " + data.totals.totalExpenses.toLocaleString("US-en"));
				$('#deliveries').html(data.totals.totalDelivered +" / "+( (data.totals.totalItems == null)?'0':data.totals.totalItems )+ " ITEMS")
				$('#orders').html("Rs " + data.totals.totalSalesDelivered);

				$('#delivery_details_container').empty();
				$(data.deliveryBoys).each(function(i,j){

					let box = $('.delivery_details.hidden').clone().removeClass("hidden");
					$(box).attr("id",j.delivery_boy)
					$(box).find('.delivery-boy').html(j.name);
					$(box).find('.delivered').html( typeof(j.delivered) == 'undefined' ? '0' : j.delivered );
					$(box).find('.undelivered').html( typeof(j.undelivered) == 'undefined' ? '0' : j.undelivered );//(j.undelivered);
					$(box).find('.items').html( typeof(j.total_item) == 'undefined' ? '0' : j.total_item );//(j.total_item);

					var list_items = "";
					var total_amt = 0;
					$(j.split).each(function(x,y){
						total_amt += parseFloat(y.amt);
						list_items += "<div class='row'><div class='col-xs-5 col-xs-offset-1'><h5 class='text-bold'>"+y.pm+"</h5></div><div class='col-xs-4'><h5>: "+parseFloat(y.amt).toLocaleString("en-US")+"</h5></div></div>";
					})
					list_items += "<div class='row totals'><div class='col-xs-5 col-xs-offset-1'><h5 class='text-bold'>TOTAL</h5></div><div class='col-xs-4'><h5>: "+total_amt.toLocaleString("en-US")+"</h5></div></div>";
					$(box).find('.split-payments').html(list_items);
					$('#delivery_details_container').append(box)
				})
				
			}
		}

	})
}

function myAlert(message)
{
	alertify.alert("Lemon Yellow System",message);
}

function includeJs(jsFilePath) {
    var js = document.createElement("script");

    js.type = "text/javascript";
    js.src = jsFilePath;

    document.body.appendChild(js);
}

function ringBell()
{	
	if(document.getElementById('ding') !== null) document.getElementById('ding').play()
}

function isPortrait() {
    return window.innerHeight > window.innerWidth;
}

function nl2br (str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

// String.prototype.nl2br = function(str) {
// 	if (typeof str === 'undefined' || str === null) {
//         return '';
//     }
// 	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br>' + '$2');
// }

Date.prototype.addDays = function(days) {
	var date = new Date(this.valueOf());
	date.setDate(date.getDate() + days);
	return date;
}

function debounce(func, delay) {
	let timeoutId;
	return function(...args) {
		clearTimeout(timeoutId);
		timeoutId = setTimeout(() => {
			func.apply(this, args);
		}, delay);
	};
}

function Filter(searchBox, tableToFilter, searchColumns) {
	// Declare variables
    // console.log(searchBox, tableToFilter)
	if(typeof searchColumns == 'undefined') searchColumns = [0,1,2];
  
	var input, filter, table, tr, td1, td2, i;
	input = document.getElementById(searchBox);
	
	filter = input.value.toUpperCase();
	table = document.getElementById(tableToFilter);
	tr = table.getElementsByTagName("tr");
  
	// Loop through all table rows, and hide those which doesn't match the search query
	for (i = 0; i < tr.length; i++) {
	  td1 = tr[i].getElementsByTagName("td")[searchColumns[0]];
	  td2 = tr[i].getElementsByTagName("td")[searchColumns[1]];
	  td3 = tr[i].getElementsByTagName("td")[searchColumns[2]];
	  if ( (td1) || (td2) || (td3) ) {
		if ( (td1.innerHTML.toUpperCase().indexOf(filter) > -1) || (td2.innerHTML.toUpperCase().indexOf(filter) > -1) || (td3.innerHTML.toUpperCase().indexOf(filter) > -1) ){
		  tr[i].style.display = "";
		} else {
		  tr[i].style.display = "none";
		}
	  }
	}
  }  


function valid(email) {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(email);
}