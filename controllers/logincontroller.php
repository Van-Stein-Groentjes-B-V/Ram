<?php

/**
 * Login Controller.
 * Handles the login and saves new password
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
 * @uses       \SG\Ram\Controller                       Extend the main controller.
 * @uses       \SG\Ram\dataHandler                      Data handler class.
 * @uses       \SG\Ram\Controllers\Accountcontroller    Account controller.
 * @uses       \SG\Ram\Models\Dbhelper                  Database helper model.
 * @uses       \SG\Ram\Functions                        General functions.
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
use SG\Ram\Controllers\AccountController;
use SG\Ram\Models\Dbhelper;

/**
 * LoginController
 * @category   Controllers
 * @package    Ram
 */
class LoginController extends Controller
{
    private $_user;
    private $_data;
    private $_db;
    
    /**
     * Constructor: __construct.
     * Assemble and pre-process the data.
     * @return  void.
     */
    public function __construct() {
        parent::__construct();
        global $user;
        if ($user) {
            $this->_user = $user;
        } else {
            $this->_user = new AccountController();
        }
        
        $this->_data = new dataHandler();
        $this->_db = new Dbhelper();
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_data);
        unset($this->_user);
    }
    
    /**
     * Index function. Default function to be called, and redirects to login page
     * @return (function) header with message.
     */
    public function index() {
        header("refresh:0; url=" . SITE_ROOT . "index/login/");
    }
    
    /**
     * Set password if correct values are set.
     * Logs if someone comes here without correct values, if log is available.
     * Usually only get here by email link.
     * @param   String $activation      Randomized string made by system.
     * @param   String $emailUrlencoded Email from person trying to reset email.
     * @return  Void.
     */
    public function setPassword($activation, $emailUrlencoded) {
        if (!is_null($activation) && !is_null($emailUrlencoded)) {
            $email = urldecode($emailUrlencoded);
            $this->_db->setSql("SELECT `id` FROM `user_accounts` WHERE `rand` = ? AND `email` = ?");
            $result = $this->_db->getRow(array($activation, $email));
            if ($result) {
                if (isset($_POST['password']) && (is_numeric($this->saveNewPassword($result)))) {
                    $this->LoadViewer('index', 'passwordset', 'Login', array("dark"), array("jquery.ui", "jquery.lavalamp.min", "standard"), false, 2);
                    return;
                }
                $this->LoadViewer('index', 'setPassword', 'Login', array("dark"), array("jquery.ui", "jquery.lavalamp.min", "standard"), false, 2);
                return;
            }
        }
        if (USE_LOG) {
            $this->_account->sentWarning('someone went to the create new passowrd without the accepted values');
        }
        header("refresh:0; url=" . SITE_ROOT);
    }

    
    /**
     * Same check as password only now reports a person trying to fake password resets.
     * Logs if someone comes here without correct values, if log is available.
     * Usually only get here by email link.
     * @param   String $activation Randomized string made by system.
     * @param   String $email      Email from person trying to reset email.
     * @return  Void.
     */
    public function reportFalse($activation, $email) {
        if (!is_null($activation) && !is_null($email)) {
            $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `rand` = ? AND `email` = ?");
            $result = $this->_db->getRow(array($activation, $email));
            if ($result) {
                if (USE_LOG) {
                    $this->_account->sentWarning("Someone other than the owner requested a new password for $email");
                }
                $this->_user->alertMainAdmin($email);
                $this->LoadViewer('index', 'thankyou', 'Login', array("dark"), array("jquery.ui", "jquery.lavalamp.min", "standard"), false, 2);
                return;
            } else {
                //link is invalid or outdated
                $this->Assign('errormessage', $this->_err->get(\SG\Ram\ErrorMessage::$invalidLink));
                $this->LoadViewer('index', 'notactivated', 'Login', array("dark"), array("jquery.ui", "jquery.lavalamp.min", "standard"), false, 2);
                return;
            }
        }
        if (USE_LOG) {
            $this->_account->sentWarning('Someone went somewhere he shouldn\'t go without the correct values');
        }
        header("refresh:0; url=" . SITE_ROOT);
    }
    
    /**
     * Activate account, unsets rand.
     * Logs if someone comes here without correct values, if log is available.
     * @param   String $activation Randomized string made by system.
     * @param   String $email      From person trying to reset email.
     * @return  (function)  LoadViewer.
     */
    public function activate($activation, $email) {
        if (!is_null($activation) && !is_null($email)) {
            $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `rand` = ? AND `email` = ? and `active` = 0");
            $result = $this->_db->getRow(array($activation, $email));
            if ($result) {
                $this->_db->setSql("UPDATE `user_accounts` SET `rand` = ?, `active` =? WHERE `id` = ?");
                $this->_db->updateRecord(array("", 1, $result['id']));
                return $this->LoadViewer('index', 'activated', 'Login', array("dark"), array("jquery.ui", "jquery.lavalamp.min", "standard"), false, 2);
            }
        }
        if (USE_LOG) {
            $this->_account->sentWarning('someone went to the create new passowrd without the accepted values');
        }
        $this->Assign('errormessage', $this->_err->get(\SG\Ram\ErrorMessage::$activationFailed));
        return $this->LoadViewer('index', 'notactivated', 'Login', array("dark"), array("jquery.ui", "jquery.lavalamp.min", "standard"), false, 2);
    }
    
    /**
     * Checks password and if correct set new password AND active AND remove the rand.
     * @param   Array $result Row from the db, with user info
     * @return  Boolean|Integer false || Last inserted ID
     */
    private function saveNewPassword($result) {
        if (!isset($_POST['password']) && !isset($_POST['password2'])) {
            return false;
        }
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        if (!$this->_fun->checkPassword($password) || $password != $password2) {
            $this->Assign('errors', array('password' => true));
            $this->Assign('oldData', $_POST);
            return false;
        }
        $hash = $this->_fun->createPasswordHash($password, 1, array('cost' => BCRYPT_COST));
        $this->_db->setSql("UPDATE `user_accounts` SET `ps` = ?, `rand` = ?, `active` = 1 WHERE `id` = ?");
        return $this->_db->insertRecord(array($hash, "", $result['id']));
    }
}
