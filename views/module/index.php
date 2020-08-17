<?php
   
?>
<div style="float:right;margin-top:35px;" onClick="javascipt:openModalForModule();" type='button' class='btn addModuleBTN btn-primary'><i class="fas fa-plus"></i> <?php echo _('Add module'); ?></div>
<div class="row">
    <div class="bs-callout bs-callout-primary col-lg-5">
      <h4 id="describingTable"><?php echo _('Overview modules'); ?></h4>
      <?php echo _('Here you have the overview off all modules in the system'); ?>
    </div>
</div>
<div class="row">
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
               data-cookie="true"
               data-cookie-id-table="saveId"
               data-search="true"
               data-page-size=10
               data-url="<?php echo SITE_ROOT . 'module' . '/' . 'json' . '/'; ?>"
               accesskey=""data-side-pagination="server"
               aria-describedby="describingTable"
               >    
            <thead>
                <tr>
                    <th data-field="id" data-sortable="true" data-visible="false" title="<?php echo _('id'); ?>"><?php echo _('ID'); ?></th>
                    <th data-sortable="true" data-field="name" title="<?php echo _('Name module'); ?>"><?php echo _('Name'); ?></th>
                    <th data-sortable="true" data-field="description" title="<?php echo _('Description module'); ?>"><?php echo _('Description'); ?></th>
                    <th data-sortable="true" data-field="version" data-class="col-md-1 text-center"  title="<?php echo _('Version module'); ?>"><?php echo _('Version'); ?></th>
                    <th class="fixedwidthcol" data-sortable="true" data-field="active_html" data-class="col-md-1 text-center" title="<?php echo _('Whether the module is active.'); ?>"><?php echo _('Active'); ?></th>
                    <th class="fixedwidthcol" data-tableexport-display="none" data-field="activate" data-class="col-md-1 text-center" title="Activate">Activate</th>
                    <th class="fixedwidthcol" data-tableexport-display="none" data-field="delete" data-class="col-md-1 text-center"  title="Delete">Delete</th>
                </tr>
            </thead>
            <tbody class="sortable">

            </tbody>
        </table>
    </div>
</div>
<div class="modal fade in" tabindex="-1" role="dialog" id="addModule" aria-hidden="true"> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><?php echo _('Upload Module'); ?></div>
            <div class="modal-body">
                <div class="col-sm-12 box-drop">
                    <div class="box-drop-input">
                        <i class="fas fa-8x fa-upload"></i>
                        <i class="fa fa-8x fa-spinner fa-pulse hidden"></i>
                        <div class="clearfix"></div>
                        <input class="box-drop-file" type="file" name="uploads[]" id="uploads" data-multiple-caption="{count} files selected" multiple />
                        <label for="uploads"><b>Choose a file</b><span class="box-drop-dragndrop"> or drag it here</span>.</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
