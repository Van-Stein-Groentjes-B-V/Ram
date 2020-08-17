<?php

/**
 * Projects Controller.
 *
 * PHP version 7+
 *
 * @category   Controllers
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 * @uses       \SG\Ram\Controller                   Extend the main controller.
 * @uses       \SG\Ram\dataHandler                  Data handler class.
 * @uses       \SG\Ram\functions                    General functions class.
 * @uses       \SG\Ram\Models\Dbhelper              Database helper class.
 * @uses       \SG\Ram\Models\Persons               The person object.
 * @uses       \SG\Ram\Models\Company               The company object.
 * @uses       \SG\Ram\Controllers\UtilController   General utilities class.
 * @uses       \SG\Ram\Models\Ticket                Ticket object.
 * @uses       \SG\Ram\Models\Attachment            Attachment object.
 * @uses       DateTime                             PHP DateTime functions.
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
 */

namespace SG\Ram\Controllers;

use SG\Ram\Controller;
use SG\Ram\dataHandler;
use SG\Ram\Models\Dbhelper;
use SG\Ram\Models\Person;
use SG\Ram\Models\Company;
use SG\Ram\Models\Project;
use SG\Ram\Controllers\UtilController;
use SG\Ram\Models\Ticket;
use SG\Ram\Models\Attachment;
use DateTime;

/**
 * Projects Controller.
 * To Add, edit, delete companies or to add new functions for the Projects page
 * @category   Controllers
 * @package    Ram
 */
class ProjectsController extends Controller
{
    private $_db;
    private $_user = "";
    private $_breadcrumb;
    private $_projectStati;
    private $_projectStatiClass;
    private $_imagenewname;
    
    /**
     * Constructor: __construct.
     * Initialization of all the objects in that are needed
     * handles the success message.
     * @return Void.
     */
    public function __construct() {
        parent::__construct();
        global $user;
        $this->_user = $user->getUser();
        $this->_db = new Dbhelper();
        $this->_dataHandler = new dataHandler();
        $this->_breadcrumb = new UtilController();
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_breadcrumb);
        unset($this->_dataHandler);
        unset($this->_db); //dbhelper
        unset($this->user);
    }

    /**
     * Assigns CSS and JS files from the settings.
     * Loads the view content with the JS and CSS files that are included in the Projects page.
     * @return Void.
     */
    public function index() {
        $this->handleMessages();
        $this->Assign('settings', $this->_fun->getJsonFromFile('projects_css', 'settings', 'settings'));
        $this->_db->setSql("SELECT count(`id`) AS `count` FROM `projects` WHERE `deleted` = 0");
        $count = $this->_db->getRow();
        $this->Assign("hasProjects", $count['count']);
        $this->LoadViewer(
            "projects",
            "index",
            "projects",
            array("bootstrap-table.min", "bootstrap-table-filter-control.min", "dashboard", "settings_invisible","sg_confirm"),
            array("view_settings", "bootstrap-table.min", "bootstrap-table-export.min", "bootstrap-table-cookie.min", "tableExport.min", "backend","sg_confirm"),
            true
        );
    }
    
    /**
     * Loads JSON data in to the bootstrap data table.
     * @return Void.
     */
    public function json() {
        $showArray = $this->getShowArray();
        $this->fillStati();
        $all = array();
        $results = $this->parseJSONRequestForCall($this->_user->isCustomer());
        $count = $results["count"];
        $resultarray = $results['all'];
        $companyName = array();
        $personName = array();
        foreach ($resultarray as $row) {
            $toReturnRow = $this->handleDataJsonFormat($showArray, $row, $companyName, $personName);
            if ($this->_user->isAdmin()) {
                $toReturnRow['edit'] = "<a href='" . SITE_ROOT . "projects/edit/" . $row['id'] . "' alt='edit project' class='edit-delete-button-table'>" .
                                        "<i class='fas fa-pencil-alt'></i>" .
                                "</a>";
                $toReturnRow['edit'] .= "<a class='delete remove_this edit-delete-button-table'  alt='delete project' data-confirm='" . _('Are you sure you want to delete this project?') .
                "' data-target-id='" . $row['id'] . "' data-target-string='project' data-callback='callbackRemove'><i class='fas fa-trash'></i></a>";
                $toReturnRow['project_status'] = "<span class=\"badge alert-" . $this->_projectStatiClass[$row['project_status']] . "\">" . $this->_projectStati[$row['project_status']] .
                "</span>";
            }
            $all[] = $toReturnRow;
        }
        
        echo '{"total":' , $count , ',' , '"rows":' , json_encode($all) , '}';
    }
    
    /**
     * Handle the date for the json call;
     * @param Boolean $show  Whether to show it.
     * @param Array   $row   The entire row with the info.
     * @param String  $index The string representing the index in the row.
     * @return string
     */
    private function handleDateJsonValues($show, $row, $index) {
        if ($show && $row[$index] && strlen($row[$index]) > 1) {
            $temp = new dateTime($row[$index]);
            return $temp->format('d-m-Y');
        }
        return '-';
    }
    
    /**
     * Handle setting and, if neccessary, getting of the names from other table.
     * @param Boolean $show               Whether to show it.
     * @param Array   $row                The entire row with the info.
     * @param Array   $arrayAlreadyGotten Array with already gotten variables.
     * @param String  $index              The string representing the index in the row.
     * @param String  $table              From which table the value must be gotten.
     * @param String  $column             From which column the values should be gotten
     * @return string
     */
    private function handleNameFromOtherTable($show, $row, &$arrayAlreadyGotten, $index, $table, $column = 'name') {
        if ($show && $row[$index] > 0) {
            if (isset($arrayAlreadyGotten[$row[$index]])) {
                return $arrayAlreadyGotten[$row[$index]];
            } else {
                $val = $this->_dataHandler->getDataFromRow($table, $row[$index], true, array($column));
                if ($val) {
                    $arrayAlreadyGotten[$row[$index]] = $val[$column];
                    return $val[$column];
                }
            }
        }
        return '-';
    }
    
    /**
     * Morph the row to the value you want in it.
     * @param Array $showArray    Which columns should be shown (if not set, default Yes)
     * @param Array $row          The standard row with information.
     * @param Array $companyNames The array containing already gotten company names on index of their id.
     * @param Array $personNames  The array containing already gotten person names on index of their id.
     * @return Array
     */
    private function handleDataJsonFormat($showArray, $row, &$companyNames, &$personNames) {
        $toReturnArray = array("id" => $row['id']);
        if (!$row['image'] || !file_exists(ROOT .  DS . "public" . DS . "img" . DS . "projects" . DS . $row['image'])) {
            $row['image'] = "no_avatar.jpg";
        }
        $toReturnArray['image'] = "<img class='small-logo-company' src='" . SITE_ROOT . "public/img/projects/" . $row['image'] . "' alt='project image'/>";
        $toReturnArray['name'] = $row['name'];
        $toReturnArray['deadline'] = $this->handleDateJsonValues($showArray['deadline'], $row, 'deadline');
        $toReturnArray['contractor'] = $this->handleNameFromOtherTable($showArray['contracter'], $row, $companyNames, 'contractor_id', 'companies');
        $toReturnArray['contractermain'] = $this->handleNameFromOtherTable($showArray['contractermain'], $row, $personNames, 'contractor_main_contact_id', 'persons');
        $toReturnArray['responsible'] = $this->handleNameFromOtherTable($showArray['mainresponsible'], $row, $personNames, 'responsible', 'persons');
        return $toReturnArray;
    }
    
    /**
     * Adds a Project.
     * @return (function)   Header with success message.
     */
    public function add() {
        $this->handleMessages();
        $this->fillStati();
        if (isset($_POST['add'])) {
            if (isset($_POST['name']) && $this->_dataHandler->checkForDouble("projects", "name", filter_var($_POST['name'], FILTER_SANITIZE_STRING))) {
                $this->Assign("errormessage", "Sorry, the name that you filled in is already in use.");
            } else {
                $returnvalues = $this->checkAndSaveData($_POST);
                if ($returnvalues === true) {
                    header("refresh:0; url=" . SITE_ROOT . 'projects/index/?successmessage=' . urlencode("Project successfully added"));
                    return;
                }
                if (is_array($returnvalues)) {
                    $this->Assign("errors", $returnvalues['errors']);
                    $this->Assign("errormessage", $returnvalues['errormessage']);
                } else {
                    $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$somethingWentWrong));
                }
            }
            $this->Assign("oldData", $_POST);
        }

        $this->Assign("companies", $this->getCompanies());
        $this->Assign("persons", $this->getPersons());
        $this->Assign("stati", $this->_projectStati);
        $this->Assign('settings', $this->_fun->getJsonFromFile('projects_css', 'settings', 'settings'));
        $this->LoadViewer(
            "projects",
            "edit_add",
            "projects",
            array("dashboard", "settings_invisible",  "bootstrap-select.min", "date-picker", "formlayout"),
            array("tinymce.min", "view_settings", "backend", "bootstrap-select.min", "customTinymce","bootstrap-datepicker", "datePickerCustom"),
            true
        );
    }
    
    /**
     * Edit the project information.
     * @param   Integer $id Contains company id.
     * @return  Void            LoadViewer | header  With success message.
     */
    public function edit($id) {
        $this->handleMessages();
        if (is_numeric($id) && $id > 0) {
            $this->fillStati();
            $data = $_POST;
            if (!$this->_dataHandler->checkIfProjectUserIsAllowed($id, $this->_user)) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed));
                return $this->index();
            }
            if (isset($data['add'])) {
                if (isset($_POST['name'])) {
                    $checkVariable = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
                    $checkPoint = $this->_dataHandler->checkForDouble("projects", "name", $checkVariable, $id);
                    if ($checkPoint) {
                         header("refresh:0; url=" . SITE_ROOT . 'projects/edit/' . $id . "/?errormessage=" . urlencode(_('Sorry, the name that you filled in is already in use.')));
                         return;
                    }
                }
                $returnvalues = $this->checkAndSaveData($data);
                if ($returnvalues === true) {
                    header("refresh:0; url=" . SITE_ROOT . 'projects/overview/' . $id . "/?successmessage=" . urlencode(_("Project successfully edited")));
                    return;
                } elseif (is_array($returnvalues)) {
                    $this->Assign("errors", $returnvalues['errors']);
                    $this->Assign("errormessage", $returnvalues['errormessage']);
                } else {
                    $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$somethingWentWrong));
                }
                $data['image'] = $this->getImagename($id);
                $this->Assign("oldData", $data);
            } else {
                if (!$this->getDataProjects($id)) {
                    $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
                    $this->index();
                    return;
                }
            }
            
            $this->Assign("edit", true);
            $this->Assign("stati", $this->_projectStati);
            $this->Assign("companies", $this->getCompanies());
            $this->Assign("persons", $this->getPersons());
            $this->Assign("pName", $this->personMainContactId($id));
            $this->Assign("rName", $this->responsibleName($id));
            $this->Assign("cName", $this->contractorId($id));
            $this->Assign("iName", $this->intermediate($id));
            $this->Assign('settings', $this->_fun->getJsonFromFile('projects_css', 'settings', 'settings'));
            $this->LoadViewer(
                "projects",
                "edit_add",
                "projects",
                array("dashboard", "settings_invisible",  "bootstrap-select.min", "date-picker", "formlayout"),
                array("tinymce.min", "view_settings","customTinymce", "backend", "bootstrap-select.min", "bootstrap-datepicker", "datePickerCustom"),
                true
            );
            return;
        }
        $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
        $this->index();
    }
    
    /**
     * Loads information based on the id.
     * @param   Integer $id Contains id.
     * @return  Void        LoadViewer | loads index page.
     */
    public function overview($id) {
        if (is_numeric($id) && $id > 0) {
            $this->fillStati();
            if (!$this->getDataProjects($id, true, true)) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
                return $this->index();
            }
            if (!$this->_dataHandler->checkIfProjectUserIsAllowed($id, $this->_user)) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed));
                return $this->index();
            }
            
            // Get information based on the given $id
            $this->_db->setSql("SELECT `id`, `responsible` FROM `projects` WHERE `id` = ?");
            $result = $this->_db->getRow(array($id));
            // Check if mainresponsible is not already connected to the person_project
            $this->_db->setSql("SELECT `person_id`, `project_id` FROM `person_project` WHERE `person_id` = ? AND `project_id` = ?");
            $checkOnExisting = $this->_db->getRow(array($result['responsible'],$result['id']));
            if (!$checkOnExisting && $result['responsible'] > 0) {
                $this->_db->setSql("INSERT INTO `person_project` (`person_id`, `project_id`) VALUES(?, ?)");
                $this->_db->updateRecord(array($result['responsible'], $result['id']));
            }
            
            $this->Assign("userinfo", $this->userInfo());
            $this->setAdditionalInfo($id);
            $this->Assign("stati", $this->_projectStati);
            $this->Assign("statiCol", $this->_projectStatiClass);
            $this->Assign('settings', $this->_fun->getJsonFromFile('projects_css', 'settings', 'settings'));
            $this->setMessagesToViewed($id);
            return $this->LoadViewer(
                "projects",
                "overzicht",
                "projects",
                array("dashboard", 'jquery-ui.min', "settings_invisible", "formlayout", "bootstrap-select.min", "sg_confirm"),
                array("backend","tinymce.min", 'jquery-ui.min', 'project', "view_settings","sg_confirm", "bootstrap-select.min", "readmore.min"),
                true
            );
        }
        $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
        $this->index();
    }
    
    /**
     * Set all the stati of the tickets to viewed if status was new.
     * @param integer $projectId The id of the project viewed.
     * @return Boolean
     */
    private function setMessagesToViewed($projectId) {
        $this->_db->setSql("UPDATE `tickets` SET `status` = 1 WHERE `status` = 0 && `deleted` = 0 && `project_id` = ?");
        return $this->_db->updateRecord(array($projectId));
    }
    
    /**
     * Set visibility of the inputs.
     * @return Void     LoadViewer | loads index page.
     */
    public function setVisibility() {
        if (!$this->_user->isSuperAdmin()) {
            return $this->index();
        }
        $this->Assign('settings', $this->_fun->getJsonFromFile('projects_css', 'settings', 'settings'));
        return $this->LoadViewer(
            "projects",
            "visibility",
            "projects",
            array("dashboard", "jquery-ui.min","settings_invisible","formlayout","visibilityStyling"),
            array("backend", "jquery-ui.min", 'project',"view_settings", 'tinymce.min'),
            true
        );
    }
    
    /**
     * Get the data from a single project.
     * @param       Integer $id      Id from target project.
     * @param       Boolean $asModel Set it as Model.
     * @param       Boolean $all     All data or not all data
     * @return      Boolean             True on success
     */
    private function getDataProjects($id, $asModel = false, $all = false) {
        $this->_db->setSql("SELECT * FROM `projects` WHERE `id` = ?");
        $project = $this->_db->getRow(array($id));
        if ($project) {
            if ($project['deleted'] === 1) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$compamyDeleted));
            }
            if ($asModel) {
                $project = $this->setNamesFromIds($project, $all);
                $this->Assign("oldData", new Project($project));
            } else {
                $project = $this->setNamesFromIds($project, $all);
                $this->_fun->extendArrayWithRandomNumber($project);
                $this->Assign("oldData", $project);
            }
            if (isset($this->_projectStati[$project['project_status']])) {
                $this->Assign("text", $this->_projectStati[$project['project_status']]);
                $this->Assign("colour", $this->_projectStatiClass[$project['project_status']]);
            }
            return true;
        }
        return false;
    }
    
    /**
     * Set names based on the id.
     * @param       Array   $all        An array with information
     * @param       Boolean $returnFull True || false.
     * @return      Company_Model | Person_Model
     */
    private function setNamesFromIds($all, $returnFull = false) {
        $array = array("person_main_contact_id" => "persons", "company_id" => "companies", "contractor_id" => "companies", "intermediate_id" => "companies",
        "contractor_main_contact_id" => "persons", "responsible" => "persons");
        foreach ($array as $key => $val) {
            if (isset($all[$key]) && $all[$key] > 0) {
                if ($returnFull) {
                    $this->_db->setSql("SELECT * FROM `" . $val . "` WHERE `id` = ?");
                } else {
                    $this->_db->setSql("SELECT `name`, `logo` FROM `" . $val . "` WHERE `id` = ?");
                }
                $result = $this->_db->getRow(array($all[$key]));
                if ($result) {
                    $newname = str_replace('_id', '', $key) . "_name";
                    $all[$newname] = $result['name'];
                    if ($returnFull) {
                        $newname2 = str_replace('_id', '', $key) . "_all";
                        if ($val === 'persons') {
                            $all[$newname2] = new Person($result);
                        } else {
                            $all[$newname2] = new Company($result);
                        }
                    }
                }
            }
        }
        return $all;
    }
    
    /**
     * Fill the stati.
     * @return void.
     */
    private function fillStati() {
        $this->_db->setSql("SELECT `titel`, `type`, `extensie` FROM `project_status`");
        $all = $this->_db->getRows();
        foreach ($all as $val) {
            $this->_projectStati[$val['type']] = $val['titel'];
            $this->_projectStatiClass[$val['type']] = $val['extensie'];
        }
    }
    
    /**
     * Checks the data with key value.
     * @param   Array $data An array with key => value.
     * @return  Boolean     True on success, false otherwise
     */
    private function checkAndSaveData($data) {
        $this->_fun->filterDataToNormal($data);
        $req = array("name" => "str");
        $opt = array(
            "dev_link" => "str",
            "description" => "str",
            "project_status"    =>  "int",
            "person_main_contact_id"    =>  "int",
            "android_link"    =>  "str",
            "ios_link"    =>  "str",
            "web_link"    =>  "str",
            "company_id"    =>  "int",
            "contractor_id"    =>  "int",
            "deadline"          =>  "date",
            "budget"    =>  "str",
            "hourly_wage"   =>  "str",
            "SO_hourly"   =>  "int",
            "SO_description"   =>  "str",
            "intermediate_id"    =>  "int",
            "contractor_main_contact_id"    =>  "int",
            "repository"    =>  "str",
            "responsible"    =>  "int",
            "slug"    =>  "str"
            );
        if ($data['deadline'] !== "") {
            $temp = new DateTime($data['deadline']);
            $data['deadline'] = $temp->format('Y-m-d');
        }
        
        $edit = false;
        $data = $this->_fun->filterVarData($data, $req, $opt);
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $edit = array("id" => $data['id']);
        }
        
        $result = $this->_dataHandler->handleInformationToDb("projects", $req, $opt, $data, $edit);
        if ($result === true || is_numeric($result)) {
            if (isset($_FILES['file']) && strlen($_FILES['file']['tmp_name']) > 1) {
                $id = is_numeric($result) ? $result : $data['id'];
                return $this->checkAndSaveLogo($id);
            }
            return true;
        }
        return $result;
    }
    
    /**
     * Sets additional info based on the id.
     * @param   Integer $id The id of the project
     * @return  Void.
     */
    private function setAdditionalInfo($id) {
        $this->setPersonsFromProject($id);
        $this->setAttachmentsProject($id);
        $this->setTicketsProject($id);
        $this->setTimeWorkedProject($id);
    }
    
    /**
     * Set worked time for overview.
     * @param   Integer $projectId id of project.
     * @return  Void.
     */
    private function setTimeWorkedProject($projectId) {
        $time = $this->_dataHandler->getTimeWorkedProject($projectId);
        $this->Assign("worked_time", $time);
    }
    
    /**
     * Gets project that are connect with the id value.
     * @param   Integer $id Contains id.
     * @return  Void.
     */
    private function setPersonsFromProject($id) {
        $persons = $this->_dataHandler->getPeopleProject($id);
        $this->Assign("coupled_persons", $persons);
    }
    
    /**
     * Attach project.
     * @param   Integer $id Contains id.
     * @return  Void.
     */
    private function setAttachmentsProject($id) {
        $this->_db->setSql("SELECT * FROM `attachments` WHERE `project_id` = ? AND `deleted` = 0");
        $allAttachments = $this->_db->getRows(array($id));
        $attachments = array();
        foreach ($allAttachments as $single) {
            $attachment = new Attachment($single);
            $attachment->setNamePerson($this->getUploaderName($attachment->getPersonId()));
            $attachment->setTypeClass($this->_fun->getIconFromType($attachment->getType()));
            $attachments[] = $attachment;
        }
        $this->Assign('attachments', $attachments);
    }
    
    /**
     * Get the name of the uploader
     * @param  Integer $id Contains id.
     * @return String       Name of the uploader
     */
    private function getUploaderName($id) {
        $this->_db->setSql("SELECT `fullname` FROM `user_accounts` WHERE `id` = ?");
        $row = $this->_db->getRow(array($id));
        return htmlentities($row['fullname']);
    }
    
    /**
     * Set tickets project.
     * @param  Integer $id Contains id.
     * @return Void.
     */
    private function setTicketsProject($id) {
        $this->_db->setSql("SELECT * FROM `tickets` WHERE `project_id` = ? AND `deleted` = 0");
        $tickets = $this->_db->getRows(array($id));
        $all = array();
        foreach ($tickets as $single) {
            $ticket = new Ticket($single);
            $ticket->setFromName($this->getUploaderName($ticket->getFromId()));
            $all[] = $ticket;
        }
        $this->Assign('tickets', $all);
    }
    
    /**
     * check and save img.
     * @param  Integer $id contains id.
     * @return Boolean | Array
     */
    private function checkAndSaveLogo($id) {
        $resultImgUpload = $this->_fun->HandleImage($_FILES, 'file', 'projects');
        if (is_array($resultImgUpload)) {
            $this->_imagenewname = $resultImgUpload['newname'];
            $this->_db->setSql("UPDATE `projects` SET `image` = ? WHERE `id` = ?");
            return $this->_db->updateRecord(array($resultImgUpload['newname'], $id));
        }
        return array("errormessage" => $resultImgUpload);
    }
    
    /**
     * Get image name by id.
     * @param  Integer $id contains id.
     * @return Array an array with results | null.
     */
    private function getImagename($id) {
        $this->_db->setSql("SELECT `image` FROM `projects` WHERE `id` = ?");
        return $this->_db->getRow(array($id))['image'];
    }
    
    /**
     * Downloads a file based on id and filename
     * @param  Integer $id     Contains project id.
     * @param  String  $fileId Contains the if of the file in the db.
     * @return function          Function that redirect to a certain page with an message
     */
    public function downloadpdf($id, $fileId) {
        $this->handleMessages();
        if (!$this->_dataHandler->checkIfProjectUserIsAllowed($id, $this->_user)) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed));
            return $this->index();
        }
        $realFilename = $this->_dataHandler->getDataFromRow(
            "attachments",
            array("project_id" => filter_var($id, FILTER_SANITIZE_NUMBER_INT) , "id" => filter_var($fileId, FILTER_SANITIZE_NUMBER_INT)),
            true,
            array("filename", "realname", "project_id")
        );
        if (!$realFilename) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues));
            return $this->index();
        }
        $file = ROOT . DS . "uploads" . DS . "files" . DS . $realFilename['project_id'] . DS . $realFilename['realname'];
        if (file_exists($file)) {
            while (ob_get_level()) {
                ob_end_clean();
            }
            header("Content-disposition: inline; filename={$realFilename['filename']}");
            header('Cache-Control: public, must-revalidate, max-age=0');
            header("Content-Type: application/octet-stream");
            header("Content-Transfer-Encoding: binary");
            header('Content-Length: ' . filesize($file));
            readfile($file);
            die();
        }
        header("refresh:0; url=" . SITE_ROOT . 'projects/index/?errormessage=' . urlencode(_("Sorry you can't download the file, If the this error persists contact the IT department.")));
    }
    
    /**
     * Fill function for dropdowns for persons.
     * @return Array.
     */
    private function getPersons() {
        $this->_db->setSql('SELECT * FROM `persons` WHERE `deleted` = 0');
        $results = $this->_db->getRows();
        $personAll = array();
        foreach ($results as $person) {
            $personAll[] = new Person($person);
        }
        return $personAll;
    }
    
    /**
     * Get the name of person main contact.
     * @param String $project_id contains id of the project.
     * @return String $pName the name of the person main contact.
     */
    private function personMainContactId($project_id = -1) {
        return $this->getSpecificPerson("person_main_contact_id", "persons", $project_id);
    }
    
    
    /**
     * Get the name of main resposiblity.
     * @param String $project_id contains id of the project.
     * @return String $rName the name of main resposiblity.
     */
    private function responsibleName($project_id = -1) {
        return $this->getSpecificPerson("responsible", "persons", $project_id);
    }
    
    /**
     * Get the name of contractor.
     * @param String $project_id contains id of the project.
     * @return String $cName the name of contractor.
     */
    private function contractorId($project_id = -1) {
        return $this->getSpecificPerson("contractor_id", "companies", $project_id);
    }
    
    /**
     * Get the name of Intermediate.
     * @param String $project_id contains id of the project.
     * @return String $iName the name of Intermediate.
     */
    private function intermediate($project_id = -1) {
        return $this->getSpecificPerson("intermediate_id", "companies", $project_id);
    }
    
    /**
     * Function to get the name of a person or company from the database
     * @param   String  $which      What variable do we want to get from projects
     * @param   String  $from       In what table do we check for the name of this variable
     * @param   Integer $project_id Id of the project
     * @return  String                  Name of the person or company
     */
    private function getSpecificPerson($which, $from, $project_id) {
        if ($project_id !== -1) {
            $person_id = $this->_dataHandler->getDataFromRow("projects", array("id" => $project_id), true, array($which));
            $iName = $this->_dataHandler->getDataFromRow($from, array("id" => $person_id[$which]), true, array("name"));
            return $iName['name'];
        }
        return "";
    }
    
    /**
     * Fill function for dropdowns for companies.
     * @return Array.
     */
    private function getCompanies() {
        $this->_db->setSql('SELECT * FROM `companies` WHERE `owned` = 1 AND `deleted` = 0');
        $results = $this->_db->getRows();
        $companies = array();
        foreach ($results as $company) {
            $companies[] = new Company($company);
        }
        return $companies;
    }
    
    
    /**
     * Get user informatio.
     * @return array.
     */
    private function userInfo() {
        $this->_db->setSql("SELECT * FROM `persons` WHERE `account_id` = ?");
        return $this->_db->getRow(array($this->_user->getId()));
    }
    
    /**
     * Get all the projects that are within the bounds of the search parameters
     * @param Boolean $isCustomer Whether it is for a customer.
     * @return Array Containing all (all rows found) and count(the total found).
     */
    private function parseJSONRequestForCall($isCustomer = false) {
        $settings = $this->_fun->getJsonFromFile('projects_css', 'settings', 'settings');
        $doPersons = array();
        $allowed = array("id", "name", "project_status");
        if (isset($settings['projectDeadline']) && $settings['projectDeadline']) {
            $allowed[] = "deadline";
        }
        $selectionArray = $allowedArray = $allowed;
        $selectionArray[] = 'image';
        if (isset($settings['projectDropdownContractor']) && $settings['projectDropdownContractor']) {
            $doPersons["contractor"] = "contractor_id";
            $allowedArray[] = "contractor";
        }
        if (isset($settings['projectCrmcDropdown']) && $settings['projectCrmcDropdown']) {
            $doPersons["contractor_maincontact"] = "contractor_main_contact_id";
            $allowedArray[] = "contractor_maincontact";
        }
        if (isset($settings['projectMainResponDropdown']) && $settings['projectMainResponDropdown']) {
            $doPersons["responsible"] = "responsible";
            $allowedArray[] = "responsible";
        }
        $selectionArray = array_merge($selectionArray, $doPersons);
        $request = $this->handleRequestDataParent($allowedArray);
        $order = $request['order'];
        $from = $request['from'];
        $total = $request['total'];
        $search = $request['search'];
        $name = $request['name'];
        if (in_array($search, $this->_projectStati)) {
            $search = array_search($search, $this->_projectStati);
        } else {
            $search = '%' . filter_var($search, FILTER_SANITIZE_STRING) . '%';
        }
        $searchSql = $this->_fun->getSqlFromArray($allowed, $search);
        $selectionString = "`" . implode('`,`', $selectionArray) . "`";
        $sql = "SELECT $selectionString FROM `projects` WHERE `deleted` = 0 AND ";
        $sqlCount = "SELECT count(`id`) AS count FROM `projects` WHERE `deleted` = 0 AND ";
        if ($isCustomer) {
            $extraString = $this->getSqlStringCustomer();
            $sql .= $extraString;
            $sqlCount .= $extraString;
        }
        if (!empty($doPersons)) {
            $extendedStringPersons = $this->extendQueryWithPeople($name, $order, $searchSql, $doPersons, $search);
            $sql .= $extendedStringPersons;
            $sqlCount .= $extendedStringPersons;
        } else {
            $sql .= $searchSql['sql'];
            $sqlCount .= $searchSql['sql'];
        }
        $sql .= " ORDER BY $name $order LIMIT $total OFFSET $from";
        $this->_db->setSql($sql);
        $rows = $this->_db->getRows($searchSql['search']);
        $this->_db->setSql($sqlCount);
        $count = $this->_db->getRow($searchSql['search']);
        return array("count" => $count['count'], "all" => $rows);
    }
    
    /**
     * Create the extended string for people and companies.
     * @param string $name      The name of the column to sort on.
     * @param string $order     The way it should be sorted.
     * @param Array  $searchSql The array with the standard search values.
     * @param Array  $doPersons Which persons to do
     * @param String $search    The search value.
     * @return string
     */
    private function extendQueryWithPeople(&$name, &$order, $searchSql, $doPersons, $search) {
        $arrayNameToDb = array('companies' => array("contractor_id"), 'persons' => array("contractor_main_contact_id", "responsible"));
        $toReturnString = "(";
        foreach ($arrayNameToDb as $table => $for) {
            $this->_db->setSql("SELECT `id` FROM `$table` WHERE `name` LIKE ? AND `deleted` = 0 ORDER BY `name` $order");
            $resultingRows = $this->_db->getRows(array($search));
            if (empty($resultingRows)) {
                if (in_array($doPersons[$name], $for)) {
                    $name = $doPersons[$name];
                }
                continue;
            }
            $allIds = implode(',', array_map(function ($pId) {
                return $pId['id'];
            }, $resultingRows));
            if (in_array($doPersons[$name], $for)) {
                $pre = "";
                $post = ",0,-1";
                if ($order === "DESC") {
                    $pre = "-1,0,";
                    $post = "";
                }
                $this->_db->setSql("SELECT `id` FROM `$table` WHERE `deleted` = 0 ORDER BY `name` $order");
                $all = $this->_db->getRows(array($search));
                $reallyAll = implode(',', array_map(function ($pId) {
                    return $pId['id'];
                }, $all));
                $name = "FIELD(`" . $doPersons[$name] . "`, $pre" . $reallyAll . "$post)";
                $order = "";
            }
            $stringAllIds = "` IN (" . $allIds . ") OR ";
            $toReturnString .= "`" . implode("$stringAllIds`", $for) . $stringAllIds;
        }
        return $toReturnString . $searchSql['sql'] . ")";
    }
    
    /**
     * Get the sql string of the project coupled to current user.
     * @return String
     */
    private function getSqlStringCustomer() {
        $involved = $this->_dataHandler->getDataFromRows('person_project', array("person_id" => $this->_user->getPersonId() ), true, array('project_id'));
        $mappedInvolved = implode(',', array_map(function ($pId) {
            return $pId['project_id'];
        }, $involved));
        if (!empty($involved)) {
            return "`id` IN ($mappedInvolved) AND ";
        } else {
            return "`id` IN (-42) AND ";
        }
    }
    
    /**
     * Whether things should be shown.
     * @return Array With the indexes: "deadline", "contracter", "contractermain", "mainresponsible".
     */
    private function getShowArray() {
        $settings = $this->_fun->getJsonFromFile('projects_css', 'settings', 'settings');
        $showArray = array(
            "deadline"  => true,
            "contracter" => true,
            "contractermain" => true,
            "mainresponsible" => true,
        );
        if (isset($settings['projectDeadline']) && !$settings['projectDeadline']) {
            $showArray["deadline"] = false;
        }
        if (isset($settings['projects_contractor_name_input']) && !$settings['projects_contractor_name_input']) {
            $showArray["contracter"] = false;
        }
        if (isset($settings['projects_contractor_main_contact_name_input']) && !$settings['projects_contractor_main_contact_name_input']) {
            $showArray["contractermain"] = false;
        }
        if (isset($settings['projects_responsible_name_input']) && !$settings['projects_responsible_name_input']) {
            $showArray["mainresponsible"] = false;
        }
        return $showArray;
    }
}
