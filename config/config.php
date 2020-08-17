<?php
/**
 * Main config file of the RAM management system, first to be called, in this you can alter the most important information
 * all defines will be explained.
 * You can manually edit these, but you can also just run the installer.
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

ob_start();
session_start();

//define whether install has been run (DON'T SET MANUEALLY TO TRUE, INSTALLATION OF THE DB WILL FIX IT);
define("PROGRAM_INSTALLED", false);

//ENTER THE BASE URL HERE (url where the tool is installed)
define("SITE_ROOT", "");

/**
 * MAIN DATABASE CONNECTION
 * */
//the host of the database, most providers want you to fill in localhost.
//if you don't know what this must be, ask your provider
define("DB_HOST", "");
//port through which the connection is created, ussually 3306, but provider might say/demand other
//as of yet unused
define("DB_PORT", "");
//Set Socket
define("DB_SOCKET", "");
//Username database for the website, see our site for the neccesary rights
define("DB_USER", "");
//password database for the website
define("DB_PASSWORD", "");
//the name of the database (if left empty the tool will make one)
define("DB_NAME", "");
//random key for the first admin, only works when there are no users in the database
define("SALT_ADMIN", "");
//if MANUALLY filled in, you will still need to run the creation function. You can go there by going to the url where this tool is and follow the instructions
