<?php
// phpcs:ignoreFile -- this file combines symbols with side effects, but in this case it is allowed.
/**
 * Router function. Whatever is put in to the address bar by the user is analysed
 * and the correct controller and views are loaded accordingly.
 * This class is one of the first being called. It determines the controller and 
 * method within that controller to route the user to the page they want to see.
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
 * @uses       SG\Ram\Controllers\InstallationController    Controller for installation
 * @uses       SG\Ram\Hooks                                 Autoload function
 * @uses       SG\Ram\Controllers\Accountcontroller         Account checker
 * @uses       ReflectionMethod                             PHP Reflection method
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
 */

namespace SG\Ram;

use SG\Ram\Controllers\InstallationController;
use SG\Ram\Hooks;
use SG\Ram\Controllers\AccountController;
use ReflectionMethod;

/**
 * Router
 * @category   Library
 * @package    Ram
 */
class Router
{
    
    /**
     * Constructor.
     * @return Void.
     */
    public function __construct() {
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
    }
    
    /**
     * Check if environment is development and display errors and warnings.
     * DEVELOPERS ONLY, Always set DEVELOPMENT_ENVIRONMENT to false when not 
     * developing to prevent display of possible vulnerabilities.
     * @return Void.
     */
    public static function SetReporting() {
        if (DEVELOPMENT_ENVIRONMENT) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 'Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', ROOT . DS . "tmp" . DS . "logs" . DS . "error.log");
        }
    }

    /**
     * Main call Function,
     * Determines which controller to call based on URL
     * @global user UserAccount
     * @global url  The URL in the address bar
     * @global hook The possible hook
     * @return  Void.
     */
    public static function callHook() {
        //if you use translations, add the logic here.
        //fix for Turkish and other weird languages breaking PHP (https://bugs.php.net/bug.php?id=18556)
        setlocale(LC_CTYPE, 'en_US'); 
        global $user;
        global $url;
        global $hook;
        $module_namespace = 'SG\\Modules\\';
        $controller_namespace = 'SG\\Ram\\Controllers\\';

        $start = count(explode('/', SITE_ROOT)) - 1;
        if (USE_HTTPS && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
            header("refresh:0; url=https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            return;
        }
        $urlArray = array();
        if (!isset($url) || $url == SITE_ROOT) {
            $controllerName = DEFAULT_CONTROLLER;
            $action = DEFAULT_ACTION;
        } else {
            $urlArray = explode("/", $url);
            $controllerName = $urlArray[$start];
            $action = (isset($urlArray[$start + 1]) && $urlArray[$start + 1] != '') ? $urlArray[$start + 1] : DEFAULT_ACTION;
        }
        $quaryParams = array();
        for ($i = $start + 2; $i < count($urlArray); $i++) {
            $quaryParams[] = $urlArray[$i] != '' ? $urlArray[$i] : null;
        }

        // Modify controller name to fit naming convention
        $class = $controller_namespace . ucfirst($controllerName) . 'Controller';
        if(in_array(strtolower($controllerName), array("account", "logger", "databaseinstall", "util"))){
            $controllerName = DEFAULT_CONTROLLER;
            $class = $controller_namespace . ucfirst($controllerName) . 'Controller';
        }
        if (!class_exists($class)) {
            $class = $module_namespace . ucfirst($controllerName) . '\\Controllers\\' . ucfirst($controllerName) . 'Controller';
            if(!class_exists($class)){
                $controllerName = DEFAULT_CONTROLLER;
                $action = DEFAULT_ACTION;
                $class = $controller_namespace . ucfirst($controllerName) . 'Controller';
            } 
        }
        if (!self::exceptionList($controllerName) && !$user->isLoggedIn()) {
            header("refresh:0; url=" . SITE_ROOT . 'index/login/');
            exit(1);
        }
                // Instantiate the appropriate class
        if (class_exists($class) && (int)method_exists($class, $action)) {
            $reflection = new ReflectionMethod($class, $action);
            if (!$reflection->isPublic()) {
                $action = DEFAULT_ACTION;
            }
            $controller = new $class();
            call_user_func_array(array($controller, $action), $quaryParams);
        } elseif (class_exists($class) && !(int)method_exists($class, $action)) {
            $controller = new $class();
            $action = DEFAULT_ACTION;
            $controller->$action();
        } else {
            // Error: Controller Class not found
            die("1. File <strong>'$controllerName.php'</strong> containing class <strong>'$class'</strong> might be missing. 2. Method <strong>'$action'</strong> is missing in <strong>'$controllerName.php'</strong>");
        }
    }
    
    /**
     * Function to check if string in exception list.
     * @param  String $string The string to check.
     * @return Boolean              True if in list.
     **/
    public static function exceptionList($string) {
        $arr = json_decode(EXCEPTION_URL, true);
        return in_array($string, $arr);
    }
    
     /**
      * Start the router
      * @return Void
      */
    public static function start() {
        global $user;
        self::setReporting();
        if (!PROGRAM_INSTALLED) {
            return new InstallationController();
        }
        $user = new AccountController();
        $hook = new Hooks();
        self::callHook();
    }
}
