<a id="addButton" href="<?php echo SITE_ROOT . 'usermanagement' . '/' . 'add' . '/'; ?>" type='button' class='btn btn-primary'><i class="fas fa-plus"></i></i> <?php echo _('ADD USER'); ?></a>
<div class="row">
    <div class="bs-callout bs-callout-primary col-lg-5">
      <h4 id="describingTable"><?php echo _('Overview users'); ?></h4>
      <?php echo _('Here you have the overview off all users in the system'); ?>
    </div>
</div>
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
           data-search="true"
           data-page-size=10
           data-cookie="true"
           data-cookie-id-table="saveId"
           data-url="<?php echo SITE_ROOT . 'usermanagement' . '/' . 'json' . '/'; ?>"
           data-side-pagination="server"
           aria-describedby="describingTable"
           >    
        <thead>
            <tr>
                <th class="text-center" data-field="id" data-sortable="true" data-visible="false" title="<?php echo _('id'); ?>"><?php echo _('ID'); ?></th>
                <th class="" data-sortable="true" data-field="fullname" title="<?php echo _('Full name user'); ?>"><?php echo _('Name'); ?></th>
                <th class="" data-sortable="true" data-field="username" title="<?php echo _('Username'); ?>"><?php echo _('Username'); ?></th>
                <th class="text-center" data-sortable="true" data-field="email" title="<?php echo _('User email'); ?>"><?php echo _('Email'); ?></th>
                <th data-sortable="true" data-field="admin" data-class="col-md-1 text-center" title="<?php echo _('User admin lvl'); ?>"><?php echo _('User access level'); ?></th>
                <th data-sortable="true" data-tableexport-display="none" data-class="col-md-1 text-center ignore-column" data-field="active" title="<?php echo _('active'); ?>"><?php echo _('Active'); ?></th>
                <th data-sortable="true" data-field="joined" data-class="col-md-1 text-center" data-visibility="false" title="<?php echo _('User date joined'); ?>"><?php echo _('Joined'); ?></th>
                <th data-tableexport-display="none" data-class="ignore-column coupledEye col-md-1 text-center" data-field="person_ignore" title="overview person"><?php echo _('Coupled person'); ?></th>
                <th data-tableexport-display="none" data-class="col-md-1 text-center ignore-column" data-field="edit" title="Edit"><?php echo _('Edit/Delete'); ?></th>
            </tr>
        </thead>
        <tbody class="sortable">

        </tbody>
    </table>
</div>
