<?php

/**
 * Usermanagement Controller.
 * To add, edit, delete Usermanagement or to add new functions for the Usermanagement page.
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
 * @uses       \SG\Ram\controller       Extend the main controller.
 * @uses       \SG\Ram\dataHandler      Data handler.
 * @uses       \SG\Ram\functions        General functions class.
 * @uses       \SG\Ram\Models\Dbhelper  Database helper functions.
 * @uses       \SG\Ram\Models\User      User object.
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
use SG\Ram\dataHandler;
use SG\Ram\Models\Dbhelper;
use SG\Ram\Models\User;

/**
 * UsermanagementController
 * @category   Controllers
 * @package    Ram
 */
class UsermanagementController extends controller
{
    private $_db;
    private $_account;
    private $_user;
    private $_datahandler;

     /**
     * Constructor: __construct.
     * Assemble and pre-process the data.
     * @return  Void.
     */
    public function __construct() {
        parent::__construct();
        global $user;
        $this->_account = $user;
        $this->_user = $user->getUser();
        if (!$this->_user->isSuperAdmin()) {
            header("refresh:0;url=" . SITE_ROOT . "dashboard/");
            die();
        }
        $this->_db = new Dbhelper();
        $this->_datahandler = new dataHandler();
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_datahandler);
        unset($this->_db); //dbhelper
        unset($this->_user);
        unset($this->_account);
    }

    /**
     * Loads the index page.
     * @return Void.
     */
    public function index() {
        $this->LoadViewer(
            "usermanagement",
            "index",
            "usermanagement",
            array("bootstrap-table.min", "bootstrap-table-filter-control.min", "dashboard", "settings_invisible","sg_confirm"),
            array("view_settings", "bootstrap-table.min", "backend", "bootstrap-table-export.min", "bootstrap-table-cookie.min", "tableExport.min","sg_confirm"),
            true
        );
    }
    
    /**
     * Loads JSON data in to the bootstrap data table.
     * @return Void.
     */
    public function json() {
        $all = array();
        $results = $this->parseJSONrequest($this->getSearchArray(), $this->_db, "user_accounts");
        $count = $results["count"];
        $resultarray = $results["data"];

        $this->_db->setSql("SELECT `id` FROM `persons` WHERE `account_id` = ? AND `deleted` = 0");
        foreach ($resultarray as $row) {
            $personId = $this->_db->getRow(array($row['id']));
            if ($personId) {
                $row['person_ignore'] = "<a href='" . SITE_ROOT . "person/overview/" . $personId['id'] .
                                            "' alt='overview person' target='_blank' class='edit-delete-button-table'>" .
                                            "<i class='fas fa-eye'></i>" .
                                        "</a>";
            }
            $row['active'] = $row['active'] < 1 ? "<div class='activeCircle'><i class='fas fa-times-circle'></i></div>" : "<div class='activeCircle'><i class='fas fa-check-circle'></i></div>";
            
            if (isset($row['admin'])) {
                $row['admin'] = $this->getAdminName($row['admin']);
            }
            $row['edit'] = "<a href='" . SITE_ROOT . "usermanagement/edit/" . $row['id'] . "' alt='edit delete button' class='edit-delete-button-table'>" .
                                    "<i class='fas fa-pencil-alt'></i>" .
                            "</a>" .
                            "<a class='delete btn-sm btn remove_this edit-delete-button-table' alt='delete user' data-confirm='" . _('Are you sure you want to delete this person?') .
                            "' data-target-id='" . $row['id'] . "' data-target-string='user' data-callback='callbackRemove'><i class='fas fa-trash'></i></a>";
            $this->_fun->morphToIconLinks($row);
            $all[] = $row;
        }
        echo '{"total":' . $count['count'] . ',' . '"rows":' . json_encode($all) . '}';
    }
    
    /**
     * Adds a user.
     * @return Void     Header with successmessage.
     */
    public function add() {
        if (isset($_POST['add'])) {
            $returnvalues = $this->checkAndSaveData($_POST);
            if ($returnvalues === true) {
                $this->Assign("successmessage", _('Person successfully added'));
                return $this->index();
            }
            if (is_array($returnvalues)) {
                $this->Assign("errors", $returnvalues['errors']);
                $this->Assign("errormessage", $returnvalues['errormessage']);
            } elseif (is_string($returnvalues)) {
                $this->Assign("errormessage", $returnvalues);
            } else {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$somethingWentWrong));
            }
            $this->Assign("oldData", $_POST);
        }
        $this->LoadViewer("usermanagement", "edit_add", "usermanagement", array("dashboard", "formlayout"), array("backend"), true);
    }
    
    /**
     * Edit the user information.
     * @param Integer $id User id.
     * @return Void         LoadViewer | loads index page.
     */
    public function edit($id) {
        if (is_numeric($id) && $id > 0) {
            if (isset($_POST['add'])) {
                $returnvalues = $this->checkAndSaveData($_POST);
                if ($returnvalues === true) {
                    $this->Assign("successmessage", _('Person successfully edited'));
                } elseif (is_array($returnvalues)) {
                    $this->Assign("errors", $returnvalues['errors']);
                    $this->Assign("errormessage", $returnvalues['errormessage']);
                } elseif (is_string($returnvalues)) {
                    $this->Assign("errormessage", $returnvalues);
                } else {
                    $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$somethingWentWrong));
                }
            }
            if (!$this->getDataUser($id)) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
                return $this->index();
            }
            $this->Assign("edit", true);
            return $this->LoadViewer("usermanagement", "edit_add", "usermanagement", array("dashboard", "formlayout"), array("backend"), true);
        }
        $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
        $this->index();
    }
    
    /**
     * Loads information based on the id
     * @param   Integer $id User id
     * @return  Void        LoadViewer | loads index page.
     */
    public function overview($id) {
        $couples = array();
        if (is_numeric($id) && $id > 0) {
            if (!$this->getDataUser($id, true)) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
                return $this->index();
            }
            
            //get coupled person id
            $getCoupledPerson = $this->_datahandler->getDataFromRows("persons", array("id" => $id), true, array("id"));
            foreach ($getCoupledPerson as $person) {
                $coupled = $this->_datahandler->getDataFromRow("persons", array("id" => $person['id']), true, array("*"));
                if ($coupled) {
                    $couples[] = new \SG\Ram\Models\Person($coupled);
                }
            }
            $this->assign("allCoupledPerson", $couples);
            return $this->LoadViewer("usermanagement", "overzicht", "usermanagement", array("dashboard","formlayout"), array("backend"), true);
        }
        $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
        $this->index();
    }
    
    /**
     * Search array options for the JSON().
     * @return Void.
     */
    private function getSearchArray() {
        return array("username", "email", "fullname", "joined");
    }
    
    /**
     * check the data posted and save it if acceptabel.
     * @param array $data data to check and save.
     * @return Boolean | String | Array     True || errormesaage || array(errors , errormessage).
     */
    private function checkAndSaveData($data) {
        $this->_fun->filterDataToNormal($data);
        $req = array("username" => "str", "email" => "em", "fullname" => "str");
        $optAll = array('admin' => "int", "person_id" => "int");
        $data = $this->_fun->filterVarData($data, $req, $optAll);
        $check = $this->_fun->checkValuesValidity($req, $data);
        if (is_array($check)) {
            return $check;
        }
        $edit = false;
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $edit = array("id" => $data['id']);
            $check2 = $this->editCheck($data);
            if ($check2 !== true) {
                header("refresh:0; url=" . SITE_ROOT . 'usermanagement/edit/' . $data['id'] . "/?errormessage=" . urlencode(_('Sorry, the email already used.')));
                return true;
            }
            $result = $this->_datahandler->handleInformationToDb("user_accounts", $req, $optAll, $data, $edit);
            if ($result === true && isset($data['person_id']) && is_numeric($data['person_id']) && $data['person_id'] > 0) {
                $this->coupleUserAccountToPerson($data['id'], $data['person_id']);
                return true;
            }
        } else {
            $id = $this->_account->registerAnotherUser($data['username'], $data['email'], $data['admin'], $data);
            if (is_numeric($id) && isset($data['person_id']) && is_numeric($data['person_id']) && $data['person_id'] > 0) {
                $this->coupleUserAccountToPerson($id, $data['person_id']);
            }
            return true;
        }
        return false;
    }
    
    /**
     * Gets the informattion of the user with id.
     * @param   Integer $id      contains id.
     * @param   Boolean $asModel true || false.
     * @return  Boolean
     */
    private function getDataUser($id, $asModel = false) {
        $this->_db->setSql("SELECT `id`, `username`, `email`, `fullname`, `admin`, `active`, `joined` FROM `user_accounts` WHERE `id` = ?");
        $user = $this->_db->getRow(array($id));
        if ($user) {
            if ($user['active'] === 0) {
                $this->Assign("errormessage", _('This person is') . $this->_err->get(\SG\Ram\ErrorMessage::$notActive));
            }
            $person = $this->getCoupledPerson($id);
            if ($person) {
                $user['person_name'] = $person['name'];
                $user['person_id'] = $person['id'];
            }
            if ($asModel) {
                $newUser = new User();
                $newUser->setMinimum($user);
                $this->Assign("oldData", $newUser);
            } else {
                $this->_fun->extendArrayWithRandomNumber($user);
                $this->Assign("oldData", $user);
            }
            return true;
        }
        return false;
    }
    
    /**
     * Get coupled person.
     * @param Integer $id contains id.
     * @return Array returns an array with results.
     */
    private function getCoupledPerson($id) {
        $this->_db->setSql("SELECT `id`, `name` FROM `persons` WHERE `account_id` = ? AND `deleted` = 0");
        return $this->_db->getRow(array($id));
    }
    
    /**
     * Connect user with person
     * @param Integer $userId   User id.
     * @param Integer $personId Person id.
     * @return Boolean              True on success.
     */
    private function coupleUserAccountToPerson($userId, $personId) {
        $this->_db->setSql("SELECT `account_id` FROM `persons` WHERE `id` = ?");
        $accountId = $this->_db->getRow(array($personId));
        $this->_db->setSql("SELECT `id` FROM `persons` WHERE `account_id` = ?");
        $personIdGotten = $this->_db->getRow(array($userId));
        //person with account_id exists already
        if ($personIdGotten && $personIdGotten['id'] == $personId) {
            //we assume it is the same in both tables.
            return true;
        } elseif ($personIdGotten && $personIdGotten['id'] != $personId) {
            $this->_db->setSql("UPDATE `persons` SET `account_id` = ? WHERE `id` = ?");
            $this->_db->updateRecord(array(0, $personIdGotten['id']));
        }
        if ($accountId && $accountId['account_id'] <= 0 || $accountId['account_id'] != $userId) {
            $this->_db->setSql("UPDATE `persons` SET `account_id` = ? WHERE `id` = ?");
            return $this->_db->updateRecord(array($userId, $personId));
        }
        return false;
    }
    
    /**
     * Checks if the email already exist
     * @param Array $data an array with information.
     * @return Boolean true || false.
     */
    private function editCheck($data) {
        $id = $data['id'];
        $email = $data['email'];
        $this->_db->setSql("SELECT `id`, `email` FROM `user_accounts` WHERE `email` = ? OR `id` = ?");
        $results = $this->_db->getRows(array($email, $id));
        if ($results && count($results) > 1) {
            return _('email already used.');
        }
        if ($data['admin'] > $this->_user->getAdmin()) {
            return _('Admin level is to low to add a person with this admin level.');
        }
        return true;
    }
    
    /**
     * Get admin name
     * @param   Integer $level Code of admin level.
     * @return  Array       $arrayNames Contains an array with names.
     */
    private function getAdminName($level) {
        $arrayNames = array("<span class=\"hidden\">0</span>" . _('None'), "<span class=\"hidden\">1</span>" . _('Basic employee'), "<span class=\"hidden\">2</span>" .
        _('Admin'), "<span class=\"hidden\">3</span>" . _('High level admin'));
        return $arrayNames[intVal($level)];
    }
}
