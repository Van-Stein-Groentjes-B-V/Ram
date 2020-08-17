<?php
/**
 * Basic Messages in the system. These messages are send during install and 
 * on user registration, password reset etc.
 *
 * PHP version 7+
 *
 * @category   Defines
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
namespace SG\Ram;
define("SET_OWN_PASSWORD_EMAIL", true);

define("ACTIVATE_NOPASSWORD_EMAIL", 
            "Dear [slug_username].<br/><br/>" .
            "We are almost there! Click <a href='" . trim(SITE_ROOT, '/') . '/login/activate/' . "[slug_activation_token]/[slug_email]'>here</a> to activate your account" . "<br/><br/>" .
            "Salutations,<br/>" .
            EMAIL_FROM_NAME . ".<br/>");
define("ACTIVATE_NOPASSWORD_EMAIL_TITLE", "Activate your account");
define("ACTIVATE_OWN_PASSWORD_EMAIL", 
            "Dear [slug_username].<br/><br/>" .
            "To set your passwords for our system, you need to click <a href='" . trim(SITE_ROOT, "/") . "/login/setPassword/[slug_activation_token]/[slug_email]'>here</a><br/><br/>" .
            "Salutations,<br/>" .
            EMAIL_FROM_NAME . "<br/>");
define("ACTIVATE_OWN_PASSWORD_EMAIL_TITLE", "Set your passwords");
define("STANDARD_REGISTER_EMAIL", 
            "Dear [slug_fullname].<br/><br/>" .
            "Welcome to our system! You can log in with the following info:<br/>" .
            "username: [slug_username] <br/>" .
            "If activation is enabled, you will receive another email with the necessary info to activate your account. <br/>" .
            "If you didn't create your own account, another email will be send with info on how you can set one. <br/>" .
            "If you have troubles logging in or for other information, don't hesitate to ask! <br/><br/>" .

            "Kind regards,<br/>" .
            EMAIL_FROM_NAME . "<br/>");
define("STANDARD_REGISTER_EMAIL_TITLE", "Registration (almost) complete");

define("RESET_PASSWORD_EMAIL", 
            "Dear [slug_username].<br/><br/>" .
            "Click <a href='" . trim(SITE_ROOT, "/") . "/login/setPassword/[slug_activation_token]/[slug_email]'>here</a> to create a new password.<br/><br/>" .
            "Click <a href='" . trim(SITE_ROOT, "/") . "/login/reportFalse/[slug_activation_token]/[slug_email]'>this</a> if you did not ask for a pssword reset.<br/><br/>" .
            "Salutations,<br/>" .
            EMAIL_FROM_NAME . "<br/>");
define("RESET_PASSWORD_EMAIL_TITLE", "Password reset.");

define("ALERT_MAIN_ADMIN_EMAIL", 
            "the person with email:<br/>" .
            "[slug_email] <br/>" .
            "has confirmed he did not request a new password. <br/>" .
            "please take appropiate steps against this");
define("ALERT_MAIN_ADMIN_EMAIL_TITLE", "ALERT evil do'ers abound");

define("ADD_USER_EMAIL", 
            "Dear [slug_username].<br/><br/>" .
            "To create your password to enter our system, just click <a href='" . trim(SITE_ROOT, "/") . "/login/setPassword/[slug_activation_token]/[slug_email]'>here</a><br/><br/>" .
            "Salutations,<br/>" .
            EMAIL_FROM_NAME . "<br/>");
define("ADD_USER_EMAIL_TITLE", "Create a password.");
