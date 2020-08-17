<?php

/**
 * Installation Controller.
 * If the system is not yet installed, this controller with assigned view will get the bare essentials
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
 * @uses       \SG\Controller           Extend the main controller.
 * @uses       \SG\Models\Dbhelper      Database helper class.
 * @uses       \SG\Functions            General functions.
 * @uses       PDO                      PHP PDO.
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
use SG\Ram\Models\Dbhelper;
use PDO;

/**
 * InstallationController
 * @category   Controllers
 * @package    Ram
 */
class InstallationController extends controller
{
    private static $db;
    private $_dbhelper;
    
    /**
     * Checks if it's installed.
     * @return (function) header with message.
     */
    public function __construct() {
        parent::__construct();
        if (PROGRAM_INSTALLED) {
            header("refresh:0; url=" . SITE_ROOT);
            die('Hello');
        }
        if ((isset($_POST['specialVal']) && $_POST['specialVal'] === SALT_ADMIN || (isset($_COOKIE["ADMIN_SALT"]) && $_COOKIE["ADMIN_SALT"] === SALT_ADMIN) )) {
            $this->addAdmin();
            return;
        }
        $this->Assign("secure", $this->isSecure());
        if (empty($_POST) && empty($_GET)) {
            $this->indexInstall();
            return;
        }
        
        if (isset($_POST['main_info'])) {
            $this->handleMainSettings();
            return;
        }
    }
    
    /**
     * index is called when there is still nothing set
     * the start of the installation if you will.
     * here we ask the most essantials, namely::
     * Website url.
     * db name.
     * db login.
     * db pw.
     * db port //will be set already on 3306, but must be able to change this.
     * table.
     * use https.
     * @return (function) LoadViewer.
     */
    private function indexInstall() {
        $this->LoadViewer("installation", "index", "RAM Management configuration", array("install-style"), array("standard"), false, 2);
    }
    
    /**
     * handle main setting.
     * this function will try the db connection. if connection fails, return data and back to index.
     * if it succeeds, go to install sql.
     * @return (function) LoadViewer.
     */
    private function handleMainSettings() {
        $doreturn = false;
        if (isset($_POST['useHttps']) && ($_POST['useHttps'] === 'on' || $_POST['useHttps'] === 1)) {
            if ($this->testIfHttpsIsPossible()) {
                if (!$this->setConfigForceHttps()) {
                    $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$NeedToHavePermissionToWriteInFile));
                    $this->Assign("oldData", $_POST);
                    $this->indexInstall();
                    $doreturn = true;
                }
            } else {
                $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$uncheckForceHTTPS));
                $this->Assign("oldData", $_POST);
                $this->indexInstall();
                $doreturn = true;
            }
        }
        if ($doreturn) {
            return;
        }
        $toCheck = array("url" => "str","dbhost" => "str", "dbname" => "str", "dblogin" => "str", "dbport" => "int");
        $checkResult = $this->_fun->checkValuesValidity($toCheck, $_POST);
        if (is_array($checkResult)) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$cantLoginIntoDB));
            $this->Assign("errors", $checkResult["errors"]);
            $this->Assign("oldData", $_POST);
            $this->indexInstall();
            $doreturn = true;
        } elseif (!$this->testDb($_POST['dbhost'], $_POST['dbname'], $_POST['dblogin'], $_POST['dbpass'], $_POST['dbport'])) {
            $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$cantLoginIntoDB));
            $this->Assign("oldData", $_POST);
            $this->indexInstall();
            $doreturn = true;
        }
        if ($doreturn) {
            return;
        }
        
        $resultCreation = $this->createNewConfig($_POST);
        if ($resultCreation > 1000) {
            $this->installDatabase($_POST['dbhost'], $_POST['dbname'], $_POST['dblogin'], $_POST['dbpass'], $_POST['dbport']);
        } else {
            $this->Assign("errormessage", $resultCreation);
            $this->Assign("oldData", $_POST);
            $this->indexInstall();
        }
    }
    
    /**
     * test the filled in data.
     * @param string $host     hostname.
     * @param string $db_name  database name.
     * @param string $user     username to log in with.
     * @param string $password password of user to db.
     * @param string $port     portname.
     * @return Boolean  true | false.
     */
    private function testDb($host, $db_name, $user, $password, $port) {
        try {
            $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $db_name . ';charset=utf8';
            self::$db = new PDO($dsn, $user, $password);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->Assign("errormessage", $e->getMessage());
            return false;
        }
        return true;
    }
    
   
    /**
     *  Get the tables to fill in and make the databases.
     * @param String $host     String host.
     * @param String $db_name  String db_name.
     * @param String $user     String user.
     * @param String $password String password.
     * @param String $port     String port.
     * @return Void            (function) LoadViewer | header with message.
     */
    private function installDatabase($host, $db_name, $user, $password, $port) {
        $databaseTables = new DatabaseinstallController(array('host' => $host, 'dbname' => $db_name, 'username' => $user, 'password' => $password, 'port' => $port));
        $tables = $databaseTables->fillWithData();
        $results = $databaseTables->createOrEditTables($tables['db'], $user, $host);
        $databaseTables->fillTablesWithBareInfo($tables['data']);
        if (empty($results)) {
            if ($databaseTables->checkIfAdminAlreadyExists()) {
                $this->finishInstallation();
                $this->LoadViewer('installation', 'success', 'success', array("install-style"), array("standard"), false, 2);
                header("refresh:5; url=" . $_POST['url'] . "index/login/");
                return;
            } else {
                $salty = $this->createSalty();
                if ($salty !== false) {
                    setcookie("ADMIN_SALT", $salty, time() + 90000, '/', "", $this->isSecure(), true);
                    $this->Assign("random", $salty);
                    $this->LoadViewer('installation', 'admincreate', 'RAM Management configuration', array("install-style"), array("standard"), false, 2);
                    return;
                }
            }
        }
        
        $this->Assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$errorsDuringDBInstallation));
        $this->Assign("oldData", $_POST);
        $this->indexInstall();
    }
    
    /**
     * Make the main admin if the filled in values are correct.
     * @return Void     (function) LoadViewer | header with message.
     */
    private function addAdmin() {
        $this->_fun->filterDataToNormal($_POST);
        $req = array("username" => "str", "email" => "em", "fullname" => "str", "company" => "str", "password" => "ps", "password2" => "ps");
        $check = $this->_fun->checkValuesValidity($req, $_POST);
        if ($check === true) {
            $register = new AccountController();
            $result = $register->registerPost($_POST, 3);
            if ($result === true) {
                $this->AddPersonAndCompany();
                $this->finishInstallation();
                $this->Assign("successmessage", _("All information is provided"));
                $this->LoadViewer('installation', 'success', 'Installation successful', array("install-style"), array("standard"), false, 2);
                header("refresh:5; url=" . SITE_ROOT . "dashboard/");
                return;
            }
            $this->Assign("errormessage", $result);
        } else {
            $this->Assign("errormessage", $check['errormessage']);
            $this->Assign("errors", $check['errors']);
        }
        $this->Assign("oldData", $_POST);
        $this->LoadViewer('installation', 'admincreate', 'RAM Management configuration', array("install-style"), array("standard"), false, 2);
    }
    
    /**
     * add a person and company for the admin.
     * @return void.
     */
    private function addPersonAndCompany() {
        $this->_dbhelper = new Dbhelper();
        $this->_dbhelper->setSql("INSERT INTO `companies` (`name`, `owned`) VALUES (?, ?)");
        $compResult = $this->_dbhelper->insertRecord(array(filter_var($_POST['company'], FILTER_SANITIZE_SPECIAL_CHARS), 1));
        if (is_numeric($compResult)) {
            $this->_dbhelper->setSql("SELECT `id` FROM `user_accounts` WHERE `username` = ?");
            $person = $this->_dbhelper->getRow(array(filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS)));
            if ($person) {
                $this->_dbhelper->setSql("INSERT INTO `persons` (`name`,`email`,`company`,`company_id`, `account_id`) VALUES (?, ?, ?, ?, ?)");
                $this->_dbhelper->insertRecord(array(filter_var($_POST['fullname'], FILTER_SANITIZE_SPECIAL_CHARS),filter_var(
                    $_POST['email'],
                    FILTER_SANITIZE_EMAIL
                ),filter_var($_POST['company'], FILTER_SANITIZE_SPECIAL_CHARS), $compResult, $person['id']));
            }
        }
    }
    
    /**
     * Does a check.
     * @return GLOBAL $_SERVER Request.
     */
    private function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }
    
    /**
     * open the config and change the necessary things.
     * @param $data => (array) data with the accepted info.
     * @return (int) bytes | false.
     */
    private function createNewConfig($data) {
        $contents = file_get_contents(ROOT . DS . "config" . DS . "config.php");
        $contents = preg_replace('/define\("SITE_ROOT", ".*"\)/', 'define("SITE_ROOT", "' . $data['url'] . '")', $contents);
        $contents = preg_replace('/define\("DB_HOST", ".*"\)/', 'define("DB_HOST", "' . $data['dbhost'] . '")', $contents);
        $contents = preg_replace('/define\("DB_USER", ".*"\)/', 'define("DB_USER", "' . $data['dblogin'] . '")', $contents);
        $contents = preg_replace('/define\("DB_PASSWORD", ".*"\)/', 'define("DB_PASSWORD", "' . $data['dbpass'] . '")', $contents);
        $contents = preg_replace('/define\("DB_NAME", ".*"\)/', 'define("DB_NAME", "' . $data['dbname'] . '")', $contents);
        $contents = preg_replace('/define\("DB_PORT", ".*"\)/', 'define("DB_PORT", "' . $data['dbport'] . '")', $contents);
        $config = fopen(ROOT . DS . "config" . DS . "config.php", 'w');
        $result = fwrite($config, $contents, strlen($contents));
        fclose($config);
        return $result;
    }
    
    /**
     * create an salt so that not everyone can make the first admin, only the one that filled in the information.
     * @return false | (string) salty.
     */
    private function createSalty() {
        $salty = $this->_fun->createRandomDirName();
        $contents = file_get_contents(ROOT . DS . "config" . DS . "config.php");
        $contents = preg_replace('/define\("SALT_ADMIN", ".*"\)/', 'define("SALT_ADMIN", "' . $salty . '")', $contents);
        $config = fopen(ROOT . DS . "config" . DS . "config.php", 'w');
        $result = fwrite($config, $contents, strlen($contents));
        fclose($config);
        return $result > 1000 ? $salty : false;
    }
    
    /**
     * set program installed op true in config.
     * @return boolean true || false
     */
    private function finishInstallation() {
        $contents = file_get_contents(ROOT . DS . "config" . DS . "config.php");
        $contents = preg_replace('/define\("PROGRAM_INSTALLED", false\)/', 'define("PROGRAM_INSTALLED", true)', $contents);
        $config = fopen(ROOT . DS . "config" . DS . "config.php", 'w');
        $result = fwrite($config, $contents, strlen($contents));
        fclose($config);
        return $result > 1000;
    }
    
    /**
     * simple check whether https is possible on the current system.
     * @return String $r contains integer || boolean.
     */
    private function testIfHttpsIsPossible() {
        global $url;
        $tempurl = str_replace("http://", "https://", $url);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $tempurl);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        $r = curl_exec($s);
        $q = curl_getinfo($s, CURLINFO_HTTP_CODE);
        curl_close($s);
        return ($r !== false && $q !== 0);
    }
    
    /**
     * Sets the fors https.
     * @return Integer $result returns an integer.
     */
    private function setConfigForceHttps() {
        $contents = file_get_contents(ROOT . DS . "config" . DS . "config_ext.php");
        $contents = preg_replace("/define\(\"USE_HTTPS\", .*\)/", "define(\"USE_HTTPS\", true)", $contents);
        $config = fopen(ROOT . DS . "config" . DS . "config_ext.php", 'w');
        $result = fwrite($config, $contents, strlen($contents));
        fclose($config);
        return is_numeric($result);
    }
}
