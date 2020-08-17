<?php
/**
 * View that handles the first steps of the installation. De place where the
 * database connection is made.
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
?>
    <div class="container installationContainer">
        <div class="col-sm-10 col-sm-offset-1">
            <div class="content">
                <div class="row">
                    <div class="panel panel-default installation">
                        <div class="panel-heading">
                            <div class="panel-title InstasllTitel text-center"> <?php echo _('RAM MANAGEMENT CONFIGURATION'); ?></div>
                        </div>
                        <form action="" method="post" enctype="multipart/form-data">
                            <p class="welcomeText medium-text"><?php echo _('Welcome and thank you for choosing RAM project management! Please provide the following information to start the installation.  '); ?></p>
                            <div class="control-group panel-body">
                                <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                    <input id="url" class="form-control installationFormInput <?php if($hD && isset($errors["url"]) && $errors["url"]){echo 'has-error';}?>" name="url" type="text" placeholder="<?php echo _('https://www.website.org'); ?>" required value="<?php if($hD){echo $oldData['url'];}else{echo $url;}?>">
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                    <input id="dbhost" class="form-control installationFormInput <?php if($hD && isset($errors["dbhost"]) && $errors["dbhost"]){echo 'has-error';}?>" name="dbhost" type="text" placeholder="<?php echo _('Database host (localhost)'); ?>"value="<?php if($hD){echo $oldData['dbhost'];}else{echo "localhost";}?>">
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                    <input id="dbname" class="form-control installationFormInput <?php if($hD && isset($errors["dbname"]) && $errors["dbname"]){echo 'has-error';}?>" name="dbname" type="text" placeholder="<?php echo _('Database name'); ?>" <?php if($hD){echo 'value="' . $oldData['dbname'] . '"';}?>>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                    <input id="dblogin" class="form-control installationFormInput <?php if($hD && isset($errors["dblogin"]) && $errors["dblogin"]){echo 'has-error';}?>" name="dblogin" type="text" placeholder="<?php echo _('Database username'); ?>" <?php if($hD){echo 'value="' . $oldData['dblogin'] . '"';}?>>
                                </div>
                                <div class="clearfix"></div>
                                <div class="clearfix"></div>
                                <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                    <input id="dbpass" class="form-control installationFormInput <?php if($hD && isset($errors["dbpass"]) && $errors["dbpass"]){echo 'has-error';}?>" name="dbpass" type="password" placeholder="<?php echo _('Database password'); ?>" <?php if($hD){echo 'value="' . $oldData['dbpass'] . '"';}?>>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block">
                                    <input id="dbport" class="form-control installationFormInput <?php if($hD && isset($errors["dbport"]) && $errors["dbport"]){echo 'has-error';}?>" name="dbport" type="number" placeholder="<?php echo _('port'); ?>" value="<?php if($hD){echo $oldData['dbport'];}else{echo "3306";}?>">
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group input-group col-xs-12 col-sm-8 col-lg-9 center-block extra-padding-left">
                                    <label class="form-check-label customradio" for="useHttps">
                                        <input type="checkbox" class="custom-control-input" id="useHttps" name="useHttps" <?php if(($hD && isset($oldData['useHttps'])) || $secure){echo 'checked';}?>>
                                        <span class="checkmark"></span>
                                        <?php echo _('Force the use of https?'); ?>
                                    </label>
                                </div>
                                <div class="clearfix"></div>
                                <div class="button-row">
                                    <button type="submit" name="main_info" value="send" class="btn btn-primary InstalLoginBTN"><?php echo _("INSTALL");?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php if(!$secure && $error === ""){ ?>
            <div class="alert alert-warning">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <h4><i class="fas fa-exclamation-triangle"></i><?php echo _(" Warning");?></h4>
                <?php echo _("You are currently using http. for security reasons we suggest to change the url to https.");?>
            </div>
        <?php }elseif($error !== ""){ ?>
            <div class="alert alert-warning">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <h4><i class="fas fa-exclamation-triangle"></i><?php echo _(" Warning");?></h4>
                <?php echo $error;?>
            </div>
        <?php } ?>
    </div>
</body>

