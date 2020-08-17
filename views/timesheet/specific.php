<?php
$oldValues = array();

?>
<div class="row">
    <div class="bs-callout bs-callout-primary col-lg-5">
        <h4><?php echo _('Timesheets: specific'); ?></h4>
        <?php echo _('Here you can find the specific time sheets.'); ?>
    </div>
</div>
<div class="row" id="specificTimePage">
    <div class="col-md-12">
        <div class="panel panel-danger ">
            <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-clock-o"></i> <?php echo _('What do you wish to retrieve?');?></h3>
            </div>
            <div class="panel-body">
                <form method="post" class="form-horizontal" role="form" onsubmit="return getSpecificStatsUser();">
                    <div class="form-check checkbox-teal col-xs-6 col-md-4">
                        <input type="checkbox" class="form-check-input" id="specific_Project" name="specific_Project">w
                        <label class="form-check-label" for="specific_Project"><?php echo _('get specific project'); ?></label>
                        <div id="projectPicker">
                            <div class="stop-to-large-dropdown">
                                <input type="number" class="form-control bedrijfpersoonpickerLazy hidden" name="project_id" id="project_id" data-id="project" value="<?php if(isset($oldValues['project_id'])){echo $oldValues['project_id'];}else{echo '-1';}?>"/>
                                <input name="project" type="text" class="form-control bedrijfpersoonpickerLazy searchOnKeyUp" data-callback="project" data-method="getProjects" data-target-id="project_id" data-control="api" <?php if(isset($oldValues['project'])){echo 'value="' . $oldValues['project'] . '"';}?> disabled autocomplete="off"/>
                                <div class="seng_standard_dropdown seng_dropdown_searchOnKeyUp" data-for="project">
                                    <div class="info"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-6 col-md-4">
                        <label class="control-label" for="savetime_timedate"><i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="" data-original-title="<?php echo _('choose an date and the system will get the week and month corresponding with this date.'); ?>"></i><?php echo _('Date:');?></label>
                        <input type="date" class="form-control" data-date-format="yyyy-mm" id="savetime_timedate" name="timedate" required="required" value="<?php if(isset($oldValues['timedate'])){echo $oldValues['timedate'];}else{echo '';}?>">
                    </div>
                    <div class="col-xs-offset-3 col-xs-6 col-md-offset-1 col-md-3">
                        <label class="control-label" for="addtimebutton"><br></label>
                        <button type="submit" class="btn btn-primary form-control" name="changeTime" id="addtimebutton" value="view">
                            <i class="fa fa-search"></i> <?php echo _('SEARCH'); ?>
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
                <div class="timesheet color-scheme-default white" id="monthtimsheet" data-title="monthname" data-between="2" data-click="goToOverviewProject" data-length="month" data-rows="3" 
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
                <div class="timesheet color-scheme-default white" id="yeartimesheet" data-title="#yearnumber" data-between="2" data-click="changeTime" data-length="year" data-rows="4" 
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
                    <div class="col-xs-12">
                        <label class="control-label" for="time_date"><?php echo _('Date'); ?></label>
                        <input type="date" class="form-control" data-date-format="yyyy-mm-dd" id="time_date" name="time_date" required="required" value="">    
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