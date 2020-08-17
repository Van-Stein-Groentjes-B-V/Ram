<?php

/**
 * Account Controller
 * Handles login, register, password reset, session management
 * and cookie management.
 * Uses Account class for user credentials.
 * This controller has no views
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
 * @uses       \SG\Ram\Controller           Extend the main controller.
 * @uses       \SG\Ram\Controllers\Logger   Log controller
 * @uses       \SG\Ram\Models\Dbhelper      Database helper
 * @uses       \SG\Ram\Models\Account       Account object
 * @uses       \SG\Ram\ErrorMessage         Error message controller
 * @uses       \SG\Ram\functions            General functions class
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
 **/

namespace SG\Ram\Controllers;

use SG\Ram\Controller;
use SG\Ram\Models\Dbhelper;
use SG\Ram\Models\Account;

/**
 * AccountController
 * @category   Controllers
 * @package    Ram
 */
class AccountController extends Controller
{
    private $_user;
    private $_db;
    private $_log;
    private $_bcryptOptions;
    
    /**
     * Constructor with access management.
     * @return Void.
     */
    public function __construct() {
        parent::__construct();
        $this->_db = new Dbhelper();
        $this->_user = new Account();
        if (USE_LOG) {
            $this->_log = new LoggerController();
        }
        $this->_bcryptOptions = array('cost' => BCRYPT_COST);
        // See if user is loggedin and try to restore session.
        $this->restoreSession();
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_user);
        unset($this->_log);
    }
    
    /**
     * Restore Session to log in user that already has cookies or session set.
     * @return  String  $functionName       Contains an error message and some error codes return a custom message.
     * */
    private function restoreSession() {
        if (isset($_SESSION[SESSION_PREFIX . 'USER_ID'])) {
            $userid = $_SESSION[SESSION_PREFIX . 'USER_ID'];
            $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `id` = ? LIMIT 1");
            $this->_user = new Account($this->_db->getRow(array($userid)));
            if ($this->_user->hasContent()) {
                $this->_user->setLoggedIn(true);
            } else {
                return $this->_err->get(\SG\Ram\ErrorMessage::$queryError);
            }
        }
        if (USE_COOKIE && isset($_COOKIE["USER_NAME"]) && isset($_COOKIE["USER_SESSION"])) {
            $username = $_COOKIE["USER_NAME"];
            $usersession = $_COOKIE["USER_SESSION"];
            $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `username` = ? LIMIT 1");
            $USERtemp = new Account($this->_db->getRow(array($username)));
            if ($USERtemp->hasContent()) {
                $userid = $USERtemp->getId();
                $this->_db->setSql("SELECT * FROM `user_session` WHERE `user_id` = ? AND `sessioncode` = ?");
                $sessionResult = $this->_db->getRow(array($userid, $usersession));
                if ($sessionResult && !empty($sessionResult)) {
                    // User has valid session cookie. So lets log him in.
                    $this->_user = $USERtemp;
                    $this->_user->setLoggedIn(true);
                    $this->setSession();
                } else {
                    // User has no valid session cookie.
                    // Destroy cookies.
                    $this->eatCookies();
                }
            } else {
                return $this->_err->get(\SG\Ram\ErrorMessage::$queryError);
            }
        }
        // Set personal settings, if exists.
        $this->personalSettings();
    }

    /**
     * Sets the session for $USER.
     * @return Void.
     * */
    private function setSession() {
        $_SESSION[SESSION_PREFIX . 'USER_ID'] = $this->_user->getId();
        $_SESSION[SESSION_PREFIX . 'USER_TYPE'] = $this->_user->getAdmin();
        if ($this->_user->getFullname() != "") {
            $_SESSION[SESSION_PREFIX . 'USER_NAME'] = $this->_user->getFullname();
        } else {
            $_SESSION[SESSION_PREFIX . 'USER_NAME'] = $this->_user->getUsername();
        }
    }

    /**
     * Destroys current session.
     * @return  Void.
     * */
    private function deleteSession() {
        unset($_SESSION[SESSION_PREFIX . 'USER_ID']);
        unset($_SESSION[SESSION_PREFIX . 'USER_TYPE']);
        unset($_SESSION[SESSION_PREFIX . 'USER_NAME']);
        unset($_SESSION[SESSION_PREFIX . 'ACTIVEPROJECT']);
    }

    /**
     * Creates a new session for current user.
     * @return  Boolean     True if session was created successfully.
     * */
    private function createSession() {
        $newsessionid = $this->_fun->createRandomID();
        $userid = $this->_user->getId();
        $this->_user->setSessionid($newsessionid);
        $ip = "";
        if (USE_IP) {
            $ip = $this->_fun->getRealIpAddr();
        }
        $this->_db->setSql("INSERT INTO `user_session` (`user_id`, `sessioncode`, `ip`) VALUES (?, ?, ?)");
        $result = $this->_db->insertRecord(array($userid, $newsessionid, $ip));
        if (!is_numeric($result)) {
            return false;
        }
        $this->setCookies();
        $this->setSession();
        return true;
    }

    /**
     * Sets the cookies for $USER
     * @return  Void.
     * */
    private function setCookies() {
        if (USE_COOKIE) {
            // Expire in 10 hour
            setcookie("USER_NAME", $this->_user->getUsername(), time() + 90000, '/', "", true, true);
            setcookie("USER_SESSION", $this->_user->getSessionid(), time() + 90000, '/', "", true, true);
        }
    }

    /**
     * Destroys current cookies. (yum yum).
     * @return  Void.
     * */
    private function eatCookies() {
        setcookie("USER_NAME", "", time() - 3600, '/', "", true, true);
        setcookie("USER_SESSION", "", time() - 3600, '/', "", true, true);
    }
    
    /**
     * Handle the failure of the login
     * @param integer $error     The integer representing the error.
     * @param Boolean $doPenalty Whether penalty checker must be set.
     * @param String  $log       The string to be set as error in the log.
     * @param String  $logExtra  The extra parameter for the log.
     * @return String
     */
    private function handleFailureLogin($error, $doPenalty, $log, $logExtra = "Unknown") {
        if (USE_LOG) {
            $this->_log->warning($log, $logExtra !== "" ? $logExtra : "Unknown");
        }
        if ($doPenalty) {
            $this->penaltyChecker();
        }
        return $this->_err->get($error);
    }
                
    /**
     * Lets a user login into the system.
     * @param   String $username unique username.
     * @param   String $password user password.
     * @param   String $captcha  [Optional] code from image.
     * @return  Boolean|String          True if user is successfully logged in, string with error otherwise.
     * */
    private function login($username, $password, $captcha = "") {
        if (!isset($_SESSION[SESSION_PREFIX . 'iteratie'])) {
            $_SESSION[SESSION_PREFIX . 'iteratie'] = 1;
        }
        if (USE_SECUREIMAGE_LOGIN && (!is_array($captcha) || !isset($captcha['success']) || !$captcha['success'])) {
            return $this->handleFailureLogin(\SG\Ram\ErrorMessage::$securityCodeIncorrect, true, "Login with incorrect captcha");
        }
        $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `username` = ? LIMIT 1");
        $this->_user = new Account($this->_db->getRow(array($username)));
        if (!$this->_user->hasContent() ||  !$this->_fun->verifyPasswordHash($password, $this->_user->getPassword())) {
            return $this->handleFailureLogin(\SG\Ram\ErrorMessage::$incorrectCredentials, true, "Incorrect password or username", $this->_user->getUsername());
        }
        if ($this->_user->getActive() == 0) {
            return $this->_err->get(\SG\Ram\ErrorMessage::$activationInvalid);
        }
        if ($this->createSession()) {
            $this->_user->setLoggedIn(true);
            $this->personalSettings();
            if (USE_LOG) {
                $this->_log->info("User logged in", $username);
            }
            return true;
        }
        return $this->handleFailureLogin(\SG\Ram\ErrorMessage::$somethingWentWrong, true, "Could not make the session!", $this->_user->getUsername());
    }
    
    /**
     * Checks for penalties.
     * @return  Boolean.
     */
    private function penaltyChecker() {
        $tijd = time();
        $send = false;
        if (isset($_SESSION[SESSION_PREFIX . "time"]) && (time() - $_SESSION[SESSION_PREFIX . "time"] < 3600)) {
            $logophaal = $this->getActiveLog();
            $teller = 0;
            foreach ($logophaal as $row) {
                $_SESSION[SESSION_PREFIX . "ip"] = $this->_fun->getRealIpAddr();
                if ($_SESSION[SESSION_PREFIX . "ip"] == $row['ip'] && strtotime($row['logtime']) > $_SESSION[SESSION_PREFIX . "time"] && $row['description'] == "Incorrect password or username") {
                    $teller++;
                }
                if ($teller == 3 && !$send) {
                    $_SESSION[SESSION_PREFIX . 'penalty'] = time() + (120 * $_SESSION[SESSION_PREFIX . 'iteratie']);
                    $_SESSION[SESSION_PREFIX . 'iteratie'] ++;
                    $send = true;
                }
            }
        } else {
            $_SESSION[SESSION_PREFIX . "time"] = $tijd;
        }
        return $send;
    }
    
    /**
     * Get the log from today.
     * @return  Array       An array with the log.
     */
    private function getActiveLog() {
        $this->_db->setSql("SELECT * FROM `log` WHERE `logtime` >= now() - interval 1 DAY");
        return $this->_db->getRows();
    }

    /**
     * Lets a user login into the system with a post form.
     * @param   Array $POST Array of post values, should contain username and password.
     * @return  Boolean         If it's false it returns an error message instead of boolean.
     * */
    public function loginPost($POST) {
        $captcha = "";
        if (USE_SECUREIMAGE_LOGIN) {
            if (isset($_POST['g-recaptcha-response'])) {
                $captcha = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . CAPTCHA_SECRET .
                "&response=" . $_POST['g-recaptcha-response'] . "&remoteip=" . $_SERVER['REMOTE_ADDR']), true);
            } else {
                $captcha = array("success" => false);
            }
        }
        return $this->login($POST['username'], $POST['password'], $captcha);
    }

    /**
     * Lets a user register into the system.
     * @param String  $username  Unique username.
     * @param String  $password  User password.
     * @param String  $password2 User password retyped.
     * @param String  $email     Email for recovery etc.
     * @param String  $captcha   [Optional] code from image.
     * @param Boolean $admin     If the account is an admin account or not.
     * @param Array   $optionals [optional] array with extra user info. Can include:
     *                           fullname, street, streetnumber, postcode, country,
     *                           company.
     * @return Boolean|String    True if user is successfully registered, string with error otherwise.
     * */
    private function register($username, $password, $password2, $email, $captcha = "", $admin = false, $optionals = array()) {
        $insertVals = array();
        $insertKeys = "";
        $questionmarks = "";
        $errormessage = "";
        // Sanity checks
        if (!$this->_fun->checkEmail($email)) {
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$emailNotValid);
        }
        if ($this->_fun->containsNotAllowedValues($username)) {
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$notAllowedChar);
        }
        $password_errors = array();
        if (!$this->_fun->checkPassword($password, $password_errors)) {
            $errormessage = $password_errors[0];
        }
        if ($errormessage != "") {
            return $errormessage;
        }
        $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `username` = ? OR `email` = ? LIMIT 1");
        $userResult = new Account($this->_db->getRow(array($username, $email)));
        
        if ($userResult->Exists()) {
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$cantSendEmail);
        }
        if ($password != $password2) {
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$passwordsDoNotMatch);
        }
        if ($errormessage != "") {
            return $errormessage;
        }
        $hash = $this->_fun->createpasswordhash($password, 1, $this->_bcryptOptions);
        $activationToken = $this->_fun->createRandomID();
        $fullname = isset($optionals['fullname']) ? $optionals['fullname'] : $username;
        $emails = $this->handleregisterActivationMail($insertKeys, $insertVals, $questionmarks, $activationToken);
        $result = $this->handleSavingNewUser($optionals, $insertKeys, $insertVals, $questionmarks, array($username, $hash, $email), $admin);
        if (is_numeric($result)) {
            $toReturn = $this->handleLastStepRegister($emails, $username, $email, $activationToken, $fullname);
        } else {
            $toReturn = $this->_err->get(\SG\Ram\ErrorMessage::$queryError);
        }
        return $toReturn;
    }
    
    /**
     * Handle the last step of logging in.
     * @param array   $emails            The title and body to be send.
     * @param String  $username          The username of the person registered.
     * @param String  $email             The email of the person registered
     * @param String  $activationToken   The activation token.
     * @param String  $fullname          The fullname of the user registered.
     * @param Boolean $registeredByOther Whether login should be done.
     * @return Boolean|String
     */
    private function handleLastStepRegister($emails, $username, $email, $activationToken, $fullname, $registeredByOther = false) {
        $errormessage = "";
        if (USE_LOG) {
            $this->_log->info("User registered", $username);
        }
        if (SEND_MAIL) {
            if (ACTIVATE_ACCOUNTS || SET_OWN_PASSWORD_EMAIL || $registeredByOther) {
                $message = $this->_fun->replaceSlugs($emails['body'], array('username' => $username, "activation_token" => urlencode($activationToken), "email" => urlencode($email)));
                if (!$this->_fun->sendMailHtmlWithAttachment($email, $emails['title'], $message)) {
                    $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$emailFailedToSend);
                }
            }
            // Send email with first time login details
            if (
                !$this->_fun->sendMailHtmlWithAttachment(
                    $email,
                    STANDARD_REGISTER_EMAIL_TITLE,
                    $this->_fun->replaceSlugs(STANDARD_REGISTER_EMAIL, array('username' => $username, "email" => $email, "fullname" => $fullname))
                )
            ) {
                $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$emailFailedToSend);
            }
        }
        $toReturn = $errormessage === "" ? true : $errormessage;
        if ($registeredByOther && !ACTIVATE_ACCOUNTS && $toReturn === true) {
            return $this->login($username, $password);
        }
        return $toReturn;
    }
    
    /**
     * Handle the saving of a new user.
     * @param Array           $optionals         The optionals send to the function to be filled in at the user.
     * @param String          $insertKeys        The insertion string.
     * @param Array           $insertVals        The values to be inserted.
     * @param String          $questionmarks     The string with the question marks.
     * @param Array           $usernameHashEmail The array with username, hash , email
     * @param Boolean|Integer $admin             Whether it should be an admin
     * @return Integer|Boolean
     */
    private function handleSavingNewUser($optionals, $insertKeys, $insertVals, $questionmarks, $usernameHashEmail, $admin) {
        if ($admin !== false) {
            $insertKeys .= ", `admin`";
            $insertVals[] = is_numeric($admin) ? $admin : 1;
            $questionmarks .= ", ?";
        }
        $allowed = array("fullname", "street", "streetnumber", "postcode", "country", "company");
        foreach ($optionals as $name => $value) {
            if (in_array($name, $allowed)) {
                $insertKeys .= ", `$name`";
                $insertVals[] = $value;
                $questionmarks .= ", ?";
            }
        }
        $this->_db->setSql("INSERT INTO `user_accounts` (`username`, `ps`, `email` $insertKeys) VALUES (?, ?, ? $questionmarks)");
        $toInsert = array_merge($usernameHashEmail, $insertVals);
        return $this->_db->insertRecord($toInsert);
    }
    
    /**
     * Handle the insert values on whether send mail and other things are set.
     * @param String $insertKeys      The insertion string
     * @param Array  $insertVals      The values to be inserted.
     * @param String $questionmarks   The question mark string.
     * @param String $activationToken The activation token
     * @return array The email body and title
     */
    private function handleregisterActivationMail(&$insertKeys, &$insertVals, &$questionmarks, $activationToken) {
        $toReturnEmails = array("body" => "", "title" => "");
        if (SEND_MAIL && (ACTIVATE_ACCOUNTS || SET_OWN_PASSWORD_EMAIL)) {
            $insertKeys .= ", `rand`";
            $insertVals[] = $activationToken;
            $questionmarks .= ", ?";
            if (ACTIVATE_ACCOUNTS && !SET_OWN_PASSWORD_EMAIL) {
                $toReturnEmails['body'] = ACTIVATE_NOPASSWORD_EMAIL;
                $toReturnEmails['title'] = ACTIVATE_NOPASSWORD_EMAIL_TITLE;
            } elseif (SET_OWN_PASSWORD_EMAIL) {
                $toReturnEmails['body'] = ACTIVATE_OWN_PASSWORD_EMAIL;
                $toReturnEmails['title'] = ACTIVATE_OWN_PASSWORD_EMAIL_TITLE;
            }
        } else {
            $insertKeys .= ", `active`";
            $insertVals[] = 1;
            $questionmarks .= ", ?";
        }
        return $toReturnEmails;
    }

    /**
     * Lets a user register into the system with a post form.
     * @param   Array   $POST  Post values, should contain at least username, password, password2 and email.
     * @param   Boolean $admin If the user should be an admin or not.
     * @return  Boolean | String        True if user is successfully registered, string with error otherwise.
     * */
    public function registerPost($POST, $admin = false) {
        $username = $POST['username'];
        if (isset($POST['password']) && isset($POST['password2'])) {
            $password = $POST['password'];
            $password2 = $POST['password2'];
        } elseif (isset($POST['password'])) {
            $password = $password2 = $POST['password'];
        } else {
            $password = $password2 = $this->_fun->createRandomPass();
        }
        $email = $POST['email'];
        $captcha = "";
        unset($POST['username']);
        unset($POST['password']);
        unset($POST['password2']);
        unset($POST['email']);
        return $this->register($username, $password, $password2, $email, $captcha, $admin, $POST);
    }

    /**
     * Activate account using token and email.
     * @param   String $token Security token.
     * @param   String $email Username.
     * @return  Boolean                 True if activated, false if error.
     */
    public function activate($token, $email) {
        $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `email` = ? AND `rand` = ? LIMIT 1");
        $userResult = new Account($this->_db->getRow(array($email, $token)));
        if ($userResult->Exists()) {
            $this->_db->setSql("UPDATE `user_accounts` SET `active` = '1' ,`rand` = '' WHERE `email` = ? AND `rand` = ?");
            $activated = $this->_db->updateRecord(array($email, $token));
            if ($activated === true) {
                if (USE_LOG) {
                    $this->_log->info("User activated", $email);
                }
                return true;
            }
        }
        if (USE_LOG) {
            $this->_log->warning("User failed to activate", $email);
        }
        return false;
    }

    /**
     * Logout user and reset session.
     * @return  Boolean     Returns true.
     * */
    public function logout() {
        $userid = $this->_user->getId();
        $sessionid = $this->_user->getSessionid();
        $this->deleteSession();
        $this->eatCookies();
        $this->_user->setLoggedIn(false);
        $this->_db->setSql("DELETE FROM `user_session` WHERE `user_id` = ? AND `sessioncode` = ?");
        $this->_db->updateRecord(array($userid, $sessionid));
        return true;
    }

    /**
     * Gets current user.
     * @return Account.
     * */
    public function getUser() {
        return $this->_user;
    }
    
    /**
     * Get current users id.
     * @return Integer Id of the user.
     */
    public function getUserId() {
        return $this->_user->getId();
    }

    /**
     * Sends password reset email.
     * @param   String $email Username.
     * @return  String | Boolean        True or error messagee.
     * */
    public function sendResetPassword($email) {
        $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `email` = ?");
        $userr = new Account($this->_db->getRow(array($email)));
        if ($userr->hasContent()) {
            if (USE_LOG) {
                $this->_log->info("User requested new pasword", $email);
            }
            $username = $userr->getUsername();
            $randToken = $this->_fun->createRandomID();
            $body = $this->_fun->replaceSlugs(RESET_PASSWORD_EMAIL, array('username' => $username, "activation_token" => $randToken, "email" => $email));
            $this->_db->setSql("UPDATE `user_accounts` SET `rand` = ? WHERE `email` = ?");
            if ($this->_db->updateRecord(array($randToken, $email))) {
                if (!$this->_fun->sendMailHtmlWithAttachment($userr->getEmail(), RESET_PASSWORD_EMAIL_TITLE, $body)) {
                    return $this->_err->get(\SG\Ram\ErrorMessage::$emailFailedToSend);
                }
                return true;
            }
        }
        return _("User does not exist.");
    }
    
    /**
     * Alert the main admin.
     * @param   String $email Email address / username.
     * @return  Integer | Boolean       Error code or true.
     **/
    public function alertMainAdmin($email) {
        $body = $this->_fun->replaceSlugs(ALERT_MAIN_ADMIN_EMAIL, array("email" => $email));
        $this->_db->setSql("UPDATE `user_accounts` SET `rand` = '' WHERE `email` = ?");
        if ($this->_db->updateRecord(array($email)) && !$this->_fun->sendMail(EMAIL_DANGERS_TO, ALERT_MAIN_ADMIN_EMAIL_TITLE, $body)) {
            return $this->_err->get(\SG\Ram\ErrorMessage::$emailFailedToSend);
        }
        return true;
    }
    
    /**
     * Check if the user is an admin.
     * @return  Integer     Adminlevel.
     **/
    public function isAdmin() {
        return $this->_user->isAdmin();
    }
    
    /**
     * Check if the user is an admin.
     * @return  Integer     Adminlevel.
     **/
    public function isCustomer() {
        return $this->_user->isCustomer();
    }
    
    /**
     * Check if user is logged in.
     * @return  Boolean     True if user is logged in.
     **/
    public function isLoggedIn() {
        return $this->_user->isLoggedIn();
    }
    
    /**
     * Add an account for someone else.
     * @param  String  $username    Name of the account.
     * @param  String  $email       Username of the account.
     * @param  Boolean $admin       If the new account is an admin account or not.
     * @param  Array   $extraValues Any extra values you want to store.
     * @return Integer                  New id or errorcode.
     **/
    public function registerAnotherUser($username, $email, $admin = false, $extraValues = array()) {
        $errormessage = "";
        if (!SEND_MAIL) {
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$cantSendEmail);
        }
        if (!$this->_user->isSuperAdmin() || ($admin !== false && $admin > $this->_user->getAdmin())) {
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$adminLevel);
        }
        // Sanity checks
        if (!$this->_fun->checkEmail($email)) {
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$emailNotValid, $email);
        }
        if ($errormessage != "") {
            return $errormessage;
        }
        
        $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `email` = ? LIMIT 1");
        $userResult = new Account($this->_db->getRow(array($email)));
        if ($userResult->Exists()) {
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$emailAlreadyExists);
        }
        $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `username` = ? LIMIT 1");
        $userResult2 = new Account($this->_db->getRow(array($username)));
        if ($userResult2->Exists()) {
            $errormessage = $this->_err->get(\SG\Ram\ErrorMessage::$usernameExists);
        }
        if ($errormessage != "") {
            return $errormessage;
        }
        
        $activationToken = $this->_fun->createRandomID();
        $insertKeys = ", `rand`";
        $insertVals = array($activationToken);
        $questionmarks = ", ?";
        $fullname = isset($extraValues['fullname']) ? $extraValues['fullname'] : $username;
        $result = $this->handleSavingNewUser($extraValues, $insertKeys, $insertVals, $questionmarks, array($username, -42, $email), $admin);
        if (is_numeric($result)) {
            return $this->handleLastStepRegister(array('body' => ADD_USER_EMAIL, 'title' => ADD_USER_EMAIL_TITLE), $username, $email, $activationToken, $fullname, true);
        } else {
            return $this->_err->get(\SG\Ram\ErrorMessage::$queryError);
        }
    }
    
    /**
     * Adds a warning to the log database. Also logs the Ip adress of the user and the username if given.
     * @param   String $string Warningtext.
     * @param   String $email  If the email is null the var will be declared with unkown.
     * @return  Void.
     **/
    public function warning($string, $email = "unknown") {
        if (USE_LOG) {
            $this->_log->warning($string, $email);
        }
    }
    
    /**
     * Change the username of the current user.
     * @param   String $username New username.
     * @return  Boolean             True if success.
     **/
    public function changeUsername($username) {
        $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `username` = ? LIMIT 1");
        $userResult = new Account($this->_db->getRow(array($username)));
        if ($userResult->Exists()) {
            return $this->_err->get(\SG\Ram\ErrorMessage::$usernameExists);
        }
        $this->_db->setSql("UPDATE `user_accounts` SET `username` = ? WHERE `id` = ?");
        $idN = $this->_user->getId();
        return $this->_db->updateRecord(array($username, $idN));
    }
    
    /**
     * Change email of the current user.
     * @param   String $email Email.
     * @return  Boolean             True if success.
     **/
    public function changeEmail($email) {
        $this->_db->setSql("SELECT * FROM `user_accounts` WHERE `email` = ? LIMIT 1");
        $userResult = new Account($this->_db->getRow(array($email)));
        if ($userResult->Exists()) {
            return $this->_err->get(\SG\Ram\ErrorMessage::$emailAlreadyExists);
        }
        $this->_db->setSql("UPDATE `user_accounts` SET `email` = ? WHERE `id` = ?");
        $idN = $this->_user->getId();
        return $this->_db->updateRecord(array($email, $idN));
    }
    
    /**
     * This functions sets the person's information.
     * @return Void.
     */
    private function personalSettings() {
        //set personal settings, if exists.
        if ($this->_user->hasContent()) {
            $this->_db->setSql("SELECT * FROM `account_settings` WHERE `account_id` = ?");
            $settings = $this->_db->getRow(array($this->_user->getId()));
            if ($settings) {
                $this->_user->setPlaySounds($settings['play_sounds']);
                $this->_user->setShowStats($settings['show_stats']);
            }
            $this->_db->setSql("SELECT `logo` FROM `persons` WHERE `account_id` = ?");
            $logo = $this->_db->getRow(array($this->_user->getId()));
            if ($logo && trim($logo['logo']) !== "") {
                $this->_user->setLogo($logo['logo']);
            }
        } else {
            return false;
        }
    }
}
