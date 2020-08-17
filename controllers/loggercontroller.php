<?php

/**
 * Logger Controller.
 * With this log system we track user activity on secured systems and errors that might happen
 * There are 3 levels of logs.
 * - Information: Just information of the use of several systems
 * - Warning: Might be important events such as failed loggin attempts
 * - Errors: Real errors like mysql errors
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
 * @uses       SG\Ram\functions                     General functions
 * @uses       SG\Ram\Models\Dbhelper               Database helper
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

use SG\Ram\Models\Dbhelper;

/**
 * LoggerController
 * @category   Controllers
 * @package    Ram
 */
class LoggerController
{
    private $website_name = WEBSITE_NAME;
    private $_db = null;
    private $loglevel = "Information";
    private $_tableName = 'log';
   
    /**
     * Contructor.
     * @return Void.
     */
    public function __construct() {
        if (USE_EXTERNAL_LOG) {
            $this->_db = new Dbhelper(null, true);
            $this->_tableName = DB_LOG_TABLE_NAME;
        } else {
            $this->_db = new Dbhelper(null, false);
        }
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
    }
    
    /**
     * Adds an information entry to the log database. Also logs the Ip address of the user and the username if given.
     * @param String $text     contains string.
     * @param String $username contains string.
     * @return boolean true || false.
     */
    public function info($text, $username = "Unknown") {
        if (DB_LOG_LEVEL > 1) {
            return false;
        }
        $this->loglevel = "Information";
        $this->witeLog($text, $username);
        return true;
    }
    
    /**
     * Adds a warning to the log database. Also logs the Ip address of the user and the username if given.
     * @param String $text     contains string.
     * @param String $username contains string.
     * @return Boolean true || false.
     */
    public function warning($text, $username = "Unknown") {
        if (DB_LOG_LEVEL > 2) {
            return false;
        }
        $this->loglevel = "Warning";
        $this->witeLog($text, $username);
        return true;
    }
 
    /**
     * Adds an error to the log database. Also logs the Ip address of the user and the username if given.
     * @param String $text     contains string.
     * @param String $username contains string.
     * @return void.
     */
    public function error($text, $username = "Unknown") {
        $this->loglevel = "Error";
        $this->witeLog($text, $username);
    }
   
    /**
     * Private function to write the actual log to the database
     * @param String $text     contains string.
     * @param String $username contains string.
     * @return void.
     */
    private function witeLog($text, $username) {
        $ip = "";
        if (USE_IP) {
            $ip = $this->_fun->getRealIpAddr();
        }
        $this->_db->setSql("INSERT INTO `" . $this->_tableName . "` (`username`, `description`, `ip`, `level`, `website`) VALUES (?, ?, ?, ?, ?)");
        $result = $this->_db->insertRecord([$username, $text, $ip, $this->loglevel, $this->website_name]);
        if (!is_numeric($result)) {
            $message = _("An error was encountered during the process of saving the following information to the log<br/>"
                        . "username:") . $username . _('<br/>'
                        . "ip:") . $ip . _('<br/>'
                        . "level:") . $this->loglevel . _('<br/>'
                        . "website:") . $this->website_name . _('<br/>'
                        . 'text:') . $text . _('<br>'
                        . "error:") . $result . _('<br/>'
                        . 'please look at it.');
            $this->_fun->sendMailHtmlWithAttachment(EMAIL_DANGERS_TO, _('An error was encountered'), $message);
        }
    }
}
