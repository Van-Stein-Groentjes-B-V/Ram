<?php
$hasProjects = true;
if(isset($data['hasProjects'])){
    $hasProjects = $data['hasProjects'] > 0;
}
$settings = array();
if(isset($data['settings'])){
    $settings = $data['settings'];
}
global $user;
?>
<?php if($user->isAdmin()){?>
    <a id="addButton" href="<?php echo SITE_ROOT . 'projects' . '/' . 'add' . '/'; ?>" type='button' class='btn btn-primary'><i class="fas fa-plus"></i> <?php echo _('ADD PROJECT'); ?></a>
<?php }?>
<div class="row">
    <div class="bs-callout bs-callout-primary col-lg-5">
      <h4 id="describingTable"><?php echo _('Overview projects'); ?></h4>
      <?php echo _('Here you have the overview off all projects in the system'); ?>
    </div>
</div>
<?php if(!$hasProjects){ ?>

<div class="row">
    <div class="first-timer">
        <a class="btn btn-primary first-not-yet-added" style="" href="<?php echo SITE_ROOT . 'projects' . '/' . 'add' . '/'; ?>" type='button' ><i class="fas fa-plus"></i> <?php echo _('Add your first project!'); ?></a>
    </div>
</div>
<?php }else{ ?>

<div class="table-responsive" >
    <table id="exporttable" 
           class="table table-striped table-hover" 
           data-toggle="table"
           data-show-export="true"
           data-export-data-type="all"
           data-export-types="['excel','json', 'csv','doc']"
           data-show-columns="true"
           data-show-toggle="false"
           data-show-refresh="true"
           data-pagination="true"
           data-sort-order="desc"
           data-search="true"
           data-page-size=10
           data-cookie="true"
           data-cookie-id-table="saveId"
           data-url="<?php echo SITE_ROOT . 'projects' . '/' . 'json' . '/'; ?>"
           data-side-pagination="server"
           aria-describedby="describingTable"
           >    
        <thead>
            <tr>
                <th class="text-center" data-field="id" data-sortable="true" data-visible="false" title="<?php echo _('id'); ?>"><?php echo _('ID'); ?></th>
                <th class="col-md-1 text-center" data-sortable="true" data-field="image" title="<?php echo _('Logo'); ?>"><?php echo _('Logo'); ?></th>
                <th class="" data-sortable="true" data-field="name" title="<?php echo _('Name project'); ?>"><?php echo _('Project name'); ?></th>
                <th class="text-center" data-sortable="true" data-field="project_status" title="<?php echo _('Status project'); ?>"><?php echo _('Status'); ?></th>
                <?php if(isset($settings['projectDeadline']) && $settings['projectDeadline']){ ?>
                    <th class="text-center" data-sortable="true" data-field="deadline" title="<?php echo _('Deadline project'); ?>"><?php echo _('Deadline'); ?></th>
                <?php 
                } 
                if(isset($settings['projectDropdownContractor']) && $settings['projectDropdownContractor']){ 
                ?>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="contractor" title="<?php echo _('Contractor project'); ?>"><?php echo _('Contractor'); ?></th>
                <?php 
                } 
                if(isset($settings['projectCrmcDropdown']) && $settings['projectCrmcDropdown']){ 
                ?>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="contractor_maincontact" title="<?php echo _('Contractor main contact project'); ?>"><?php echo _('Contractor main contact'); ?></th>
                <?php 
                } 
                if(isset($settings['projectMainResponDropdown']) && $settings['projectMainResponDropdown']){ 
                ?>
                    <th class="" data-sortable="true" data-visible="false" data-field="responsible" title="<?php echo _('Responsible for project'); ?>"><?php echo _('Main responsible'); ?></th>
                <?php } ?>
                <th class="fixedwidthcol col-md-5" data-tableexport-display="none" data-class="col-md-1 text-center" data-field="edit" title="Edit"><?php echo _('Edit/Delete'); ?></th>
                <!--<th class="fixedwidthcol" data-tableexport-display="none" data-class="center" data-field="delete" class="center" title="Delete">Delete</th>-->
            </tr>
        </thead>
        <tbody class="sortable">

        </tbody>
    </table>
</div>
<?php } 