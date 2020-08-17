<?php
global $user;
if(isset($data['oldData'])){
    $person = $data['oldData'];
}else{
    $person = new SG\Ram\Models\Person();
}

if(isset($data['allProjects'])){
    $project = $data['allProjects'];
}

if(isset($data['linkedUser'])){
    $linkedUser = $data['linkedUser'];
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
    <div class = "bs-callout bs-callout-default col-lg-5">
        <h4><?php if($person->getAccountId() == $user->getUserId()){echo _("Your profile");}else{echo _("Customer profile");}?></h4>
        <?php if($person->getAccountId() == $user->getUserId()){
            echo _("Below you can view the information the system has about you. If you want to add information or edit it click on the edit button below."); 
        } else {
            echo _("Below you can view the customer profile, if you want to edit the profile use the edit button on the bottom left. "); 
        }
        ?>
    </div>
    <a href="<?php echo SITE_ROOT . 'person' . '/'; ?>" type='button' class='custom_back_btn btn btn-primary pull-right'><i class="fas fa-chevron-left"></i> <?php echo _('Go back'); ?></a>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $person->getName(); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="general">
                <h4>General</h4>
            </div>
            <div class="col-md-2 col-lg-2 custom_picture_div"> 
                <img alt="Customer logo" title="User's Profile picture" src="<?php echo SITE_ROOT;?>img/person/logos/<?php echo  $person->getParsedString('getLogo'); ?> " class="img-responsive"> 
            </div>
            <div class=" col-md-9 col-lg-9 overzicht customOverzicht">
                <div class="col-sm-4">
                        <img id="user-solid-2" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/user-solid@2x.png">
                        <p class="custom_P" title="User's name"><?php if(strlen($person->getParsedString('getName')) > 0){echo $person->getParsedString('getName');}else{echo "-";} ?></p>
                </div>
                <div class="person_company_input col-sm-4" id="personCompany">
                    <img id="building-solid" alt="building logo" src="<?php echo SITE_ROOT;?>img/icons/building-solid@2x.png">
                    <p class="custom_P" title="Name of company"><?php if(strlen($person->getParsedString('getCompany')) > 0){echo $person->getParsedString('getCompany');}else{echo "-";} ?></p>
                </div>
                <div class="person_email_input col-sm-4" id="personEmail">
                    <img id="link-solid" alt="email logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                    <p class="custom_P" title="User's E-mail"><?php if(strlen($person->getParsedString('getEmail')) > 0){echo $person->getParsedString('getEmail');}else{echo "-";} ?></p>
                </div>
                <div class="person_tel_input col-sm-4" id="personTel">
                    <img id="link-solid" alt="tel logo" src="<?php echo SITE_ROOT;?>img/icons/phone-alt-solid@2x.png">
                    <p class="custom_P" title="User's phone number"><?php if(strlen($person->getParsedString('getTel')) > 0){echo $person->getParsedString('getTel');}else{echo "-";} ?></p>
                </div>
                <div class="person_title_input col-sm-4" id="personTitle">
                    <img id="user-tag-solid-2" alt="logo" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid@2x.png">
                    <p class="custom_P" title="User's function"><?php if(strlen($person->getParsedString('getTitle')) > 0){echo $person->getParsedString('getTitle');}else{echo "-";} ?></p>
                </div>
                <div class="person_address_input col-sm-4" id="personStreet">
                    <img id="home-solid" alt="logo" src="<?php echo SITE_ROOT;?>img/icons/home-solid@2x.png">
                    <p class="custom_P" title="User's adres"><?php if(strlen($person->getAddress()->getParsedString('getStreet')) > 0){echo $person->getAddress()->getParsedString('getStreet');}else{echo "-";} ?></p>
                </div>
                <div class="person_address_input col-sm-4" id="personNumber">
                    <img id="home-solid" alt="logo" src="<?php echo SITE_ROOT;?>img/icons/home-solid@2x.png">
                    <p class="custom_P" title="User's adres number"><?php if(strlen($person->getAddress()->getParsedString('getNumber')) > 0){echo $person->getAddress()->getParsedString('getNumber');}else{echo "-";} ?></p>
                </div>
                <div class="person_postcode_input col-sm-4" id="personPostalcode">
                    <img id="envelope-solid" alt="logo" src="<?php echo SITE_ROOT;?>img/icons/envelope-solid@2x.png">
                    <p class="custom_P" title="User's postal code"><?php if(strlen($person->getAddress()->getParsedString('getPostalcode')) > 0){echo $person->getAddress()->getParsedString('getPostalcode');}else{echo "-";} ?></p>
                </div>
                <div class="person_place_input col-sm-4" id="personPlace">
                    <img id="home-solid" alt="logo" src="<?php echo SITE_ROOT;?>img/icons/home-solid@2x.png">
                    <p class="custom_P" title="User's local place"><?php if(strlen($person->getAddress()->getParsedString('getCity')) > 0){echo $person->getAddress()->getParsedString('getCity');}else{echo "-";} ?></p>
                </div>
                <div class="person_country_input col-sm-4" id="personCountry">
                    <img id="link-solid" alt="logo" src="<?php echo SITE_ROOT;?>img/icons/globe-europe-solid@2x.png">
                    <p class="custom_P" title="User's country"><?php if(strlen($person->getAddress()->getParsedString('getCountry')) > 0){echo $person->getAddress()->getParsedString('getCountry');}else{echo "-";} ?></p>
                </div>
            </div>
            <div class="socialMedia <?php if(!$showSocials){ echo 'hidden';}?>">
                <div class="clearfix"></div>
                <hr class="fieldSeparators" />
                <div class="clearfix"></div>    
                <div class="social finanOverview">
                    <h4>Social media</h4>
                </div>
                <div class="socialMediaIconLink">
                    <div class="person_Facebook_input col-sm-3" id="personFB">
                        <img id="facebook-f-brands" alt="Facebook logo" src="<?php echo SITE_ROOT;?>img/icons/facebook-f-brands@2x.png">
                        <a class="custom_a" href="<?php if(strlen($person->getParsedString('getFacebook')) > 0){echo $person->getParsedString('getFacebook');}else{echo "-";} ?>"><?php if(strlen($person->getParsedString('getFacebook')) > 0){echo $person->getParsedString('getFacebook');}else{echo "-";} ?></a>
                    </div>
                    <div class="person_Twitter_input col-sm-3" id="personTwitter">
                        <img id="link-solid" alt="Facebook logo" src="<?php echo SITE_ROOT;?>img/icons/twitter-brands@2x.png">
                        <a class="custom_a" href="<?php if(strlen($person->getParsedString('getTwitter')) > 0){echo $person->getParsedString('getTwitter');}else{echo "-";} ?>"><?php if(strlen($person->getParsedString('getTwitter')) > 0){echo $person->getParsedString('getTwitter');}else{echo "-";} ?></a>
                    </div>
                    <div class="person_youtube_input col-sm-3" id="personYoutube">
                        <img id="youtube-brands" alt="Facebook logo" src="<?php echo SITE_ROOT;?>img/icons/youtube-brands@2x.png">
                        <a class="custom_a" href="<?php if(strlen($person->getParsedString('getYoutube')) > 0){echo $person->getParsedString('getYoutube');}else{echo "-";} ?>"><?php if(strlen($person->getParsedString('getYoutube')) > 0){echo $person->getParsedString('getYoutube');}else{echo "-";} ?></a>
                    </div>
                    <div class="person_linkedin_input col-sm-3" id="personLinkedin">
                        <img id="link-solid" alt="Linkedin logo" src="<?php echo SITE_ROOT;?>img/icons/linkedin-in-brands@2x.png">
                        <a class="custom_a" href="<?php if(strlen($person->getParsedString('getLinkedin')) > 0){echo $person->getParsedString('getLinkedin');}else{echo "-";} ?>"><?php if(strlen($person->getParsedString('getLinkedin')) > 0){echo $person->getParsedString('getLinkedin');}else{echo "-";} ?></a>
                    </div>
                    <div class="person_website_input col-sm-3" id="personWebsite">
                        <img id="link-solid" alt="link logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                        <a class="custom_a" href="<?php if(strlen($person->getParsedString('getWebsite')) > 0){echo $person->getParsedString('getWebsite');}else{echo "-";} ?>"><?php if(strlen($person->getParsedString('getWebsite')) > 0){echo $person->getParsedString('getWebsite');}else{echo "-";} ?></a>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="fieldSeparators" />
            <div class="clearfix"></div>    
            <div class="button-row">
                <a href="person/edit/<?php echo $person->getId(); ?>" data-original-title="<?php echo _("EDIT PROFILE"); ?>" data-toggle="tooltip" class="custom_btn btn btn-primary pull-left"><?php echo _("EDIT PROFILE"); ?></a>
            </div>
        </div>
    </div>
</div>

<?php if(isset($linkedUser)){?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _('User'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="user">
                <h4>User information</h4>
            </div>
            <div class=" col-md-9 col-lg-9 overzicht customOverzicht userOverzichtArea">
                <div class="col-sm-3">
                    <img id="user-solid-2" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/user-solid@2x.png">
                    <p class="custom_P" title="User's Fullname"><?php if(strlen($linkedUser->getParsedString('getFullname')) > 0){echo $linkedUser->getParsedString('getFullname');}else{echo "-";} ?></p>
                </div>
                <div class="col-sm-3">
                    <img id="user-shield-solid" alt="link logo" src="<?php echo SITE_ROOT;?>img/icons/user-shield-solid@2x.png">
                    <p class="custom_P" title="Admin level">
                        <?php 
                        if(strlen($linkedUser->getParsedString('getAdmin')) > 0){
                            switch ($linkedUser->getParsedString('getAdmin')){
                                case "0":
                                    echo 'Customer';
                                    break;
                                case "1":
                                    echo 'Basic employee';
                                    break;
                                case "2":
                                    echo 'Admin';
                                    break;
                                case "3":
                                    echo 'Higher level Admin';
                                    break;
                                default:
                                    echo '-';
                                    break;
                            }
                        } 
                        ?>
                    </p>
                </div>
                <div class="col-sm-5">
                    <img id="link-solid" alt="link logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                    <p class="custom_P" title="User's Email"><?php if(strlen($linkedUser->getParsedString('getEmail')) > 0){echo $linkedUser->getParsedString('getEmail');}else{echo "-";} ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }?>

<?php if(isset($project)){?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _("My projects");?></h3>
    </div>
    <div class="panel-body">
        <div class="row projectRow">
        <?php foreach ($project as $projectInfo) {?>
            <div class="col-md-4 col-lg-4 overzicht">
                <div class="col-lg-2 col-md-2 pic">
                    <img alt="Customer logo" src="<?php echo SITE_ROOT;?>img/projects/<?php if(file_exists("img/projects/".$projectInfo['image']) && $projectInfo['image']){echo $projectInfo['image'];}else{echo "no_avatar.jpg";} ?> " class="">
                </div>
                <div class="col-lg-6 col-md-6 tekst">
                    <?php if(isset($projectInfo['name'])){ ?>
                        <img id="building-solid" alt="building logo" src="<?php echo SITE_ROOT;?>img/icons/building-solid@2x.png">
                        <p class="custom_P_project"> <?php echo $projectInfo['name']; ?></p>
                    <?php }?>
                    <?php if(isset($projectInfo['deadline'])){ ?>
                        <img id="building-solid" alt="building logo" src="<?php echo SITE_ROOT;?>img/icons/calendar-day-solid@2x.png">
                        <p class="custom_P_project"> <?php echo $projectInfo['deadline']; ?></p>
                    <?php }?>    
                    <a class="custom_a2" href="<?php echo SITE_ROOT."projects/overview/".$projectInfo['id']; ?>">View project <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        <?php }?>
        </div>
    </div>
</div>
<?php }?>
