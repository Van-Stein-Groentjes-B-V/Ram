<?php
    $settings = array();
    if(isset($data['settings'])){
        $settings = $data['settings'];
    }
?>
<div class="row">
    <div class="bs-callout bs-callout-info col-lg-10">
        <h4><?php echo _('Personal settings'); ?></h4>
        <p><?php echo _('Here you can specify your own preferences for viewing your Dashboard, lists and other content. <br>Note that these preferences are for you only, other users are not affected by these settings.');?></p>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo _("General settings");?></h3>
    </div>
    <div class="panel-body">
        <div class="row customRow">
          <form id="userSettingsForm" onsubmit="return storeUserSettings();">
                <div class="checkbox">
                    <label>
                        <input id="show_stats" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(!isset($settings['show_stats']) || $settings['show_stats']){echo 'checked';} ?>>
                        <span onclick="$('#show_stats').bootstrapToggle('toggle')"><?php echo _('Show statistics on dashboard.');?></span>
                    </label>
                </div>
                <div class="clearfix"></div>
                <div class="checkbox">
                    <label>
                        <input id="play_sounds" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(!isset($settings['play_sounds']) || $settings['play_sounds']){echo 'checked';} ?>>
                        <span onclick="$('#play_sounds').bootstrapToggle('toggle')"><?php echo _('Play sounds.');?></span>
                    </label>
                </div>
                <div class="clearfix"></div>
                <hr />
                <div>
                    <img class="userIcon" src="./img/icons/user-solid@2x.png" alt="user icon">
                    <label class="userLabel control-label col-form-label col-lg-2" for="username"><?php echo _('username:'); ?></label>
                    <div class="form-group input-group col-sm-4 col-lg-4">
                        <input id="username" class="form-control " name="username" type="text" required <?php if($settings['username']){echo 'value="' , $settings['username'] , '"';}?>>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div>
                    <img class="emailIcon" src="./img/icons/envelope-solid@2x.png" alt="email icon"> 
                    <label class="emailLabel control-label col-form-label col-lg-2" for="email"><?php echo _('email:'); ?></label>
                    <div class="form-group input-group col-sm-4 col-lg-4">
                        <input id="email" class="form-control" name="email" type="text" required <?php if($settings['email']){echo 'value="' , $settings['email'] , '"';}?>>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr />
                <div class="button-row overflow-auto">
                    <button type="submit" class="custom_btn btn btn-primary pull-left" name="saveButton" id="saveButton" value="view">
                        <?php echo _('SAVE EDITS'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
