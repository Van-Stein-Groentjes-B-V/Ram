<?php

/**
 * Index Controller.
 * Main controller of the system. Whenever a page or link does not exist the
 * Index function in this class is called.
 * Has public login and logout functions
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
 * @uses       \SG\Ram\function                         General functions file.
 * @uses       \SG\Ram\dataHandler                      Handler class for data.
 * @uses       \SG\Ram\Controllers\AccountController    Account controller class.
 * @uses       \SG\Ram\Controllers\DashboardController  Dashboard / home controller.
 * @uses       \SG\Ram\Models\Dbhelper                  Database helper class
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
use SG\Ram\Controllers\DashboardController;
use SG\Ram\Models\Dbhelper;

/**
 * IndexController
 * @category   Controllers
 * @package    Ram
 */
class IndexController extends Controller
{
    private $_user;
    private $_data;
    private $_db;
    
    /**
     * Checks if user is true and logs the login session.
     * @global AccountController $user.
     * @return Void.
     */
    public function __construct() {
        parent::__construct();
        global $user;
        $this->_user = $user ? $user : new AccountController();

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
     * Loads the content in the index page.
     * @return Void.
     */
    public function index() {
        $this->assign("indicater", 'home');
        
        $this->_db->setSql("SELECT `name` FROM `companies` WHERE `id` = 1;");
        $companyname = $this->_db->getRow();
        if (isset($companyname['name'])) {
            $this->assign("company", $companyname['name']);
        }
        $this->LoadViewer("index", "index", 'Login', array("dark"), array("jquery.ui", "jquery.lavalamp.min", "standard"), false, 2);
    }
    
    /**
     * Checks if user logged in or already is logged in.
     * @return Void.
     */
    public function login() {
        $this->assign("indicater", 'loginUser');
        if ($this->_user->isLoggedIn()) {
            $this->LoadViewer('index', 'alreadyloggedin', 'Login', array("dark"), array("jquery.ui", "jquery.lavalamp.min", "standard"), false, 2);
            header("refresh:5; url=" . SITE_ROOT . "dashboard/");
            return;
        }
        if (isset($_POST['password'])) {
            $result = $this->_user->loginPost($_POST);
            if ($result === true) {
                $dash = new DashboardController();
                $dash->index();
                return;
            }
            $this->Assign("errormessage", $result);
            $this->Assign("oldData", $_POST);
        }
        $this->LoadViewer("index", "login", "Login", array("dark", "login"), array("backend"), false, 2);
    }
    
    /**
     * Makes an request to reset email.
     * @return Void.
     */
    public function requestPassword() {
        $noSuccess = false;
        if (isset($_POST['emailRQPS'])) {
            $emailCheck = $this->_fun->checkEmail($_POST['emailRQPS']);
            if ($emailCheck !== false) {
                $this->_db->setSql("SELECT `email` FROM `user_accounts` WHERE `email` = ?");
                $results = $this->_db->getRow(array($_POST['emailRQPS']));
                if ($results != null) {
                    $result = $this->_user->sendResetPassword(htmlspecialchars($_POST['emailRQPS']));
                    if ($result !== true) {
                        $this->Assign("errormessage", $result);
                        $noSuccess = true;
                    }
                }
            }
            if (!$noSuccess) {
                $this->Assign("successmessage", _("If the email adres you submitted is known, you will receive a link to reset your password."));
            }
        }
        $this->LoadViewer("index", "requestpassword", 'Login', array("dark"), array("jquery.ui", "jquery.lavalamp.min", "standard"), false, 2);
    }
    
    /**
     * Logs the user out.
     * @return Void.
     */
    public function logout() {
        $this->_user->logout();
        header("refresh:0; url=" . SITE_ROOT);
    }
}
