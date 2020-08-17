<?php
/**
 * Login page
 * 
 * PHP version 7+
 *
 * @category   Views
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @editor     Thomas Shamoian <thomas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details. <https://www.gnu.org/licenses/>
 *
 **/

if(USE_SECUREIMAGE_LOGIN) { 
?>
    <script>
        var onSubmit = function(token){
            document.getElementById("token").value = token;
            document.getElementById("form").submit();
        };   
    </script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
   
<?php } ?>

<div class="container">
    <div class="col-sm-6">
        <div class="panel panel-default loginPanel" >
            <div class="panel-heading">
                <div class="panel-title loginTitel text-center"><?php if(USE_DEVICE_TOKEN){ echo _('Log in with device.'); }elseif(USE_SECUREIMAGE_LOGIN){echo _('Log in with captcha.');}else{echo _('Log in to your account.');} ?></div>
            </div>

            <div class="panel-body" >
                <form action="" method="post" enctype="multipart/form-data" class="form-signin"><br>
                    <div class="input-group row-margin  jui-input-inputFieldWrapper">
                        <div id="spn">
                            <input id="gebruiker" class="form-control loginInputFields" name="username" type="text" placeholder="Username" required="" autocomplete="off">
                        </div>
                    </div>
                    <div class="input-group row-margin">
                        <div id="spnPAS">
                            <input id="password" type="password" class="form-control loginInputFields" name="password" required placeholder="<?php echo _('Password');?>" autocomplete="off">
                        </div>
                        <?php if(USE_SECUREIMAGE_LOGIN) { ?>
                            <input id="token" type="hidden" name="token" value="">
                        <?php } ?>
                        <div class="input-group-addon eyeIcon">
                            <i class="glyphicon glyphicon-eye-open" onClick="javascript:showHidePassword('password')"></i>
                        </div>
                    </div>
                    <div class="button-row">
                        <button type="submit" name="submit" class="g-recaptcha btn btn-primary pull-right loginBTN" data-sitekey='<?php echo CAPTCHA_PUBLIC; ?>' data-callback='onSubmit'><?php echo _("LOGIN");?></button>
                        <a class="forgotPAS" href="<?php echo SITE_ROOT . "index/requestpassword/" ?>"><?php echo _('Forgot your password?'); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>