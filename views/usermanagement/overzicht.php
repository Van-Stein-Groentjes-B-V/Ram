<?php
if(isset($data['oldData'])){
    $user = $data['oldData'];
}

if(isset($data['allCoupledPerson'])){
    $allCoupledPerson = $data['allCoupledPerson'];
}
?>
<div class="row">
    <div class = "bs-callout bs-callout-default col-lg-5">
        <h4><?php echo _("user overview"); ?></h4>
        <?php echo _("Below you can view the user profile, if you want to edit the profile use the edit button on the bottom left."); ?>
    </div>
    <a href="<?php echo SITE_ROOT . 'usermanagement' . '/'; ?>" type='button' class='btn btn-primary pull-right'><i class="fas fa-chevron-left"></i> <?php echo _('Go back'); ?></a>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $user->getParsedString('getFullname'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="user">
                <h4>User information</h4>
            </div>
            <div class=" col-md-9 col-lg-9 overzicht customOverzicht">
                <div class="col-sm-4">
                    <img id="user-solid-2" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/user-solid@2x.png">
                    <p class="custom_P" title="User's Fullname"><?php if(strlen($user->getParsedString('getFullname')) > 0){echo $user->getParsedString('getFullname');}else{echo "-";} ?></p>
                </div>
                <div class="col-sm-4">
                    <img id="user-solid-2" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/user-solid@2x.png">
                    <p class="custom_P" title="User's username"><?php if(strlen($user->getParsedString('getUsername')) > 0){echo $user->getParsedString('getUsername');}else{echo "-";} ?></p>
                </div>
                <div class="col-sm-4">
                    <img id="link-solid" alt="link logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                    <p class="custom_P" title="User's Email"><?php if(strlen($user->getParsedString('getEmail')) > 0){echo $user->getParsedString('getEmail');}else{echo "-";} ?></p>
                </div>
                <div class="col-sm-4">
                    <img id="link-solid" alt="link logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                    <p class="custom_P" title="Admin level"><?php if(strlen($user->getParsedString('getAdmin')) > 0){echo $user->getParsedString('getAdmin');}else{echo "-";} ?></p>
                </div>
                <div class="col-sm-4">
                    <img id="link-solid" alt="link logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                    <p class="custom_P" title="Activation status icon"><?php echo $user->getActiveIcon() ?></p>
                </div>
                <div class="col-sm-4">
                    <img id="link-solid" alt="calendar logo" src="<?php echo SITE_ROOT;?>img/icons/calendar-day-solid@2x.png">
                    <p class="custom_P" title="Date of joined the RAM - MANAGEMENT"><?php if(strlen($user->getParsedString('getJoined')) > 0){echo $user->getParsedString('getJoined');}else{echo "-";} ?></p>
                </div>
            </div>
        </div>
        <hr class="fieldSeparators" />
        <div class="button-row">
            <a href="usermanagement/edit/<?php echo $user->getId(); ?>" data-original-title="<?php echo _("EDIT THE USER"); ?>" data-toggle="tooltip" class=" custom_btn btn btn-primary pull-left"><?php echo _("EDIT USER"); ?></a>
        </div>
    </div>
</div>
<?php if($allCoupledPerson){?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _("My linked person");?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php foreach ($allCoupledPerson as $allCp){?>
                <div class="col-sm-4 col-md-4 PictureCard projectImg">
                    <img alt="Customer logo" src="<?php echo SITE_ROOT;?>img/person/logos/<?php echo $allCp->getParsedString('getLogo'); ?> " class="img-responsive img-circle">
                </div>
                <div class="col-md-7 col-lg-7 overzicht informationSection">
                    <div class="col-sm-4">
                        <img id="user-solid-2" alt="user logo" src="<?php echo SITE_ROOT;?>img/icons/user-solid@2x.png">
                        <p class="custom_P" title="User's linked person name"><?php if(strlen($allCp->getParsedString('getName')) > 0){echo $allCp->getParsedString('getName');}else{echo "-";} ?></p>
                    </div>
                    <div class="col-sm-4">
                        <img id="building-solid" alt="building logo" src="<?php echo SITE_ROOT;?>img/icons/building-solid@2x.png">
                        <p class="custom_P" title="User's linked person company name"><?php if(strlen($allCp->getParsedString('getCompany')) > 0){echo $allCp->getParsedString('getCompany');}else{echo "-";} ?></p>
                    </div>
                    <div class="col-sm-4">
                        <img id="link-solid" alt="link logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                        <p class="custom_P" title="User's linked person email"><?php if(strlen($allCp->getParsedString('getEmail')) > 0){echo $allCp->getParsedString('getEmail');}else{echo "-";} ?></p>
                    </div>
                    <div class="col-sm-4">
                        <img id="link-solid" alt="tel logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                        <p class="custom_P" title="User's linked person phone number"><?php if(strlen($allCp->getParsedString('getTel')) > 0){echo $allCp->getParsedString('getTel');}else{echo "-";} ?></p>
                    </div>
                    <div class="col-sm-7">
                        <img id="link-solid" alt="tel logo" src="<?php echo SITE_ROOT;?>img/icons/link-solid@2x.png">
                        <a class="custom_a" title="User's linked person website" href="<?php if(strlen($allCp->getParsedString('getWebsite')) > 0){echo $allCp->getParsedString('getWebsite');}else{echo "-";}?>"><?php if(strlen($allCp->getParsedString('getWebsite')) > 0){echo $allCp->getParsedString('getWebsite');}else{echo "-";} ?></a>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
</div>
<?php }?>