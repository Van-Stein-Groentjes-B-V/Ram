<?php
 /**
 * Company overview interface
 * Shows company details. 
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

// Get company data. These variables are always set, because if getting data failed the user is rerouted to index.
$company = $data['oldData'];
$allCompanyPersons = $data['allCompanyPersons'];
$involved = $data['involved'];
$website = "-";
global $user;
if(strlen($company->getLogo()) < 1){
    $logo = "no_avatar.";
} else {
    $logo = $company->getLogo();
}
$adminActions = false;
if($user->isAdmin() || $user->isSuperAdmin()){
    $adminActions = true;
}
if(strlen($company->getParsedString('getWebsite')) > 0){
    $website = $company->getParsedString('getWebsite');
}
$facebook = "-";
if(strlen($company->getParsedString('getFacebook')) > 0){
    $facebook = $company->getParsedString('getFacebook');
}
$twitter = "-";
if(strlen($company->getParsedString('getTwitter')) > 0){
    $twitter = $company->getParsedString('getTwitter');
}
$youtube = "-";
if(strlen($company->getParsedString('getYoutube')) > 0){
    $youtube = $company->getParsedString('getYoutube');
}
$linkedin = "-";
if(strlen($company->getParsedString('getLinkedin')) > 0){
    $linkedin = $company->getParsedString('getLinkedin');
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
?>

<div class="row customRow">
    <div class="bs-callout bs-callout-primary col-lg-5">
        <h4><?php echo  _('Company profile'); ?></h4>
            <?php echo  _('Below you can view the company profile, if you want to edit the profile use the edit button on the bottom left.'), 
                        "<br>", 
                        _('To add employees to a company, fill in the company at the people\'s profile.'); ?>
    </div>
    <a id="addButton" href="<?php echo SITE_ROOT . 'companies' . '/'; ?>" type='button' class='custom_back_btn btn btn-primary'>
        <i class="fas fa-chevron-left"></i> <?php echo _('Go back'); ?>
    </a>    
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $company->getName(); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="general">
                <h4>General</h4>
            </div>
            <div class="col-md-2 col-lg-2 custom_picture_div"> 
                <img alt="Company logo" title="Company Profile picture" src="<?php echo SITE_ROOT;?>img/company/logos/<?php echo $logo; ?> " class="img-responsive" style="width:128px;"> 
            </div>
            <div class=" col-md-9 col-lg-9 overzicht customOverzicht">
                <div class="col-sm-4">
                    <img id="user-solid-2" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/user-solid@2x.png">
                    <p class="custom_P" title="companies' name">
                        <?php echo strlen($company->getParsedString('getName')) > 0 ? $company->getParsedString('getName') : "-"; ?>
                    </p>
                </div>
                <div class="col-sm-4" id="companyStreet">
                    <img id="building-solid" alt="building logo" src="<?php echo SITE_ROOT;?>img/icons/building-solid@2x.png">
                    <p class="custom_P" title="The adres of the company">
                        <?php echo strlen($company->getAddress()->getParsedString('getStreet')) > 0 ? $company->getAddress()->getParsedString('getStreet') : "-";?>
                    </p>
                </div>
                <div class="col-sm-4" id="companyPostalcode">
                    <img id="envelope-solid" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/envelope-solid@2x.png">
                    <p class="custom_P" title="The company logo">
                        <?php echo strlen($company->getAddress()->getParsedString('getPostalcode')) > 0 ? $company->getAddress()->getParsedString('getPostalcode') : "-"; ?>
                    </p>
                </div>
                <div class="col-sm-4" id="companyCity">
                    <img id="home-solid" alt="postal code" src="<?php echo SITE_ROOT;?>img/icons/home-solid@2x.png">
                    <p class="custom_P" title="The companies' postal code">
                        <?php echo strlen($company->getAddress()->getParsedString('getCity')) > 0 ? $company->getAddress()->getParsedString('getCity') : "-"; ?>
                    </p>
                </div>
                <div class="col-sm-4" id="companyCountry">
                    <img id="globe-europe-solid" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/globe-europe-solid@2x.png">
                    <p class="custom_P" title="Country, where company is located">
                        <?php echo strlen($company->getAddress()->getParsedString('getCountry')) > 0 ? $company->getAddress()->getParsedString('getCountry') : "-"; ?>
                    </p>
                </div>
                <div class="col-sm-4">
                    <img id="globe-europe-solid" alt="tel logo" src="<?php echo SITE_ROOT;?>img/icons/phone-alt-solid@2x.png">
                    <p class="custom_P" title="Phonenumber">
                        <?php echo strlen($company->getParsedString('getTel')) > 0 ? $company->getParsedString('getTel') : "-"; ?>
                    </p>
                </div>
                <div class="col-sm-4" id="companyVat">
                    <img id="file-invoice-solid" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/file-invoice-solid@2x.png">
                    <p class="custom_P" title="The VAT number">
                        <?php echo strlen($company->getParsedString('getVatNr')) > 0 ? $company->getParsedString('getVatNr') : "-"; ?>
                    </p>
                </div>
                <div class="col-sm-4" id="companyIban">
                    <img id="money-check-solid" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/money-check-solid@2x.png">
                    <p class="custom_P" title="Creditcard, Meastro, Visa number">
                        <?php echo strlen($company->getParsedString('getIban')) > 0 ? $company->getParsedString('getIban') : "-"; ?>
                    </p>
                </div>
                <div class="col-sm-4" id="companyCC">
                    <img id="money-check-solid" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/money-check-solid@2x.png">
                    <p class="custom_P" title="Chamber of commerce">
                        <?php echo strlen($company->getParsedString('getKvk')) > 0 ? $company->getParsedString('getKvk') : "-"; ?>
                    </p>
                </div>
                <div class="col-sm-4">
                    <p class="ownedByUs" title="Checkbox, marked when you own this company">
                        <?php 
                            if($company->getParsedString('getOwned') > 0){
                                echo _('This is my company:'), '<i class="glyphicon glyphicon-check"></i>'; 
                            }
                        ?>
                    </p>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6" id="companyWebsite">
                    <img id="link-solid" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                    <a class="custom_a" title="Companies' website" href="<?php echo $website; ?>">
                        <?php echo $website; ?>
                    </a>
                </div>
            </div>
            
            <div class="socialMedia <?php if(!$showSocials){echo "hidden";}?>">
                <div class="clearfix"></div>
                <hr class="fieldSeparators" />
                <div class="social finanOverview">
                    <h4>Social media</h4>
                </div>
                <div class="clearfix"></div>
                <div class="socialMediaIconLink">
                    <div class="col-sm-4" id="companyFB">
                        <img id="facebook-f-brands" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/facebook-f-brands@2x.png">
                        <a class="custom_a" href="<?php echo $facebook; ?>">
                            <?php echo $facebook; ?>
                        </a>
                    </div>
                    <div class="col-sm-4" id="companyTwitter">
                        <img id="twitter-brands" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/twitter-brands@2x.png">
                        <a class="custom_a" href="<?php echo $twitter; ?>">
                            <?php echo $twitter; ?>
                        </a>
                    </div>
                    <div class="col-sm-4" id="companyYoutube">
                        <img id="youtube-brands" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/youtube-brands@2x.png">
                        <a class="custom_a" href="<?php echo $youtube; ?>">
                            <?php echo $youtube; ?>
                        </a>
                    </div>
                    <div class="col-sm-4" id="companyLinkedin">
                        <img id="linkedin-in-brands" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/linkedin-in-brands@2x.png">
                        <a class="custom_a" href="<?php echo $linkedin; ?>">
                            <?php echo $linkedin; ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <?php if($user->isAdmin()){?>
            <hr class="fieldSeparators" />
            <div class="button-row">
                <a href="companies/edit/<?php echo $company->getId(); ?>" data-original-title="<?php echo _("EDIT COMPANY"); ?>" data-toggle="tooltip" class="custom_btn btn btn-primary pull-left"><?php echo _("EDIT COMPANY"); ?></a>
            </div>
            <?php }?>
        </div>
    </div>
</div>

<?php if($allCompanyPersons){?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _('Persons'); ?></h3>
    </div>
    <div class="panel-body flex-works">
        <?php foreach ($allCompanyPersons as $cPersons){ ?>
            <div class="personcompany-card" data-id="<?php echo $cPersons->getId(); ?>">
                <div class="overzicht customOverzicht userOverzichtArea">
                    <div class="user">
                        <h4><?php echo $cPersons->getParsedString('getName'); ?></h4>
                    </div>
                    <div class="image-personcompany">
                        <img class="image-personcompany-large" alt="user image" src="<?php echo SITE_ROOT;?>public/img/person/logos/<?php echo $cPersons->getLogo(); ?>">
                    </div>
                    <div class="textholder-personcompany">
                        <div class="">
                            <img class="logo-personcompany" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/user-solid@2x.png">
                            <p class="custom_P" title="User's Fullname">
                                <?php echo strlen($cPersons->getName()) > 0 ? $cPersons->getParsedString('getName') : "-"; ?>
                            </p>
                        </div>
                        <div class="">
                            <img class="logo-personcompany" alt="user tag logo" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid@2x.png">
                            <p class="custom_P" title="User's title">
                                <?php echo strlen($cPersons->getTitle()) > 0 ? $cPersons->getParsedString('getTitle') : "-"; ?>
                            </p>
                        </div>
                        <div class="">
                            <img class="logo-personcompany" alt="link logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                            <p class="custom_P" title="User's Email">
                                <?php echo strlen($cPersons->getEmail()) > 0 ? $cPersons->getParsedString('getEmail') : "-"; ?>
                            </p>
                        </div>
                    </div>
                    <div class="personcompany-buttons">
                        <a href="<?php echo SITE_ROOT,'person/overview/',$cPersons->getId(),'/';?>" type='button' class="btn-personcompany" data-title=""><i class="far fa-eye"></i></a>
                        <?php if($adminActions) { ?>
                        <a href="<?php echo SITE_ROOT,'person/edit/',$cPersons->getId(),'/';?>" type='button' class="btn-personcompany" data-title=""><i class="far fa-edit"></i></a>
                        <div data-href="<?php echo SITE_ROOT,'api/removepersonfromcompany/',$cPersons->getId(),'/',$company->getId(),'/';?>" class="btn-personcompany" onclick="confirmDeleteAction(this)" 
                             data-confirm="<?php echo _("Are you sure you want to remove this person from this company? (This does not delete the person!)"); ?>" data-id="<?php echo $cPersons->getId(); ?>"><i class="fas fa-trash"></i></div>
                        <?php } ?>  
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
    <?php if($user->isAdmin()){?>
        <a href="person/add/<?php echo $company->getId(); ?>" type='button' class='custom_pTOc_btn btn btn-primary'> 
            <?php echo _('ADD PERSON TO COMPANY'); ?>
        </a>
    <?php }?>
</div>
<?php }?>

<?php if($involved){?>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo _("Projects");?></h3>
        </div>
        <div class="panel-body flex-works">
        <?php foreach ($involved as $projectInfo) { ?>
            <a class="overzicht companycompany-card" href="<?php echo SITE_ROOT."projects/overview/".$projectInfo->getId();?>">
                <div class="companycompany-cardview">
                    <div class="companycompany-image pic">
                        <img alt="Customer logo" src="<?php echo SITE_ROOT;?>img/projects/<?php echo $projectInfo->getImage() ? $projectInfo->getImage() : "no_avatar.jpg"; ?> ">
                    </div>
                    <div class="companycompany-text">
                        <div class="">
                            <img class="companycompany-logo" alt="building logo" src="<?php echo SITE_ROOT;?>img/icons/building-solid@2x.png">
                            <p class="custom_P">
                                <?php echo $projectInfo->getName() && $projectInfo->getName() !== "" ? $projectInfo->getParsedString('getName') : "-"; ?>
                            </p>
                        </div>
                        <div class="">
                            <img class="companycompany-logo" alt="building logo" src="<?php echo SITE_ROOT;?>img/icons/calendar-day-solid@2x.png">
                            <p class="custom_P">
                                <?php echo $projectInfo->getDeadline() && $projectInfo->getDeadline() !== "" ? $projectInfo->getParsedString('getDeadline') : "-"; ?>
                            </p>
                        </div>
                    </div>
                    <div class="companycompany-buttons">
                        <div class="btn-companycompany"><i class="far fa-eye"></i></div>
                    </div>
                </div>
            </a>
        <?php } ?>
        </div>
    </div>
<?php }?>