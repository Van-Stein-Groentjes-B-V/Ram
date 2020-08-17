<?php

/**
 * Module Controller.
 * Handles module installation
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
 * @uses       \SG\Ram\Modelds\Dbhelper     Database helper class.
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

/**
 * ModuleController
 * @category   Controllers
 * @package    Ram
 */
class ModuleController extends controller
{
    private $_db;
    private $_account;
    private $_user;
    private $_datahandler;
    private $_installed;
    
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
            header("refresh:0; url=" . SITE_ROOT . 'dashboard/?errormessage=' . urlencode(_("You are not allowed to do this.")));
            die();
        }
        $this->_db = new Dbhelper();
        $this->_datahandler = new dataHandler();
        $this->_installed = explode(',', INSTALLED_MODULES);
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_datahandler);
        unset($this->_installed);
        unset($this->_db);  //dbhelper
        unset($this->_user);
        unset($this->_account);
    }
    
    /**
     * Loads the index page.
     * Dafault function to be called in this class
     * @return Void.
     */
    public function index() {
        $this->handleMessages();
        $this->LoadViewer(
            "module",
            "index",
            'Module',
            array("bootstrap-table.min","bootstrap-table-filter-control.min", "dashboard", "settings_invisible", 'module', "sg_confirm"),
            array("view_settings", "bootstrap-table.min", "backend", "bootstrap-table-export.min", "bootstrap-table-cookie.min", "tableExport.min", 'module', "sg_confirm"),
            true
        );
    }
    
    /**
     * Loads JSON into the bootstrap data table.
     * @return Void.
     */
    public function json() {
        $all = array();
        $results = $this->parseJSONrequest(array("name", "description"), $this->_db, "modules");
        //might want to use this to get which things should be gotten from db
        //for now it is not neccessary
        $count = $results["count"];
        $resultarray = $results["data"];
        
        foreach ($resultarray as $row) {
            if ($row['active']) {
                $row['active_html'] = '<i class="fas fa-check-square green"></i>' ;
                $row['activate'] = "<a href='" . SITE_ROOT . "module/deactivate/" . $row['id'] . "' alt='deactivate module'>" .
                                            "<i class='far fa-minus-square'></i>" .
                                    "</a>";
            } else {
                $row['active_html'] = '<i class="far fa-minus-square red"></i>' ;
                $row['activate'] = "<a href='" . SITE_ROOT . "module/activate/" . $row['id'] . "' alt='activate module'>" .
                                        "<i class='far fa-plus-square'></i>" .
                                "</a>";
            }
            $row['delete'] = "<a class='delete  remove_this' alt='delete study' data-confirm='" . _('Are you sure to remove this module from the server?') .
            "' data-target-id='" . $row['id'] . "' data-target-string='module' data-callback='callbackResettable'><i class='fas fa-trash'></i></i></a>";
            $all[] = $row;
        }
        echo '{"total":' . $count['count'] . ',' . '"rows":' . json_encode($all) . '}';
    }
    
    /**
     * Activates the module.
     * @param   Integer $id Id of module to activate.
     * @return  Void
     */
    public function activate($id) {
        $this->updateModuleActive($id, true);
    }
    
    /**
     * Deactivates the module.
     * @param   Integer $id Id of the module to deactivate.
     * @return  Void
     */
    public function deActivate($id) {
        $this->updateModuleActive($id, false);
    }
    
    /**
     * Activates or deactivates the module.
     * @param   Integer $id        Id of the module
     * @param   Boolean $setactive if true activates module, else deactivates
     * @return  Void               loads the index page.
     */
    private function updateModuleActive($id, $setactive = true) {
        if (is_numeric($id) && $id > 0) {
            $this->_db->setSql("SELECT * FROM `modules` WHERE `id` = ?");
            $module = $this->_db->getRow(array($id));
            if ($module) {
                if (
                    $setactive &&  !in_array($module['name'], $this->_installed) &&
                    file_exists(ROOT . DS . "modules" . DS . $module['name']) &&
                    file_exists(ROOT . DS . "modules" . DS . $module['name'] . DS . "install.php") &&
                    $this->installModule($module)
                ) {
                    $this->Assign("successmessage", _("Successfully activated the module."));
                    return $this->index();
                } elseif (in_array($module['name'], $this->_installed) && $this->remakeConfigWithout($module)) {
                    $this->_db->setSql("UPDATE `modules` SET `active` = 0 WHERE `id` = ?");
                    if ($this->_db->updateRecord(array($id))) {
                        $this->Assign("successmessage", _("successfully deactivated the module."));
                    }
                    return $this->index();
                }
            }
        }
        $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$moduleDeactivate));
        return $this->index();
    }
    
    /**
     * Installs the module.
     * @param   String $module Holds the name of the module.
     * @return  Boolean             True on success
     */
    private function installModule($module) {
        require_once(ROOT . DS . "modules" . DS . $module['name'] . DS . "install.php");
        if (!isset($amountOfFiles) || !$this->checkFiles($amountOfFiles, $module)) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$amountFilesAreNotConsistent));
            return false;
        }
        if (!isset($requires) || !$this->checkDependencies($requires)) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$needModuleOrModulesForThisToWork));
            return false;
        }
        if (!isset($insertionWhere) || !$this->checkWhereInstall($insertionWhere)) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$incorrectModuleSetup));
            return false;
        }
        if (!isset($insertionMenu) || !$this->checkMenuInstall($insertionMenu)) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$incorrectModuleSetup));
            return false;
        }
        if (!isset($databaseUpdate) || !$this->installDataBase($databaseUpdate)) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$WentWrongDBUpdate));
            return false;
        }
        if ($this->checkExisingAndUpdateConfig($insertionWhere, $insertionMenu, $module) && count($insertionMenu) > 0) {
            $this->_db->setSql("UPDATE `modules` SET `active` = ? WHERE id = ?");
            return $this->_db->updateRecord(array(1, $module['id']));
        }
        return false;
    }
    
    /**
     * Loop through the files and count them.
     * If it doesn't match the given amount, deny install.
     * @param   Array $amountOfFiles Information of amount files.
     * @param   Array $module        Array of module information.
     * @return  boolean              True on success.
     */
    private function checkFiles($amountOfFiles, $module) {
        if (!is_array($amountOfFiles)) {
            return false;
        }
        $acceptedFolders = array('controllers', 'views', 'models', 'public');
        $i = 0;
        $total = 0;
        foreach ($acceptedFolders as $tocheck) {
            if (!$this->checkPerFolder($module, $amountOfFiles, $tocheck, $i, $total)) {
                return false;
            }
        }
        return $total === $i;
    }
    
    /**
     * Open the folder and check the items in it.
     * @param Array   $module        Array of module information.
     * @param Array   $amountOfFiles Information of amount files.
     * @param String  $tocheck       The folder name to check.
     * @param Integer $i             The amount of total that we went through (Safety net)
     * @param Integer $total         The total we went through in comparison to allowed (if set)
     * @return boolean
     */
    private function checkPerFolder($module, $amountOfFiles, $tocheck, &$i, &$total) {
        $teller = 0;
        if (file_exists(ROOT . DS . "modules" . DS . $module['name'] . DS . $tocheck) && $handle = opendir(ROOT . DS . "modules" . DS . $module['name'] . DS . $tocheck)) {
            while (false !== ($file = readdir($handle))) {
                if ($i > 500) {
                    return false;
                }
                if ('.' === $file || '..' === $file) {
                    continue;
                }
                if (!$this->checkAmountOfFilesInFolder($module, $tocheck, $file, $i, $teller)) {
                    return false;
                }
            }
            closedir($handle);
            if (isset($amountOfFiles[$tocheck])) {
                $total += $amountOfFiles[$tocheck];
                if ($teller !== $amountOfFiles[$tocheck]) {
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * Check the amount of files in the folder, or go deeper
     * @param Array   $module  Array of module information.
     * @param String  $tocheck The folder where the files need to be counted.
     * @param String  $file    The folders|files beneath the main folder
     * @param Integer $i       The total amount that were
     * @param Integer $teller  The amount of files
     * @return boolean
     */
    private function checkAmountOfFilesInFolder($module, $tocheck, $file, &$i, &$teller) {
        if ($tocheck === 'views' || $tocheck === 'public') {
            if ($handle2 = opendir(ROOT . DS . "modules" . DS . $module['name'] . DS . $tocheck . DS . $file)) {
                while (false !== ($file2 = readdir($handle2))) {
                    if ($i > 500) {
                        return false;
                    }
                    if ('.' === $file2 || '..' === $file2) {
                        continue;
                    }
                    $i++;
                    $teller++;
                }
                closedir($handle2);
            }
        } else {
            $i++;
            $teller++;
        }
        return true;
    }
    
    /**
     * Check whether the dependencies are installed.
     * Will stop the installation if dependency is not found.
     * @param   Array $requires Array with all dependencies that are required.
     * @return  Boolean             True if all dependencies are present.
     */
    private function checkDependencies($requires) {
        if (!is_array($requires)) {
            return false;
        }
        foreach ($requires as $required) {
            if (!in_array($required, $this->_installed)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Install the needed tables/columns.
     * @param   Array $data Array of data.
     * @return  Boolean         True on success
     */
    private function installDataBase($data) {
        if (!is_array($data)) {
            return false;
        }
        $dbinstaller = new DatabaseinstallController();
        $results = $dbinstaller->extendOrCreateTables($data);
        if (is_array($results) && !empty($results)) {
            $errormessage = "";
            foreach ($results as $result) {
                $errormessage .= $result . ', ';
            }
            $this->Assign("errormessage", trim($errormessage, ', '));
            return false;
        }
        return true;
    }
    
    /**
     * Loop through the controller extensions for validation.
     * @param   Array $insertionWhere Array with key values to place in sidebar.
     * @return  Boolean                 True on success
     */
    private function checkWhereInstall($insertionWhere) {
        // If $insertionWhere is not an array, it is build incorrectly and should not be allowed
        if (!is_array($insertionWhere)) {
            return false;
        }
        // The controllers that should never be extended by the modules, might increase in the future
        $notAllowedControllers = array("installationcontroller", "modulecontroller", "accountcontroller");
        // Loop through the array. If made incorrectly or used incorrect characters, will stop it entirely
        foreach ($insertionWhere as $controllerName => $array) {
            if (in_array($controllerName, $notAllowedControllers) || count($array) !== 2) {
                return false;
            }
            foreach ($array as $k => $t) {
                if (($k !== "controller" && $k !== "method") || preg_match("/[^a-zA-Z0-9]/", $t)) {
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * Loop through the menu extensions for validation.
     * @param   Array $insertionMenu Array with  key values to place in sidebar.
     * @return  Boolean                 True on success.
     */
    private function checkMenuInstall($insertionMenu) {
        // If $insertionMenu is not an array, it is build incorrectly and should not be allowed
        if (!is_array($insertionMenu)) {
            return false;
        }
        // Loop trhough the array. If made incorrectly or used incorrect characters, will stop it entirely
        foreach ($insertionMenu as $array) {
            if (count($array) !== 5) {
                return false;
            }
            foreach ($array as $k => $t) {
                if (
                    ($k !== "position" && $k !== "mustBeAdmin" && $k !== "url" && $k !== "name" && $k !== 'icon') ||
                    (($k === "position" && !is_numeric($t)) || ($k === "mustBeAdmin" && !is_bool($t)) || ($k === "url" && (strpos($t, SITE_ROOT) !== 0 || !filter_var($t, FILTER_VALIDATE_URL)))) ||
                    ($k === "name"  && preg_match("/[^a-zA-Z0-9 ]/", $t)) ||
                    ($k === 'icon'  && preg_match("/[^a-zA-Z0-9 \-]/", $t) && $t !== "")
                ) {
                    return false;
                }
            }
        }
        return true;
    }
    
   /**
    * Open the config and change the necessary things.
    * @param    Array $insertionWhere Where to insert the mudule.
    * @param    Array $insertionMenu  Where to insert the module in the menu.
    * @param    Array $module         Module info.
    * @return   Integer | Boolean     Integer of bytes or false
    */
    private function checkExisingAndUpdateConfig($insertionWhere, $insertionMenu, $module) {
        $existingControllerExtensions = unserialize(MODIFY_CONSTRUCT_EXISTING);
        $existingMenuExtensions = unserialize(MODIFY_MENU_EXISTING);
        $installedMods = explode(',', INSTALLED_MODULES);
        foreach ($insertionWhere as $controllerName => $values) {
            if (isset($existingControllerExtensions[$controllerName])) {
                $existingControllerExtensions[$controllerName][$module['name']] = array($module['name'], $values['controller'], $values['method']);
            } else {
                $existingControllerExtensions[$controllerName] = array($module['name'] => array($module['name'], $values['controller'], $values['method']));
            }
        }
        foreach ($insertionMenu as $menuValue) {
            $number = $this->findNewNumber($existingMenuExtensions, $menuValue, $module['name']);
            if (isset($existingMenuExtensions[$number][$module['name']])) {
                $existingMenuExtensions[$number][$module['name']][] = array('mustBeAdmin' => $menuValue['mustBeAdmin'], "url" => $menuValue['url'], "name" => $menuValue['name'],
                "icon" => $menuValue["icon"]);
            } else {
                $existingMenuExtensions[$number] = array($module['name'] => array(array('mustBeAdmin' => $menuValue['mustBeAdmin'], "url" => $menuValue['url'],
                "name" => $menuValue['name'], "icon" => $menuValue["icon"])));
            }
        }
        if ($installedMods[0] === "") {
            $installedMods[0] = $module['name'];
        } else {
            $installedMods[] = $module['name'];
        }
        $bytes = $this->saveToConfig($existingControllerExtensions, $existingMenuExtensions, $installedMods);
        if (is_numeric($bytes)) {
            $this->Assign("successmessage", "success");
            return true;
        }
        $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$configUpdateIncorrect));
        return false;
    }
    
    /**
     * Remakes the config
     * @param   Array $module Array with module information.
     * @return  String  $result     Contains config and defines.
     */
    private function remakeConfigWithout($module) {
        $existingControllerExtensions = unserialize(MODIFY_CONSTRUCT_EXISTING);
        $existingMenuExtensions = unserialize(MODIFY_MENU_EXISTING);
        $installedMods = explode(',', INSTALLED_MODULES);
        if ($existingControllerExtensions) {
            foreach ($existingControllerExtensions as $controllerName => $values) {
                if (isset($values[$module['name']])) {
                    unset($existingControllerExtensions[$controllerName][$module['name']]);
                }
            }
        }
        
        foreach ($existingMenuExtensions as $number => $value) {
            if (isset($value[$module['name']])) {
                unset($existingMenuExtensions[$number]);
            }
        }
        if (($key = array_search($module['name'], $installedMods)) !== false) {
            unset($installedMods[$key]);
        }
        return $this->saveToConfig($existingControllerExtensions, $existingMenuExtensions, $installedMods);
    }
    
    /**
     * Function that finds a possible position in the menu for the new module
     * @param   Array $existingMenuExtensions Array with menu postions etc.
     * @param   Array $menuValue              Array with menuplacement.
     * @param   Array $moduleName             Array with module information.
     * @return  Integer
     */
    private function findNewNumber($existingMenuExtensions, $menuValue, $moduleName) {
        if (!isset($existingMenuExtensions[$menuValue['position']]) || isset($existingMenuExtensions[$menuValue['position']][$moduleName])) {
            return $menuValue['position'];
        }
        for ($i = 0; $i < 40; $i++) {
            $tempNumber = $menuValue['position'] + $i;
            if (!isset($existingMenuExtensions[$tempNumber])) {
                return $tempNumber;
            }
        }
        return 100;
    }
    
    /**
     * Saves the config file
     * @param   String $extControllers String with controllers.
     * @param   String $extMenu        String menu
     * @param   String $mods           String mods
     * @return  String      $result         Contains config and defines.
     */
    private function saveToConfig($extControllers, $extMenu, $mods) {
        $contents = file_get_contents(ROOT . DS . "config" . DS . "config_modules.php");
        $config = fopen(ROOT . DS . "config" . DS . "config_modules.php", 'w');
        $contents = preg_replace("/define\('INSTALLED_MODULES', '.*'\)/", "define('INSTALLED_MODULES', '" . implode(',', $mods) . "')", $contents);
        $contents = preg_replace("/define\('MODIFY_CONSTRUCT_EXISTING', '.*'\)/", "define('MODIFY_CONSTRUCT_EXISTING', '" . serialize($extControllers) . "')", $contents);
        $contents = preg_replace("/define\('MODIFY_MENU_EXISTING', '.*'\)/", "define('MODIFY_MENU_EXISTING', '" . serialize($extMenu) . "')", $contents);
        $result = fwrite($config, $contents, strlen($contents));
        fclose($config);
        return $result;
    }
}
