<?php
 /**
 * View that handles the creation of the first admin.
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

     global $url;
    //check whether the connection is more secure than http
    $secure = true;
    if(isset($data["secure"])){
        $secure = $data["secure"];
    }
    
    //hasData, will be set to  true if data is assigned
    $hD = false;
    //$errors array with errors
    $errors = array();
    //array with previously filled in data
    $oldData = array();
    if(isset($data['oldData'])){
        $oldData = $data['oldData'];
        $hD = true;
    }
    if(isset($data['errors'])){
        $errors = $data['errors'];
    }
    //errormessage
    $error = "";
    if(isset($data['errormessage'])){
        $error = $data['errormessage'];
    }
    //prevent random people to add themselves
    $salty = "";
    if(isset($data['random'])){
        $salty = $data['random'];
    }
?>

        <div class="container installationContainer createAdmin">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="content">
                    <div class="row">
                        <div class="panel panel-default installation">
                            <div class="panel-heading">
                                <div class="panel-title InstasllTitel text-center"> <?php echo _('CREATE ADMIN ACCOUNT'); ?></div>
                            </div>
                            <div class="panel-body" >
                                <form action="" method="post" enctype="multipart/form-data">
                                    <p class="welcomeText medium-text"><?php echo _('Installation was successful! Now we need to create your admin account. '); ?></p>
                                    <div class="control-group">
                                        <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                            <input id="username" class="form-control installationFormInput <?php if($hD && isset($errors["username"]) && $errors["username"]){echo 'has-error';}?>" name="username" type="text" placeholder="<?php echo _('Username'); ?>" required <?php if($hD){echo 'value="' . $oldData['username'] . '"';}?>>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                            <input id="email" class="form-control installationFormInput<?php if($hD && isset($errors["email"]) && $errors["email"]){echo 'has-error';}?>" name="email" type="email" placeholder="<?php echo _('email@adres.com'); ?>" <?php if($hD){echo 'value="' . $oldData['email'] . '"';}?>>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                            <input id="fullname" class="form-control installationFormInput<?php if($hD && isset($errors["fullname"]) && $errors["fullname"]){echo 'has-error';}?>" name="fullname" type="text" placeholder="<?php echo _('Name Surname'); ?>" <?php if($hD){echo 'value="' . $oldData['fullname'] . '"';}?>>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                            <input id="company" class="form-control installationFormInput<?php if($hD && isset($errors["company"]) && $errors["company"]){echo 'has-error';}?>" name="company" type="text" placeholder="<?php echo _('Company name'); ?>" <?php if($hD){echo 'value="' . $oldData['company'] . '"';}?>>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block eyeIcon">
                                            <p class="infotext">The password must contain a lowercase letter a uppercase letter a number.</p>
                                            <input id="password" class="form-control installationFormInput<?php if($hD && isset($errors["password"]) && $errors["password"]){echo 'has-error';}?>" placeholder="password"  name="password" type="password" <?php if($hD){echo 'value="' . $oldData['password'] . '"';}?> aria-describedby="helpBlock2">
                                            <i class="glyphicon glyphicon-eye-open"  onClick="javascript:showHidePassword('password')"></i>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block eyeIcon">
                                            <p class="infotext">The password 2 must contain a lowercase letter a uppercase letter a number.</p>
                                            <input id="password2" class="form-control installationFormInput<?php if($hD && isset($errors["password2"]) && $errors["password2"]){echo 'has-error';}?>" placeholder="password 2" name="password2" type="password" <?php if($hD){echo 'value="' . $oldData['password2'] . '"';}?>>
                                            <i class="glyphicon glyphicon-eye-open" onClick="javascript:showHidePassword('password2')"></i>
                                        </div>
                                        <input id="specialVal" class="form-control hidden" name="specialVal" type="password" value="<?php if($hD){echo $oldData['specialVal'];}else{echo $salty;}?>">
                                        <div class="clearfix"></div>
                                        <input type="submit" name="registerAdmin" value="CREATE" class="btn btn-primary loginBTN adminCreateBTN" disabled>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!$secure && $error === "") { ?>
                <div class="alert alert-warning">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <h4><i class="fas fa-exclamation-triangle"></i><?php echo _(" Warning");?></h4>
                    <?php echo _("You are currently using http. for security reasons we suggest to change the url to https.");?>
                </div>
            <?php } elseif ($error !== "") { ?>
                <div class="alert alert-warning">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <h4><i class="fas fa-exclamation-triangle"></i><?php echo _(" Warning");?></h4>
                    <?php echo $error;?>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

