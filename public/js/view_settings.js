$(document).ready(function(){
    if($('.input-group-addon.input_show').length > 0 || $('.input-group-addon.input_hide').length > 0){
        $('.input-group-addon').on('click', function(){
            //toggle the show / not show
            $(this).toggleClass('input_show').toggleClass('input_hide').find('i').toggleClass('glyphicon-eye-open').toggleClass('glyphicon-eye-close');
        });
    }
});
/**
 * call the api to make the changes
 * @param {string} name name of the target
 * @returns {} location change || ErrorHandler(error)
 */
function setVisibility(name){
    var data = {};
    var url =$('.bs-callout.bs-callout-primary>a').attr('href');
    $('.input-group-addon').each(function(){
        data[$(this).data('name')] = $(this).hasClass('input_show');
    });
    $.ajax({
        url: api_url + "setVisibilityData" + '/',
        type: "POST",
        data: {
            data : data,
            name : name
        },
        complete: function (e) {
            var response = JSON.parse(e.responseText);
            if(response.success === 'success'){
                location.reload();
            }else{
                ErrorHandler(response.errormessage);
            }
        }
    });
}