//define the globals i nthe head please

//emailreg => (regexp) the regex to check email userside
var emailreg = new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);

/**
 * Show or hide password by changing type.
 * @param {string} target The id of the field to change.
 * @return {undefined}
 */
function showHidePassword(target){
    var type = $('#'+target).prop('type');
    if(type === 'password' || type === 'password2'){
        $('#'+target).prop('type', 'text');
    }else{
        $('#'+target).prop('type', 'password');
    }
}

$('document').ready(function(){
    if($('#password').length > 0){
        $('#password').on('keyup', function(){
            checkPassword();
            compareps();
        });
        $('#password').on('blur', function(){
            checkPassword();
            compareps();
        });
    } 
    if($('#password2').length > 0){
        $('#password2').on('keyup', function(){
            compareps();
        });
        $('#password2').on('blur', function(){
            compareps();
        });
    }
    if($("#darkmenu").length > 0){
        $("#darkmenu").lavaLamp({
            fx: "easeOutBack",
            speed: 700
        });
    }
    if($('#desc').length > 0){
        tinymce.init({
            selector: "#desc",
            theme: "modern",
            skin: 'lightgray'
        });
    }
});

/*
 * checks the value of the password and if it has the following::
 * a lowercase letter
 * a uppercase letter
 * a number
 * and length > 6
 * does not yet escape strange values
 */
function checkPassword(){
    var invalid = false;
    var val = $('#password').val();
    
    if(val.length < 2){
        $('#password').parent().addClass("has-error");
        $('#password2').parent().addClass("has-error");
        return false;
    }
    if(!val.match(/[a-z]/)){
        invalid = true;
    }
    if(!val.match(/[A-Z]/)){
        invalid = true;
    }
    if(!val.match(/[0-9]/)){
        invalid = true;
    }
    if(val.length < 8){
        invalid = true;
    }
    if(invalid){
        $('#password').parent().addClass("has-error");
        return false;
    }
    if(!invalid){
        $('#password').parent().removeClass("has-error");
        $('#password').parent().addClass("has-success");
    }
    return true;
}

/**
 * Compare the passwords and add the classes depending on the results.
 * @return {Boolean}
 */
function compareps(){
    var val = $('#password').val();
    var val2 = $('#password2').val();
    if(checkPassword() && val === val2){
        $('#password2').parent().addClass("has-success");
        $('#password').parent().removeClass("has-error");
        $('#password2').parent().removeClass("has-error");
        $('.btn[type="submit"]').prop('disabled', false);
        return true;
    } else {
        $('#password').parent().addClass("has-error");
        $('#password2').parent().addClass("has-error");
        $('.btn[type="submit"]').prop('disabled', true);
        return false;
    }
}