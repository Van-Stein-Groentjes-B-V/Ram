<?php

/**
 * ErrorMessage class. Defines the possible error messages that can be send to
 * the user.
 * Gets a message with a certain error-code
 *
 * PHP version 7+
 *
 * @category   Library
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Thomas Shamoian <thomas@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 *
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
 * </TODO> Make all error messages in the system use this class.
 */

namespace SG\Ram;

/**
 * ErrorMessage
 * @category   Library
 * @package    Ram
 */
class ErrorMessage
{
    //Error messages
    public static $queryError = 1;
    public static $incorrectCredentials = 2;
    public static $usernameExists = 3;
    public static $passwordsDoNotMatch = 4;
    public static $activationInvalid = 5;
    public static $emailFailedToSend = 6;
    public static $emailNotValid = 7;
    public static $emailAlreadyExists = 8;
    public static $teamNameIsUsed = 9;
    public static $fillInCaptcha = 12;
    public static $securityCodeIncorrect = 13;
    public static $cantSendEmail = 14;
    public static $adminLevel = 15;
    public static $notAllowedChar = 16;
    public static $usernameOrEmailRegistered = 17;
    public static $errorsInTheValues = 18;
    public static $somethingWentWrong = 19;
    public static $youHaveNoPermission = 20;
    public static $pluginError1 = 21;
    public static $incorrectValues = 22;
    public static $timeslotIsTaken = 23;
    public static $pageDoesNotExists = 24;
    public static $compamyDeleted = 25;
    public static $NeedToHavePermissionToWriteInFile = 26;
    public static $uncheckForceHTTPS = 27;
    public static $cantLoginIntoDB = 28;
    public static $errorsDuringDBInstallation = 29;
    public static $amountFilesAreNotConsistent = 30;
    public static $needModuleOrModulesForThisToWork = 31;
    public static $incorrectModuleSetup = 32;
    public static $WentWrongDBUpdate = 33;
    public static $configUpdateIncorrect = 34;
    public static $personDeleted = 35;
    public static $createAccountPersonError = 36;
    public static $cantUseHTTPS = 37;
    public static $unableToEditConfigFile = 38;
    public static $errorEmailOptions = 39;
    public static $errorSMTPOptions = 40;
    public static $errorExternalDBOptions = 41;
    public static $notActive = 42;
    public static $moduleDeactivate  = 43;
    public static $activationFailed = 44;
    public static $invalidLink = 45;
    public static $dbExist = 46;
    public static $noPermissionToDelete = 47;
    public static $duplicate = 48;
    public static $notAllowed = 49;
    public static $errorArray = array();
    
    /**
     * construct the errormessages.
     * @return Void
     */
    public function __construct() {
        self::$errorArray = array(
            self::$incorrectCredentials => _("Incorrect username or password."),
            self::$usernameExists => _("This username is already taken."),
            self::$passwordsDoNotMatch => _("Passwords do not match."),
            self::$activationInvalid => _("Your account is not activated yet."),
            self::$emailFailedToSend => _("Email failed to send."),
            self::$emailNotValid => _("No valid email given or email is invalid."),
            self::$emailAlreadyExists => _("Email is already registered."),
            self::$fillInCaptcha => _("Please fill in the captcha."),
            self::$securityCodeIncorrect => _("Security code incorrect."),
            self::$cantSendEmail => _("Can\'t send emails."),
            self::$adminLevel => _("Admin level is to low to add a person with this admin level."),
            self::$notAllowedChar => _("Not allowed characters used in username."),
            self::$usernameOrEmailRegistered => _("Username or email already registered."),
            self::$errorsInTheValues => _("Error in the values suplied."),
            self::$incorrectValues => _("Incorrect values."),
            self::$somethingWentWrong => _("Something went wrong. If this error persists, please contact an administrator."),
            self::$youHaveNoPermission => _("You do not have the rights to do this."),
            self::$pluginError1 => _("Can\'t removed installed plugins, please uninstall first."),
            self::$timeslotIsTaken => _("Timeslot already taken."),
            self::$pageDoesNotExists => _("This page does not exists."),
            self::$compamyDeleted => _("The company with this id is deleted!"),
            self::$NeedToHavePermissionToWriteInFile => _("Writing to config failed, you need to have the permission to write in the config folder."),
            self::$uncheckForceHTTPS => _("HTTPS cannot be forced on a server that has no HTTPS protocol. Please uncheck the checkbox."),
            self::$cantLoginIntoDB => _("Could not login to the database with these values."),
            self::$errorsDuringDBInstallation => _("There were errors during the database installation. please contact us if the error persists."),
            self::$moduleDeactivate => _("There was an error during the deactivation of the module."),
            self::$needModuleOrModulesForThisToWork => _("You need a module/modules for this module to work."),
            self::$incorrectModuleSetup => _("This module is set up incorrectly."),
            self::$WentWrongDBUpdate => _("Something went wrong with database-update."),
            self::$amountFilesAreNotConsistent => _("Amount of files are not consistent with parameter given, please remove and reupload it. If this error persists, contact us."),
            self::$configUpdateIncorrect => _("Configupdate was impossible."),
            self::$personDeleted => _("The person with this id is deleted!"),
            self::$createAccountPersonError => _("something went wrong during the account creation for this person."),
            self::$cantUseHTTPS => _("Can\'t use https. You will need to turn this on on the server."),
            self::$unableToEditConfigFile => _("We were unable to edit the config file, if the error persists contact us."),
            self::$errorEmailOptions => _("There are errors in the email options."),
            self::$errorSMTPOptions => _("There are errors in the SMTP options."),
            self::$errorExternalDBOptions => _("There are errors in the external db options."),
            self::$notActive => _("not active."),
            self::$activationFailed => _("Something went wrong with your account activation. The link you clicked is probably no longer active."),
            self::$noPermissionToDelete => _("You are not allowed to delete this."),
            self::$duplicate => _("You can not add this person twice, he's already added to the team."),
            self::$notAllowed => _("You are not allowed to do this."),
            "default" => _("A general error occurred, please contact the site admin."),
        );
    }
    /**
    * Gets a message with a certain error code.
    * @param    Integer $errorcode Integer for a certain errormessage.
    * @return   String                      The error message
    **/
    public function get($errorcode) {
        if ($errorcode === self::$queryError) {
            return _("An error occurred in a query in") . ' ' . $this->showError();
        }
        if (isset(self::$errorArray[$errorcode])) {
            return self::$errorArray[$errorcode];
        }
        return self::$errorArray["default"];
    }
    
    /**
     * Shows where the error is with an backtrace.
     * @return $calltxt     String      contains the direction to the error.
     */
    public function showError() {
        $callers = debug_backtrace();
        $calltxt = "";
        if (DEVELOPMENT_ENVIRONMENT === true) {
            foreach ($callers as $call) {
                if (isset($call['class']) && isset($call['function']) && isset($call['line'])) {
                    $calltxt .= '<br>' . $call['class'] . ' -> ' . $call['function'] . ' at line: ' . $call['line'];
                }
            }
        }
        return $calltxt;
    }
}
