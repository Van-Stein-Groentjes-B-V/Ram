<?php
 /**
 * Dashboard view
 * Main dashboard view. Shows statistics for admins, active projects and todos
 * 
 * PHP version 7+
 *
 * @category   Views
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @editor     Thomas Shamoian <thomas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details. <https://www.gnu.org/licenses/>
 *
 **/
$db = new \SG\Ram\DataHandler();
$user = "";
if(isset($data["gebruiker"])){
    $user = $data["gebruiker"];
}

$projects = array();
if(isset($data['projects'])){
    $projects = $data['projects'];
}

   
$show_stats = false;
if(isset($data['show_stats'])){
    $show_stats = $data['show_stats'];
}
$personID = -1;
if (isset($data['personId'])){
    $personID = $data['personId'];
}
?>
<?php 
    if($user->getLogo() == "no_avatar"){
?>
        <div class="col-md-12 alert alert-warning" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="panel-text">
                We have noticed that you haven't uploaded your picture/Logo yet, upload your logo/picture to complete your instalation.
            </p>
            <a id="addButton" href="<?php echo SITE_ROOT . 'person/edit/' . $personID['id']; ?>" type='button' class='btn btn-primary'>
                <i class="fas fa-upload"></i>
                <?php echo _('Upload picture'); ?>
            </a>
        </div>
<?php 
    }
    
    if($show_stats){ ?>
        <div class="col-md-12" style="padding-left:0px;">
            <div class="panel panel-info ">
                <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-pie-chart"></i> <?php echo _("Statistics")?></h3>
                </div>
                <div class="panel-body" id="MyStats">
                    <div class="animated flipInY col-md-2 col-sm-6 col-xs-6 tile_stats_count">
                        <div class="stats_block noborder">
                            <span class="count_top">Users online</span>
                            <div class="count aero text-center" id="users_online"></div>
                        </div>
                    </div>
                    <div class="animated flipInY col-md-3 col-sm-6 col-xs-6 tile_stats_count">
                        <div class="stats_block">
                            <span class="count_top"><i class="fa fa-clock-o"></i> <?php echo _("Worked time today")?></span>
                            <div class="count aero text-center" id="work_count"></div>
                            <span class="count_bottom" id="work_perc"><i class="green" ><i class="fa fa-sort-asc"></i></i> <?php echo _("From yesterday")?> </span>
                        </div>
                    </div>
                    <div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count">
                        <div class="stats_block">
                            <span class="count_top"><i class="fa fa-files-o"></i> <?php echo _("Projects in progress")?> </span>
                            <div class="count green text-center" id="projects_in_progress"></div>
                        </div>
                    </div>
                    <div class="animated flipInY col-md-3 col-sm-4 col-xs-4 tile_stats_count">
                        <div class="stats_block">
                            <span class="count_top"><i class="fa fa-files-o"></i> <?php echo _("Projects waiting for feedback")?></span>
                            <div class="count aero text-center" id="projects_in_feedback"></div>
                        </div>
                    </div>
                    <div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count">
                        <div class="stats_block">
                            <span class="count_top"><i class="fa fa-files-o"></i> <?php echo _("Projects in test")?></span>
                            <div class="count red text-center" id="projects_in_testing"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }?>
<div class="col-md-4" style="padding-left:0px;"> 
    <div class="panel panel-info ">
        <div class="panel-heading">
            <h3 class="panel-title" id="describingTable"><i class="fa fa-files-o fa-2"></i> <?php echo _('Active projects'); ?></h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped" aria-describedby="describingTable">
                <thead>
                    <tr>
                        <th><?php echo _('Project'); ?></th>
                        <th><?php echo _('Assigned to'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($projects AS $project){ ?>
                        <tr>
                            <td>
                                <a href="<?php echo SITE_ROOT . 'projects/overview/' . $project->getId(); ?>" class="name-entry">
                                    <div class="img-circle fixedimage">
                                        <img alt="Project logo" src="<?php echo SITE_ROOT . 'public/img/projects/' . $project->getImage(); ?>" class="img-responsive img-circle" />
                                    </div>
                                    <div class="projecttext">
                                        <?php echo $project->getParsedString("getName"); ?>
                                    </div>
                                </a>
                            </td>
                            <td>
                                <a href="<?php echo SITE_ROOT . 'person/overview/' . $project->getResponsibleAll()->getId(); ?>" class="projectimg">
                                    <div class="img-circle fixedimage">
                                        <img alt="person logo" src="<?php echo SITE_ROOT . 'public/img/person/logos/' . $project->getResponsibleAll()->getLogo(); ?>" class="img-responsive img-circle" /> 
                                    </div>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-md-8" style="padding-left:0px;">
    <div class="panel panel-info ">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo _('My To-do list'); ?></h3>
        </div>
        <div class="panel-body" id="MyTodos" summary="<?php echo _("Table with active todos")?>">
            <div class="todo-table">

            </div>
        </div>
        <div class="panel-body" id="MyTodosAreLoading" style="display:block;">
            <div class="All-Done">
                <i class="fas fa-spinner fa-spin"></i>
                <span>
                    <?php echo _('Loading'); ?>
                </span>
            </div>
        </div>
        <div class="panel-body" id="MyTodosAreDone" style="display:none;">
            <div class="All-Done">
                <i class="fas fa-grin-beam"></i>
                <span>
                    <?php echo _('All done!'); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" tabindex="-1" role="dialog" id="editTodo" aria-hidden="true"> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><?php echo _('Edit Todo'); ?></div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="errorrow-modal"></div>
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

