<?php
 /**
 * Edit and Add view for companies.
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

    $edit = false;
    $string = _("Here you can add a new company.");
    if(isset($data["edit"]) && $data["edit"] === true){
        $string = _("You can edit a company here");
        $edit = true;
    }
    //hasData, will be set to  true if data is assigned
    $hD = false;
    //$errors array with errors from the input
    $errors = array();
    //array with previously filled in data
    $oldValues = array();
    if(isset($data["oldData"])){
        $oldValues = $data["oldData"];
        $hD = true;
    }
    if(isset($data["errors"])){
        $errors = $data["errors"];
    }

    global $user;
    
    $newlogo = "no_avatar.jpg";
    if(isset($oldValues['logo']) && file_exists("img/company/logos/".$oldValues['logo']) && strlen($oldValues['logo']) > 4){
        $newlogo = $oldValues['logo'];
    }
    if(isset($data['newlogo'])){
        $newlogo = $data['newlogo'];
    }
    $settings = array();
    if(isset($data["settings"])){
        $settings = $data["settings"];
    }
    $showSocials = false;
    if((isset($settings["companyFB"]) && $settings["companyFB"])||
                            (isset($settings["companyTwitter"]) && $settings["companyTwitter"])||
                            (isset($settings["companyYoutube"]) && $settings["companyYoutube"])||
                            (isset($settings["companyLinkedin"]) && $settings["companyLinkedin"])){
        $showSocials = true;
    }
    $showEconomic = false;
    if((isset($settings["companyVat"]) && $settings["companyVat"])||
                            (isset($settings["companyIban"]) && $settings["companyIban"])||
                            (isset($settings["companyCC"]) && $settings["companyCC"])){
        $showEconomic = true;
    }
    
?>
<div class="row customRow">
    <div class="bs-callout bs-callout-primary col-lg-5">
        <h4><?php if(isset($oldValues["name"])){ echo _("Edit") . " " . $oldValues["name"];}else{echo _("Add Company");} ?></h4>
        <?php echo $string; ?>
    </div>
    <a style="float:right;" href="<?php echo SITE_ROOT . "companies/"; ?>" type="button" class="custom_back_btn btn btn-primary"><i class="fas fa-chevron-left"></i></i> <?php echo _("Go back"); ?></a>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _("Company"); ?></h3>
    </div>
    <div class="panel-body">
        <form  method="post" enctype="multipart/form-data">
            <div class="row">
                <input type="hidden" id="company_id" name="id" value="<?php if(isset($oldValues["id"])){ echo $oldValues["id"]; } ?>">
                <div class=" midDiv col-md-12 col-lg-12 ">
                    <div class="general">
                        <h4>General</h4>
                    </div>
                    <div class="control-group">
                        <div class="col-md-3 col-lg-3 pull-left pfDiv"> 
                            <img id="previewImage" alt="Company logo" src="<?php echo SITE_ROOT;?>public/img/company/logos/<?php echo $newlogo;?>" class="img-responsive" style="width:128px;"> 
                            <input accept="image/*" type="file" id="file" name="file" class="<?php if(isset($errors["logo"]) && $errors["logo"]){echo "has-error";} ?>">
                            <input type="hidden" id="logo" name="logo" value="<?php echo $newlogo;?>">
                        </div>
                        <div id="companyName" class=" inputGroup generalGroup col-sm-12 col-md-4 col-lg-4">
                            <input id="name" class="form-control <?php if($hD && isset($errors["name"]) && $errors["name"]){echo "has-error";}?>" name="name" required type="text" placeholder="<?php echo _("Company name"); ?>" required <?php if(isset($oldValues["name"])){echo 'value="' . $oldValues["name"] . '"';}?>>
                        </div>
                        <div id="companyStreet" class=" inputGroup generalGroup col-sm-12 col-md-3 col-lg-3">
                            <input id="address" class="form-control <?php if($hD && isset($errors["street"]) && $errors["street"]){echo "has-error";}?>" name="street" type="text" placeholder="<?php echo _("Street"); ?>" <?php if(isset($oldValues["street"])){echo 'value="' . $oldValues["street"] . '"';}?>>
                        </div>
                        <div id="companyNumber" class=" inputGroup generalGroup col-sm-12 col-md-1 col-lg-1">
                            <input id="number" class="form-control <?php if($hD && isset($errors["number"]) && $errors["number"]){echo "has-error";}?>" name="number" type="text" placeholder="<?php echo _("number"); ?>" <?php if(isset($oldValues["number"])){echo 'value="' . $oldValues["number"] . '"';}?>>
                        </div>
                        <div id="companyPostalcode" class=" inputGroup generalGroup col-sm-12 col-md-4 col-lg-4">
                            <input id="postcode" class="form-control <?php if($hD && isset($errors["postalcode"]) && $errors["postalcode"]){echo "has-error";}?>" name="postalcode" type="text" placeholder="<?php echo _("1234 AB"); ?>" <?php if(isset($oldValues["postalcode"])){echo 'value="' . $oldValues["postalcode"] . '"';}?>>
                        </div>
                        <div id="companyCity" class=" inputGroup generalGroup col-sm-12 col-md-4 col-lg-4">
                            <input id="place" class="form-control <?php if($hD && isset($errors["city"]) && $errors["city"]){echo "has-error";}?>" name="city" type="text" placeholder="<?php echo _("City name"); ?>" <?php if(isset($oldValues["city"])){echo 'value="' . $oldValues["city"] . '"';}?>>
                        </div>
                        <div id="companyCountry" class=" inputGroup generalGroup col-sm-12 col-md-4 col-lg-4">
                            <input id="country" class="form-control <?php if($hD && isset($errors["country"]) && $errors["country"]){echo "has-error";}?>" name="country" type="text" placeholder="<?php echo _("Netherlands"); ?>" <?php if(isset($oldValues["country"])){echo 'value="' . $oldValues["country"] . '"';}?>>
                        </div>
                        <div id="companyTel" class=" inputGroup generalGroup col-sm-12 col-md-4 col-lg-4">
                            <input id="tel" class="form-control <?php if($hD && isset($errors["tel"]) && $errors["tel"]){echo "has-error";}?>" name="tel" type="text" placeholder="<?php echo _("06 12345678"); ?>" <?php if(isset($oldValues["tel"])){echo 'value="' . $oldValues["tel"] . '"';}?>>
                        </div>
                        <div id="companyWebsite" class=" inputGroup generalGroup col-sm-12 col-md-4 col-lg-4">
                            <input id="website" class="form-control <?php if($hD && isset($errors["website"]) && $errors["website"]){echo "has-error";}?>" name="website" type="text" placeholder="<?php echo _("https://www.website.com"); ?>" <?php if(isset($oldValues["website"])){echo 'value="' . $oldValues["website"] . '"';}?>>
                        </div>
                        <?php if($user->getUser()->isSuperAdmin()){ ?>
                            <div class="form-check col-lg-12">
                                <label class="form-check-label customradio" for="owned"> 
                                    <?php echo _("This is my own company");?>
                                    <input type="checkbox" class="form-check" id="owned" name="owned" <?php if(isset($oldValues["owned"]) && $oldValues["owned"] == 1){echo "checked";}?> value="1">
                                    <span class="checkmark"></span>
                                </label>
                                <div class="own">
                                    <span class="owntext">If you own the company that you are about to register, click on the checkbox</span>
                                    <img class="questionMark3" src="<?php echo SITE_ROOT;?>img/icons/question-circle-solid-11@2x.png" alt="questionmark image">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="<?php if(!$showEconomic){echo "hidden";} ?>">
                            <hr class="fieldSeparators" />
                            <div class="clearfix"></div>
                            <div class="financial">
                                <h4>Financial</h4>
                            </div>
                            <div class="clearfix"></div>
                            <div id="companyIban" class=" inputGroup col-sm-12 col-md-3 col-lg-3">
                                <input id="iban" class="form-control <?php if($hD && isset($errors["iban"]) && $errors["iban"]){echo 'has-error';}?>" name="iban" type="text" placeholder="<?php echo _('IBAN'); ?>" <?php if(isset($oldValues['iban'])){echo 'value="' . $oldValues['iban'] . '"';}?>>
                            </div>
                            <div id="companyCC" class=" inputGroup col-sm-12 col-md-3 col-lg-3">
                                <input id="kvk" class="form-control <?php if($hD && isset($errors["kvk"]) && $errors["kvk"]){echo 'has-error';}?>" name="kvk" type="text" placeholder="<?php echo _('Chamber of Commerce'); ?>" <?php if(isset($oldValues['kvk'])){echo 'value="' . $oldValues['kvk'] . '"';}?>>
                            </div>
                            <div id="companyVat" class=" inputGroup col-sm-12 col-md-3 col-lg-3">
                                <input id="vat_nr" class="form-control <?php if($hD && isset($errors["vat_nr"]) && $errors["vat_nr"]){echo 'has-error';}?>" name="vat_nr" type="text" placeholder="<?php echo _('VAT'); ?>" <?php if(isset($oldValues['vat_nr'])){echo 'value="' . $oldValues['vat_nr'] . '"';}?>>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="<?php if(!$showSocials){echo "hidden";} ?>">
                            <hr class="fieldSeparators" />
                            <div class="clearfix"></div>    
                            <div class="social">
                                <h4>Social media</h4>
                            </div>
                            <div class="clearfix"></div>
                            <div id="companyFB" class=" inputGroup col-sm-12 col-lg-3">
                                <input id="facebook" class="form-control <?php if($hD && isset($errors["facebook"]) && $errors["facebook"]){echo 'has-error';}?>" name="facebook" type="text" placeholder="<?php echo _(' https://www.facebook.com/linkname'); ?>" <?php if(isset($oldValues['facebook'])){echo 'value="' . $oldValues['facebook'] . '"';}?>>
                            </div>
                            <div id="companyTwitter" class=" inputGroup col-sm-12 col-md-3 col-md-3 col-lg-3">
                                <input id="twitter" class="form-control <?php if($hD && isset($errors["twitter"]) && $errors["twitter"]){echo 'has-error';}?>" name="twitter" type="text" placeholder="<?php echo _(' https://www.twitter.com/linkname'); ?>" <?php if(isset($oldValues['twitter'])){echo 'value="' . $oldValues['twitter'] . '"';}?>>
                            </div>
                            <div id="companyYoutube" class=" inputGroup col-sm-12 col-md-3 col-lg-3">
                                <input id="youtube" class="form-control <?php if($hD && isset($errors["youtube"]) && $errors["youtube"]){echo 'has-error';}?>" name="youtube" type="text" placeholder="<?php echo _(' https://www.youtube.com/linkname'); ?>" <?php if(isset($oldValues['youtube'])){echo 'value="' . $oldValues['youtube'] . '"';}?>>
                            </div>
                            <div class="clearfix"></div>
                            <div id="companyLinkedin" class=" inputGroup col-sm-12 col-md-3 col-lg-3">
                                <input id="linkedin" class="form-control <?php if($hD && isset($errors["linkedin"]) && $errors["linkedin"]){echo 'has-error';}?>" name="linkedin" type="text" placeholder="<?php echo _(' https://www.linkdin.com/linkname'); ?>" <?php if(isset($oldValues['linkedin'])){echo 'value="' . $oldValues['linkedin'] . '"';}?>>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="fieldSeparators" />
                        <div class="clearfix"></div>
                    </div>
                    <div class="button-row">
                        <button name="add" type="submit" class="custom_btn btn btn-primary pull-left"><i class="fa fa-floppy-o"></i><?php 
                            echo $edit ? _("SAVE") : _("CREATE"); 
                        ?></button> 
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
