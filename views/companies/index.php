<?php
 /**
 * Companies index. Shows a list of companies in the system
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
global $user;
?>
<?php if($user->isAdmin()){?>
    <a id="addButton" href="<?php echo SITE_ROOT . "companies/addCompany/"; ?>" type="button" class="btn btn-primary"><i class="fas fa-plus"></i> <?php echo _("ADD COMPANY"); ?></a>
<?php }?>
<div class="row">
    <div class="bs-callout bs-callout-primary col-lg-5">
        <h4 id="describingTable"><?php echo _("Overview companies"); ?></h4>
        <p><?php 
            echo _("Here you have the overview off all companies in the system"); 
        ?></p>
    </div>
</div>
<div class="table-responsive">
    <table id="exporttable" 
           class="table table-striped table-hover" 
           data-toggle="table"
           data-show-export="true"
           data-export-data-type="all"
           data-export-types="['excel', 'json', 'csv', 'doc']"
           data-show-columns="true"
           data-show-toggle="false"
           data-show-refresh="true"
           data-pagination="true"
           data-cookie="true"
           data-cookie-id-table="saveId"
           data-search="true"
           data-page-size=10
           data-url="<?php echo SITE_ROOT . "companies/json/"; ?>"
           data-side-pagination="server"
           aria-describedby="describingTable"
           >    
        <thead>
            <tr>
                <th class="text-center" data-field="id" data-sortable="true" data-visible="false" title="<?php echo _("id"); ?>"><?php echo _("ID"); ?></th>
                <th class="" data-field="name" title="<?php echo _("Name company"); ?>">
                    <?php echo _("Logo and name"); ?>
                </th>
                <?php 
                    if(isset($settings["companyWebsite"]) && $settings["companyWebsite"]){
                        echo '<th class="text-center" data-sortable="false" data-field="website" title="', _("Website company"), '">', _("Website"), '</th>';
                    }
                    if($showSocials){ 
                        echo '<th class="text-center" data-visible="false" data-sortable="false" data-field="social" title="', _("Contact company"), '">', _("Contact"), '</th>';
                    } 
                    if(isset($settings["companyStreet"]) && $settings["companyStreet"]){ 
                        echo '<th class="text-center" data-sortable="true" data-visible="false" data-field="street" title="', _("Address company"), '">', _("Address"), '</th>';
                    } 
                    if(isset($settings["companyCity"]) && $settings["companyCity"]){ 
                        echo '<th data-sortable="true" data-field="city" title="', _("City company"), '">', _("City"), '</th>';
                    } 
                    if(isset($settings["companyCountry"]) && $settings["companyCountry"]){ 
                        echo '<th data-sortable="true" data-field="country" title="', _("Country company"), '">', _("Country"), '</th>';
                    } 
                    if(isset($settings["companyVat"]) && $settings["companyVat"]){ 
                        echo '<th  data-sortable="true" data-visible="false" data-field="vat_nr" title="', _("VAT nr company"), '">', _("VAT nr"), '</th>';
                    } 
                    if(isset($settings["companyIban"]) && $settings["companyIban"]){ 
                        echo '<th class="text-center"  data-sortable="true" data-field="iban" title="', _("Iban company"), '">', _("Iban"), '</th>';
                    } 
                    if(isset($settings["companyCC"]) && $settings["companyCC"]){ 
                        echo '<th class="text-center" data-sortable="true" data-field="kvk" title="', _("Kvk company"), '">', _("Kvk"), '</th>';
                    } 
                ?>
                <th class="fixedwidthcol" data-tableexport-display="none" data-class="col-md-1 text-center ignore-column" data-field="edit" title="Edit"><?php echo _("Edit/Delete"); ?></th>   
            </tr>
        </thead>
        <tbody class="sortable">
            
        </tbody>
    </table>
</div>