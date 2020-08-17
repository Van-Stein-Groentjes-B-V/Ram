$(document).ready(function(){
    /**
     * Add functionality to open more options
     * 
     */
   if($('.checkboxSettings .has-extra').length > 0){
        $('.checkboxSettings .has-extra').on('change', function(){
            var id = $(this).attr('id');
            if($(this).is(":checked")){
                $('.extra-values-box[data-for="' + id + '"]').addClass('show-box'); 
                $('.extra-values-box[data-for="' + id + '"] input.willberequired').each(function(){
                    console.log($(this));
                    if($(this).closest('.extra-values-box').data("for") !== id){
                        return true;
                    }
                    $(this).prop("required", true);
                });
            }else{
                $('.extra-values-box[data-for="' + id + '"]').removeClass('show-box'); 
                $('.extra-values-box[data-for="' + id + '"] input.willberequired').each(function(){
                    $(this).prop("required", false);
                });
            }
        });
   } 
   /**
    * remove the captcha if changed
    */
   if($('#login_captcha_public').length > 0){
        $('#login_captcha_public').on('change', function(){
            if($('#g-recaptcha-target').children().length > 0){
                $('#g-recaptcha-target').remove();
            }
        });
   }
});

/**
 * create an captcha when the public and secret are changed.
 * @return {undefined}
 */
function changePublicKey(){
    var publicPass = $('#login_captcha_public').val();
    if(publicPass.length > 4){
        if($('#g-recaptcha-target').children().length <= 0){
            $('#recaptcha-holder-holder').html('<div id="g-recaptcha-target" data-sitekey="' + publicPass + '"></div>');
        }
        grecaptcha.render('g-recaptcha-target');
    }else{
        ErrorHandler('captcha not set');
    }
}