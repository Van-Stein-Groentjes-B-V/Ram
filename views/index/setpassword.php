<?php
 /**
 * Page to set a password
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

$hD = false;
$errors = array();
$oldValues = array();
if(isset($data['errors'])){
    $errors = $data['errors'];
}
if(isset($data['oldValues'])){
    $oldValues = $data['oldValues'];
    $hD = true;
}
?>
<div class="container">
    <div class="col-sm-6">
        <div class="panel panel-default loginPanel">
            <div class="panel-heading">
                <div class="panel-title text-center loginTitel"><?php echo _('RESET YOUR PASSWORD'); ?></div>
            </div>
            <div class="panel-body" >
                <form action="" method="post" enctype="multipart/form-data" class="form-signin"><br>
                    <div class="input-group row-margin medium-text">
                        <p><?php echo _("Password must contain a uppercase letter, lowercase letter, number and must be at least 6 long."); ?></p>
                    </div>
                    <div class="form-group input-group row-margin">
                        <input id="password" class="form-control <?php if($hD && isset($errors["password"]) && $errors["password"]){echo 'has-error';}?>" placeholder="password" name="password" type="password" <?php if($hD){echo 'value="' . $oldValues['password'] . '"';}?> aria-describedby="helpBlock2">
                        <div class="input-group-addon eyeIcon">
                            <i class="glyphicon glyphicon-eye-open" onClick="javascript:showHidePassword('password')"></i>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group input-group row-margin">
                        <input id="password2" class="form-control <?php if($hD && isset($errors["password2"]) && $errors["password2"]){echo 'has-error';}?>" placeholder="password 2" name="password2" type="password" <?php if($hD){echo 'value="' . $oldValues['password2'] . '"';}?>>
                        <div class="input-group-addon eyeIcon">
                            <i class="glyphicon glyphicon-eye-open" onClick="javascript:showHidePassword('password2')"></i>
                        </div>
                    </div>
                    <?php if(USE_SECUREIMAGE_LOGIN){ ?>
                        <p><?php echo _('If you have no linked devices, please check this'); ?></p>
                        <div class="g-recaptcha" data-sitekey="<?php echo CAPTCHA_PUBLIC; ?>"></div>
                        <script src='https://www.google.com/recaptcha/api.js'></script>
                    <?php } ?>
                    <div class="button-row">
                        <button type="submit" class="btn btn-primary loginBTN" name="submit" disabled><?php echo _('SEND'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>