<?php
global $user;
if(isset($data['oldData'])){
    $project = $data['oldData'];
}else{
    $project = new Project();
}

$stati = array();
$statiCol = array();
if(isset($data['stati']) && isset($data['statiCol'])){
    $stati = $data['stati'];
    $statiCol = $data['statiCol'];
}

if(isset($data['text'])){
    $statusText = $data['text'];
}else{
    $statusText = 'Unavaillable';
}
if(isset($data['colour'])){
    $classnameStatus = $data['colour'];
}else{
    $classnameStatus = 'warning';
}
if(isset($data['coupled_persons'])){
    $coupled_persons = $data['coupled_persons'];
}else{
    $coupled_persons = array();
}
if(isset($data['attachments'])){
    $attachments = $data['attachments'];
}else{
    $attachments = array();
}
if(isset($data['tickets'])){
    $tickets = $data['tickets'];
}else{
    $tickets = array();
}
if(isset($data['worked_time'])){
    $workedTime = $data['worked_time'];
}else{
    $workedTime = 0;
}
$settings = array();
if(isset($data['settings'])){
    $settings = $data['settings'];
}

$showProjectLinks = (isset($settings['projectDevLink']) && $settings['projectDevLink']) || 
        (isset($settings['projectAndroidLink']) && $settings['projectAndroidLink']) || 
        (isset($settings['projectiOSLink']) && $settings['projectiOSLink']) || 
        (isset($settings['projectResposiLink']) && $settings['projectResposiLink']) || 
        (isset($settings['projectSlug']) && $settings['projectSlug']);


if(isset($data['userinfo'])){
    $userinfo = $data['userinfo'];
}else{
    $userinfo = 0;
}

$project_id = $project->getId();
?>
<div class="projectOverview">
</div>
<div class="row">
    <a href="<?php echo SITE_ROOT . 'projects' . '/'; ?>" type='button' class='btn btn-primary pull-right'><i class="fas fa-chevron-left"></i> <?php echo _('Go back'); ?></a>
    <div class="bs-callout bs-calloutProject bs-callout-default col-lg-6">
        <h4><?php echo _('Project overview'); ?></h4>
        <?php echo _('Here you will find a complete overview of a specific project.</br>
            You can add Todo tasks, send a ticket to the complete project team, or chat with other team members.</br>
            While you are on this page, you can log the time to indicate you work on this project.'); ?>
    </div>
    <?php if ($user->isAdmin()){ ?>
    <div class="statusDiv pointer"  onclick="javascript:$('#changeStatus').modal('show');">
    <?php }else{ ?>
    <div class="statusDiv">
    <?php } ?>
        <h4><?php echo _('Project status'); ?></h4>
        <div class="circleOfStatus alert alert-<?php echo $classnameStatus; ?>"></div>
        <p class="statusText">
            <?php echo $statusText; ?>
        </p>
    </div>
</div>
<div class="col-md-8">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $project->getParsedString('getName'); ?></h3>
        </div>
        <div class="panel-body projectPanelBody">
            <div class="clearfix"></div>
            <div class="overzicht">
                <div class="projects_company_input col-md-12 projectPart">
                    <p class="customerinformation">Customer information</p>
                    <div class="col-md-12 pull-left projectPicture"> <img alt="Company logo" src="<?php echo SITE_ROOT;?>img/projects/<?php echo $project->getParsedString('getImage'); ?> " class="img-responsive" style="width:128px;"> </div>
                    <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-solid.png" alt="customer icon"> <?php echo _('Customer:'); ?></div>
                    <div class="col-md-8  labelString">
                        <a href="<?php echo SITE_ROOT . 'companies/overzicht/' . $project->getAllCompany()->getId(); ?>"><?php echo $project->getAllCompany()->getParsedString('getName'); ?></a>
                    </div>
                </div>
            </div>
            <?php if(!$user->isCustomer()){?>
                <div class="col-md-12 projectPart" id="projectBudget">
                    <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/file-invoice-dollar-solid.png" alt="budget icon"> <?php echo _('Budget:'); ?></div>
                    <div class="col-md-8  labelString"><?php echo $project->getParsedString('getBudget');?></div>
                </div>
            <?php } ?>
            <div class="clearfix"></div>
            <hr class="fieldSeparators" />
            <p class="projectinformation">Project information</p>
            <div class="projects_contractor_name_input col-md-12 projectPart"  id="projectDropdownContractor">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="contractor icon"> <?php echo _('Contractor:'); ?></div>
                    <div class="col-md-8  labelString">
                        <a href="<?php echo SITE_ROOT . 'companies/overzicht/' . $project->getContractorAll()->getId(); ?>"><?php echo $project->getContractorAll()->getParsedString('getName'); ?></a>
                    </div>
            </div>
            <div class="clearfix"></div>
            <div class="projects_deadline_input col-md-12 projectPart">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/calendar-day-solid.png" alt="deadline icon"> <?php echo _('Deadline:'); ?></div>
                <div class="col-md-8 deadline  labelString"><?php echo $project->getParsedString('getDeadlineDmy') ?></div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 projectPart">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/clock-solid.png" alt="last status change icon"> <?php echo _('Last status change:'); ?></div>
                <div class="col-md-8  labelString"><?php echo $project->getParsedString('getLastchangedDmy') ?></div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 projectPart">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/clock-solid.png" alt="hours worked icon"> <?php echo _('Hours worked:'); ?></div>
                <div class="col-md-8  labelString"><?php echo $workedTime ?></div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 projectPart">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/file-invoice-dollar-solid.png" alt="hourly wage icon"> <?php echo _('Hourly Wage:'); ?></div>
                <div class="col-md-8  labelString"><?php echo $project->getParsedString('getHourlywage') ?></div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 projectPart">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/clock-solid.png" alt="SO hourly icon"> <?php echo _('SO Hourly:'); ?></div>
                <div class="col-md-8  labelString"><?php echo $project->getParsedString('getSOHourly') ?></div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 projectPart">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/pen-solid.png" alt="SO Description icon"> <?php echo _('SO Description:'); ?></div>
                <div class="col-md-8  labelString"><?php echo $project->getParsedString('getSODescription') ?></div>
            </div>
            <div class="clearfix"></div>
            <div class="projects_deadline_input col-md-12 projectPart" id="projectCmcDropdown">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="user icon"> <?php echo _('Customer Main Contact:'); ?></div>
                <div class="col-md-8 deadline  labelString"><?php echo $project->getParsedString('getContractorMainContactName') ?></div>
            </div>
            
            <div class="clearfix"></div>
            <div class="col-md-12 projectPart" id="projectCrmcDropdown">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="contractor main contact icon"> <?php echo _('Contractor Main Contact:'); ?></div>
                <div class="col-md-8  labelString"><?php echo $project->getParsedString('getContractorMainContactName') ?></div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 projectPart" id="projectInterDropdown">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="intermediate icon"> <?php echo _('Intermediate:'); ?></div>
                <div class="col-md-8  labelString"><?php echo $project->getParsedString('getIntermediateName'); ?></div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 projectPart" id="projectMainResponDropdown">
                <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="main resposiblity icon"> <?php echo _('Main resposiblity of:'); ?></div>
                <div class="col-md-8  labelString"><?php echo $project->getParsedString('getResponsibleName') ?></div>
            </div>
            <div class="clearfix"></div>
            <?php if($project->getDescription()){?>
                <div class="projects_description_input col-md-12 projectPart">
                    <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/pen-solid.png" alt="description icon"> <?php echo _('Description:'); ?></div>
                    <div class="col-md-8  labelString"><article><?php echo $project->getDescription();?></article></div>
                </div>
            <?php } ?>
            <div class="clearfix"></div>
            <div class="<?php if(!$showProjectLinks){echo "hidden";}?>">
                <hr class="fieldSeparators" />
                <p class="projectinformation">Project links</p>
                <div class="clearfix"></div>
                <div class="col-md-12 projectPart" id="projectDevLink">
                    <!-- Rare plek voor de input met project_id-->
                    <input type="number" class="form-control hidden" name="project_id" id="project_id" value="<?php echo $project->getId(); ?>"/>
                    <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/link-solid.png" alt="test page icon"> <?php echo _('Test page:'); ?></div>
                    <div class="col-md-8  labelString"><?php echo $project->getParsedString('getDev_link') ?></div>
                </div>
                <div class="clearfix"></div>
                <div class="projects_repository_input col-md-12 projectPart" id="projectAndroidLink">
                    <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/link-solid.png" alt="android link icon"> <?php echo _('Android link:'); ?></div>
                    <div class="col-md-8  labelString">
                        <a href="<?php echo $project->getParsedString('getAndroidLink') ?>"><?php echo $project->getParsedString('getAndroidLink') ?></a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="projects_repository_input col-md-12 projectPart" id="projectiOSLink">
                    <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/link-solid.png" alt="ios link icon"> <?php echo _('IOS link:'); ?></div>
                    <div class="col-md-8  labelString">
                        <a href="<?php echo $project->getParsedString('getIosLink') ?>"><?php echo $project->getParsedString('getIosLink') ?></a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="projects_repository_input col-md-12 projectPart" id="projectResposiLink">
                    <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/link-solid.png" alt="repo link icon"> <?php echo _('Repository link:'); ?></div>
                    <div class="col-md-8  labelString">
                        <a href="<?php echo $project->getParsedString('getRepository') ?>"><?php echo $project->getParsedString('getRepository') ?></a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="projects_repository_input col-md-12 projectPart" id="projectSlug">
                    <div class="col-md-4 labelProject"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/globe-europe-solid.png" alt="test page icon"> <?php echo _('Test page:'); ?></div>
                    <div class="col-md-8 labelString">
                        <a href="<?php echo $project->getParsedString('getDevLink') ?>"><?php echo $project->getParsedString('getDevLink') ?></a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php if($user->isAdmin()){?>
        <hr class="fieldSeparators" />
        <div class="">
            <a href="projects/edit/<?php echo $project->getId(); ?>" data-original-title="Edit this project" data-toggle="tooltip" class="btn btn-primary btnProject">EDIT PROJECT</a>
        </div>
    <?php }?>
</div>
<div class="col-md-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-users"></i> <?php echo _('Project team'); ?></h3>
        </div>
        <div class="panel-body overzicht">
            <div class="row header-row">
                <p><b><?php echo _('Name'); ?></b></p>
            </div>
            <div id="teamofproject">
                <?php foreach($coupled_persons AS $single){ ?>
                    <div class="row" data-id="<?php echo $single->getId(); ?>">
                        <p class="teamMemberName col-md-4">
                            <?php echo $single->getParsedString('getName'); ?>
                        </p>  
                        <?php if($user->isAdmin()){?>
                            <div id="deleteProjectMember" class="btn btn-xs btn-danger fadebuttons fadebuttonsPTeam" data-callback="removeTeamProjectMember" onClick="callDelete(this)" data-extra="<?php echo $project_id;?>" data-target-id="<?php echo $single->getId(); ?>" data-target-string="coupled_person" data-confirm="<?php echo _('Are you sure to delete this person?'); ?>">
                                <i class="fa fa-trash"></i>
                            </div>
                        <?php }?>
                    </div>
                <?php } ?>
            </div>
            <?php if($user->isAdmin()){?>
                <div id="searchforteammembers">
                    <div class="form-group input-group">
                        <input name="project_person" type="text" class="form-control searchOnKeyUp" data-function="addToTeam" data-callback="addToTeam_id" data-method="getContactPersonAll" data-target-id="addToTeam_id" data-control="api" value="" placeholder="<?php echo _('add person to project');?>" autocomplete="off"/>
                        <input type="number" class="form-control hidden" name="addToTeam_id" id="addToTeam_id" data-id="addToTeam_id" value="-1"/>
                        <div class="input-group-addon" onClick="javascript:addToList()">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-paperclip"></i> <?php echo _('Attachments'); ?></h3>
        </div>
        <div class="panel-body overzicht">
            <div class="row header-row">
                 <?php if($attachments != null){?>
                <div class="half-by-half">
                    <b><?php echo _('By'); ?></b> 
                </div>
                <div class="half-by-half">
                    <b><?php echo _('File'); ?></b>
                </div>
                 <?php } else {?>
                <div class="">
                    <p>No attachments yet. To upload an attachment use the input below.</p>
                </div>
                 <?php }?>
            </div>
            <div id="attachmentsProjects">
                <?php foreach($attachments AS $attachment){ ?>
                    <div class="row" data-id="<?php echo $attachment->getId(); ?>">
                        <div class="half-by-half" >
                            <?php echo $attachment->getParsedString('getNamePerson'); ?>
                        </div>
                        <div class="half-by-half">
                            <a href="<?php echo SITE_ROOT . "projects/downloadpdf/" . $project->getId() . "/" . $attachment->getId()  ?>"><i class="fa fa-<?php echo $attachment->getTypeClass(); ?>"></i> <?php echo $attachment->getParsedString('getFilename'); ?></a>
                        </div>
                        <div class="half-by-half-del">
                            <div class="btn btn-xs btn-danger fadebuttons" data-callback="removeattachmentsProjects" onClick="callDelete(this)" data-target-id="<?php echo $attachment->getId(); ?>" data-target-string="attachment" data-confirm="<?php echo _('Are you sure to delete this attachment?'); ?>">
                                <i class="fa fa-trash"></i>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="input-group" id="addAttachmentToProject">
                <label class="input-group-btn">
                    <div class="btn btn-primary">
                        Browse&hellip; <input id="upload" name="upload" type="file"  style="display: none;">
                    </div>
                </label>
                <input type="text" class="form-control" readonly>
                <div class="input-group-addon" onClick="javascript:uploadAttachment()">
                    <i class="fas fa-plus"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12"> 
    <div class="panel panel-info ">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-ticket-alt"></i> <?php echo _('Tickets'); ?></h3>
        </div>
        <div class="panel-body overzicht">
            <div class="row header-row">
                <div class="quarter-by-quarter">
                    <b><?php echo _('Date'); ?></b> 
                </div>
                <div class="quarter-by-quarter">
                    <b><?php echo _('From'); ?></b> 
                </div>
                <div class="quarter-by-quarter">
                    <b><?php echo _('subject'); ?></b> 
                </div>
                <div class="quarter-by-quarter del">
                    
                </div>
            </div>
            <div id="ticketsoffproject">
                <?php foreach($tickets AS $ticket){ ?>
                    <?php if($userinfo['account_id'] == $ticket->getParsedString('getFromId') || !$user->isCustomer()){?>
                        <div class="row" data-id="<?php echo $ticket->getId(); ?>">
                            <div class="quarter-by-quarter">
                                <?php echo $ticket->getSendDmy(); ?>
                            </div>
                                <div class="quarter-by-quarter">
                                    <?php echo $ticket->getParsedString('getFromName'); ?>
                                </div>
                            <div class="quarter-by-quarter">
                                <a data-toggle="collapse" href="#ticket<?php echo $ticket->getId(); ?>" aria-expanded="false" aria-controls="ticket<?php echo $ticket->getId(); ?>" class=""><?php echo $ticket->getParsedString('getSubject'); ?></a>
                            </div>
                            <div class="quarter-by-quarter del">
                            <?php if($user->isAdmin() || $user->isCustomer()){ ?>
                                <div class="btn btn-danger fadebuttons" data-callback="removeticketsProjects" onClick="callDelete(this)" data-target-id="<?php echo $ticket->getId(); ?>" data-target-string="ticket" data-confirm="<?php echo _('Are you sure to delete this ticket?'); ?>">
                                    <i class="fa fa-trash"></i>
                                </div>
                            <?php } ?>
                            </div>
                            <div class="collapse" id="ticket<?php echo $ticket->getId(); ?>" aria-expanded="false"><?php echo $ticket->getParsedString('getMessage'); ?></div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div id="sendTicketsToApi">
                <div class="form-group input-group col-md-12">
                    <input class="form-control" name="subject" id="subjectTicket" type="text" placeholder="<?php echo _('Subject'); ?>"/>
                </div>
                <div class="form-group input-group col-md-12">
                    <textarea name="message" class="form-control" id="messageTicket"></textarea>
                </div>
                <div class="button-row">
                    <div class="btn btn-primary pull-right" onClick="sendTicket()"><?php echo _('Send ticket'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(!$user->isCustomer()){?>
<div class="col-md-12"> 
    <div class="panel panel-default ">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-tasks"></i> <?php echo _('Add Tasks'); ?></h3>
        </div>
        <div class="panel-body">
            <div id="addtodo_form">
                <div class="row" >
                    <div class="col-sm-12">
                        <input name="message" id="todo_message" type="text" class="form-control" placeholder="<?php echo _('Todo...'); ?>">
                    </div>
                </div>
                <div class="row" >
                    <div class="col-sm-3">
                        <select class="form-control" id="todo_prio" name="prio">
                            <option value="" disabled selected hidden><?php echo _('Priority'); ?></option>
                            <option value="0"><?php echo _('Low'); ?></option>
                            <option value="1"><?php echo _('Medium'); ?></option>
                            <option value="2"><?php echo _('High'); ?></option>
                         </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" id="todo_user_id" name="user_id">
                            <option value="-1"><?php echo _('Unassigned'); ?></option>
                            <?php foreach($coupled_persons AS $person){ ?>
                                <option value="<?php echo $person->getAccountId(); ?>"><?php echo $person->getParsedString('getName'); ?></option>
                            <?php } ?>
                         </select>
                    </div>
                    <div class="col-sm-3">
                        <input name="deadline" id="todo_deadline" type="date" class="form-control" data-date-format="<?php echo _('dd-mm-yyyy'); ?>" placeholder="<?php echo _('deadline'); ?>">
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-primary btnaddTodo" onClick="addTodoToProject()" id="addTodo" value="+" >
                                <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-4"> 
    <div class="panel panel-warning ">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="far fa-square"></i> <?php echo _('Todo'); ?></h3>
        </div>
        <div class="panel-body no-side-padding todo-panel">
            <div id="ScrumTodoPassive">
                <div class="todo-table">

                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-4"> 
    <div class="panel panel-info ">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-spinner fa-pulse"></i> <?php echo _('In progress'); ?></h3>
        </div>
        <div class="panel-body no-side-padding todo-panel">
            <div id="ScrumTodoActive">
                <div class="todo-table">

                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-4"> 
    <div class="panel panel-success ">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="far fa-check-square"></i> <?php echo _('Done'); ?></h3>
        </div>
        <div class="panel-body no-side-padding todo-panel">
            <div id="ScrumTodoDone">
                <div class="todo-table">

                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" tabindex="-1" role="dialog" id="editTodo" aria-hidden="true"> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><?php echo _('Edit Todo'); ?></div>
            <div class="modal-body">
                <div class="errorrow-modal"></div>
                <div class="col-sm-12">
                    <input id="todo_id_todo" type="hidden" value="-1"/>
                    <input id="project_id_todo" type="hidden" value="-1"/>
                    <input id="message_todo" type="text" class="form-control" value="">
                </div>
                <div class="col-sm-4">
                    <select class="form-control" id="prio_todo">
                        <option value="0"><?php echo _('Low'); ?></option>
                        <option value="1"><?php echo _('Medium'); ?></option>
                        <option value="2"><?php echo _('High');?></option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <select class="form-control" id="user_id_todo">
                        <option class="standard" value="-1"><?php echo _('Unassinged'); ?></option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <input id="deadline_todo" type="date" class="form-control" data-date-format="yyyy-mm-dd" value="">
                </div>
                <div class="col-sm-12">
                    <button type="button" class="btn btn-danger pull-left" onclick="removeTodoFromMain('Are you sure you wish to delete this?')" value="-">
                        <i class="fa fa-trash"></i> <?php echo _('Delete');?>
                    </button>
                    <button type="submit" class="btn btn-primary" onclick="upateTodoFromMain()" value="+">
                        <i class="fa fa-edit"></i> <?php echo _('Save');?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($user->isAdmin()){ ?>
<div class="modal fade in" tabindex="-1" role="dialog" id="changeStatus" aria-hidden="true"> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><?php echo _('Change status'); ?></div>
            <div class="modal-body">
                <div class="errorrow-modal"></div>
                <div class="col-sm-12">
                    <select class="form-control selectpicker"  id="changestatuspicker" data-container="body" data-size="4">
                        <?php 
                        foreach($stati AS $key=>$singleStatus){ 
                            if(isset($statiCol[$key])){
                                $selected = "";
                                if(intval($project->getProjectStatus()) === $key){
                                    $selected = "selected";
                                }
                        ?>
                        <option value="<?php echo $key;?>" data-content="<div class='colorholderpicker alert-<?php echo $statiCol[$key];?>'></div><div class='statinamepicker'><?php echo $singleStatus; ?></div>" <?php echo $selected; ?>><?php echo $singleStatus; ?></option>
                        <?php 
                            } 
                        } 
                        ?>
                    </select>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary" onclick="changeStatusProjectOverview()" value="+">
                        <i class="fa fa-edit"></i> <?php echo _('Save');?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div class="seng_standard_dropdown seng_dropdown_searchOnKeyUp seng_dropdown_calc_pos" data-for="addToTeam_id">
    <div class="info"></div>
</div>

<?php } ?>