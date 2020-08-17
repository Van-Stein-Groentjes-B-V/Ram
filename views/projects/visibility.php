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

$stati = "<option value=\"-1\" >" . _("Status") . "</option>";
$settings = $data["settings"];
$elements = array(
                array('label' => _('Test page:'), 'class' => 'labelIcon', 'img' => 'link-solid.png', 'id' => "projectDevLink", 'name' => "test", 'placeholder' => _('www.test.com')),
                array('label' => _('Android link:'), 'class' => 'inputIcon inputIcon14', 'img' => 'link-solid.png', 'id' => "projectAndroidLink", 'name' => "android_link", 'placeholder' => _('android link')),
                array('label' => _('iOS link:'), 'class' => 'labelIcon', 'img' => 'link-solid.png', 'id' => "projectiOSLink", 'name' => "ios_link", 'placeholder' => _('ios link')),
                array('label' => _('Respository:'), 'class' => 'labelIcon', 'img' => 'link-solid.png', 'id' => "projectResposiLink", 'name' => "repository", 'placeholder' => _('repository')),
                array('label' => _('Slug:'), 'class' => 'labelIcon', 'img' => 'link-solid.png', 'id' => "projectSlug", 'name' => "slug", 'placeholder' => _('slug')),
                array('label' => _('Deadline:'), 'class' => 'labelIcon', 'img' => 'calendar-day-solid.png', 'id' => "projectDeadline", 'name' => "deadline", 'placeholder' => _('16-10-2020')),
                array('label' => _('Budget:'), 'class' => 'labelIcon', 'img' => 'file-invoice-solid.png', 'id' => "projectBudget", 'name' => "budget", 'placeholder' =>  _('$ 300')),
                array('label' => _('Hourly wage:'), 'class' => 'labelIcon', 'img' => 'file-invoice-solid.png', 'id' => "projectHourlywage", 'name' => "hourly_wage", 'placeholder' => _('$ 12,45')),
                array('label' => _('Customer:'), 'class' => 'labelIcon', 'img' => 'user-solid.png', 'id' => "projectCustomer", 'name' => "customer", 'placeholder' => _('John Doe')),
                array('label' => _('Customer main contact:'), 'class' => 'labelIcon', 'img' => 'user-solid.png', 'id' => "projectCmcDropdown", 'name' => "person_main_contact_id", 'placeholder' => _('John Doe')),
                array('label' => _('S&O Hourly:'), 'class' => 'labelIcon', 'img' => 'clock-solid.png', 'id' => 'projectSOhourly', 'name' => 'SO_hourly', 'placeholder' => _('S&O')),
                array('label' => _('S&O Description:'), 'class' => 'labelIcon', 'img' => 'pen-solid.png', 'id' => 'projectSOdescription', 'name' => 'SO_description', 'placeholder' => _('S&O')),
                array('label' => _('Contractor:'), 'class' => 'labelIcon', 'img' => 'user-tag-solid.png', 'id' => 'projectDropdownContractor', 'name' => 'contractor', 'placeholder' => _('contractor')),
                array('label' => _('Contractor main contact:'), 'class' => 'labelIcon', 'img' => 'user-tag-solid.png', 'id' => 'projectCrmcDropdown', 'name' => 'contractor_main_contact_id', 'placeholder' => _('John Doe')),
                array('label' => _('Intermediate:'), 'class' => 'labelIcon', 'img' => 'user-tag-solid.png', 'id' => 'projectInterDropdown', 'name' => 'intermediate_id', 'placeholder' => _('intermediate')),
                array('label' => _('Main responsibility:'), 'class' => 'labelIcon', 'img' => 'user-tag-solid.png', 'id' => 'projectMainResponDropdown', 'name' => 'responsible', 'placeholder' => _('responsible')),
                array('label' => _('Status:'), 'class' => 'labelIcon', 'img' => 'pen-solid.png', 'id' => 'projectStatusDropdown', 'name' => 'project_status', 'placeholder' => _('status')),
                array('label' => _('Description:'), 'class' => 'labelIcon', 'img' => 'pen-solid.png', 'id' => 'projectDescription', 'name' => 'description', 'placeholder' => _('Lorum ipsum'))
            );
?>
<div class="row">
    <div class="bs-callout bs-callout-primary col-lg-5">
      <h4><?php echo _('Set what you see.'); ?></h4>
      <?php echo _('Here you can customize the fields which will be visible in de screens.'); ?>
      <?php echo _('green is visible, red is invisible in the other screens.'); ?>
    </div>
      <a style="float:right;" href="<?php echo SITE_ROOT . "settings/admin/"; ?>" type='button' class='btn btn-primary'><i class='glyphicon glyphicon-chevron-left'></i> <?php echo _('Go back'); ?></a>

</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _('Fields'); ?></h3>
    </div>
    <div class="panel-body">
        <form autocomplete="off" method="post" enctype="multipart/form-data">
            <div class="row">
                <input type="hidden" id="id" name="id" value="">
                <div class=" col-sm-12 col-lg-9 ">
                    <div class="control-group">
                        <div class="col-md-4 labelProject required-input"><img class="inputIcon" alt="icon for the Project name input field" src="<?php echo SITE_ROOT;?>img/icons/building-solid@2x.png"> <?php echo _('Project name:'); ?></div>
                        <div class="form-group input-group col-sm-12 col-lg-6">
                            <input id="name_Project" disabled class="form-control " name="name" type="text" required placeholder="<?php echo _('Project name'); ?>" required >
                        </div>
                      <?php 
                        foreach ($elements as $element){
                            echo '<div class="col-md-4 labelProject">', 
                                        '<img class="' , $element['class'],'" src="', SITE_ROOT, 'img/icons/', $element['img'], '" alt="Project ', $element['id'], ' icon" />',
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
                    <button name="add" type="submit" onClick="javascript:setVisibility('projects_css')" class="compani_visi_btn btn btn-primary pull-left"><i class="fa fa-floppy-o"></i> SAVE EDITS</button>  
                </div>
        </form>
    </div>
</div>