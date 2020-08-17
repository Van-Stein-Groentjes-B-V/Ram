<?php

/**
 * Companies Controller.
 * To Add, edit, delete companies or to add new functions for the companie page
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
 * @uses       \SG\Ram\Controller       Extend the main controller.
 * @uses       \SG\Ram\dataHandler      Datahandler class.
 * @uses       \SG\Ram\Models\Dbhelper  Class that does the queries.
 * @uses       \SG\Ram\Models\Company   The company class.
 * @uses       \SG\Ram\functions        General functions.
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
use SG\Ram\Models\Company;

/**
 * CompaniesController
 * @category   Controllers
 * @package    Ram
 */
class CompaniesController extends controller
{
    private $_db;
    private $_account;
    private $_user;
    private $_dataHandler;
    
    /**
     * Constructor: __construct
     * Assemble and pre-process the data.
     * @return  Void.
     */
    public function __construct() {
        parent::__construct();
        global $user;
        $this->_account = $user;
        $this->_user = $user->getUser();
        $this->_db = new Dbhelper();
        $this->_dataHandler = new dataHandler();
        if ($this->_user->getAdmin() <= 0) {
            header("refresh:0; url=" . SITE_ROOT . "dashboard/index/?errormessage=" . urlencode(_("You are not allowed here.")));
            die();
        }
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_user);
        unset($this->_dataHandler);
        unset($this->_account);
    }
    
    /**
     * Assigns css and js files from the settings.
     * Loads the view content with the js and css files that are included in the company page.
     * @return Void.
     */
    public function index() {
        $this->handleMessages();
        $this->Assign('settings', $this->_fun->getJsonFromFile('companies_css', 'settings', 'settings'));
        $this->LoadViewer(
            "companies",
            "index",
            'companies',
            array("bootstrap-table.min","bootstrap-table-filter-control.min", "dashboard", "settings_invisible", "sg_confirm"),
            array("view_settings", "bootstrap-table.min", "backend", "bootstrap-table-export.min", "bootstrap-table-cookie.min", "tableExport.min", "sg_confirm"),
            true
        );
    }
    
    /**
     * Loads JSON data in to the bootstrap data table.
     * @return Void.
     */
    public function json() {
        $all = array();
        $settings = $this->_fun->getJsonFromFile('companies_css', 'settings', 'settings');
        $results = $this->parseJSONRequestCompanies($settings);
        //might want to use this to get which things should be gotten from db
        //for now it is not neccessary
        $count = $results["count"];
        $resultarray = $results["all"];
        foreach ($resultarray as $row) {
            $this->_fun->morphToIconLinks($row);
            $logo = "no_avatar.jpg";
            if ($row['logo'] && file_exists(ROOT . DS . "public" . DS . "img" . DS . "company" . DS . "logos" . DS . $row['logo'])) {
                $logo = $row['logo'];
            }
            $row['name'] = "<span style='display:none'>" . $row['name'] . "</span><img class='small-logo-company' src='" . SITE_ROOT . "public/img/company/logos/" .
                            $logo . "' alt='company logo'/>" . "&emsp;" . $row['name'];
            if ($this->_user->isAdmin()) {
                $row['edit'] = "<a href='" . SITE_ROOT . "companies/edit/" . $row['id'] . "' alt='edit company' class='edit-delete-button-table'>" .
                                    "<i class='fas fa-pencil-alt'></i>" .
                            "</a>" .
                            "<a class='delete remove_this edit-delete-button-table' alt='delete company' data-confirm='" . _('Are you sure you want to delete this company?') .
                            "' data-target-id='" . $row['id'] . "' data-target-string='company' data-callback='callbackRemove'><i class='fas fa-trash'></i></a>";
            }
            $all[] = $row;
        }
        echo '{"total":' . $count . ',' . '"rows":' . json_encode($all) . '}';
    }
    
    /**
     * Adds a company.
     * @return Void.
     */
    public function addCompany() {
        if (!$this->_user->isSuperadmin()) {
            return $this->index();
        }
        $this->handleMessages();
        $this->handleSave();
        $this->Assign('settings', $this->_fun->getJsonFromFile('companies_css', 'settings', 'settings'));
        $this->LoadViewer("companies", "edit_add", 'companies', array("dashboard", "settings_invisible", "formlayout"), array("view_settings", "backend"), true);
    }
    
    /**
     * Edit company information.
     * @param Integer $id Company identifier.
     * @return Void.
     */
    public function edit($id) {
        if (!$this->_user->isSuperadmin()) {
            return $this->index();
        }
        $this->handleMessages();
        if (!is_numeric($id) || $id <= 0 || ($id > 0 && !$this->getDataCompany($id))) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
            $this->index();
            return;
        }
        $this->Assign("edit", true);
        $this->handleSave($id);
        
        $this->Assign('settings', $this->_fun->getJsonFromFile('companies_css', 'settings', 'settings'));
        $this->LoadViewer("companies", "edit_add", 'companies', array("dashboard", "settings_invisible", "formlayout"), array("view_settings", "backend"), true);
    }
    
    /**
     * Loads company information based on the company id.
     * @param Integer $id Identifier of the company from which we want to see the overview
     * @return Void.
     */
    public function overview($id) {
        $this->handleMessages();
        if (is_numeric($id) && $id > 0) {
            if (!$this->getDataCompany($id, true)) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
                $this->index();
                return;
            }
            $this->Assign("involved", $this->getCompanyInvolvedProject($id));
            $this->Assign("allCompanyPersons", $this->getCompanyPersons($id));
            $this->Assign('settings', $this->_fun->getJsonFromFile('companies_css', 'settings', 'settings'));
            $this->LoadViewer("companies", "overzicht", 'companies', array("dashboard", "settings_invisible", "formlayout","companiesoverview", "sg_confirm"), array("view_settings", "backend","sg_confirm"), true);
            return;
        }
        $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$pageDoesNotExists));
        $this->index();
    }
    
    /**
     * Set visibility of the inputs.
     * @return Void.
     */
    public function setVisibility() {
        if (!$this->_user->isSuperAdmin()) {
            $this->index();
            return;
        }
        $this->Assign("settings", $this->_fun->getJsonFromFile("companies_css", "settings", "settings"));
        $this->LoadViewer("companies", "visibility", "companies", array("dashboard", "settings_invisible", "visibilityStyling", "formlayout"), array("view_settings", "backend"), true);
    }

    /**
     * Get data from a single company.
     * @param Integer $id      Identifier of the target company.
     * @param Boolean $asModel Return the data as a Company Model.
     * @return Boolean          True on success else false.
     */
    private function getDataCompany($id, $asModel = false) {
        $this->_db->setSql("SELECT * FROM `companies` WHERE `id` = ?");
        $company = $this->_db->getRow(array($id));
        if ($company) {
            if ($company['deleted'] === 1) {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$compamyDeleted));
            }
            if ($asModel) {
                $this->Assign("oldData", new Company($company));
            } else {
                $this->Assign("oldData", $company);
            }
            return true;
        }
        return false;
    }
    
    /**
     * Check the data posted and save it if acceptable.
     * @param Array $data Data to check and save
     * @return Boolean | Array  True on success else (errors, errormessage).
     */
    private function checkAndSaveData($data) {
        $req = array("name" => "str");
        $opt = array("street" => "str",
                    "postalcode" => "str",
                    "number" => "str",
                    "city" => "str",
                    "country" => "str",
                    "vat_nr" => "str",
                    "iban" => "str",
                    "kvk" => "str",
                    "website" => "str",
                    "facebook" => "str",
                    "twitter" => "str",
                    "youtube" => "str",
                    "linkedin" => "str",
                    "tel" => 'str',
                    "owned" => "bool");
        $edit = false;
        $data = $this->_fun->filterVarData($data, $req, $opt);
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $edit = array("id" => $data['id']);
        }
        
        $result = $this->_dataHandler->handleInformationToDb("companies", $req, $opt, $data, $edit);
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
     * Check and save image.
     * @param Integer $id Identifier of the company.
     * @return Boolean | Array  True ||  (errors, errormessage).
     */
    private function checkAndSaveLogo($id) {
        $resultImgUpload = $this->_fun->HandleImage($_FILES, "file", "company/logos");
        if (is_array($resultImgUpload)) {
            $this->_db->setSql("UPDATE `companies` SET `logo` = ? WHERE `id` = ?");
            $temp = $this->_db->updateRecord(array($resultImgUpload['newname'], $id));
            $this->assign("newlogo", $resultImgUpload['newname']);
            return $temp;
        }
        return array("errormessage" => $resultImgUpload);
    }
    
    /**
     * Show all persons that are linked to the company.
     * @param integer $id integer id of the company.
     * @return array  $couples || boolean.
     */
    private function getCompanyPersons($id) {
        $companyPerson = $this->_dataHandler->getDataFromRows("persons", array("company_id" => $id), false, array("id"));
        $persons = array();
        if ($companyPerson) {
            foreach ($companyPerson as $cPerson) {
                $person = $this->_dataHandler->getDataFromRow("persons", array("id" => $cPerson['id']), false, array("*"));
                if ($person) {
                    $persons[] = new \SG\Ram\Models\Person($person);
                }
            }
            return $persons;
        } else {
            return false;
        }
    }
    
    /**
     * Shows the projects where the company is the contractor.
     * @param integer $id integer id of the contracters.
     * @return array  $couples || boolean.
     */
    private function getCompanyInvolvedProject($id) {
        $involvedProjects = $this->_dataHandler->getDataFromRows("projects", array("contractor_id" => $id), false, array("id"));
        $allProjects = array();
        foreach ($involvedProjects as $involved) {
            $project = $this->_dataHandler->getDataFromRow("projects", array("id" => $involved['id']), false, array("*"));
            if ($project) {
                $allProjects[] = new \SG\Ram\Models\Project($project);
            } else {
                return false;
            }
        }
        return $allProjects;
    }
    
    /**
     * Create the sql call to make for the json check.
     * @param Array $settings The settings whether or not things should be shown (and be searchable etc.)
     * @return Array Containing all (all rows found) and count(the total found).
     */
    private function parseJSONRequestCompanies($settings) {
        $allowedAndSettings = $this->getAllowedArrayCompanies($settings);
        $order = $allowedAndSettings['settings']['order'];
        $from = $allowedAndSettings['settings']['from'];
        $total = $allowedAndSettings['settings']['total'];
        $search = '%' . filter_var($allowedAndSettings['settings']['search'], FILTER_SANITIZE_STRING) . '%';
        $name = $allowedAndSettings['settings']['name'];
        $searchSql = $this->_fun->getSqlFromArray($allowedAndSettings['searchable'], $search);
        $toSelectValues = array_merge($allowedAndSettings['searchable'], $allowedAndSettings['notsearchable']);
        $selectionString = "`" . implode('`,`', $toSelectValues) . "`";
        $sql = "SELECT $selectionString FROM `companies` WHERE `deleted` = 0 AND ";
        $sqlCount = "SELECT count(`id`) AS count FROM `companies` WHERE `deleted` = 0 AND ";
        if ($this->_user->isCustomer()) {
            $extraString = $this->getSqlStringCustomer();
            $sql .= $extraString;
            $sqlCount .= $extraString;
        }
        $sql .= $searchSql['sql'];
        $sqlCount .= $searchSql['sql'];
        $sql .= " ORDER BY $name $order LIMIT $total OFFSET $from";
        $this->_db->setSql($sql);
        $rows = $this->_db->getRows($searchSql['search']);
        $this->_db->setSql($sqlCount);
        $count = $this->_db->getRow($searchSql['search']);
        return array("count" => $count['count'], "all" => $rows);
    }
    
    /**
     * Get the sql string for the customer (whether they have access to companies.)
     * @return string
     */
    private function getSqlStringCustomer() {
        $involved = $this->_dataHandler->getDataFromRows('persons', array("person_id" => $this->_user->getPersonId() ), true, array('company_id'));
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
     * Get the settings to db column name to get the arrays of which columns should be gotten.
     * @param Array $settings Which columns should be gotten and which not.
     * @return Array Containing the keys: searchable[columns that are searchable], notsearchable[columns that are not searchable] and settings[the settings for the table.]
     */
    private function getAllowedArrayCompanies($settings) {
        $allowed = array("id", "name");
        $toCheckArray = array(
            "companyStreet" => 'street',
            "companyCity" => 'city',
            "companyCountry" => 'country',
            "companyVat" => 'vat_nr',
            "companyIban" => 'iban',
            "companyCC" => 'kvk'
        );
        $allowed2 = array_merge($allowed, $this->checkWhetherTodoColumns($settings, $toCheckArray));
        $settingsForTable = $this->handleRequestDataParent($allowed2);
        $unsortableToCheck = array(
            "companyPostalcode" => 'postalcode',
            "companyNumber" => 'number',
            "companyWebsite" => 'website',
            "companyFB" => 'facebook',
            "companyTwitter" => 'twitter',
            "companyYoutube" => 'youtube',
            "companyLinkedin" => 'linkedin'
        );
        $unsearchable = $this->checkWhetherTodoColumns($settings, $unsortableToCheck);
        $unsearchable[] = 'logo';
        return array("searchable" => $allowed2,"notsearchable" => $unsearchable, "settings" => $settingsForTable);
    }
    
    /**
     * Loop through the tables and check whether they need to be gotten.
     * @param Array $settings     The settings of which columns should be gotten.
     * @param Array $toCheckArray The array with key the name in the settings and value the name of the column.
     * @return Array
     */
    private function checkWhetherTodoColumns($settings, $toCheckArray) {
        $allowed = array();
        foreach ($toCheckArray as $settingName => $dbName) {
            if (isset($settings[$settingName]) && $settings[$settingName]) {
                $allowed[] = $dbName;
            }
        }
        return $allowed;
    }
    
    /**
     * Helper function to save the data of the company.
     * @param String $id To check, for the checkForDouble function, if it's for the edit so that the user can edit.
     * @return Void.
     */
    private function handleSave($id = null) {
        if (isset($_POST['add'])) {
            if (isset($_POST['name']) && $this->_dataHandler->checkForDouble("companies", "name", filter_var($_POST['name'], FILTER_SANITIZE_STRING), $id)) {
                $this->Assign("oldData", $_POST);
                $this->Assign("errormessage", _('Sorry, the name that you filled in is already in use.'));
                return;
            }
            $returnvalues = $this->checkAndSaveData($_POST);
            if ($returnvalues === true) {
                if ($id) {
                    header("refresh:0; url=" . SITE_ROOT . 'companies/edit/' . $id . '/?successmessage=' . urlencode(_('Company successfully updated.')));
                } else {
                    header("refresh:0; url=" . SITE_ROOT . 'companies/index/?successmessage=' . urlencode(_('Company successfully created.')));
                }
                die();
            } elseif (is_array($returnvalues)) {
                $this->Assign("errors", $returnvalues['errors']);
                $this->Assign("errormessage", $returnvalues['errormessage']);
            } else {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$somethingWentWrong));
            }
            $this->Assign("oldData", $_POST);
        }
    }
}
