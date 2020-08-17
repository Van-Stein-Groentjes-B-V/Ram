<?php

/**
 * Person Controller.
 * To Add, edit, delete persons or to add new functions for the person page
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
 * @uses       \SG\Ram\Controller           Extend the main controller.
 * @uses       \SG\Ram\dataHandler          Data handler class.
 * @uses       \SG\Ram\functions            General functions class.
 * @uses       \SG\Ram\Models\Person        Person object
 * @uses       \SG\Ram\Models\Dbhelper      Database helper class
 * @uses       \SG\Ram\StatusTypes          Different Status types
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
use SG\Ram\Models\Person;
use SG\Ram\Models\Dbhelper;
use SG\Ram\StatusTypes;

/**
 * PersonController
 * @category   Controllers
 * @package    Ram
 */
class PersonController extends Controller
{
    private $_db;
    private $_account;
    private $_user;
    private $_dataHandler;
    private $_statusArray;
    
    /**
     * Constructor: __construct
     * Assemble and pre-process the data.
     * Handles also the success message.
     * @return  Void.
     */
    public function __construct() {
        parent::__construct();
        global $user;
        $this->_account = $user;
        $this->_user = $user->getUser();
        $this->_db = new Dbhelper();
        $this->_dataHandler = new dataHandler();
        $this->_statusArray = StatusTypes::$statusTypesArray;
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_statusArray);
        unset($this->_db); // db helper
        unset($this->_dataHandler);
        unset($this->_user);
        unset($this->_account);
    }

    /**
     * Assignes css and js files from the settings.
     * Loads the view content with the js and css files that are included in the person page.
     * @return Void.
     */
    public function index() {
        $this->assign('settings', $this->_fun->getJsonFromFile('persons_css', 'settings', 'settings'));
        $this->handleMessages();
        if ($this->_user->isCustomer() && $this->_user->getPersonId()) {
            $this->overview($this->_user->getPersonId());
            return;
        }
        $this->LoadViewer(
            "persons",
            "index",
            "person",
            array("bootstrap-table.min","bootstrap-table-filter-control.min", "dashboard", "settings_invisible","sg_confirm"),
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
        if ($this->_user->isCustomer()) {
            echo '{"total":0,"rows":[]}';
            return;
        }
        $results = $this->parseJSONrequest($this->getSearchArray(), $this->_db, "persons");
        $count = $results["count"];
        $resultarray = $results["data"];
        foreach ($resultarray as $row) {
            $logo = "no_avatar.jpg";
            if (file_exists("img/person/logos/" . $row['logo']) && $row['logo']) {
                $logo = $row['logo'];
            }
            $row['name'] = "<span style='display:none'>" . $row['name'] . "</span><img class='small-logo-person' src='" . SITE_ROOT . "public/img/person/logos/" .
                            $logo . "' alt='person logo'/>" . "&emsp;" . $row['name'];
            if ($this->_user->isAdmin() || $this->_user->getId() == $row['account_id']) {
                $row['edit'] =  "<a href='" . SITE_ROOT . "person/edit/" . $row['id'] . "' alt='edit person' class='edit-delete-button-table'>" .
                                    "<i class='fas fa-pencil-alt'></i>" .
                                "</a>";
                if ($this->_user->isAdmin()) {
                    $row['edit'] .= "<a class='delete remove_this edit-delete-button-table' alt='delete person' data-confirm='" . _('Are you sure you want to delete this person?') .
                    "' data-target-id='" . $row['id'] . "' data-target-string='person' data-callback='callbackRemove'><i class='fas fa-trash'></i></a>";
                }
            }
            $this->_fun->morphToIconLinks($row);
            $all[] = $row;
        }
        echo '{"total":' . $count['count'] . ',' . '"rows":' . json_encode($all) . '}';
    }
    
    /**
     * Helper function to save the data of the person.
     * @param integer $id To check,for the checkForDouble function, if it's for the edit so that the user can edit.
     * @return Void.
     */
    private function handleSave($id = null) {
        if (isset($_POST['add'])) {
            if (isset($_POST['email']) && $this->_dataHandler->checkForDouble("persons", "email", filter_var($_POST['email'], FILTER_SANITIZE_EMAIL), $id)) {
                $this->Assign("oldData", $_POST);
                $this->Assign("errormessage", _('Sorry, the email that you filled in is already in use.'));
                return;
            }
            $returnvalues = $this->checkAndSaveData($_POST);
            if ($returnvalues === true) {
                if ($id) {
                    header("refresh:0; url=" . SITE_ROOT . 'person/edit/' . $id . '/?successmessage=' . urlencode(_('Person successfully updated.')));
                } else {
                    header("refresh:0; url=" . SITE_ROOT . 'person/index/?successmessage=' . urlencode(_('Person successfully created.')));
                }
                die();
            } elseif (is_array($returnvalues)) {
                $this->Assign("errors", $returnvalues['errors']);
                $this->Assign("errormessage", $returnvalues['errormessage']);
            } else {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$somethingWentWrong));
                $this->Assign("oldData", $_POST);
            }
            $this->Assign("oldData", $_POST);
        }
    }
    
    /**
     * Adds a person.
     * @param int $id The id of the record being added.
     * @return Void     Header with successmessage.
     */
    public function add($id = -1) {
        $this->handleMessages();
        $this->handleSave();
        if ($id > 0) {
            $company = $this->_dataHandler->getDataFromRow("companies", array("id" => $id), false, array("name"));
            $this->Assign("selectedCompany", $company);
        }
        $this->assign('settings', $this->_fun->getJsonFromFile('persons_css', 'settings', 'settings'));
        $this->LoadViewer("persons", "edit_add", 'person', array("dashboard", "settings_invisible", "formlayout"), array("view_settings", "backend"), true);
    }
    
    /**
     * Edit the person information.
     * @param Integer $id Contains person id of person to be edited.
     * @return Void.
     */
    public function edit($id) {
        $this->handleMessages();
        if (!is_numeric($id) || $id <= 0 || !$this->getDataPerson($id)) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
            return $this->index();
        }
        $person = $this->_dataHandler->getDataFromRow('persons', filter_var($id, FILTER_SANITIZE_NUMBER_INT), true, false, "\\SG\\Ram\\Models\\Person");
        if (!$this->_user->isSuperAdmin() && $person->getId() !== $this->_user->getPersonId() && $person->getCompanyId() > 0) {
            $isOwned = $this->_dataHandler->getDataFromRow('companies', $person->getCompanyId(), true, array("owned"));
            if ($isOwned && $isOwned['owned'] === "1") {
                header("refresh:0; url=" . SITE_ROOT . 'person/index/?errormessage=' . urlencode(_('You are not allowed to change this user.')));
                die();
            }
        }
        $this->Assign("edit", true);
        $this->handleSave($id);
        $this->assign('settings', $this->_fun->getJsonFromFile('persons_css', 'settings', 'settings'));
        $this->LoadViewer("persons", "edit_add", 'person', array("dashboard", "settings_invisible", "formlayout"), array("view_settings", "backend"), true);
    }
    
    /**
     * Loads information based on the id.
     * @param Integer $id Contains id of person for overview.
     * @return Void.
     */
    public function overview($id) {
        $this->handleMessages();
        if (is_numeric($id) && $id > 0) {
            if (!$this->getDataPerson($id, true)) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
                return $this->index();
            }
            
            // Get involved projects
            $getProject = $this->_dataHandler->getDataFromRows("person_project", array("person_id" => $id), true, array("project_id"));
            $allProjects = array();
            foreach ($getProject as $project) {
                $involved = $this->_dataHandler->getDataFromRow("projects", array("id" => $project['project_id']), false, array("*"));
                if ($involved && $involved['deleted'] != '1' && in_array($involved['project_status'], $this->_statusArray)) {
                    $allProjects[] = $this->_dataHandler->getDataFromRow("projects", array("id" => $project['project_id']), true, array("*"));
                }
            }
            
            $linkedUser = $this->getLinkedUser($id);
            $this->assign("linkedUser", $linkedUser);
            $this->assign("allProjects", $allProjects);
            $this->assign('settings', $this->_fun->getJsonFromFile('persons_css', 'settings', 'settings'));
            return $this->LoadViewer("persons", "overzicht", 'person', array("dashboard", "settings_invisible", "formlayout"), array("view_settings", "backend"), true);
        }
        $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
        $this->index();
    }
    
    /**
     * Set visibility of the inputs.
     * @return Void.        Index page or the person page.
     */
    public function setVisibility() {
        if (!$this->_user->isSuperAdmin()) {
            return $this->index();
        }
        $this->Assign('settings', $this->_fun->getJsonFromFile('persons_css', 'settings', 'settings'));
        return $this->LoadViewer("persons", "visibility", 'person', array("dashboard","formlayout", "settings_invisible", "visibilityStyling"), array("view_settings", "backend"), true);
    }
    
    /**
     * An array that is used for the search function in JSON().
     * @return Array    An array with search options.
     */
    private function getSearchArray() {
        return array('name', 'company', 'street', 'postalcode', 'email', 'city', 'country', 'title');
    }
    
    /**
     * Get the data from a single company.
     * @param   Integer $id      Id from target person.
     * @param   Boolean $asModel Set it as Model.
     * @sets    Messages AND data.
     * @return  Boolean      True on found person
     */
    private function getDataPerson($id, $asModel = false) {
        $this->_db->setSql("SELECT * FROM `persons` WHERE `id` = ?");
        $person = $this->_db->getRow(array($id));
        if ($person) {
            if ($person['deleted'] === 1) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$personDeleted));
            }
            if ($asModel) {
                $this->Assign("oldData", new Person($person));
            } else {
                $this->_fun->extendArrayWithRandomNumber($person);
                $this->Assign("oldData", $person);
            }
            return true;
        }
        return false;
    }
    
    /**
     * Check the data posted, and save it, if acceptable.
     * @param Array $data Data to check and save.
     * @return Boolean | String | Array     True if person added, false if image upload failed, error message of Array of errors
     */
    private function checkAndSaveData($data) {
        $this->_fun->filterDataToNormal($data);
        $req = array("name" => "str", "email" => "em");
        $opt = array("title" => "str",
                     "street" => "str",
                     "company" => "str",
                     "company_id" => "int",
                     "postalcode" => "str",
                     "city" => "str",
                     "number" => "str",
                     "country" => "str",
                     "website" => "str",
                     "facebook" => "str",
                     "twitter" => "str",
                     "youtube" => "str",
                     "linkedin" => "str",
                     "tel" => 'str',
                     "notes" => "str");
        $edit = false;
        $data = $this->_fun->filterVarData($data, $req, $opt);
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $edit = array("id" => $data['id']);
        }
        $result = $this->_dataHandler->handleInformationToDb("persons", $req, $opt, $data, $edit);
        if ($result === true || is_numeric($result)) {
            if (is_numeric($result) && $result > 0) {
                $this->createAccountForUser($result, $data);
            }
            if (isset($_FILES['file']) && strlen($_FILES['file']['tmp_name']) > 1) {
                $id = is_numeric($result) ? $result : $data['id'];
                return $this->checkAndSaveLogo($id);
            }
            return true;
        }
        return $result;
    }
    
    /**
     * Creates account for user.
     * @param String $person_id Contains person related id.
     * @param Array  $data      Array with data.
     * @return Boolean          True if update succeeded
     */
    private function createAccountForUser($person_id, $data) {
        $name = $data['name'];
        $email = $data['email'];
        if (NO_ACCOUNTS_FOR_CUSTOMERS && !$this->isCompanyOwned($data['company_id'])) {
            return false;
        }
        $adminLvl = intval($this->isCompanyOwned($data['company_id'])) === 1;
        $result = $this->_account->registerAnotherUser($email, $email, $adminLvl, array('fullname' => $name));
        if (is_numeric($result)) {
            $this->_db->setSql("UPDATE `persons` SET `account_id` = ? WHERE `id` = ?");
            return $this->_db->updateRecord(array($result, $person_id));
        } else {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$createAccountPersonError));
            return false;
        }
    }
    
    /**
     * Gets the companies that are owned by the id.
     * @param Integer $id Id of the person.
     * @return Boolean          True if company is property of the person
     */
    private function isCompanyOwned($id) {
        $this->_db->setSql("SELECT `id` FROM `companies` WHERE `id` = ? and `owned` = 1");
        $result = $this->_db->getRow(array($id));
        return !$result == null;
    }
    
    /**
     * Check and save image.
     * @param Integer $id Person id.
     * @return Boolean | Array  True if update succeeded, else false or array (errors,  errormessage).
     */
    private function checkAndSaveLogo($id) {
        $resultImgUpload = $this->_fun->HandleImage($_FILES, "file", "person/logos");
        if (is_array($resultImgUpload)) {
            $this->_db->setSql("UPDATE `persons` SET `logo` = ? WHERE `id` = ?");
            $temp = $this->_db->updateRecord(array($resultImgUpload['newname'], $id));
            $this->assign("newlogo", $resultImgUpload['newname']);
            return $temp;
        }
        return array("errormessage" => $resultImgUpload);
    }
    
    /**
     * Get the user that's linked to a person.
     * @param Integer $id Person id to check if has account.
     * @return User | Boolean   User model of person that is linked to user account. If no user is linked than false.
     */
    public function getLinkedUser($id) {
        // Get the user account_id from person, from the db table person.
        $linkedUserId = $this->_dataHandler->getDataFromRow("persons", $id, false, array("account_id"));
        $coupled = false;
        if ($linkedUserId) {
            $coupled = $this->_dataHandler->getDataFromRow("user_accounts", $linkedUserId['account_id'], false, array("*"));
        }
        return $coupled ? new \SG\Ram\Models\User($coupled) : $coupled;
    }
}
