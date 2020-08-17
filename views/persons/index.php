<?php
$settings = array();
if(isset($data['settings'])){
    $settings = $data['settings'];
}
global $user;
?>
<?php if($user->isAdmin()){?>
    <a id="addButton" href="<?php echo SITE_ROOT . 'person' . '/' . 'add' . '/'; ?>" type='button' class='btn btn-primary'><i class="fas fa-plus"></i> <?php echo _('ADD PERSON'); ?></a>
<?php }?>
<div class="row">
    <div class="bs-callout bs-callout-primary col-lg-5">
      <h4 id="describingTable"><?php echo _('Overview persons'); ?></h4>
      <?php echo _('Here you have the overview off all persons in the system'); ?>
    </div>
</div>
<div class="table-responsive">
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
           data-url="<?php echo SITE_ROOT . 'person' . '/' . 'json' . '/'; ?>"
           data-side-pagination="server"
           aria-describedby="describingTable"
           >    
        <thead>
            <tr>
                <th class="text-center" data-field="id" data-sortable="true" data-visible="false" title="<?php echo _('id'); ?>"><?php echo _('ID'); ?></th>
                <th class="" data-sortable="true" data-field="name" title="<?php echo _('Name person'); ?>"><?php echo _('Name'); ?></th>
                <th class="text-center" data-sortable="true" data-field="tel" title="<?php echo _('Phone number'); ?>"><?php echo _('Phone number'); ?></th>
                <th class="text-center" data-sortable="true" data-field="company" title="<?php echo _('Person company'); ?>"><?php echo _('Company'); ?></th>
                <?php if((isset($settings['personStreet']) && $settings['personStreet'])){ ?>
                    <th class="text-center" data-sortable="true" data-field="street" title="<?php echo _('Address person'); ?>"><?php echo _('Address'); ?></th>
                <?php 
                } 
                if(isset($settings['personPostalcode']) && $settings['personPostalcode']){ 
                ?>
                    <th class="text-center" data-sortable="true" data-field="postalcode" title="<?php echo _('Postal code person'); ?>"><?php echo _('Postal code'); ?></th>
                <?php 
                } 
                if(isset($settings['personPlace']) && $settings['personPlace']){ 
                ?>
                    <th class="text-center" data-sortable="true" data-field="place" title="<?php echo _('City company'); ?>"><?php echo _('City'); ?></th>
                <?php 
                } 
                if(isset($settings['personCountry']) && $settings['personCountry']){ 
                ?>
                    <th class="text-center" data-sortable="true" data-field="country" title="<?php echo _('Country company'); ?>"><?php echo _('Country'); ?></th>
                <?php } ?>
                    <th class="text-center" data-sortable="true" data-field="email" title="<?php echo _('Email person'); ?>"><?php echo _('Email'); ?></th>
                <?php if(isset($settings['personTitle']) && $settings['personTitle']){ ?>
                    <th class="text-center" data-sortable="true" data-field="title" title="<?php echo _('Title person'); ?>"><?php echo _('Title'); ?></th>
                <?php 
                } 
                if(isset($settings['personWebsite']) && $settings['personWebsite']){ 
                ?>
                    <th class="text-center" data-visible="false" data-sortable="false" data-field="website" title="<?php echo _('Website person'); ?>"><?php echo _('Website'); ?></th>
                <?php 
                } 
                if((isset($settings['personFB']) && $settings['personFB'])||
                        (isset($settings['personTwitter']) && $settings['personTwitter'])||
                        (isset($settings['personYoutube']) && $settings['personYoutube'])||
                        (isset($settings['personLinkedin']) && $settings['personLinkedin'])){ 
                ?>
                    <th class="text-center" data-class="center" data-visible="false" class="center" data-sortable="false" data-field="social" title="<?php echo _('Contact person'); ?>"><?php echo _('Contact'); ?></th>
                <?php } ?>
                <th class="fixedwidthcol col-md-3" data-tableexport-display="none" data-class="col-md-1 text-center ignore-column" data-field="edit"  title="Edit"><?php echo _('Edit/Delete'); ?></th>
            </tr>
        </thead>
        <tbody class="sortable">

        </tbody>
    </table>
</div>