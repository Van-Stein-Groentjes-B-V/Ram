<?php
 /**
 * Visibility view. Allows the admin to select which elements of the object should be visible in the 
 * add and edit interfaces.
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

$settings = $data["settings"];

$elements = array(
                array('label' => _('Address:'), 'class' => 'inputIcon inputIcon14', 'img' => 'home-solid@2x.png', 'id' => "companyStreet", 'name' => "street", 'placeholder' => _('Street 23')),
                array('label' => _('Postal Code:'), 'class' => 'labelIcon', 'img' => 'envelope-solid.png', 'id' => "companyPostalcode", 'name' => "postalcode", 'placeholder' => _('1234 AB')),
                array('label' => _('City:'), 'class' => 'inputIcon inputIcon14', 'img' => 'home-solid@2x.png', 'id' => "companyCity", 'name' => "city", 'placeholder' => _('City name')),
                array('label' => _('Phone number:'), 'class' => 'inputIcon inputIcon14', 'img' => 'home-solid@2x.png', 'id' => "companyTel", 'name' => "tel", 'placeholder' => _('06 12345678')),
                array('label' => _('Country:'), 'class' => 'labelIcon', 'img' => 'globe-europe-solid.png', 'id' => "companyCountry", 'name' => "country", 'placeholder' => _('Netherlands')),
                array('label' => _('Vat number:'), 'class' => 'labelIcon', 'img' => 'money-check-solid.png', 'id' => "companyVat", 'name' => "vat_nr", 'placeholder' => _('123456798')),
                array('label' => _('Bank account nr.:'), 'class' => 'labelIcon', 'img' => 'money-check-solid.png', 'id' => "companyIban", 'name' => "iban", 'placeholder' => _('123456798')),
                array('label' => _('Chamber of commerce:'), 'class' => 'labelIcon', 'img' => 'money-check-solid.png', 'id' => "companyCC", 'name' => "kvk", 'placeholder' => _('123456798')),
                array('label' => _('Website:'), 'class' => 'labelIcon', 'img' => 'link-solid.png', 'id' => "companyWebsite", 'name' => "website", 'placeholder' =>  _('https://www.website.com')),
                array('label' => _('Facebook:'), 'class' => 'labelIcon', 'img' => 'facebook-f-brands.png', 'id' => "companyFB", 'name' => "facebook", 'placeholder' => _('https://www.facebook.com/linkname')),
                array('label' => _('Twitter:'), 'class' => 'labelIcon', 'img' => 'twitter-brands.png', 'id' => "companyTwitter", 'name' => "twitter", 'placeholder' => _('https://www.twitter.com/linkname')),
                array('label' => _('Youtube:'), 'class' => 'labelIcon', 'img' => 'youtube-brands.png', 'id' => "companyYoutube", 'name' => "youtube", 'placeholder' => _('https://www.youtube.com/linkname')),
                array('label' => _('LinkedIn:'), 'class' => 'inputIcon inputIcon14', 'img' => 'linkedin-in-brands.png', 'id' => "companyLinkedin", 'name' => "linkedin", 'placeholder' => _('https://www.linkedin.com/linkname')),
            );

?>
<div class="row">
    <div class="bs-callout bs-callout-primary col-lg-5">
      <h4><?php echo _("Set what you see."); ?></h4>
      <?php     echo _("Here you can customize the fields which will be visible in de screens."),
                     _("green is visible, red is invisible in the other screens."); ?>
    </div>
    <a id="backBtnVisi" href="<?php echo SITE_ROOT . "settings/admin/"; ?>" type="button" class="btn btn-primary pull-right"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo _("Go back"); ?></a>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _("Fields"); ?></h3>
    </div>
    <div class="panel-body">
        <div>
            <div class="row">
                <input type="hidden" id="company_id" name="id" value="">
                <div class="col-sm-12 col-lg-9 ">
                    <div class="control-group">
                        <div class="col-md-4 labelCompany required-input">
                            <img class="inputIcon" src="<?php echo SITE_ROOT;?>img/icons/building-solid@2x.png" alt="company name icon" /> 
                            <?php echo _('Company name:'); ?>
                        </div>
                        <div class="form-group input-group col-sm-12 col-lg-6">
                            <input id="company_name" disabled class="form-control " name="name" required type="text" placeholder="<?php echo _('Company name'); ?>">
                        </div>
                        <?php 
                            
                            foreach ($elements as $element){
                                echo '<div class="col-md-4 labelCompany">', 
                                        '<img class="' , $element['class'],'" src="', SITE_ROOT, 'img/icons/', $element['img'], '" alt="Company ', $element['id'], ' icon" />',
                                        $element['label'],
                                     '</div>',
                                     '<div class="form-group input-group col-sm-12 col-lg-6">',
                                        '<input id="', $element['id'], '" disabled class="form-control " name="', $element['name'], '" type="text" placeholder="', $element['placeholder'], '" style="display:block;">',
                                        $this->showElement($element['id'], $settings),
                                     '</div>',
                                     '<div class="clearfix"></div>';   
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="button-row">
                <button name="add" type="button" onClick="javascript:setVisibility('companies_css')" class="compani_visi_btn btn btn-primary"><i class="fa fa-floppy-o"></i> SAVE EDITS</button> 
            </div>
        </div>
    </div>
</div>