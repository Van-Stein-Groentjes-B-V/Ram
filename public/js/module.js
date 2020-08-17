
/**
 * open the modal
 * @return {undefined}
 */
function openModalForModule(){
    $('#addModule').modal();
}
/**
 * Whether it already is uploaded (fix for drop vs select file)
 * @type {Boolean}
 */
var alrUploaded = false;
/**
 * create the functionality of dropping the files
 */
$(document).ready(function(){
    var $form = $('.box-drop');
    $('body').on('dragover drop', function(e){
        e.preventDefault();
        e.stopPropagation();
    });
    if (isAdvancedUpload()) {
        $form.addClass('has-advanced-upload');
        var droppedFiles = false;

        $form.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
        })
        .on('dragover dragenter', function() {
            $form.addClass('is-dragover');
        })
        .on('dragleave dragend drop', function() {
            $form.removeClass('is-dragover');
        })
        .on('drop', function(e) {
            droppedFiles = e.originalEvent.dataTransfer.files;
            if(checkDroppedFiles(droppedFiles)){
                alrUploaded = true;
                startUploadItems(droppedFiles);
            }
        });
    }
    /**
     * set the onchange listener if the old way of adding an image is used.
     */
    $('#uploads').on("change", function(){
        if(!alrUploaded){
            startUploadItems($('#uploads')[0].files);
        }
    });
});

/**
 * check whether drag and drop is allowed by the browser.
 * @returns {Boolean}
 */
function isAdvancedUpload() {
    var div = document.createElement('div');
    // Internet Explorer 6-11
    var isIE = /*@cc_on!@*/false || !!document.documentMode;

    // Edge 20+
    var isEdge = !isIE && !!window.StyleMedia;
    return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window && !isIE && !isEdge;
};
/**
 * put the dropped file into the fileinput
 * @param {array} files array of the dropped files
 * @returns {undefined}
 */
function showFiles(files){
    var label = $('.box-drop').find('label');
    var input = $('.box-drop').find('input');
    label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace( '{count}', files.length ) : files[ 0 ].name);
    return;
}
/**
 * check the formats of the files dropped in the dropbox.
 * Only svg is allowed
 * @param {array} droppedFiles array of files
 * @returns {Boolean}
 */
function checkDroppedFiles(droppedFiles){
    var t = 0;
    $.each(droppedFiles, function(i,v){
        if(v.name.indexOf(".zip") < 0){
            dropImageHasError('Wrong format file');
            return false;
        }
        t++;
    });
    if(t === 1){
        return true;
    }
    if(t > 0){
        dropImageHasError('Only one file can be dropped here at a time.');
    }
    return false;
}

/**
 * handle the error message
 * @param {string} error the errormessage to show
 * @returns {undefined}
 */
function dropImageHasError(error){
    $('.box-drop').addClass("has-error");
    ErrorHandler(error);
    setTimeout(function(){$('.box-drop').removeClass("has-error");},1000);
}
/**
 * Start uploading the file.
 * @param {object} droppedFiles The files dropped or selected.
 * @return {undefined}
 */
function startUploadItems(droppedFiles){
    $('.box-drop .fa-upload').toggleClass('hidden');
    $('.box-drop .fa-spinner').toggleClass('hidden');
    var ajaxData = new FormData();
    $.each( droppedFiles, function(i, file) {
        ajaxData.append( 'to_be_uploaded', file , file.name);
    });
    ajaxData.append('add_new_module', '1');
    $.ajax({
        url: base_url + 'api/handleuploadmodule/' ,
        type: 'POST',
        data: ajaxData,
        processData: false,
        contentType: false,
        success: function(data) {
            $('.box-drop .fa-spinner').toggleClass('hidden');
            $('.box-drop .fa-upload').toggleClass('hidden');
            $('#addModule').modal('toggle');
            if(data.success === "success"){
                $('#exporttable').bootstrapTable('refresh');
                showSucces("success");
            }else{
                alrUploaded = false;
                if(typeof data.errorarray != 'undefined' && data.errorarray != 'undefined'){
                    ErrorHandler(data.errorarray[0].reason);
                }else{
                    ErrorHandler(data.errormessage);
                }
            }
        },
        error: function() {
          // Log the error, show an alert, whatever works for you
        }
    });
}

/**
 * Refresh table on success | show error provided
 * @param {object} e    The response of the server.
 * @return {undefined}
 */
function callbackResettable(e){
    if(e.errormessage !== ""){
        ErrorHandler(e.errormessage);
    }else{
        showSucces("success");
    }
    $('#exporttable').bootstrapTable('refresh');
}