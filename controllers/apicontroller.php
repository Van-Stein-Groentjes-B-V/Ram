<?php

/**
 * API Controller, controls all API calls from javascript.
 *
 * This controller handles the bootstrap table calls and other
 * javascript API calls to get data in JSON format.
 *
 * PHP version 7+
 *
 * @category   Controllers
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @editor     Thomas Shamoian <thomas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 * @uses       \SG\Ram\Controller       Extend the main controller
 * @uses       \SG\Ram\CssModifier      Load custom CSS
 * @uses       \SG\Ram\dataHandler      datahandler claas
 * @uses       \SG\Ram\Models\Dbhelper  Database helper
 * @uses       \SG\Ram\Models\Project   Project
 * @uses       \SG\Ram\functions        general functions
 * @uses       DateTime                 php datetime
 * @uses       ZipArchive               php ziparchive
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

use SG\Ram\controller;
use SG\Ram\CssModifier;
use SG\Ram\dataHandler;
use SG\Ram\Models\Dbhelper;
use SG\Ram\Models\Project;
use DateTime;
use ZipArchive;

/**
 * ApiController
 * @category   Controllers
 * @package    Ram
 */
class ApiController extends controller
{
    private $_db;
    private $_account;
    private $_user;
    private $_dataHandler;
    private $_request;
    private $_method;
    private $_file;
    private $_data = array("success" => "", "errormessage" => "");
    
    /**
     * Constructor: __construct
     * Initialize the needed objects
     * @return  Void
     */
    public function __construct() {
        parent::__construct();
        global $user;
        $this->_account = $user;
        $this->_user = $user->getUser();
        $this->_db = new Dbhelper();
        $this->_dataHandler = new dataHandler();
        $this->_method = $this->getMethod();
        $this->_request = $this->handleRequest();
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_user);
        unset($this->_dataHandler);
        unset($this->_method);
        unset($this->_request);
        unset($this->_account);
    }
    
    /**
     * Get the method of giving data to the api.
     * @return String   $method         HTTP method.
     */
    private function getMethod() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($this->_method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $method = 'DELETE';
            } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
        return $method;
    }
    
    /**
     * Switch to see which method the data is in and sent it to be cleaned.
     * @return Array Cleaned array.
     */
    private function handleRequest() {
        switch ($this->_method) {
            case 'DELETE':
                break;
            case 'POST':
                return $this->cleanInputs($_POST);
            case 'GET':
                return $this->cleanInputs($_GET);
            break;
            case 'PUT':
                $this->_file = file_get_contents("php://input");
                return $this->cleanInputs($_GET);
            default:
                $this->response('Invalid Method', 405);
                break;
        }
    }
    
    /**
     * Old response function
     * Still used by the handleRequest.
     * @deprecated v1.0       Will be removed.
     * @param   Array   $data   Will be printed json encoded (output).
     * @param   Integer $status Http status code, defaults to 200 (OK).
     * @return  String                  Json encoded data.
     */
    private function response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }
    
    /**
     * Clean the inputed data.
     * @param   Array | String $data Array or string to clean.
     * @return  Array | String          Cleaned array or string.
     */
    private function cleanInputs($data) {
        $clean_input = array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
    
    /**
     * Function to get status.
     * @param   Integer $code Integer of the status code.
     * @return  String              Status text.
     */
    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            400 => 'Bad Request',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code]) ? $status[$code] : $status[500];
    }
    
    /**
     * Default return 404.
     * @return  Void.
     */
    public function index() {
        $this->createReturn(404);
    }
    
    /**
     * Create the return for calls, sets headers and prints the data json encoded.
     * @param   Integer $status Status code.
     * @return  Void.
     */
    protected function createReturn($status = 200) {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        if (array_key_exists('callback', $this->_request)) {
            print $this->_request['callback'] . "(";
        }
        echo json_encode($this->_data);
        if (array_key_exists('callback', $this->_request)) {
            print ")";
        }
        exit(1);
    }
    
    /**
     * Check if person has high enough admin level.
     * @param Integer $level  The level that is no longer allowed.
     * @param Boolean $strict If set, will check whether the user has the specific level.
     * @return Void
     */
    private function checkLevelAllowed($level, $strict = false) {
        if ((!$strict && $this->_user->getAdmin() <= $level) || ($strict && intval($this->_user->getAdmin()) !== $level)) {
            $this->_data['success'] = "";
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed);
            $this->createReturn();
            die();
        }
    }
    
    /**
     * This function will set the stats to alert
     * @return Void.
     */
    public function alertStats() {
        $this->checkLevelAllowed(0);
        if ($this->_method != 'GET') {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$errorsInTheValues);
            $this->createReturn();
            return false;
        }
        $userid = $this->_account->getUserId();
        $this->_db->setSql("SELECT * FROM `tickets` WHERE `deleted` = 0 AND `project_id` IN (SELECT `project_id` FROM `person_project` WHERE `person_id` = ?)");
        $tickets = $this->_db->getRows(array($this->_user->getPersonId()));
        $this->_data['tickets'] = $tickets;
        $this->_data['unread'] = 0;
        foreach ($tickets as $ticket) {
            if ($ticket['status'] === "0") {
                $this->_data['unread']++;
            }
        }
        $this->_db->setSql("SELECT * FROM `notification` WHERE `status` = 0 AND `user_id` = ? AND `type` = 0");
        $notifications = $this->_db->getRows(array($userid));
        $this->_data['notifications'] = $notifications;
        $this->_data['notificationcount'] = count($notifications);
        $this->_db->setSql("SELECT * FROM `notification` WHERE `status` = 0 AND `user_id` = ? AND `type` = 1");
        $messages = $this->_db->getRows(array($userid));
        $this->_data['messages'] = $messages;
        $this->_data['unreadmessages'] = count($messages);
        $this->createReturn();
    }
    
    /**
     * Gets the todos.
     * @return Void.
     */
    public function getTodos() {
        $this->checkLevelAllowed(0);
        $this->_db->setSql("SELECT * FROM `todos` WHERE (`user_id` = ? OR `user_id` = -1) AND `done` < 3 ORDER BY `project_id` ASC");
        $results = $this->_db->getRows(array($this->_user->getId()));
        $data = array();
        foreach ($results as $single) {
            //check if project still exist if so show else dont
            $this->_db->setSql("SELECT * FROM `projects` WHERE `id` = ? AND `deleted` = 0");
            $results = $this->_db->getRow(array($single['project_id']));
            if ($results) {
                $information = $single;
                if (!is_null($information['deadline'])) {
                    $deadline = new DateTime($information['deadline']);
                    $information['deadlineWanted'] = $deadline->format('d-m-Y');
                } else {
                    $information['deadlineWanted'] = "-";
                }
                if (!isset($data[$information['project_id']])) {
                    $data[$information['project_id']] = array();
                    $tempProject = $this->_dataHandler->getDataFromRow('projects', $information['project_id'], true, array('id','name','image'));
                    array_walk_recursive($tempProject, [$this, "filterValue"]);
                    $data[$information['project_id']]['project'] = $tempProject;
                    $data[$information['project_id']]['coupledPersons'] = array();
                    foreach ($this->_dataHandler->getPeopleProject($information['project_id']) as $person) {
                        $temp = array('id' => $person->getAccountid(), 'name' => $person->getParsedString('getName'));
                        $data[$information['project_id']]['coupledPersons'][] = $temp;
                    }
                    $data[$information['project_id']]['todos'] = array();
                }
                $data[$information['project_id']]['todos'][] = $information;
            }
        }
        $this->_data['success'] = "success";
        $this->_data['response'] = $data;
        $this->createReturn();
    }
    
    /**
     * Gets the todos that are connected to the project id.
     * @return  Void.
     */
    public function getTodosFromProject() {
        $this->checkLevelAllowed(0);
        $isAllowed = $this->_dataHandler->checkIfProjectUserIsAllowed($this->_request['project_id'], $this->_user);
        if (isset($this->_request['project_id']) && $this->_request['project_id'] > 0 && $isAllowed) {
            $this->_db->setSql("SELECT * FROM `todos` WHERE `project_id` = ? ORDER BY `prio` DESC");
            $results = $this->_db->getRows(array($this->_request['project_id']));
            $data = array();
            foreach ($results as $single) {
                $information = $single;
                if (!is_null($information['deadline'])) {
                    $deadlin = new DateTime($information['deadline']);
                    $information['deadlineWanted'] = $deadlin->format('d-m-Y');
                } else {
                    $information['deadlineWanted'] = "-";
                }
                if (isset($information['user_id']) && $information['user_id'] > 0) {
                    $tempPerson = $this->_dataHandler->getDataFromRow('persons', array('account_id' => $information['user_id']), true, array('id','name'));
                    array_walk_recursive($tempPerson, [$this, "filterValue"]); /** waarom word data uit de database gehtml special charred? **/
                    $tempPerson['name'] = htmlspecialchars_decode($tempPerson['name'], ENT_HTML5);
                    $information['person_data'] = $tempPerson;
                } else {
                    $information['person_data'] = array("name" => _('All'), "id" => -1);
                }
                $data[] = $information;
            }
            $this->_data['success'] = "success";
            $this->_data['response'] = $data;
        } elseif (!$isAllowed) {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed);
        } else {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$errorsInTheValues);
        }
        $this->createReturn();
    }
    
    /**
     * Checks which todos are done.
     * Then it checks if its smaller then 3 if so it will be updated.
     * @return  Void
     */
    public function progressTodo() {
        $this->updateTodoStatus(true);
    }
    
    /**
     * Checks which todos are done.
     * Then it checks if its bigger then 3 if so it will be updated.
     * @return  Void
     */
    public function deProgressTodo() {
        $this->updateTodoStatus(false);
    }
    
    /**
     * Function that does the actual update of the todo
     * @param Boolean $progress If true adds 1 to status, else removes 1
     * @return Void
     */
    private function updateTodoStatus($progress = true) {
        if (isset($this->_request['id']) && $this->_request['id'] > 0) {
            $this->_db->setSql("SELECT `id`, `done`, `project_id` FROM `todos` WHERE `id` = ?");
            $result = $this->_db->getRow(array(filter_var($this->_request['id'], FILTER_SANITIZE_NUMBER_INT)));
            if ($result && $this->_dataHandler->checkIfProjectUserIsAllowed($result['project_id'], $this->_user)) {
                if ($result['done'] > 1 && !$progress) {
                    --$result['done'];
                } elseif ($result['done'] < 3) {
                    ++$result['done'];
                }
                $this->_db->setSql("UPDATE `todos` SET `done` = ? WHERE `id` = ?");
                $this->_db->updateRecord(array($result['done'], $result['id']));

                $this->_data['success'] = "success";
                $this->_data['response'] = $result;
                $this->createReturn();
            }
        }
        $this->_data['success'] = "";
        $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        $this->createReturn();
    }
    
    /**
     * Deletes the todo based on the id.
     * @return Void.
     */
    public function deleteTodo() {
        if (isset($this->_request['id']) && $this->_request['id'] > 0) {
            $this->_db->setSql("SELECT `id`, `project_id` FROM `todos` WHERE `id` = ?");
            $result = $this->_db->getRow(array(filter_var($this->_request['id'], FILTER_SANITIZE_NUMBER_INT)));
            if ($result && $this->_dataHandler->checkIfProjectUserIsAllowed($result['project_id'], $this->_user)) {
                $this->_db->setSql("DELETE FROM `todos` WHERE `id` = ?");
                $result2 = $this->_db->updateRecord(array($result['id']));
                if ($result2 === true) {
                    $this->_data['success'] = "success";
                    $this->createReturn();
                }
            }
        }
        $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        $this->_data['success'] = "";
        $this->createReturn();
    }
    
    /**
     * Deletes a specific target from one of the ALLOWED_DELETE_TABLES.
     * using the request data.
     * ($this->_request['id'] should be set).
     * @param   String $target Target table name.
     * @param   String $extra  extra  param if needed.
     * @return  Void.
     */
    public function delete($target, $extra = "") {
        if ($target === 'module') {
            return $this->deleteModule();
        }
        if ($target === 'user') {
            return $this->deleteUser();
        }
        if (isset($this->_request['id']) && $this->_request['id'] > 0) {
            $requestID = filter_var($this->_request['id'], FILTER_SANITIZE_NUMBER_INT);
        }
        $trueTarget = $this->_fun->getAllowed($target, ALLOWED_DELETE_TABLES);
        $this->_db->setSql("SELECT * FROM `tickets` WHERE `from_id` = ? AND `id` = ?");
        $involved = $this->_db->getRow(array($this->_user->getId(), $requestID));
        if (
            (isset($requestID) && $requestID > 0 && $trueTarget && $this->_user->isAdmin()) ||
            ($trueTarget == "tickets" && isset($requestID) && $requestID > 0 && $this->_user->isCustomer() && $involved['id'] == $requestID)
        ) {
            if ($trueTarget == "person_project") {
                $this->_db->setSql("UPDATE " . $trueTarget . " SET `deleted` = 1 WHERE `project_id` = ? AND `person_id` = ?");
                $result =  $this->_db->updateRecord(array($extra,$this->_request['id']));
            } else {
                $result = $this->_dataHandler->setDeleted($trueTarget, array('id' => $requestID));
            }
            if ($result) {
                $this->_data['success'] = 'success';
                $this->_data['id'] = $requestID;
            } else {
                $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$errorsInTheValues);
            }
        } else {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$noPermissionToDelete);
        }
        $this->createReturn();
    }
    
    /**
     * Delete for user with admin check.
     * @return  Void.
     */
    private function deleteUser() {
        $this->checkLevelAllowed(2);
        if (isset($this->_request['id']) && is_numeric($this->_request['id']) && $this->_request['id'] > 0) {
            $filteredId = filter_var($this->_request['id'], FILTER_SANITIZE_NUMBER_INT);
            $this->_db->setSql("SELECT `username` FROM `user_accounts` WHERE `id` = ?");
            $person = $this->_db->getRow(array($filteredId));
            if ($person) {
                $this->_db->setSql("DELETE FROM `user_accounts` WHERE `id` = ?");
                $result = $this->_db->updateRecord(array($filteredId));
                if ($result) {
                    $this->_dataHandler->removeCouplingUserPerson($filteredId);
                    $this->_data['success'] = 'success';
                    $this->_data['id'] = $filteredId;
                } else {
                    $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$youHaveNoPermission);
                }
            } else {
                $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$youHaveNoPermission);
            }
        } else {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        }
        $this->createReturn();
    }
    
    /**
     * Delete for module
     * Check whether it is an admin.
     * @return  Void.
     */
    public function deleteModule() {
        $this->checkLevelAllowed(3);
        $this->_data['success'] = '';
        $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$youHaveNoPermission);
        if (isset($this->_request['id']) && is_numeric($this->_request['id']) && $this->_request['id'] > 0 && $this->_user->isMainAdmin()) {
            $this->_db->setSql("SELECT `name` FROM `modules` WHERE `id` = ?");
            $name = $this->_db->getRow(array(filter_var($this->_request['id'], FILTER_SANITIZE_NUMBER_INT)));
            $installed = explode(',', INSTALLED_MODULES);
            if (in_array($name['name'], $installed)) {
                $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$pluginError1);
            } elseif ($name) {
                $this->_data['errormessage'] = _('Could not delete the module.');
                $this->_db->setSql("DELETE from `modules` WHERE `id` = ?");
                $result = $this->_db->updateRecord(array($this->_request['id']));
                if ($result === true) {
                    $this->_data['name'] = $name['name'];
                    if ((file_exists(ROOT . DS . "modules" . DS . $name['name']) && $this->rmDirCont(ROOT . DS . "modules" . DS . $name['name'])) || !file_exists(ROOT . DS . "modules" . DS . $name['name'])) {
                        $this->_data['success'] = "success";
                        $this->_data['errormessage'] = '';
                    }
                }
            }
        }
        $this->createReturn();
    }
    
    /**
     * Removes the directory.
     * @param String $dir Contains dir name.
     * @return Boolean      True if successful
     */
    private function rmDirCont($dir) {
        $result = true;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $result = $this->rmDirCont($dir . "/" . $object);
                    } else {
                        $result = unlink($dir . "/" . $object);
                    }
                }
                if ($result === false) {
                    $this->Assign("errorarray", array("object" => $object, "result" => $result, "reason" => "Permissions incorrect."));
                    return false;
                }
            }
            $result = rmdir($dir);
        }
        return $result;
    }

    /**
     * Fill function for dropdowns for companies.
     * @return Void.
     */
    public function getCompany() {
        $this->checkLevelAllowed(0);
        $this->getObject("companies");
    }
    
    /**
     * Fill function for dropdowns for projects.
     * @return Void.
     */
    public function getProjects() {
        $this->checkLevelAllowed(0);
        $this->getObject("projects");
    }
    
    /**
     * fill function for dropdowns for persons.
     * @return void.
     */
    public function getPersons() {
        $this->checkLevelAllowed(0);
        $this->getObject("persons");
    }
    
    /**
     * Fill function for dropdowns for owned companies.
     * @return Void.
     **/
    public function getContractor() {
        $this->checkLevelAllowed(0);
        $this->getObject("companies", " AND `owned` = 1");
    }
    
    /**
     * Actual getObject function
     * @param String $type        The table from which the objects need to be fetched
     * @param String $extraString Possible addition to the query for owned companies
     * @return Void
     */
    private function getObject($type, $extraString = "") {
        if (isset($this->_request['searchval']) && strlen($this->_request['searchval']) > 1) {
            $this->_db->setSql('SELECT `name`, `id` FROM `' . $type . '` WHERE `name` LIKE ? AND `deleted` = 0' . $extraString);
            $results = $this->_db->getRows(array('%' . filter_var($this->_request['searchval'], FILTER_SANITIZE_STRING) . '%'));
            array_walk_recursive($results, [$this, "filterValue"]); /** again html special charring data from db, it is a bit weird... **/
            $this->_data['success'] = "success";
            $this->_data['response'] = $results;
        } else {
            $this->_data['success'] = "";
        }
        $this->createReturn();
    }
    
    /**
     * Gets the possible contact persons. Either of owned companies or of other companies
     * @param String $extraSearchParameter Add extra parameters to the query.
     * @return Void.
     */
    private function getContactPersonsHelper($extraSearchParameter = "") {
        /** This inserts directly into the sql, MUST BE PRIVATE! */
        $this->_data['success'] = "";
        if (isset($this->_request['searchval']) && strlen($this->_request['searchval']) > 1) {
            $this->_db->setSql("SELECT `id` FROM `companies` WHERE `deleted` = 0" . $extraSearchParameter);
            $companies = $this->_db->getRows();
            $ids = array();
            foreach ($companies as $row) {
                $ids[] = $row["id"];
            }
            if (count($ids) > 0) {
                $idString = join(',', $ids);
                $this->_db->setSql('SELECT `id`, `name`, `logo` FROM `persons` WHERE `name` LIKE ? AND `deleted` = 0 AND `company_id` IN (' . $idString . ') ');
                $results = $this->_db->getRows(array('%' . filter_var($this->_request['searchval'], FILTER_SANITIZE_STRING) . '%'));
                array_walk_recursive($results, [$this, "filterValue"]);/** again **/
                /**
                 * </Todo> Wat is de bedoelding van dit blok??
                 * Add suggestions when less than 10 results in the search
                 * (can be seen in projects edit_add && overview)
                 */
                if ($extraSearchParameter === "" && count($results) < 10) {
                    $limit = 10 - count($results);
                    $this->_db->setSql('SELECT `id`, `name`, `logo` FROM `persons` WHERE `deleted` = 0 AND `company_id` IN (' . $idString . ') ORDER BY `id` DESC LIMIT ' . $limit);
                    $extra = $this->_db->getRows();
                    if ($extra) {
                        array_walk_recursive($extra, [$this, "filterValue"]);/** again **/
                        $this->_data['response_extra'] = $extra;
                    }
                }
                $this->_data['success'] = "success";
                $this->_data['response'] = $results;
            }
        }
    }
    
    /**
     * Fill function for dropdowns for persons from owned companies.
     * @return Void.
     */
    public function getContactPerson() {
        $this->checkLevelAllowed(0);
        $this->getContactPersonsHelper(" AND `owned` = 1 ");
        $this->createReturn();
    }
    
    /**
     * Fill function for dropdowns for persons from owned companies.
     * Adds other suggestions if count lower than 10.
     * @return Void.
     */
    public function getContactPersonAll() {
        $this->checkLevelAllowed(0);
        $this->getContactPersonsHelper("");
        $this->createReturn();
    }
    
    /**
     * Add a person by id to the project.
     * @return Void.
     */
    public function addPersonToProject() {
        $this->checkLevelAllowed(0);
        $success = false;
        if (isset($this->_request['id']) && is_numeric($this->_request['id']) && isset($this->_request['project']) && is_numeric($this->_request['project'])) {
            $this->_db->setSql("SELECT `id` FROM `projects` WHERE `id` = ? AND `deleted` = 0");
            $resultProject = $this->_db->getRow(array(filter_var($this->_request['project'], FILTER_SANITIZE_NUMBER_INT)));
            $this->_db->setSql("SELECT `id`, `name`, `logo`, `account_id` FROM `persons` WHERE `id` = ? AND `deleted` = 0");
            $resultPerson = $this->_db->getRow(array(filter_var($this->_request['id'], FILTER_SANITIZE_NUMBER_INT)));
            if ($resultProject && $resultPerson) {
                $this->_db->setSql("SELECT `id`, `deleted` FROM `person_project` WHERE `person_id` = ? AND `project_id` = ?");
                $result = $this->_db->getRow(array($resultPerson['id'], $resultProject['id']));
                if (!$result) {
                    $this->_db->setSql("INSERT INTO `person_project` (`person_id`, `project_id`) VALUES(?, ?)");
                    $success = $this->_db->updateRecord(array($resultPerson['id'], $resultProject['id']));
                } elseif ($result['deleted'] == 1) {
                    $this->_db->setSql("UPDATE `person_project` SET `deleted` = 0 WHERE `id` = ?");
                    $success = $this->_db->updateRecord(array($result['id']));
                }
                if ($resultPerson['logo'] == "") {
                    $resultPerson['logo'] = "no_avatar.jpg";
                }
                $this->_data['add'] = $resultPerson;
                $this->_data['success'] = "success";
            }
        }
        if (!$success) {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$duplicate);
            $this->_data['success'] = "";
        }
        $this->createReturn();
    }
    
    /**
     * Check an attachment and add it to the project in the db.
     * @return Void.
     */
    public function addAttachmentToProject() {
        $success = false;
        $newname = _("variables are not correct.");
        $this->_data['success'] = "";
        if (isset($this->_request['id']) && $this->_request['id'] > 0) {
            $requestID = filter_var($this->_request['id'], FILTER_SANITIZE_NUMBER_INT);
        }
        if ((isset($this->_request['id']) && is_numeric($requestID) && $this->_user->isSuperAdmin()) || (isset($requestID) && is_numeric($requestID) && $this->_user->isCustomer())) {
            $this->_db->setSql("SELECT `id` FROM `projects` WHERE id = ? AND `deleted` = 0");
            $resultProject = $this->_db->getRow(array($requestID));
            if ($resultProject && $this->checkIfMapExistsElseMakeIt($resultProject['id'])) {
                $newname = $this->_fun->moveFile($_FILES, 'upload', '../uploads/files/' . $resultProject['id'] . '/');
                if (is_array($newname)) {
                    $this->_db->setSql("INSERT INTO `attachments` (`project_id`, `type`, `location`, `message`, `filename`, `realname`, `person_id`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $result = $this->_db->insertRecord(array($resultProject['id'], $this->_fun->getExtensionType($newname['ext']), 'uploads/files/' .
                              $resultProject['id'] . '/', '', $_FILES['upload']['name'],$newname['newname'], $this->_user->getId()));
                    if ($result) {
                        $this->_data['success'] = "success";
                        $this->_data['response'] = array('id' => $result, 'by' => $this->_user->getParsedString('getFullname'), 'fileName' =>
                        $_FILES['upload']['name'], 'location' => $resultProject['id'] . '/' . $result . '/', 'type' => $this->_fun->getIconFromType($newname['ext']));
                        $success = true;
                    }
                }
            }
        }
        if (!$success) {
            $this->_data['errormessage'] = $newname;
        }
        $this->createReturn();
    }
    
    /**
     * Add a ticket to the project.
     * @return Void.
     */
    public function addTicketToProject() {
        $success = false;
        $newname = _("variables are not correct.");
        if (!isset($this->_request['project']) || !is_numeric($this->_request['project']) || !isset($this->_request['subject']) || !isset($this->_request['message'])) {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
            $this->_data['success'] = "";
            $this->createReturn();
            return;
        }
        $this->_db->setSql("SELECT `id` FROM `projects` WHERE `id` = ? AND `deleted` = 0");
        $resultProject = $this->_db->getRow(array(filter_var($this->_request['project'], FILTER_SANITIZE_NUMBER_INT)));
        /** Mag alleen de admin een ticket toevoegen? of is elke user connected to owned company/ the project allowed **/
        if (
                $resultProject &&
                $this->_dataHandler->checkIfProjectUserIsAllowed($resultProject['id'], $this->_user) &&
                strlen($this->_request['subject']) >= 2 &&
                strlen($this->_request['message']) >= 2
        ) {
            $this->_db->setSql('INSERT INTO `tickets` (`project_id`, `from_id`, `from_email`, `message`, `subject`) VALUES (?, ?, ?, ?, ?)');
            $result = $this->_db->insertRecord(array(
            filter_var($this->_request['project'], FILTER_SANITIZE_NUMBER_INT),
            $this->_user->getId(),
            $this->_user->getEmail(),
            filter_var($this->_request['message'], FILTER_SANITIZE_STRING),
            filter_var($this->_request['subject'], FILTER_SANITIZE_STRING)));
            if ($result) {
                $now = new dateTime();
                $success = true;
                $this->_data['success'] = "success";
                $this->_data['response'] = array('id' => $result, 'send' => $now->format('d-m-Y'),
                'from' => $this->_user->getParsedString('getFullName'), 'subject' => $this->_request['subject'], 'message' => $this->_request['message']);
            }
        }
        
        if (!$success) {
            $this->_data['errormessage'] = $newname;
            $this->_data['success'] = "";
        }
        $this->createReturn();
    }
    
    /**
     * Add a todo to the project.
     * @return Void.
     */
    public function addTodoToProject() {
        $this->checkLevelAllowed(1);
        $success = false;
        $newname = _("variables are not correct.");
        if (!isset($this->_request['project_id']) || !is_numeric($this->_request['project_id']) || !isset($this->_request['message']) || !isset($this->_request['prio']) || !isset($this->_request['user_id'])) {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
            $this->_data['success'] = "";
            $this->createReturn();
            return;
        }
        $this->_db->setSql("SELECT `id` FROM `projects` WHERE `id` = ? AND `deleted` = 0");
        $resultProject = $this->_db->getRow(array(filter_var($this->_request['project_id'], FILTER_SANITIZE_NUMBER_INT)));
        if ($resultProject && $this->_dataHandler->checkIfProjectUserIsAllowed($resultProject['id'], $this->_user) && strlen($this->_request['message']) >= 2) {
            if ($this->_request['deadline'] != "") {
                $temp = new DateTime($this->_request['deadline']);
                $this->_request['deadline'] = $temp->format('Y-m-d');
            }
            $req = array('project_id' => 'int', 'prio' => "int");
            $opt = array('message' => "str", 'deadline' => 'date', 'user_id' => 'int');
            $edit = false;
            if (isset($this->_request['id']) && $this->_dataHandler->getDataFromRow('todos', filter_var($this->_request['id'], FILTER_SANITIZE_NUMBER_INT), false, array('id'))) {
                $edit = array("id" => $this->_request['id']);
            }
            $data = $this->_fun->filterVarData($this->_request, $req, $opt);
            $result = $this->_dataHandler->handleInformationToDb('todos', $req, $opt, $data, $edit);
            if (!is_array($result)) {
                $success = true;
                $now = new DateTime();
                $this->_data['success'] = "success";
                $todoid = is_numeric($result) ? $result : $this->_request['id'];
                $person = array("name" => "");
                if (isset($data['user_id']) && $data['user_id'] > 0) {
                    $tempPerson = $this->_dataHandler->getDataFromRow(
                        'persons',
                        array('account_id' => $data['user_id']),
                        true,
                        array('id','name')
                    );
                    array_walk_recursive($tempPerson, [$this, "filterValue"]); /** again **/
                    $person = $tempPerson;
                }
                $deadline = '-';
                if (isset($this->_request['deadline']) && $this->_request['deadline'] != "") {
                    $deadline = $temp->format('d-m-Y');
                }
                $this->_data['response'] = array('id' => $todoid, 'date' => $now->format('d-m-Y'), 'deadline' =>
                $this->_request['deadline'],"deadlineWanted" => $deadline, 'user_id' => $this->_request['user_id'], 'message' =>
                $this->_request['message'], 'prio' => $this->_request['prio'], 'person_data' => $person, 'done' => 1);
            } else {
                $newname = $result['errormessage'];
            }
        }
        
        if (!$success) {
            $this->_data['errormessage'] = $newname;
            $this->_data['success'] = "";
        }
        $this->createReturn();
    }
    
    /**
     * Update the userstats.
     * @return Void.
     */
    public function updateUserStats() {
        $this->checkLevelAllowed(0);
        $project = -1;
        if (isset($this->_request["logButton"]) && $this->_request['logButton'] === "stop") {
            date_default_timezone_set('Europe/Amsterdam');
            if (isset($this->_request['project']) && $this->_request['project'] == -1) {
                $_SESSION[SESSION_PREFIX . 'ACTIVEPROJECT'] = -1;
            }
            $this->_data['project'] = '-';
            $this->_data['success'] = "success";
            $this->_data['response'] = _('The log time has been stopped');
            $this->createReturn();
            return;
        }
        if (isset($this->_request["logButton"]) && $this->_request['logButton'] === "play") {
            date_default_timezone_set('Europe/Amsterdam');
            $projectId = isset($this->_request['project']) && $this->_request['project'] > 0 ? filter_var($this->_request['project'], FILTER_SANITIZE_NUMBER_INT) : $_SESSION[SESSION_PREFIX . 'ACTIVEPROJECT'];
            $project =  $projectId !== false ? $projectId : -1;
            $date = new DateTime();
            if ($this->checkForAccess($project) === false) {
                $project = -1;
            } else {
                $_SESSION[SESSION_PREFIX . 'ACTIVEPROJECT'] = $project;
            }
            $this->_db->setSql("SELECT `id` ,`endtime` ,`secondsonline` FROM `user_stats` WHERE `date` = ? AND `user_id` = ? AND `project_id` = ?");
            $results = $this->_db->getRows(array($date->format('Y-m-d'), $this->_user->getId(), $project));
            $insert = true;
            if ($results) {
                foreach ($results as $single) {
                    //update the secondsonline
                    $dateLast = new DateTime($single['endtime']);
                    $diff = abs(($date->getTimestamp() - $dateLast->getTimestamp()));
                    if ($diff < 120) {
                        $insert = false;
                        $secondsonline = $single['secondsonline'] + ($diff);
                        $this->_db->setSql("UPDATE `user_stats` SET `secondsonline` = ? WHERE `id` = ?");
                        $this->_db->updateRecord(array($secondsonline, $single['id']));
                        break;
                    }
                }
            }
            if ($insert) {
                $this->_db->setSql("INSERT INTO `user_stats` (`date`, `user_id`, `ip`, `secondsonline`, `project_id`) VALUES (?, ?, ?, ?, ?)");
                $ip = $this->_fun->getRealIpAddr();
                $this->_db->insertRecord(array($date->format('Y-m-d'), $this->_user->getId(), $ip, 0, $project));
            }
            $this->_data['project'] = $this->getProjectName($project);
            $this->_data['success'] = "success";
            $this->_data['response'] = _('The time continues');
            $this->createReturn();
            return;
        }
        $this->_data['response'] = _('The log time has been paused');
        $this->createReturn();
    }
    
    /**
     * Get the name of the project or no project on failure.
     * @param Integer $projectId The id of target project.
     * @return String
     */
    private function getProjectName($projectId) {
        $this->_db->setSql("SELECT `name` FROM `projects` WHERE `id` = ?");
        $row = $this->_db->getRow(array($projectId));
        return $row ? $row['name'] : "-";
    }
    
    /**
     * CheckForAccess
     * @param  Integer $projectID The id of the project the user is trying to edit.
     * @return Boolean | String     A string that contains an id. or false
     */
    private function checkForAccess($projectID) {
        //Check project exist
        $existProject = $this->_dataHandler->getDataFromRow("projects", array("id" => $projectID), true, array("id"));
        //Get person ID.
        $personID = $this->_user->getPersonId() > 0 ? $this->_user->getPersonId() : $this->_dataHandler->getDataFromRow("persons", array("account_id" => $this->_user->getId()), true, array("id"));
        //Check if person is connected to project
        if ($personID) {
            $hasAccess = $this->_dataHandler->getDataFromRow("person_project", array("person_id" => $personID['id'], "project_id" => $projectID), true, array("person_id, project_id"));
            if ($existProject && ($this->_user->isSuperAdmin() || $hasAccess)) {
                return $projectID;
            }
        }
        return false;
    }
    
    /**
     * Get the data for weeks for the timesheetJS#.js library.
     * @return Void.
     */
    public function getTimesheetWeek() {
        if ($this->_user->isSuperAdmin()) {
            $this->_data['title'] = 'timesheet';
            $this->_data['lang'] = 'en';
            $all = $this->_dataHandler->getRowsBetween('user_stats', array("time" => "now", "column" => "date", "format" => "Y-m-d"), array("time" => '-1 week', "column" => "date", "format" => "Y-m-d"));
            $this->_data['events'] = $this->SortByProjectAndDate($all);
            $this->createReturn();
        }
    }
    
    /**
     * Get the data for today for the timesheetJS#.js library.
     * @return Void.
     */
    public function getTimesheetToday() {
        if ($this->_user->isSuperAdmin()) {
            $this->_data['title'] = 'timesheet';
            $this->_data['lang'] = 'en';
            $all = $this->_dataHandler->getRowsBetween('user_stats', array("time" => "now", "column" => "date", "format" => "Y-m-d"), array("time" => 'now', "column" => "date", "format" => "Y-m-d"));
            $this->_data['events'] = $this->SortByProjectAndDate($all);
            $this->createReturn();
        }
    }
    
    /**
     * Fill in the data for our own timeline.js.
     * Returns the week, month and year version.
     * @return Void.
     */
    public function getEverytimeLinePossible() {
        $this->checkLevelAllowed(0);
        if (isset($this->_request['specific'])) {
            $user_id = $this->_user->getId();
            if (isset($this->_request['person_id']) && $this->_request['person_id'] > 0 && $this->_user->isSuperAdmin()) {
                $user_id = $this->_dataHandler->getUserIdByPersonId(filter_var($this->_request['person_id'], FILTER_SANITIZE_NUMBER_INT));
            }

            if (isset($this->_request['date'])) {
                $now = new DateTime($this->_request['date']);
                $weekTimeDayOne = ((new DateTime($this->_request['date']))->setISODate($now->format('Y'), $now->format('W')));
                $weekTimeDayLast = ((new DateTime($this->_request['date']))->setISODate($now->format('Y'), $now->format('W'), 7));
            } else {
                $now = new DateTime();
                $weekTimeDayOne = ((new DateTime())->setISODate($now->format('Y'), $now->format('W')));
                $weekTimeDayLast = ((new DateTime())->setISODate($now->format('Y'), $now->format('W'), 7));
            }

            $extraTargets = array("user_id" => $user_id);
            if ($this->_request['specific'] == "true") {
                $extraTargets['project_id'] = filter_var($this->_request['project_id'], FILTER_SANITIZE_NUMBER_INT);
                $year = $this->getActiveProjectsThisYear($extraTargets['project_id']);
            } else {
                $year = $this->getActiveProjectsThisYear();
            }
            $week = $this->_dataHandler->getRowsBetween(
                'user_stats',
                array("time" => $weekTimeDayLast->format('Y-m-d'), "column" => "date", "format" => "Y-m-d"),
                array("time" => $weekTimeDayOne->format('Y-m-d'), "column" => "date", "format" => "Y-m-d"),
                $extraTargets
            );
            
            $monthDayOne = new DateTime($now->format('Y-M-01'));
            $month = $this->_dataHandler->getRowsBetween('user_stats', array("time" =>
            $now->format('Y-M-t'), "column" => "date", "format" => "Y-m-d"), array("time" =>
            $now->format("Y-M-01"), "column" => "date", "format" => "Y-m-d"), $extraTargets);
            $this->_data['week'] = array('start_day' => $weekTimeDayOne->format('Y-m-d'), 'last_day' => $weekTimeDayLast->format('Y-m-d'),
            'week' => $now->format('W') ,'data' => $this->SortByProjectAndDate($week, false));
            
            $this->_data['month'] = array('start_day' => $monthDayOne->format('Y-m-d'), 'data' => $this->SortByProjectAndDate($month, false),
            'month' => $now->format('F'));
            
            $this->_data['year'] = array('year' => $now->format('Y-m-d'), 'data' => $year);
            
            $this->_data['success'] = "success";
            $this->createReturn();
        }
        $this->_data['success'] = '';
        $this->_data['all'] = $this->_request;
        $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        $this->createReturn();
    }
    
    /**
     * Save the time.
     * Splits on whether it already exists.
     * @return Void.
     */
    public function saveTime() {
        if (
            isset($this->_request['project_id']) && isset($this->_request['timedate']) && isset($this->_request['starttime']) &&
            isset($this->_request['endtime']) && isset($this->_request['time_id']) /*&& $this->_user->isSuperAdmin()*/
        ) {
            $this->_data['request'] = $this->_request;
            return $this->_request['time_id'] > 0 ? $this->checkIfAllowedAndUpdateTimeUser() : $this->checkIfAllowedAndInsertTimeUser();
        }
        $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        $this->createReturn(400);
    }
    
    /**
     * Return the info of a time, gotten through its id.
     * @return Void.
     */
    public function getTimeById() {
        if (isset($this->_request['id']) && is_numeric($this->_request['id']) && $this->_request['id'] > 0) {
            $user_id = $this->_user->getId();
            if (isset($this->_request['person_id']) && $this->_request['person_id'] > 0 && $this->_user->isSuperAdmin()) {
                $user_id = $this->_dataHandler->getUserIdByPersonId(filter_var($this->_request['person_id'], FILTER_SANITIZE_NUMBER_INT));
            }
            //Waar is hier de else?????
            if (!isset($this->_request['for'])) {
                $this->_db->setSql("SELECT `id`, `project_id`, `date`, `starttime`, `endtime`, `so` FROM `user_stats` WHERE `id` = ? AND `user_id` = ?");
                $result = $this->_db->getRow(array(filter_var($this->_request['id'], FILTER_SANITIZE_NUMBER_INT), $user_id));
                if ($result) {
                    $result['start'] = (new DateTime($result['starttime']))->format('H:i');
                    $result['end'] = (new DateTime($result['endtime']))->format('H:i');
                    $this->_data['requested'] = $result;
                    $this->_data['success'] = "success";
                    $this->createReturn();
                    return;
                }
            }
        }
        $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        $this->createReturn();
    }
    
    /**
     * Get online the stats for the admins
     * @return Void.
     */
    public function onlineAdminStats() {
        if (!$this->_user->isSuperAdmin()) { /** for api you always be logged in **/
            $this->_data['success'] = '';
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed);
            $this->createReturn();
            return;
        }
        //newtime
        $this->_db->setSql("SELECT SUM(`secondsonline`) AS `newtime` FROM `user_stats` WHERE `user_id` = ? AND `project_id` = ? AND `date` = CURDATE()");
        $starttime = $this->_db->getRow(array($this->_user->getId(), $_SESSION[SESSION_PREFIX . 'ACTIVEPROJECT']));
        $this->_data['newtime'] = $starttime['newtime'];
        
        $this->_db->setSql("SELECT COUNT(DISTINCT `user_id`) AS `count` FROM `user_stats` WHERE `endtime` > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
        $activePeople = $this->_db->getRow();
        $this->_data['users_online'] = $activePeople['count'];
        
        // projects in 'progress', 'waiting for feedback' and 'testing'
        $this->_db->setSql("SELECT SUM(CASE WHEN `project_status` = 1 THEN 1 ELSE 0 END) `active`, " .
                                  "SUM(CASE WHEN `project_status` = 2 THEN 1 ELSE 0 END) `feedback`, " .
                                  "SUM(CASE WHEN `project_status` = 3 THEN 1 ELSE 0 END) `testing` FROM `projects`");
        $allactiveprojects = $this->_db->getRow();
        
        $this->_data['projects_in_progress'] = $allactiveprojects['active'];
        $this->_data['projects_in_feedback'] = $allactiveprojects['feedback'];
        $this->_data['projects_in_testing'] = $allactiveprojects['testing'];
                
        //total seconds worked
        $this->_db->setSql("SELECT SUM(`secondsonline`) AS `s` FROM `user_stats` WHERE `date` = CURDATE()");
        $secondsOnline = $this->_db->getRow();
        $this->_data['secondsworked'] = $secondsOnline['s'];
        $this->_db->setSql("SELECT SUM(`secondsonline`) AS `s` FROM `user_stats` WHERE `date` = SUBDATE(CURDATE(), 1)");
        $secondsOnlineYesterday = $this->_db->getRow();
        $this->_data['secondsworked_yesterday'] = $secondsOnlineYesterday['s'];
        $this->_data['success'] = 'success';
        $this->createReturn();
    }
    
    /**
     * Stores user settings
     * @return Void.
     */
    public function storeUserSettings() {
        $this->_data['success'] = "";
        $this->_data['errormessage'] = "";
        $success = false;
        if (isset($this->_request['show_stats']) && isset($this->_request['play_sound'])) {
            $tempMessage = $this->_dataHandler->saveUserPreference($this->_request, $this->_user, $this->_err);
            if ($tempMessage) {
                $this->_data['errormessage'] = $tempMessage;
            }
        }
        if (isset($this->_request['username']) && $this->_request['username'] !== $this->_user->getUsername()) {
            if (!$this->_fun->containsNotAllowedValues($this->_request['username'])) {
                $this->_account->changeUsername(filter_var($this->_request['username'], FILTER_SANITIZE_SPECIAL_CHARS));
            } else {
                $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$notAllowedChar);
            }
        }
        if (isset($this->_request['email']) && $this->_request['email'] !== $this->_user->getEmail()) {
            if (!$this->_fun->checkEmail($this->_request['email'])) {
                $this->_account->changeEmail(filter_var($this->_request['email'], FILTER_SANITIZE_EMAIL));
            } else {
                $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$emailNotValid);
            }
        }
        if ($success) {
            $this->_data['success'] = "success";
        }
        $this->createReturn();
    }
    
    /**
     * Accept the uploaded zip file, open it and check it file for file.
     * After that, create a folder and save the file.
     * @return Void.
     */
    public function handleuploadmodule() {
        if (!$this->_user->isSuperAdmin() || !isset($_FILES['to_be_uploaded']) || !$this->_request['add_new_module']) {
            $this->_data['success'] = '';
            $this->_data['errormessage'] = !$this->_user->isSuperAdmin() ? $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed) : $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
            $this->createReturn();
            return;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $allowed = array('application/zip' => "zip", 'application/octet-stream' => "zip");
        if (is_array($_FILES['to_be_uploaded'])) {
            $ext = finfo_file($finfo, $_FILES['to_be_uploaded']['tmp_name']);
            if (!array_key_exists($ext, $allowed)) {
                $this->_data['errorarray'] = ["reason" => "extension " . $ext . " was not allowed", "name" => $_FILES['to_be_uploaded']['name'], 'stopped_zip_reading' => true];
                $this->createReturn();
                return;
            }
            if ($this->unzipTheFilesAndCheck($_FILES['to_be_uploaded']) !== true) {
                $this->createReturn();
                return;
            }
            if (!$this->unzipAndPlaceInFolder($_FILES['to_be_uploaded'])) {
                $this->_data['errorarray'] = ["reason" => "unable to write in module folder/already exists", "name" => $_FILES['to_be_uploaded']['name'], 'stopped_zip_reading' => true];
                $this->createReturn();
                return;
            }
            if (!$this->saveModuleToDb($_FILES['to_be_uploaded'])) {
                $this->_data['errorarray'] = ["reason" => "unable to add it to db/already exists", "name" => $_FILES['to_be_uploaded']['name'], 'stopped_zip_reading' => true];
                $this->createReturn();
                return;
            }
            $this->_data['success'] = "success";
            $this->createReturn();
        }
    }
    
    /** Visibility block **/
    
    /**
     * Create a css file for the visibility of certain inputs.
     * @return Void.
     */
    public function setVisibilityData() {
        if (!$this->_user->isSuperAdmin() || !isset($this->_request['data']) || !isset($this->_request['name'])) {
            $this->_data['success'] = '';
            $this->_data['errormessage'] = !$this->_user->isSuperAdmin() ? $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed) : $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
            $this->createReturn();
            return;
        }
        $arrayForCssFile = array();
        $data = array();
        foreach ($this->_request["data"] as $key => $single) {
            if ($single == "false") {
                $arrayForCssFile[filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS)] = ["display" => "none"];
                $data[$key] = false;
            } else {
                $arrayForCssFile[filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS)] = ["display" => "block"];
                $data[$key] = true;
            }
        }
        $cssMangler = new CssModifier('settings_invisible.css', filter_var($this->_request['name'], FILTER_SANITIZE_STRING), $arrayForCssFile);
        if ($cssMangler->addToCssFile()) {
            $this->saveSettingsToFile($this->_request['name'], $data);
            $this->_data['success'] = "success";
        } else {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$somethingWentWrong);
        }
        $this->createReturn();
    }
    
    /**
     * Remove a person from the company.
     * @param Integer $userId    The id of the user to be removed from the company.
     * @param Integer $companyId The id of the company from which the person needs to be removed.
     * @return Void
     */
    public function removePersonFromCompany($userId, $companyId) {
        if (!is_numeric($userId) || $userId <= 0 || !is_numeric($companyId) || $companyId <= 0) {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
            $this->createReturn();
            return;
        }
        if (!$this->_user->isSuperAdmin() && $this->_user->getId() !== $userId) {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed);
            $this->createReturn();
            return;
        }
        $this->_db->setSql("UPDATE `persons` SET `company_id` = 0 WHERE `id` = ? AND `company_id` = ?;");
        if ($this->_db->updateRecord(array(filter_var($userId, FILTER_SANITIZE_NUMBER_INT), filter_var($companyId, FILTER_SANITIZE_NUMBER_INT)))) {
            $this->_data['success'] = 'success';
        } else {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        }
        $this->createReturn();
    }
    
    /**
     * Change the status of a project to another status.
     * @param Integer $projectId The id of the project.
     * @param Integer $newStatus The id of the new status.
     * @return Void
     */
    public function changeStatusProject($projectId, $newStatus) {
        if (!is_numeric($projectId) || $projectId <= 0 || !is_numeric($newStatus) || $newStatus < 0) {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
            $this->createReturn();
            return;
        }
        if (!$this->_user->isAdmin()) {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$notAllowed);
            $this->createReturn();
            return;
        }
        $this->_db->setSql("SELECT `titel`,`extensie` FROM `project_status` WHERE `type` = ?");
        $data = $this->_db->getRow(array(filter_var($newStatus, FILTER_SANITIZE_NUMBER_INT)));
        $this->_db->setSql("UPDATE `projects` SET `project_status` = ? WHERE `id` = ? AND `deleted` = 0;");
        if ($data && $this->_db->updateRecord(array(filter_var($newStatus, FILTER_SANITIZE_NUMBER_INT), filter_var($projectId, FILTER_SANITIZE_NUMBER_INT)))) {
            $this->_data['success'] = 'success';
            $this->_data['title'] = $data['titel'];
            $this->_data['extension'] = $data['extensie'];
        } else {
            $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        }
        $this->createReturn();
    }
    
    /**
     * Create a json file to get settings if ever the admin goes back to the set visibilty.
     * @param String $name            Name of the target.
     * @param Array  $arrayForCssFile Array of the values.
     * @return Void.
     */
    private function saveSettingsToFile($name, $arrayForCssFile) {
        $file = fopen(ROOT . DS . "settings" . DS . "settings" . DS . $name . ".json", "w");
        fwrite($file, json_encode($arrayForCssFile));
        fclose($file);
        $contents = file_get_contents(ROOT . DS . "config" . DS . "config_ext.php");
        $now = time();
        $contentsNew = preg_replace("/define\(\"CSS_UPDATED\", .*\)/", "define(\"CSS_UPDATED\", \"$now\")", $contents);
        $config = fopen(ROOT . DS . "config" . DS . "config_ext.php", 'w');
        fwrite($config, $contentsNew, strlen($contentsNew));
        fclose($config);
    }
    /** End Visibility block **/
    
    
    /**
     * Unzips the file.
     * @param File $file The file that is attached.
     * @return Boolean true || false.
     */
    private function unzipTheFilesAndCheck($file) {
        $zip = zip_open($file['tmp_name']);
        $nameToCompare = explode('.', $file['name']);
        $this->_data['errorarray'] = array();
        if ($zip) {
            while ($zip_entry = zip_read($zip)) {
                if (zip_entry_open($zip, $zip_entry)) {
                    $folderArr = explode('/', zip_entry_name($zip_entry));
                    if ($folderArr[0] !== $nameToCompare[0]) {
                        $this->_data['errorarray'][] = ["reason" => _("not in folder with correct name: ") .
                        $folderArr[0] . ', must be: ' . $nameToCompare[0], "name" => zip_entry_name($zip_entry), 'stopped_zip_reading' => true];
                    }
                    $contents = zip_entry_read($zip_entry);
                    $error = $this->_fun->checkForInvalidness($contents);
                    if ($error !== false) {
                        echo "Name: " . zip_entry_name($zip_entry) . "<br />";
                        $this->_data['errorarray'][] = ["reason" => $error, "name" => zip_entry_name($zip_entry), 'stopped_zip_reading' => true];
                    }
                    zip_entry_close($zip_entry);
                }
            }
            zip_close($zip);
        }
        return empty($this->_data['errorarray']);
    }
    
    /**
     * Unzips the file and places in folder based on the path.
     * @param File $file The file thats attached.
     * @return Boolean      False if already exists or if open fails.
     */
    private function unzipAndPlaceInFolder($file) {
        $name = explode('.', $file['name'])[0];
        if (file_exists(ROOT . DS . "modules" . DS . $name) && !isset($this->_request['force'])) {
            $this->_data['exists'] = true;
            return false;
        }
        $zip = new ZipArchive();
        if ($zip->open($file['tmp_name']) === true) {
            $zip->extractTo(ROOT . DS . "modules" . DS);
            $zip->close();
            return true;
        }
        return false;
    }
    
    /**
     * Saves the module in the database.
     * @param File $file The file thats attached.
     * @return Boolean      True if adding to database is successful else False
     */
    private function saveModuleToDb($file) {
        $name = explode('.', $file['name'])[0];
        $install = fopen(ROOT . DS . "modules" . DS . $name . DS . "install.php", "r+");
        $this->_db->setSql("SELECT `id` FROM `modules` WHERE `name` = ?");
        $old = $this->_db->getRow(array($name));
        $targetBy = $old ? array("id" => $old['id']) : false;
        $result = array();
        $data = array("name" => $name, 'version' => 1, "description" => "-", "person_id" => $this->_user->getPersonId());
        if (preg_match("~\/\*(\*(?!\/)|[^*])*\*\/~", fread($install, filesize(ROOT . DS . "modules" . DS . $name . DS . "install.php")), $result)) {
            $tempResult = array();
            if (preg_match("~version:.*~", $result[0], $tempResult)) {
                $version = str_replace(array(" ", "version:"), "", trim($tempResult[0]));
                if (preg_match("~[^0-9\.]~", $version) !== 0) {
                    $this->_data['errorarray'][] = ["reason" => "version was not accepted", "name" => $name, 'stopped_zip_reading' => true];
                    return false;
                }
                $data['version'] = $version;
            }
            $tempResult = array();
            if (preg_match("~description:.*~", $result[0], $tempResult)) {
                $data['description'] = htmlspecialchars(trim(str_replace("description:", "", $tempResult[0])));
            }
        }
        return is_numeric($this->_dataHandler->handleInformationToDb('modules', array("name" => "str", "version" => "str", "description" => "str", "person_id" => "int"), array(), $data, $targetBy));
    }
    
    /**
     * Checks if the time is available and saves it.
     * @return Void.
     */
    private function checkIfAllowedAndUpdateTimeUser() {
        $user_id = $this->_user->getId();
        if (isset($this->_request['person_id']) && $this->_request['person_id'] > 0 && $this->_user->isSuperAdmin()) {
            $user_id = $this->_dataHandler->getUserIdByPersonId(filter_var($this->_request['person_id'], FILTER_SANITIZE_NUMBER_INT));
        }
        $this->_db->setSql("SELECT `id` FROM `user_stats` WHERE `id` = ? AND `user_id` = ?");
        $row = $this->_db->getRow(array(filter_var($this->_request['time_id'], FILTER_SANITIZE_NUMBER_INT), $user_id));
        if ($row) {
            $this->_data['row'] = $row;
            $dateStart = new DateTime($this->_request['timedate'] . ' ' . $this->_request['starttime']);
            $StartString = $dateStart->format('Y-m-d H:i:s');
            $endString = (new DateTime($this->_request['timedate'] . ' ' . $this->_request['endtime']))->format('Y-m-d H:i:s');
            $this->_db->setSql("SELECT `id` FROM `user_stats` WHERE ((`starttime` <= ? AND `endtime` > ?) OR (`starttime` < ? AND `endtime` >= ?)) AND `date` = ? AND `user_id` = ? AND `id` != ?");
            $all = $this->_db->getRows(array($StartString, $StartString, $endString,$endString, $dateStart->format('Y-m-d'),$user_id, $row['id']));
            $this->_data['all'] = $all;
            if (!$all) {
                $this->_db->setSql("UPDATE `user_stats` SET `starttime` = ?, `endtime` = ? WHERE `id` = ?");
                $update = $this->_db->updateRecord(array($StartString, $endString, $row['id']));
                if ($update) {
                    $this->_data['success'] = "success";
                    $this->createReturn();
                    return;
                }
            } else {
                $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$timeslotIsTaken);
                $this->createReturn();
                return;
            }
        }
        $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        $this->createReturn(400);
    }
    
    /**
     * Checks if the time is available and saves it.
     * @return Void.
     */
    private function checkIfAllowedAndInsertTimeUser() {
        $user_id = $this->_user->getId();
        if (isset($this->_request['person_id']) && $this->_request['person_id'] > 0 && $this->_user->isSuperAdmin()) {
            $user_id = $this->_dataHandler->getUserIdByPersonId(filter_var($this->_request['person_id'], FILTER_SANITIZE_NUMBER_INT));
        }
        $dateStart = new DateTime($this->_request['timedate'] . ' ' . $this->_request['starttime']);
        $StartString = $dateStart->format('Y-m-d H:i:s');
        $endString = (new DateTime($this->_request['timedate'] . ' ' . $this->_request['endtime']))->format('Y-m-d H:i:s');
        $this->_db->setSql("SELECT `id` FROM `user_stats` WHERE ((`starttime` <= ? AND `endtime` > ?) OR (`starttime` < ? AND `endtime` >= ?)) AND `date` = ? AND `user_id` = ?");
        $all = $this->_db->getRows(array($StartString, $StartString, $endString,$endString, $dateStart->format('Y-m-d'), $user_id));
        if (!$all) {
            $seconds = abs(strtotime($StartString) - strtotime($endString));
            $so =  isset($this->_request['so']) && $this->_request['so'] > 0 ? 1 : 0;
            $this->_db->setSql("INSERT INTO `user_stats` (`date`, `user_id`, `secondsonline`, `starttime`, `endtime`, `project_id`, `ip`, `so`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $update = $this->_db->insertRecord(array($dateStart->format('Y-m-d'),$user_id,$seconds,$StartString,
            $endString,$this->_request['project_id'], $this->_fun->getRealIpAddr(), $so));
            if (is_numeric($update)) {
                $this->_data['success'] = "success";
                $this->createReturn();
                return;
            }
        }
        $this->_data['errormessage'] = $this->_err->get(\SG\Ram\ErrorMessage::$timeslotIsTaken);
        $this->createReturn();
    }
    
    /**
     * Get the active projects in the given year.
     * @param Integer $project_id Id of the project.
     * @return Array                returns an array of results.
     * <TODO> perhaps rewrite it to be more logical...
     */
    private function getActiveProjectsThisYear($project_id = null) {
        if ($project_id > 0) {
            $this->_db->setSql("SELECT `pp`.`project_id` FROM `person_project` AS `pp` "
                    . "LEFT JOIN `persons` AS `p` ON `p`.`id` = `pp`.`person_id` "
                    . "WHERE `p`.`account_id` = ? AND `pp`.`project_id` = ? AND `pp`.`deleted` = 0");
            $results = $this->_db->getRows(array($this->_user->getId(), $project_id));
        } else {
            $this->_db->setSql("SELECT `pp`.`project_id` FROM `person_project` AS `pp` "
                    . "LEFT JOIN `persons` AS `p` ON `p`.`id` = `pp`.`person_id` "
                    . "WHERE `p`.`account_id` = ? AND `pp`.`deleted` = 0");
            $results = $this->_db->getRows(array($this->_user->getId()));
        }
        if ($results) {
            $arr = array();
            foreach ($results as $single) {
                $arr[] = $single['project_id'];
            }
            $string = implode(',', $arr);
            $this->_db->setSql("SELECT `id`, `name`, `project_status`, `created`, `deadline` FROM `projects`
                                WHERE (YEAR(`created`) = YEAR(CURDATE()) OR YEAR(`deadline`) = YEAR(CURDATE())) 
                                AND `project_status` IN (1,2,3,5,6) AND `deleted` = 0 AND `id` IN (" . $string . ")");
            return $this->_db->getRows();
        }
    }
    
    /**
     * Sort the data gotten from the db to the projects, and then by date.
     * @param Array   $rows Data on which to sort
     * @param Boolean $html True if we need to return HTML. default true
     * @return Array            Calls function to create HTML or JSON Arrays
     */
    private function sortByProjectAndDate($rows, $html = true) {
        $perProject = array();
        foreach ($rows as $single) {
            if ($single['secondsonline'] < 700) {
                 continue;
            }
            if ($single['project_id'] < 1) {
                $key = 0;
            } else {
                $key = $single['project_id'];
            }
            $tempDate = new DateTime($single['date']);
            if (isset($perProject[$key]) && isset($perProject[$key]['date'][$tempDate->format('Y-m-d')])) {
                //just add the person + the row
                $perProject[$key]['date'][$tempDate->format('Y-m-d')][] = $single;
            } elseif (isset($perProject[$key])) {
                //add the date and the person
                $perProject[$key]['date'][$tempDate->format('Y-m-d')] = array($single);
            } else {
                //add the entire thing
                $perProject[$key] = array("date" => array());
                $perProject[$key]['date'][$tempDate->format('Y-m-d')] = array($single);
            }
        }
        return $html ? $this->createHtmlForTimesheet($perProject) : $this->setProjectForTimePlain($perProject);
    }
    
    /**
     * Sort the result for our own javascript library.
     * @param Array $perProject contains array.
     * @return Array $perProject returns an array.
     */
    private function setProjectForTimePlain($perProject) {
        foreach ($perProject as $project_id => $project) {
            if ($project_id > 0) {
                $projectAll = new Project($this->_dataHandler->getDataFromRow('projects', $project_id));
                $perProject[$project_id]['project_name'] = $projectAll->getParsedString('getName');
            } else {
                $perProject[$project_id]['project_name'] = _('no project');
            }
            ksort($perProject[$project_id]["date"]);
        }
        return $perProject;
    }
    
    /**
     * Create the HTML tabs for the timsheetjs3.js library.
     * @param Array $data Gets data foreach project => $project.
     * @return Array        Array with events
     */
    private function createHtmlForTimesheet($data) {
        $events = array();
        foreach ($data as $project_id => $project) {
            $olddate = new DateTime('1970-01-01');
            $previous = count($events) - 1;
            $projectAll = new Project_Model($this->_dataHandler->getDataFromRow('projects', $project_id));
            krsort($project["date"]);
            foreach ($project["date"] as $date => $values) {
                $newDate = new DateTime($date);
                if (intVal($newDate->diff($olddate)->format('%a')) <= 1) {
                    $events[$previous]["start_date"]['year'] = $newDate->format('Y');
                    $events[$previous]["start_date"]['month'] = $newDate->format('m');
                    $events[$previous]["start_date"]['day'] = $newDate->format('d');
                    $events[$previous]["text"]["text"] = str_replace('</tbody></table>', '', $events[$previous]["text"]["text"]) . $this->formHtmlPanel($values, $newDate, false);
                    //couple it to the event
                } else {
                    ++$previous;
                    $temp = array();
                    $temp["start_date"]['year'] = $newDate->format('Y');
                    $temp["start_date"]['month'] = $newDate->format('m');
                    $temp["start_date"]['day'] = $newDate->format('d');
                    $temp["start_date"]['hour'] = "";
                    $temp["start_date"]['minute'] = "";
                    $temp["start_date"]['second'] = "";
                    $temp["start_date"]['millisecond'] = "";
                    $temp["start_date"]['format'] = "";
                    $temp["end_date"]['year'] = $newDate->format('Y');
                    $temp["end_date"]['month'] = $newDate->format('m');
                    $temp["end_date"]['day'] = $newDate->format('d');
                    $temp["end_date"]['hour'] = "";
                    $temp["end_date"]['minute'] = "";
                    $temp["end_date"]['second'] = "";
                    $temp["end_date"]['millisecond'] = "";
                    $temp["end_date"]['format'] = "";
                    if ($project_id == 0) {
                        $temp["text"] = array("text" => $this->formHtmlPanel($values, $newDate), "headline" => _("Worked times on no project"));
                    } else {
                        $temp["text"] = array("text" => $this->formHtmlPanel($values, $newDate), "headline" => _("Worked times on ") . $projectAll->getParsedString('getName'));
                    }
                    $temp['unique_id'] = $previous;
                    $events[] = $temp;
                }
                $olddate = $newDate;
            }
        }
        return $events;
    }
    
    /**
     * Create the html tabs for the timsheetjs3.js library.
     * @param Array   $values   Array with values to add to the HTML
     * @param Date    $dateTime Date to add to line
     * @param Boolean $header   Add a table header? if true then yes.
     * @return String           HTML with all the data
     */
    private function formHtmlPanel($values, $dateTime, $header = true) {
        $html = "";
        if ($header) {
            $html .= "<table><thead><tr><th>" . _("person") . "</th><th>" . _("time worked") . "</th><th>" . _("on date") . "</th><th>" . _("from") . "</th><th>" . _("to") . "</th></tr></thead><tbody>";
        }
        foreach ($values as $single) {
            $from = new DateTime($single['starttime']);
            $to = new DateTime($single['endtime']);
            $name = $this->_dataHandler->getDataFromRow('persons', array("account_id" => $single['user_id']), false, array('name'));
            $html .=    '<tr><td>' . $name['name'] . '</td><td>' . $this->getHourMinuteFormat($single['secondsonline']) .
                        '</td><td>' . $dateTime->format('d-m-Y') . '</td><td>' . $from->format("H:i:s") . '</td><td>' . $to->format("H:i:s") . '</td></tr>';
        }
        return $html .= "</tbody></table>";
    }
    
    /**
     * Returns a formatted string made from the provided seconds.
     * @param Integer $seconds Number of seconds to process
     * @return String               Hours +  minutes or only minutes
     */
    private function getHourMinuteFormat($seconds) {
        $hours = $seconds / 3600;
        $fullhours = floor($hours);
        $minutes = round(($hours - $fullhours) * 60);
        return $fullhours > 0 ? $fullhours . _(" hour and ") . $minutes . _(" minutes") :  $minutes . _(" minutes");
    }
    
    /**
     * Check if directory exists and if not create it.
     * @param Integer $id Id of the directory to create.
     * @return Boolean      True if exists or could create.
     */
    private function checkIfMapExistsElseMakeIt($id) {
        if (!file_exists(ROOT . DS . 'uploads' . DS . 'files' . DS . $id . '/')) {
            return mkdir(ROOT . DS . 'uploads' . DS . 'files' . DS . $id . '/');
        }
        return true;
    }
    
    /**
     * Filters the values.
     * @param  String $value Filters the string and makes it utf8.
     * @param  String $key   Column name when image or logo we do not want to transform the path.
     * @return Void.
     */
    private function filterValue(&$value, $key) {
        if ($key !== 'logo' && $key !== 'image') {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Change the current data to the new table information.
     * @return Void;
     */
    public function handleDataSenG() {
        $tables = array("companies", "persons");
        foreach ($tables as $table) {
            $this->_db->setSql("SELECT * FROM `$table`");
            $rows = $this->_db->getRows();
            foreach ($rows as $singleRow) {
                $this->handleAddressRowSenG($singleRow, $table);
            }
        }
    }
    
    /**
     * Handle the information of the row.
     * @param array  $row   The row gotten from the server.
     * @param String $table The name of the current table.
     * @return void
     */
    private function handleAddressRowSenG($row, $table) {
        $updateItems = array();
        $toUpdate = array();
        if (isset($row['place']) && isset($row['city']) && $row['place'] !== "" && $row['city'] === "") {
            $updateItems['city'] = $row['place'];
            $toUpdate['city'] = "str";
        }
        if (isset($row['address']) && isset($row['street']) && $row['address'] !== "" && $row['street'] === "") {
            $resultAddress = $this->handleAddress($row['address']);
            $updateItems['street'] = $resultAddress['street'];
            $updateItems['number'] = $resultAddress['number'];
            $toUpdate['street'] = 'str';
            $toUpdate['number'] = 'str';
        }
        if (isset($row['postcode']) && isset($row['postalcode']) && $row['postcode'] !== "" && $row['postalcode'] === "") {
            $updateItems['postalcode'] = $row['postcode'];
            $toUpdate['postalcode'] = "str";
        }
        $this->_dataHandler->handleInformationToDb($table, $toUpdate, array(), $updateItems, array("id" => $row['id']));
    }
    
    /**
     * Split the street to the correct format.
     * @param String $address The current address containing street and number (and addendum)
     * @return Array
     */
    private function handleAddress($address) {
        $temp = explode(' ', trim($address));
        $tempNumber = end($temp);
        if (is_numeric($tempNumber) || (strlen($tempNumber) > 1 && preg_match('~\d+[A-Za-z]?~', $tempNumber))) {
            array_pop($temp);
        } elseif (strlen($tempNumber) === 1 && preg_match('~[A-Za-z]~', $tempNumber) && is_numeric($temp[count($temp) - 2])) {
            $tempNumber = $temp[count($temp) - 2] . $tempNumber;
            array_pop($temp);
            array_pop($temp);
        } else {
            $tempNumber = "";
        }
        return array("street" => implode(" ", $temp), "number" => $tempNumber);
    }
}
