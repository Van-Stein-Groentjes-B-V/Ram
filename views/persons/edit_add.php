
<?php
    $edit = false;
    $name = "add";
    $string = "You can add a person here.";
    if(isset($data['edit']) && $data['edit'] === true){
        $name = "edit";
        $string = "You can edit a person here";
        $edit = true;
    }
    //randomized number
    $counterAutofill = rand();
    //hasData, will be set to  true if data is assigned
    $hD = false;
    //$errors array met errors
    $errors = array();
    //array with previously filled in data
    $oldValues = array();
    if(isset($data['oldData'])){
        $oldValues = $data['oldData'];
        $counterAutofill = $oldValues['randomString'];
        $hD = true;
    }
    if(isset($data['errors'])){
        $errors = $data['errors'];
    }
    if(isset($data['selectedCompany'])){
        $selectedComp = $data['selectedCompany'];
    } else {
        $selectedComp = "";
    }
    
    $newlogo = "no_avatar.jpg";
    if(isset($oldValues['logo']) && file_exists("img/person/logos/".$oldValues['logo']) && strlen($oldValues['logo']) > 4){
        $newlogo = $oldValues['logo'];
    }
    if(isset($data['newlogo'])){
        $newlogo = $data['newlogo'];
    }
    $settings = array();
    if(isset($data['settings'])){
        $settings = $data['settings'];
    }
    $showSocials =  (isset($settings['personFB']) && $settings['personFB']) ||
                        (isset($settings['personTwitter']) && $settings['personTwitter']) ||
                        (isset($settings['personYoutube']) && $settings['personYoutube']) ||
                        (isset($settings['personWebsite']) && $settings['personWebsite']) ||
                        (isset($settings['personLinkedin']) && $settings['personLinkedin']);
?>
<div class="row customRow">
    <div class="bs-callout bs-callout-primary col-lg-5">
      <h4><?php echo _($name . ' a person'); ?></h4>
      <?php echo _($string); ?>
    </div>
    <a id="addButton" href="<?php echo SITE_ROOT . 'person' . '/'; ?>" type='button' class='btn btn-primary'><i class="fas fa-chevron-left"></i> <?php echo _('Go back'); ?></a>
</div>
<?php if(!SEND_MAIL){ ?>
<a id="addButton" target="_blank" href="<?php echo SITE_ROOT . 'settings' . '/'; ?>" type='button' class='btn btn-primary'><i class="fas fa-chevron-left"></i> <?php echo _('Go to settings'); ?></a>
    <div class="row">
        <div class="bs-callout bs-callout-danger">
          <h4><?php echo _('Email sending is disabled'); ?></h4>
          <?php echo _('You\'ll need to activate email sending if you want them to get accounts to this system.'); ?>
        </div>
    </div>
<?php } ?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php if(isset($oldValues['name'])){ echo _('Edit  ') . $oldValues['name'];}else{echo _('Add Person');} ?></h3>
    </div>
    <div class="panel-body">
        <form method="post" autocomplete="off" enctype="multipart/form-data">
            <div class="row">
                <input type="hidden" id="id" name="id" value="<?php if(isset($oldValues['id'])){ echo $oldValues['id']; } ?>">
                <div class="midDiv col-md-12 col-lg-12 ">
                    <div class="general">
                        <h4>General</h4>
                    </div>
                    <div class="control-group">
                        <div class="inputGroup col-md-2 col-lg-2 pfDiv"> 
                            <img id="previewImage" alt="Company logo" src="<?php echo SITE_ROOT;?>img/person/logos/<?php echo $newlogo; ?>" class="img-responsive" style="width:128px;"> 
                            <input accept="image/*" type="file" id="file" name="file" class="<?php if(isset($errors['logo']) && $errors['logo']){echo 'has-error';} ?>">
                            <input type="hidden" id="logo" name="logo" value="<?php echo $newlogo;?>">
                        </div>
                        <div class="col-sm-12 inputGroup  col-md-3 col-lg-3 person_name_input">
                            <input id="name" class="form-control <?php if($hD && isset($errors["name"]) && $errors["name"]){echo 'has-error';}?>" name="name" required type="text" placeholder="<?php echo _('Person\'s name'); ?>" required <?php if(isset($oldValues['name'])){echo 'value="' . $oldValues['name'] . '"';}?>>
                        </div>
                        <div id="personStreet" class="col-sm-12 inputGroup col-lg-3 person_address_input">
                            <input id="address" class="form-control <?php if($hD && isset($errors["street"]) && $errors["street"]){echo 'has-error';}?>" name="street" type="text" placeholder="<?php echo _('Street 23'); ?>" <?php if(isset($oldValues['street'])){echo 'value="' . $oldValues['street'] . '"';}?>>
                        </div>
                        <div id="personNumber" class=" inputGroup generalGroup col-sm-12 col-md-1 col-lg-1 person_number_input  ">
                            <input id="number" class="form-control <?php if($hD && isset($errors["number"]) && $errors["number"]){echo 'has-error';}?>" name="number" type="text" placeholder="<?php echo _('Street number'); ?>" <?php if(isset($oldValues['number'])){echo 'value="' . $oldValues['number'] . '"';}?>>
                        </div>
                        <div id="personCompany" class="col-sm-12 inputGroup lazyInputGroup col-md-3 col-lg-3 person_company_input">
                            <div class="input-group-addon fasPlusDiv" onClick="window.open('<?php echo SITE_ROOT . 'companies/addCompany/'; ?>')">
                                <i class="fas fa-plus"></i>
                            </div>
                        <?php if(!$selectedComp){?>
                            <input type="number" class="form-control bedrijfpersoonpickerLazy hidden" name="company_id" id="company_id" data-id="companyPerson" placeholder="<?php echo _('company'); ?>" value="<?php if(isset($oldValues['company_id'])){echo $oldValues['company_id'];}else{echo '-1';}?>"/>
                            <input id="cmpny" name="company-<?php echo $counterAutofill; ?>" type="text" placeholder="<?php echo _('company'); ?>" class="form-control bedrijfpersoonpickerLazy searchOnKeyUp <?php if($hD && isset($errors["company"]) && $errors["company"]){echo 'has-error';}?>" data-callback="companyPerson" data-method="getCompany" data-target-id="company_id" data-control="api" <?php if(isset($oldValues['company-'.$counterAutofill])){echo 'value="' . $oldValues['company-'.$counterAutofill] . '"';}?> autocomplete="new-password"/>
                        <?php } else {?>
                            <input type="number" class="form-control bedrijfpersoonpickerLazy hidden" name="company_id" id="company_id" data-id="companyPerson" placeholder="<?php echo _('company'); ?>" value="<?php if(isset($oldValues['company_id'])){echo $oldValues['company_id'];}else{echo '-1';}?>"/>
                            <input id="cmpny" name="company-<?php echo $counterAutofill; ?>" type="text" placeholder="<?php echo _('company'); ?>" class="form-control bedrijfpersoonpickerLazy searchOnKeyUp <?php if($hD && isset($errors["company"]) && $errors["company"]){echo 'has-error';}?>" data-callback="companyPerson" data-method="getCompany" data-target-id="company_id" data-control="api" value="<?php if($selectedComp){ echo $selectedComp['name'];} else {echo "-1";} ?>" autocomplete="new-password"/>
                        <?php }?>
                            <div class="seng_standard_dropdown seng_dropdown_searchOnKeyUp" data-for="companyPerson">
                                <div class="info"></div>
                            </div>
                        </div>
                        <div id="personEmail" class="col-sm-12 inputGroup col-md-3 col-lg-3 person_email_input">
                            <input id="email" class="form-control <?php if($hD && isset($errors["email"]) && $errors["email"]){echo 'has-error';}?>" name="email" required type="email" placeholder="<?php echo _('email@address.com'); ?>" <?php if(isset($oldValues['email'])){echo 'value="' . $oldValues['email'] . '"';}?>>
                        </div>
                        <div id="personTitle" class="col-sm-12 inputGroup col-md-3 col-lg-3 person_title_input">
                            <input id="title" class="form-control <?php if($hD && isset($errors["title"]) && $errors["title"]){echo 'has-error';}?>" name="title" type="text" placeholder="<?php echo _('CEO'); ?>" <?php if(isset($oldValues['title'])){echo 'value="' . $oldValues['title'] . '"';}?>>
                        </div>
                        <div id="personPostalcode" class="col-sm-12 inputGroup col-lg-3 person_postcode_input">
                            <input id="postcode" class="form-control <?php if($hD && isset($errors["postalcode"]) && $errors["postalcode"]){echo 'has-error';}?>" name="postalcode" type="text" placeholder="<?php echo _('1234 AB'); ?>" <?php if(isset($oldValues['postalcode'])){echo 'value="' . $oldValues['postalcode'] . '"';}?>>
                        </div>
                        <div id="personPlace" class="col-sm-12 inputGroup col-lg-3 person_place_input">
                            <input id="place" class="form-control <?php if($hD && isset($errors["city"]) && $errors["city"]){echo 'has-error';}?>" name="city" type="text" placeholder="<?php echo _('City name'); ?>" <?php if(isset($oldValues['city'])){echo 'value="' . $oldValues['city'] . '"';}?>>
                        </div>
                        <div id="personCountry" class="col-sm-12 inputGroup col-lg-3 person_country_input">
                            <input id="country" class="form-control <?php if($hD && isset($errors["country"]) && $errors["country"]){echo 'has-error';}?>" name="country" type="text" placeholder="<?php echo _('Netherlands'); ?>" <?php if(isset($oldValues['country'])){echo 'value="' . $oldValues['country'] . '"';}?>>
                        </div>
                        <div id="personTel" class="col-sm-12 inputGroup col-lg-3 person_tel_input">
                            <input id="tel" class="form-control <?php if($hD && isset($errors["tel"]) && $errors["tel"]){echo 'has-error';}?>" name="tel" type="text" placeholder="<?php echo _('06 12345678'); ?>" <?php if(isset($oldValues['tel'])){echo 'value="' . $oldValues['tel'] . '"';}?>>
                        </div>
                        <div class="<?php if(!$showSocials){ echo 'hidden';}?>">
                            <div class="clearfix"></div>
                            <hr class="fieldSeparators" />
                            <div class="clearfix"></div>
                            <div class="social">
                                <h4>Social media</h4>
                            </div>
                            <div class="clearfix"></div>
                            <div id="personFB" class="inputGroup col-sm-12 col-md-3 col-lg-3 person_facebook_input">
                                <input id="facebook" class="form-control <?php if($hD && isset($errors["facebook"]) && $errors["facebook"]){echo 'has-error';}?>" name="facebook" type="text" placeholder="<?php echo _(' https://www.facebook.com/linkname'); ?>" <?php if(isset($oldValues['facebook'])){echo 'value="' . $oldValues['facebook'] . '"';}?>>
                            </div>
                            <div id="personTwitter" class="inputGroup col-sm-12 col-md-3 col-lg-3 person_twitter_input">
                                <input id="twitter" class="form-control <?php if($hD && isset($errors["twitter"]) && $errors["twitter"]){echo 'has-error';}?>" name="twitter" type="text" placeholder="<?php echo _(' https://www.twitter.com/linkname'); ?>" <?php if(isset($oldValues['twitter'])){echo 'value="' . $oldValues['twitter'] . '"';}?>>
                            </div>
                            <div id="personYoutube" class="inputGroup col-sm-12 col-md-3 col-lg-3 person_youtube_input">
                                <input id="youtube" class="form-control <?php if($hD && isset($errors["youtube"]) && $errors["youtube"]){echo 'has-error';}?>" name="youtube" type="text" placeholder="<?php echo _(' https://www.youtube.com/linkname'); ?>" <?php if(isset($oldValues['youtube'])){echo 'value="' . $oldValues['youtube'] . '"';}?>>
                            </div>
                            <div id="personLinkedin" class="inputGroup col-sm-12 col-md-3 col-lg-3 person_linkedin_input">
                                <input id="linkedin" class="form-control <?php if($hD && isset($errors["linkedin"]) && $errors["linkedin"]){echo 'has-error';}?>" name="linkedin" type="text" placeholder="<?php echo _(' https://www.linkdin.com/linkname'); ?>" <?php if(isset($oldValues['linkedin'])){echo 'value="' . $oldValues['linkedin'] . '"';}?>>
                            </div>
                            <div id="personWebsite" class="col-sm-12 inputGroup col-md-3 col-lg-3 person_website_input">
                                <input id="website" class="form-control <?php if($hD && isset($errors["website"]) && $errors["website"]){echo 'has-error';}?>" name="website" type="text" placeholder="<?php echo _('https://www.website.com'); ?>" <?php if(isset($oldValues['website'])){echo 'value="' . $oldValues['website'] . '"';}?>>
                            </div>
                        </div>
                        <div class="hidden">
                            <input type="numer" name="randomString" value="<?php echo $counterAutofill; ?>" style="display:none;"/>
                        </div>
                    </div>
                <div class="clearfix"></div>
                <hr class="fieldSeparators" />
                <div class="clearfix"></div>    
                </div>
                <div class="button-row">
                    <button name="add" type="submit" class="custom_btn btn btn-primary pull-left"><i class="fa fa-floppy-o"></i><?php if($edit == true){echo _('SAVE');}else{echo _('CREATE');} ?></button> 
                </div>
            </div>
        </form>
    </div>
</div>