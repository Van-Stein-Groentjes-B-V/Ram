<?php
    $edit = false;
    $name = "add";
    $string = "You can add a user here.";
    if(isset($data['edit']) && $data['edit'] === true){
        $name = "edit";
        $string = "You can edit a user here";
        $edit = true;
    }
    //randomized number
    $counterAutofill = rand();
    //hasData, will be set to  true if data is assigned
    $hD = false;
    //$errors array with errors
    $errors = array();
    //array with previously filled in data
    $oldValues = array();
    if(isset($data['oldData'])){
        $oldValues = $data['oldData'];
        $counterAutofill = $oldValues['randomString'];
        $hD = true;
    }
    if(isset($data['errors'])){
        $errors = $data['errors'];
    }
?>

<div class="row customRow">
    <div class="bs-callout bs-callout-primary col-lg-5">
      <h4><?php echo _($name . ' a person'); ?></h4>
      <?php echo _($string); ?>
    </div>
    <a id="addButton" href="<?php echo SITE_ROOT . 'usermanagement' . '/'; ?>" type='button' class='btn btn-primary'><i class="fas fa-chevron-left"></i> <?php echo _('Go back'); ?></a>
</div>
<?php if(!SEND_MAIL){ ?>
    <div class="row">
        <div class="bs-callout bs-callout-danger">
            <a target="_blank" href="<?php echo SITE_ROOT . 'settings' . '/admin'; ?>" type='button' class='btn btn-primary pull-right'><i class="fas fa-chevron-left"></i> <?php echo _('Go to settings'); ?></a>
          <h4><?php echo _('Email sending is disabled'); ?></h4>
          <?php echo _('You\'ll need to activate email sending if you want to use this.'); ?>
        </div>
    </div>
<?php }else{ ?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php if(isset($oldValues['username'])){ echo _('Edit  ') . $oldValues['username'];}else{echo _('Add User');} ?></h3>
    </div>
    <div class="panel-body">
        <form method="post" autocomplete="off" enctype="multipart/form-data">
            <div class="row">
                <input type="hidden" id="id" name="id" value="<?php if(isset($oldValues['id'])){ echo $oldValues['id']; } ?>">
                <div class="midDiv col-sm-12 col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1">
                    <div class="general">
                        <h4>General</h4>
                    </div>
                    <div class="control-group">
                        <div class="inputGroup col-sm-8 col-lg-9">
                            <input id="username" class="form-control <?php if($hD && isset($errors["username"]) && $errors["username"]){echo 'has-error';}?>" name="username" required type="text" placeholder="<?php echo _('Username'); ?>" <?php if(isset($oldValues['username'])){echo 'value="' . $oldValues['username'] . '"';}?>>
                        </div>
                        <div class="inputGroup col-sm-8 col-lg-9">
                            <input id="email" class="form-control <?php if($hD && isset($errors["email"]) && $errors["email"]){echo 'has-error';}?>" name="email" required type="email" placeholder="<?php echo _('email@address.com'); ?>" <?php if(isset($oldValues['email'])){echo 'value="' . $oldValues['email'] . '"';}?>>
                        </div>
                        <div class="inputGroup col-sm-8 col-lg-9">
                            <input id="fullname" class="form-control <?php if($hD && isset($errors["fullname"]) && $errors["fullname"]){echo 'has-error';}?>" name="fullname" required type="text" placeholder="<?php echo _('John Doe'); ?>" <?php if(isset($oldValues['fullname'])){echo 'value="' . $oldValues['fullname'] . '"';}?>>
                        </div>
                        <div class="inputGroup col-sm-8 col-lg-9">
                            <select class="form-control <?php if($hD && isset($errors["admin"]) && $errors["admin"]){echo 'has-error';}?>" id="admin" name="admin">
                                <option value=""><?php echo _('Select admin level'); ?></option>
                                <option value="0" <?php if(isset($oldValues['admin']) && $oldValues['admin']==0){echo 'selected';}?>><?php echo _('Customer'); ?></option>
                                <option value="1" <?php if(isset($oldValues['admin']) && $oldValues['admin']==1){echo 'selected';}?>><?php echo _('Basic employee'); ?></option>
                                <option value="2" <?php if(isset($oldValues['admin']) && $oldValues['admin']==2){echo 'selected';}?>><?php echo _('Admin'); ?></option>
                                <option value="3" <?php if(isset($oldValues['admin']) && $oldValues['admin']==3){echo 'selected';}?>><?php echo _('High level admin'); ?></option>
                            </select>
                        </div>
                        <div class="inputGroup col-sm-8 col-lg-9 lazyInputGroup">
                            <input type="number" class="form-control bedrijfpersoonpickerLazy hidden" name="person_id" id="person_id" data-id="Person_user" value="<?php if(isset($oldValues['person_id'])){echo $oldValues['person_id'];}else{echo '-1';}?>"/>
                            <input name="person_name-<?php echo $counterAutofill; ?>" type="text" class="form-control bedrijfpersoonpickerLazy searchOnKeyUp <?php if($hD && isset($errors["person_name"]) && $errors["person_name"]){echo 'has-error';}?>" data-callback="Person_user" data-method="getPersons" data-target-id="person_id" data-control="api" <?php if(isset($oldValues['person_name-'.$counterAutofill])){echo 'value="' . $oldValues['person_name-'.$counterAutofill] . '"';}?> autocomplete="new-password"/>
                            <div class="input-group-addon fasPlusDiv" onClick="window.open('<?php echo SITE_ROOT . 'person/add/'; ?>')">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="seng_standard_dropdown seng_dropdown_searchOnKeyUp" data-for="Person_user">
                                <div class="info"></div>
                            </div>
                        </div>
                        <div class="hidden">
                            <input type="numer" name="randomString" value="<?php echo $counterAutofill; ?>"/>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr class="fieldSeparators" />
                <div class="clearfix"></div>
            </div>
                <div class="button-row">
                    <button name="add" type="submit" class="custom_btn btn btn-primary pull-left"><i class="fa fa-floppy-o"></i><?php if($edit == true){echo _('SAVE');}else{echo _('CREATE');} ?></button> 
                </div>
            </div>
        </form>
    </div>
</div>
<?php } 