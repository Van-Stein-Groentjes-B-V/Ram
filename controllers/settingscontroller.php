<?php

/**
 * Settings Controller.
 * Handles setting and un-setting of different settings
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
 * @uses       \SG\Ram\dataHandler      Data handler class.
 * @uses       \SG\Ram\functions        General function class.
 * @uses       \SG\Ram\Models\Dbhelper  Database helper class.
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
 * SettingsController
 * @category   Controllers
 * @package    Ram
 */
class SettingsController extends controller
{
    private $_db;
    private $_user;
    private $_dataHandler;
    private $_errors = array();
    
    /**
     * Constructor: __construct.
     * Assemble and pre-process the data.
     * @return  Void.
     */
    public function __construct() {
        parent::__construct();
        global $user;
        $this->_user = $user->getUser();
        $this->_db = new Dbhelper();
        $this->_dataHandler = new dataHandler();
    }

    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_dataHandler);
        unset($this->_user);
        unset($this->_db); //dbhelper
    }
    
    /**
     * Assignes css and js files from the settings
     * Loads the view content with the js and css files that are included in the companie page
     * Defines the username and email.
     * @return Void.
     */
    public function index() {
        $this->handleMessages();
        $arr = $this->_dataHandler->getSettingsUser($this->_user->getId());
        $arr['username'] = $this->_user->getUsername();
        $arr['email'] = $this->_user->getEmail();
        $this->Assign("settings", $arr);
        $this->LoadViewer("settings", "index", 'settings', array("dashboard", 'bootstrap2-toggle.min', "settingpages"), array('bootstrap2-toggle.min', "backend", "project"), true);
    }
    
    /**
     * Saves the settings if the user is admin.
     * @return Void     LoadViewer | loads index page.
     */
    public function admin() {
        if (!$this->_user->isMainAdmin()) {
            return $this->index();
        }
        $this->handleMessages();
        if (isset($_POST['saveButton']) && $this->handleSettingsAdmin()) {
            return $this->LoadViewer("settings", "success", "settings", array("dashboard", "formlayout", "bootstrap2-toggle.min"), array('bootstrap2-toggle.min', "backend", "admin", "project"), true);
        }
        $this->LoadViewer("settings", "admin", 'admin settings', array("dashboard", "formlayout", "bootstrap2-toggle.min"), array("bootstrap2-toggle.min", "backend", "admin", "project"), true);
    }
    
    /**
     * Handle parsing setting force HTTPS
     * @param String $errormessage The error message till now.
     * @param Array  $needToUpdate The array with what should be updated.
     * @return Void
     */
    private function handleSettingForceHttps(&$errormessage, &$needToUpdate) {
        if (isset($_POST['force_https']) && !USE_HTTPS) {
            if ($this->testIfHttpsIsPossible()) {
                $needToUpdate['USE_HTTPS'] =  true;
            } else {
                $errormessage .= $this->_err->get(\SG\Ram\ErrorMessage::$cantUseHTTPS);
            }
        } elseif (!isset($_POST['force_https']) && USE_HTTPS) {
            $needToUpdate['USE_HTTPS'] = false;
        }
    }
    
    /**
     * Handle parsing setting send mail.
     * @param String $errormessage The error message till now.
     * @param Array  $needToUpdate The array with what should be updated.
     * @return Void
     */
    private function handleSettingSendMail(&$errormessage, &$needToUpdate) {
        // Send email options
        if (isset($_POST['send_mail'])) {
            $additional_info = $this->getAdditionalEmailOptions();
            if (is_array($additional_info)) {
                if (!SEND_MAIL) {
                    $needToUpdate['SEND_MAIL'] = true;
                }
                $needToUpdate = array_merge($needToUpdate, $additional_info);
            } else {
                $errormessage .= $additional_info;
            }
        } elseif (!isset($_POST['send_mail']) && SEND_MAIL) {
            $needToUpdate['SEND_MAIL'] = false;
        }
    }
    
    /**
     * Handle parsing setting SMTP info.
     * @param String $errormessage The error message till now.
     * @param Array  $needToUpdate The array with what should be updated.
     * @return Void
     */
    private function handleSettingSMTP(&$errormessage, &$needToUpdate) {
        // Start smtp options
        if (isset($_POST['use_smtp'])) {
            $additional_info = $this->getAdditionalSMTPOptions();
            if (is_array($additional_info)) {
                if (!USE_SMPT_EMAIL) {
                    $needToUpdate["USE_SMPT_EMAIL"] =  true;
                }
                $needToUpdate = array_merge($needToUpdate, $additional_info);
            } else {
                $errormessage .= $additional_info;
            }
        } elseif (!isset($_POST['use_smtp']) && USE_SMPT_EMAIL) {
            $needToUpdate["USE_SMPT_EMAIL"] =  false;
        }
    }
    
    /**
     * Handle parsing setting external log.
     * @param String $errormessage The error message till now.
     * @param Array  $needToUpdate The array with what should be updated.
     * @return Void
     */
    private function handleSettingUseExternalLog(&$errormessage, &$needToUpdate) {
        if (isset($_POST['use_log_ext'])) {
            $additional_info = $this->getAdditionalLOGOptions();
            if (is_array($additional_info)) {
                if (!USE_EXTERNAL_LOG) {
                    $needToUpdate["USE_EXTERNAL_LOG"] = true;
                }
                $needToUpdate = array_merge($needToUpdate, $additional_info);
            } else {
                $errormessage .= $additional_info;
            }
        } elseif (!isset($_POST['use_log_ext']) && USE_EXTERNAL_LOG) {
            $needToUpdate["USE_EXTERNAL_LOG"] = false;
        }
    }
    /**
     * Handle parsing setting log.
     * @param String $errormessage The error message till now.
     * @param Array  $needToUpdate The array with what should be updated.
     * @return Void
     */
    private function handleSettingUseLog(&$errormessage, &$needToUpdate) {
        if (isset($_POST['use_log'])) {
            if (!USE_LOG) {
                $needToUpdate["USE_LOG"] = true;
            }
            if (isset($_POST['use_log_ip']) && !USE_IP) {
                $needToUpdate["USE_IP"] = true;
            } elseif (!isset($_POST['use_log_ip']) && USE_IP) {
                $needToUpdate["USE_IP"] = false;
            }
            if (isset($_POST['log_level']) && DB_LOG_LEVEL != $_POST['log_level']) {
                $needToUpdate["DB_LOG_LEVEL"] = intval(filter_var($_POST['log_level'], FILTER_SANITIZE_NUMBER_INT));
            }
            if (isset($_POST['log_website_name']) && WEBSITE_NAME != $_POST['log_website_name']) {
                $needToUpdate["WEBSITE_NAME"] = filter_var($_POST['log_website_name'], FILTER_SANITIZE_SPECIAL_CHARS);
            }
            $this->handleSettingUseExternalLog($errormessage, $needToUpdate);
        } elseif (!isset($_POST['use_log']) && USE_LOG) {
            $needToUpdate["USE_SMPT_EMAIL"] = false;
        }
    }
    
    /**
     * Handle parsing setting SMTP info.
     * @param String $errormessage The error message till now.
     * @param Array  $needToUpdate The array with what should be updated.
     * @return Void
     */
    private function handleSettingSecureLogin(&$errormessage, &$needToUpdate) {
        if (isset($_POST['use_secure_login'])) {
            $check_captcha = $this->checkFilledInCaptcha();
            if (is_array($check_captcha)) {
                if (!USE_SECUREIMAGE_LOGIN) {
                    $needToUpdate["USE_SECUREIMAGE_LOGIN"] = true;
                }
                $needToUpdate = array_merge($needToUpdate, $check_captcha);
            } else {
                $errormessage .= $check_captcha;
            }
        } elseif (!isset($_POST['use_secure_login']) && USE_SECUREIMAGE_LOGIN) {
            $needToUpdate["USE_SECUREIMAGE_LOGIN"] = false;
        }
    }

    /**
     * Handles all settings.
     * @return Boolean true || false.
     */
    private function handleSettingsAdmin() {
        $needToUpdate = array();
        $errormessage = "";
        // Set Site title
        if (isset($_POST['title_website']) && SITE_TITLE != $_POST['title_website']) {
            $needToUpdate["SITE_TITLE"] = filter_var($_POST['title_website'], FILTER_SANITIZE_SPECIAL_CHARS);
        }
        
        $this->handleSettingForceHttps($errormessage, $needToUpdate);
        $this->handleSettingSendMail($errormessage, $needToUpdate);
        $this->handleSettingSMTP($errormessage, $needToUpdate);
         // Use activation
        if (isset($_POST['activate_account']) && !ACTIVATE_ACCOUNTS) {
            $needToUpdate["ACTIVATE_ACCOUNTS"] = true;
        } elseif (!isset($_POST['activate_account']) && ACTIVATE_ACCOUNTS) {
            $needToUpdate["ACTIVATE_ACCOUNTS"] = false;
        }
        $this->handleSettingUseLog($errormessage, $needToUpdate);
        $this->handleSettingSecureLogin($errormessage, $needToUpdate);

        
        // Captcha options
        
        
        // Default wage
        if (isset($_POST['default_wage']) && DEFAULT_WAGE != $_POST['default_wage'] && is_numeric($_POST['default_wage'])) {
            $needToUpdate["DEFAULT_WAGE"] = filter_var($_POST['default_wage'], FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if (count($needToUpdate) > 0 && $errormessage == "") {
            $res = $this->saveToConfig($needToUpdate);
            if ($res) {
                $this->Assign("successmessage", "success");
                return true;
            } else {
                $errormessage .= $this->_err->get(\SG\Ram\ErrorMessage::$unableToEditConfigFile);
            }
        }
        $this->Assign("errors", $this->_errors);
        $this->Assign("errormessage", $errormessage);
        return false;
    }
    
    /**
     * Checks the captcha input.
     * @return  Array | String      Array when captcha success else error
     */
    private function checkFilledInCaptcha() {
        if (isset($_POST['token']) && isset($_POST['login_captcha_public']) && isset($_POST['login_captcha_secret'])) {
            if ($_POST['token'] == CAPTCHA_PUBLIC && $_POST['token'] == CAPTCHA_SECRET) {
                return array();
            }
            $captcha = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $_POST['login_captcha_secret'] . "&response=" .
            $_POST['token'] . "&remoteip=" . $_SERVER['REMOTE_ADDR']), true);
            if (is_array($captcha) && $captcha['success']) {
                return array("CAPTCHA_SECRET" => filter_var($_POST['login_captcha_secret'], FILTER_SANITIZE_SPECIAL_CHARS),
                "CAPTCHA_PUBLIC" => filter_var($_POST['token'], FILTER_SANITIZE_SPECIAL_CHARS));
            }
        }
        return _('Captcha could not be validated with the filled in values.');
    }
    
    /**
     * Test https for possibility.
     * @return  Boolean     True if possible, false otherwise
     */
    private function testIfHttpsIsPossible() {
        $url = str_replace("http://", "https://", SITE_ROOT);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $url);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        $r = curl_exec($s);
        $q = curl_getinfo($s, CURLINFO_HTTP_CODE);
        curl_close($s);
        return ($r !== false && $q !== 0);
    }
    
    /**
     * Get email options.
     * @return String $errormessage returns error message.
     */
    private function getAdditionalEmailOptions() {
        $errormessage = "";
        $returnArray = array();
        $needupdate = false;
        $array = array( "EMAIL_FROM_NAME" => "from_name",
                        "EMAIL_FROM_REAL" => "email_real",
                        "EMAIL_FROM_NOREPLY" => "from_noreply",
                        "EMAIL_REPLY_TO" => "email_reply",
                        "EMAIL_DANGERS_TO" => "emails_dangers");
        $currentValues = array(
                        "EMAIL_FROM_NAME" => EMAIL_FROM_NAME,
                        "EMAIL_FROM_REAL" => EMAIL_FROM_REAL,
                        "EMAIL_FROM_NOREPLY" => EMAIL_FROM_NOREPLY,
                        "EMAIL_REPLY_TO" => EMAIL_REPLY_TO,
                        "EMAIL_DANGERS_TO" => EMAIL_DANGERS_TO);
        foreach ($array as $new_name => $input_name) {
            if (isset($_POST[$input_name])) {
                if ($currentValues[$new_name] != $_POST[$input_name]) {
                    $needupdate = true;
                }
                if ($input_name === "from_name") {
                    $returnArray[$new_name] = htmlspecialchars($_POST[$input_name]);
                    continue;
                } elseif ($this->_fun->checkEmail($_POST[$input_name])) {
                    $returnArray[$new_name] = $_POST[$input_name];
                    continue;
                }
            }
            $this->_errors[$input_name] = true;
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$errorEmailOptions);
        }
        if (!$needupdate) {
            return array();
        }
        return $errormessage === "" ? $returnArray : $errormessage;
    }
    
    /**
     * Get SMTP options.
     * @return  String  $errormessage       Returns error message.
     */
    private function getAdditionalSMTPOptions() {
        $errormessage = "";
        $returnArray = array();
        $needupdate = false;
        if (isset($_POST["smtp_auth"])) {
            $returnArray['SMTP_AUTH'] = true;
        } else {
            $returnArray['SMTP_AUTH'] = false;
        }
        $allowed = array('false', 'tls', 'ssl');
        $array = array( "SMTP_HOST" => "smtp_host",
                        "SMTP_USERNAME" => "smtp_username",
                        "SMTP_PASSWORD" => "smtp_password",
                        "SMTP_SECURE" => "smtp_security",
                        "SMTP_PORT" => "smtp_port");
        $currentValues = array(
                        "SMTP_HOST" => SMTP_HOST,
                        "SMTP_USERNAME" => SMTP_USERNAME,
                        "SMTP_PASSWORD" => SMTP_PASSWORD,
                        "SMTP_SECURE" => SMTP_SECURE,
                        "SMTP_PORT" => SMTP_PORT);
        foreach ($array as $new_name => $input_name) {
            if (isset($_POST[$input_name])) {
                if ($currentValues[$new_name] != $_POST[$input_name]) {
                    $needupdate = true;
                }
                if ($input_name === "smtp_host" || $input_name === "smtp_username" ||  $input_name === "smtp_password" || ($input_name === "smtp_port" && is_numeric($_POST[$input_name]))) {
                    $returnArray[$new_name] = $_POST[$input_name];
                    continue;
                } elseif ($input_name === "smtp_security" && in_array($_POST[$input_name], $allowed)) {
                    $returnArray[$new_name] = $_POST[$input_name] == 'false' ? false : $_POST[$input_name];
                    continue;
                }
            }
            $this->_errors[$input_name] = true;
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$errorSMTPOptions);
        }
        if (!$needupdate) {
            return array();
        }
        if ($errormessage === "" && DEVELOPMENT_ENVIRONMENT == true) {
            $checkSmtp = $this->_fun->testSMTPOptions($returnArray, $this->_user);
            if ($checkSmtp === true) {
                return $returnArray;
            }
            $errormessage = $checkSmtp;
        }
        return $errormessage;
    }
    
    /**
     * Save the config
     * @param   Array $array Array with key => value pairs.
     * @return  Integer     $result      Number of characters written on success.
     */
    private function saveToConfig($array) {
        $contents = file_get_contents(ROOT . DS . "config" . DS . "config_ext.php");
        foreach ($array as $key => $newVal) {
            if (is_null($newVal)) {
                continue;
            }
            if (is_bool($newVal)) {
                $written = $newVal ? "true" : "false";
                $contents = preg_replace("/define\(\"$key\", .*\)/", "define(\"$key\", " . $written . ")", $contents);
            } elseif (is_int($newVal)) {
                $contents = preg_replace("/define\(\"$key\", .*\)/", "define(\"$key\", " . $newVal . ")", $contents);
            } else {
                $contents = preg_replace("/define\(\"$key\", \".*\"\)/", "define(\"$key\", \"" . $newVal . "\")", $contents);
            }
        }
        $config = fopen(ROOT . DS . "config" . DS . "config_ext.php", 'w');
        $result = fwrite($config, $contents, strlen($contents));
        fclose($config);
        return is_numeric($result);
    }
    
    /**
     * Get log options.
     * @return  String  $errormessage   Returns error message.
     */
    private function getAdditionalLOGOptions() {
        $errormessage = "";
        $returnArray = array();
        $needupdate = false;
        $array = array( "DB_LOG_HOST" => "db_log_host",
                        "DB_LOG_PORT" => "db_log_port",
                        "DB_LOG_USER" => "db_log_user",
                        "DB_LOG_PASSWORD" => "db_log_password",
                        "DB_LOG_NAME" => "db_log_name",
                        "DB_LOG_TABLE_NAME" => "db_log_table_name");
        $currentValues = array(
                        "DB_LOG_HOST" => DB_LOG_HOST,
                        "DB_LOG_PORT" => DB_LOG_PORT,
                        "DB_LOG_USER" => DB_LOG_USER,
                        "DB_LOG_PASSWORD" => DB_LOG_PASSWORD,
                        "DB_LOG_NAME" => DB_LOG_NAME,
                        "DB_LOG_TABLE_NAME" => DB_LOG_TABLE_NAME);
        foreach ($array as $new_name => $input_name) {
            if (isset($_POST[$input_name])) {
                if ($currentValues[$new_name] != $_POST[$input_name]) {
                    $needupdate = true;
                }
                if (in_array($input_name, array("db_log_host", "db_log_user", "db_log_name", "db_log_table_name"))) {
                    $returnArray[$new_name] = filter_var($_POST[$input_name], FILTER_SANITIZE_SPECIAL_CHARS);
                    continue;
                } elseif ($input_name === "db_log_port" && is_numeric($_POST[$input_name])) {
                    $returnArray[$new_name] = filter_var($_POST[$input_name], FILTER_SANITIZE_NUMBER_INT);
                    continue;
                } elseif ($input_name === "db_log_password") {
                    $returnArray[$new_name] = $_POST[$input_name];
                    continue;
                }
            }
            $this->_errors[$input_name] = true;
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$errorExternalDBOptions) . $new_name;
        }
        if (!$needupdate) {
            return array();
        }
        if ($errormessage === "") {
            $errormessage = $this->testDbConnection($returnArray['DB_LOG_HOST'], $returnArray['DB_LOG_NAME'], $returnArray['DB_LOG_USER'], $returnArray['DB_LOG_PASSWORD'], $returnArray['DB_LOG_PORT']);
        }
        if ($errormessage === true) {
            return $returnArray;
        }
        return $errormessage;
    }
    
    /**
     * test the filled in data.
     * @param   String $host     hostname.
     * @param   String $db_name  database name.
     * @param   String $user     username to log in with.
     * @param   String $password password of user to db.
     * @param   String $port     portnumber .
     * @return  Boolean                 true on success else false.
     */
    private function testDbConnection($host, $db_name, $user, $password, $port) {
        $temp;
        try {
            $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $db_name . ';charset=utf8';
            $temp = new PDO($dsn, $user, $password);
            $temp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $temp->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
        $temp = null;
        return true;
    }
}
