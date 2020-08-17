<?php
/**
 * Extended configuration file. Holds all different kinds of defines
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

// Use HTTP Strict Transport Security to force client to use secure connections only
// We advice to keep USE_HTTPS on true, if you do not own an SSL certificate you might turn it off
define('USE_HTTPS', false);

// Email notifications, please enter your email adress, and the initial username and password of the admin account.
// NOTE: you can always change this later.
define("EMAIL_TO", "");
define("INITIAL_USERNAME", "");
define("INITIAL_PASSWORD", "");

// Whether to send e-mails or not (don't set to true, unless the smtp information works)
define("SEND_MAIL", false);

// Set the title of the website (optional)
define("SITE_TITLE", "");

// Email notifications are send from the following source:
define("EMAIL_FROM_NAME", "");
define("EMAIL_FROM_REAL", "");
define("EMAIL_FROM_NOREPLY", "");
define("EMAIL_REPLY_TO", "");
define("EMAIL_DANGERS_TO", "");

// SMTP settings for email
define("USE_SMPT_EMAIL", false);
define("DEBUG_EMAIL", false);
define("SMTP_HOST", "");            // Specify main SMTP servers
define("SMTP_AUTH", false);         // Enable SMTP authentication
define("SMTP_USERNAME", "");        // SMTP username
define("SMTP_PASSWORD", "");        // SMTP password
define("SMTP_SECURE", "");          // Enable TLS encryption, `ssl` also accepted
define("SMTP_PORT", "");            // TCP port to connect to

define("API_VERSION", "1");

// Optional, if using a log to track errors and login actions.
define("DB_LOG_HOST", "");
define("DB_LOG_PORT", "");
define("DB_LOG_SOCKET", "");
define("DB_LOG_USER", "");
define("DB_LOG_PASSWORD", "");
define("DB_LOG_NAME", "");
define("DB_LOG_LEVEL","");
define("DB_LOG_TABLE_NAME","");

// Name of the log used by the s-g logger system
define("LOG_NAME", "");

// Link to reset password
define("RESET_LINK", "");

// SECURITY/LOGIN OPTIONS
// If set to true, a log will be created using the DB connection above.
define("USE_LOG", false);
define("USE_EXTERNAL_LOG", false);

// If set to true, a cookie with a hash and username is stored to auto login.
define("USE_COOKIE", false);

// If set to true, uses a device token to log in
define("USE_DEVICE_TOKEN", false);

// If set to true, checks the secureimage (captcha) in the register form
define("USE_SECUREIMAGE_REGISTER", false);

// If set to true, checks the secureimage (captcha) in the login form
define("USE_SECUREIMAGE_LOGIN", false);

// Set the two captcha's, neccesary if either USE_SECUREIMAGE_LOGIN or/and USE_SECUREIMAGE_REGISTER is true
define("CAPTCHA_PUBLIC", "");
define("CAPTCHA_SECRET", "");

// If set to true, the IP will be logged and checked against untrusted locations.
define("USE_IP", false);

// ACTIVATE_ACCOUNTS: if true, an email will be send with a link to activate the account.
// if false: the account is activated immediately. 
// TODO add functionality for variable set to true
define("ACTIVATE_ACCOUNTS", false);
// TODO is now unused, but will be used when functionality is added to activate accounts.
define("ACTIVATE_LINK_PW", SITE_ROOT . "login/setPassword/");

//Higher cost is more secure but also takes more time to compute.
define("BCRYPT_COST", 11);

// if you want to upload images through the function checker
// you need to specify the location
define("UPLOAD_FOLDER_IMAGES", "../public/img/");
// Max size an image may be (remember alot might be uploaded)
define("MAX_SIZE_IMAGE", 10000000);

// Is the allowed array to string, in which the names of the string => db tablename
define("ALLOWED_DELETE_TABLES", serialize(array('company' => 'companies', 'person' => 'persons', 'attachment' => 'attachments', 'project' => 'projects', 'ticket' => 'tickets', 'coupled_person' => 'person_project')));

// Extra Settings
// Set default hourly_wage for calculations of time spend and budgets
define("DEFAULT_WAGE", "");
// Your key to tiny MCE, if not provided will fallback to default editor
define("TINY_MCE_KEY", "");

// Set development environment to see php error messages and warnings.
define("DEVELOPMENT_ENVIRONMENT", false);
/*
 * true for NO accounts for customers, false FOR accounts for customers. Will be used in a later version
 */
define("NO_ACCOUNTS_FOR_CUSTOMERS", true);

// DO NOT CHANGE FROM HERE 
define('DEFAULT_CONTROLLER', "dashboard");
define('DEFAULT_ACTION', "index");

// Exception for where you DON'T NEED to login
define("EXCEPTION_URL", '["register", "public", "index", "login", "contact"]');

// Defines for db_helper
define("SETNEWSESSION", 0);
define("INSERTSESSION", 1);
define("CHECKSESSION", 2);
define("UPDATENEWSESSION", 3);

// Name of the website to save the logs, only visible for the admin
define("WEBSITE_NAME", "Ram management");

define("ADMIN_REGISTER", false);
define("SESSION_PREFIX", "RAM_");
define("START_CODE_BLOCK", "/****###");
define("END_CODE_BLOCK", "###****/");


define("CSS_UPDATED", "1597143982");
