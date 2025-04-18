/**
 * createModal will create a modal on the fly, eliminating the
 * need to add it to your html file.
 * 
 * 
 * @author Reeaz Ramoly <reeaz@ramoly.info>
 * @param {String} modalElement 
 * @param {String} modalTitle 
 * @param {Array} buttons = [
            {type:'dismiss',label:'Cancel',icon:'fa fa-times',buttonClass:'btn btn-info'},
            {type:'button',label:'Save',icon:'fa fa-save',buttonClass:'btn bg-navy',id:'modal-save'}
    	] 
 * 
 */
function createModal(modalElement, modalTitle, buttons)
{
	if(typeof modalElement === 'undefined') modalElement = 'modalForm';
	if(typeof modalTitle === 'undefined') modalTitle = 'Modal Form';
	if(typeof buttons  === 'undefined') {
		buttons = [
            {type:'dismiss',label:'Close',icon:'fa fa-times',buttonClass:'btn btn-info'},
            // {type:'button',label:'Save',icon:'fa fa-save',buttonClass:'btn bg-navy',id:'modal-save'}
    	]
	}

    var modal = "";
    modal += '<div class="modal fade" id="' + modalElement + '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">';
    modal += '<div class="modal-dialog modal-lg" role="document">';
    modal += '<div class="modal-content">';
    modal += '<div class="modal-header">';
    modal += '        <h5 class="modal-title" id="exampleModalLongTitle">' + modalTitle + '<span class="d-none">for <span class="number">x</span> / <span class="product">y</span></span></h5>';
    modal += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    modal += '<span aria-hidden="true">&times;</span>';
    modal += '</button>';
    modal += '</div>';
    modal += '<div class="modal-body">';
    modal += '<div class="row">';
    modal += '</div>';
    modal += '</div>';
    modal += '<div class="modal-footer">';
    $(buttons).each(function(i,j){
		console.log(j)
        if(j.type == 'dismiss'){
            modal += '<button type="button" class="'+j.buttonClass+'" data-dismiss="modal"><i class="'+j.icon+'"></i> '+j.label+'</button>';
        }else{
            modal += '<button type="button" class="'+j.buttonClass+'" id="'+j.id+'"><i class="'+j.icon+'"></i> '+j.label+'</button>';
        }
    })
    modal += '</div>';
    modal += '</div>';
    modal += '</div>';
    modal += '</div>';

    $('body').append(modal);
}  