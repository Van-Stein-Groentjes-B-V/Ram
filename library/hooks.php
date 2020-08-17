<?php

/**
 * Hooks file, loads modules and sets an autoloader for module classes
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
 */

namespace SG\Ram;

/**
 * Hooks
 * @category   Library
 * @package    Ram
 */
class Hooks
{
    private $_constructModifications;
    private $_installedMods;
    
     /**
     * Constructor: __construct.
     * Check installed modules and do processing
     * @return  Void.
     */
    public function __construct() {
        $this->_installedMods = explode(',', INSTALLED_MODULES);
        $this->_constructModifications = unserialize(MODIFY_CONSTRUCT_EXISTING);
        if ($this->_installedMods) {
            $this->createLoaders();
        }
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
    }
    
    /**
     * Checks if class has construct functions.
     * @param   String $classname Class to check for constructor
     * @return  Boolean             True if has constructor
     */
    public function hasConstructFunctions($classname) {
        return isset($this->_constructModifications[$classname]);
    }
    
    /**
     * Checks if controller module.
     * @param   String $class Class name to check
     * @return  Boolean | String    False or classname
     */
    public function isModuleController($class) {
        $class = ucfirst($class);
        foreach ($this->_installedMods as $ext) {
            $controllerName = $class . ucfirst($ext) . 'Controller';
            if (class_exists($controllerName)) {
                return $controllerName;
            }
        }
        return false;
    }
    
    /**
     * Get classname and checks if it exists.
     * @param   String $classname Classname to check.
     * @return  Array               Array with clasnames.
     */
    public function constructFunctions($classname) {
        $returnArray = array();
        $all = $this->_constructModifications[$classname];
        foreach ($all as $module => $array) {
            $class = ucfirst($array[1]) . ucfirst($array[0]) . 'Controller';
            $method = $array[2];
            if (class_exists($class) && (int)method_exists($class, $method)) {
                $controller = new $class();
                $returnArray[$module] = $controller->$method();
            }
        }
        return $returnArray;
    }
    
    /**
     * Create loaders.
     * @return  Void.
     */
    private function createLoaders() {
        foreach ($this->_installedMods as $folder) {
            spl_autoload_register(function ($className) use ($folder) {
                $name = str_replace("module_", "", $folder);
                $class = ucfirst($name) . 'Controller';
                $nameSpaces = explode("\\", $className);
                if (in_array("Controllers", $nameSpaces) && file_exists(ROOT . DS . "modules" . DS . $folder . DS . "controllers" . DS . strtolower($class) . ".php")) {
                    require_once(ROOT . DS . "modules" . DS . $folder . DS . "controllers" . DS . strtolower($class) . ".php");
                } elseif (in_array("Models", $nameSpaces) && file_exists(ROOT . DS . "modules" . DS . $folder . DS . "models" . DS . strtolower($name) . ".php")) {
                    require_once(ROOT . DS . "modules" . DS . $folder . DS . "models" . DS . strtolower($name) . ".php");
                } elseif (file_exists(ROOT . DS . "modules" . DS . $folder . DS . "library" . DS . strtolower($name) . ".php")) {
                    require_once(ROOT . DS . "modules" . DS . $folder . DS . "library" . DS . strtolower($name) . ".php");
                }
            });
        }
    }
}
