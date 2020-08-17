<?php
// phpcs:ignoreFile -- this file combines symbols with side effects, but in this case it is allowed.
/**
 * Autoloader class
 * 
 * PHP version 7+
 *
 * @category   Library
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
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

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

// Require the composer autoload. Make sure you use composer install the first time. 
// Composer autoload
require_once(ROOT . DS . "vendor" . DS . "autoload.php");
// Load all the configs
require_once(ROOT . DS . "config" . DS . "config.php");
require_once(ROOT . DS . "config" . DS . "config_ext.php");
require_once(ROOT . DS . "config" . DS . "config_emails.php");
require_once(ROOT . DS . "config" . DS . "config_modules.php");

/**
 * Automatically includes files containing classes that are called.
 * @param   String  $className  Class name that is called.
 * @return  Void.
 */
function sg_web_autoloader($className) {
    // Get last part of className with namespaces
    $namespaces = explode("\\", $className);
    $nr = count($namespaces);
    $className = $namespaces[$nr - 1];
    if ($nr > 3) {
        $folderNamespace = $namespaces[$nr - 2];
    } else {
        $folderNamespace = "library";
    }
    
    if (file_exists(ROOT . DS . strtolower($folderNamespace) . DS . strtolower($className) . ".php")) {
        require_once(ROOT . DS . strtolower($folderNamespace) . DS . strtolower($className) . ".php");
    }
}
// Register autoloader
spl_autoload_register('SG\Ram\sg_web_autoloader');
