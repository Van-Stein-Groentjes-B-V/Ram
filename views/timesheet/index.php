<?php
$oldValues = array();
global $user;
$projects = new \SG\Ram\Controllers\TimesheetController();
$projectArray = $projects->getProjects();
?>
<?php if($user->getUser()->isSuperAdmin()){ ?>
            <a href="<?php echo SITE_ROOT . 'timesheet' . '/' . 'admin' . '/'; ?>" type='button' class='btn btn-primary pull-right specificBtn'><i class="fas fa-search"></i> <?php echo _('specific'); ?></a>
        <?php }else{ ?>
            <a href="<?php echo SITE_ROOT . 'timesheet' . '/' . 'specific' . '/'; ?>" type='button' class='btn btn-primary pull-right specificBtn'><i class="fas fa-search"></i> <?php echo _('Specific'); ?></a>
        <?php } ?>
<div class="row">
    <div class="bs-callout timesheet-callout bs-callout-primary col-lg-5">
        <h4><?php echo _('Timesheets'); ?></h4>
        <?php echo _('Here you can find the time sheets of projects or persons.'); ?>
            </div>
</div>
<div class="row" id="mainTimePage">
    <div class="col-md-12">
        <div class="panel panel-danger ">
            <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-clock-o"></i> <?php echo _('Change / Add time.');?></h3>
            </div>
            <div class="panel-body">
                <form method="post" class="form-horizontal" role="form" onsubmit="return saveTime(-1);">
                    <div class="col-xs-6 col-md-3">
                        <label class="control-label" for="project"><?php echo _('Project:');?></label>
                        <div class="stop-to-large-dropdown">
                            <select id="selectPickerTimeSheet" class="selectpicker" data-live-search="true">
                                <option>Please select a project</option>
                                <?php foreach ($projectArray as $project) {?>
                                <option id="project_id" data-id="project_id" name="project_id" value="<?php echo $project['id']; ?>"><?php echo $project['name']; ?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>                    
                    <div class="col-xs-6 col-md-3">
                        <label class="control-label" for="savetime_timedate"><?php echo _('Date:');?></label>
                        <input type="text" class="form-control" data-date-format="dd-mm-yyyy" id="savetime_timedate" placeholder="dd/mm/yyyy" name="timedate" autocomplete="off" required="required" value="<?php if(isset($oldValues['timedate'])){echo $oldValues['timedate'];}else{echo '';}?>">
                    </div>
                    <div class="col-xs-6 col-md-2">
                        <label class="control-label" for="savetime_timepicker1"><?php echo _('Start time:');?></label>
                        <div class="input-group bootstrap-timepicker timepicker">
                            <input id="savetime_timepicker1" type="text" name="starttime" class="form-control input-small timepicker">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-2">
                        <label class="control-label" for="savetime_timepicker2"><?php echo _('End time:');?></label>
                        <div class="input-group bootstrap-timepicker timepicker">
                            <input id="savetime_timepicker2" type="text" name="endtime" class="form-control input-small timepicker">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                    </div>
                    <div class="form-check checkbox-teal col-xs-6 col-md-1">
                        <input type="checkbox" class="form-check-input" id="so" name="so" value="1">
                        <label class="form-check-label soLabel" for="so"><?php echo _('s&o'); ?></label>
                    </div>
                    <div class="col-xs-12 col-md-2">
                        <label class="control-label" for="addtimebutton"><br></label>
                        <button type="submit" class="btn btn-primary form-control" name="changeTime" id="addtimebutton" value="view">
                            <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-calendar"></i> <?php echo _('Current week: ') ?><span id="weeknumber"></span></h3>
            </div>
            <div class="panel-body">
                <div class="timesheet color-scheme-default white"id="weektimesheet" 
                    data-title="#weeknumber"
                    data-between="2"
                    data-length="week"
                    data-rows="3"
                    data-click="changeTime"
                    data-colors="#ef5350,#7E57C2,#29B6F6,#66BB6A,#FFEE58,#FF7043,#78909C,#EC407A,#5C6BC0,#26C6DA,#9CCC65,#FFCA28,#8D6E63,#AB47BC,#42A5F5,#26A69A,#D4E157,#FFA726,#BDBDBD"
                   >
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-calendar"></i> <?php echo _('Current month: ') ?><span id="monthname"></span></h3>
            </div>
            <div class="panel-body">
                <div class="timesheet color-scheme-default white" id="monthtimsheet" data-title="#monthname" data-between="2" data-click="changeTime" data-length="month" data-rows="3" 
                    data-colors="#ef5350,#7E57C2,#29B6F6,#66BB6A,#FFEE58,#FF7043,#78909C,#EC407A,#5C6BC0,#26C6DA,#9CCC65,#FFCA28,#8D6E63,#AB47BC,#42A5F5,#26A69A,#D4E157,#FFA726,#BDBDBD"
                >
                   
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-calendar"></i> <?php echo _('Year: ') ?><span id="yearnumber"></span></h3>
            </div>
            <div class="panel-body">
                <div class="timesheet color-scheme-default white" id="yeartimesheet" data-title="#yearnumber" data-between="2" data-click="goToOverviewProject" data-length="year" data-rows="4" 
                    data-colors="#ef5350,#7E57C2,#29B6F6,#66BB6A,#FFEE58,#FF7043,#78909C,#EC407A,#5C6BC0,#26C6DA,#9CCC65,#FFCA28,#8D6E63,#AB47BC,#42A5F5,#26A69A,#D4E157,#FFA726,#BDBDBD"
                >
                    
                </div>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="false" class="modal" tabindex="-1" role="dialog" id="editTimeDialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><?php echo _('Edit Timesheet'); ?></div>
            <div class="modal-body">
                <div class="modal-errormessage"></div>
                <form method="get" class="form-horizontal" role="form" onsubmit="return saveChangeTime();">
                    <input id="time_id" value="" type="hidden">
                    <input id="time_project_id" value="-1" type="hidden">
                    <div class="col-xs-9">
                        <label class="control-label" for="time_date"><?php echo _('Date'); ?></label>
                        <input type="date" class="form-control" data-date-format="yyyy-mm-dd" id="time_date" name="time_date" required="required" value="">    
                    </div>
                    <div class="form-check checkbox-teal col-xs-3">
                        <input type="checkbox" class="form-check-input" id="so2" name="so" value="1">
                        <label class="form-check-label soLabel" for="so2"><?php echo _('s&o'); ?></label>
                    </div>
                    <div class="col-xs-12">
                        <label class="control-label" for="time_timepicker1"><?php echo _('Start time'); ?></label>
                        <div class="input-group bootstrap-timepicker timepicker">
                            <input id="time_timepicker1" type="text" name="starttime" class="form-control input-small timepicker">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label class="control-label" for="time_timepicker2"><?php echo _('End time'); ?></label>
                        <div class="input-group bootstrap-timepicker timepicker">
                            <input id="time_timepicker2" type="text" name="starttime" class="form-control input-small timepicker">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn btn-primary pull-right" name="editTime" id="editTimebutton" value="+"><i class="fa fa-edit"></i> <?php echo _('Save'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="test">

</div>