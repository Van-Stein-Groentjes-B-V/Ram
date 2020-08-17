<?php

/**
 * Controller master class. This controller will be extended by all the
 * other controllers in the system.
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
 * @uses       \SG\Ram\View         Uses the View class
 * @uses       \SG\Ram\Functions    General functions class
 * @uses       \SG\Ram\ErrorMessage General error message class
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

use SG\Ram\View;
use SG\Ram\Functions;
use SG\Ram\ErrorMessage;

/**
 * Controller
 * @category   Library
 * @package    Ram
 */
class Controller
{
    protected $_view;
    protected $_model;
    protected $_view_name;
    protected $_hooks;
    protected $_err;
    protected $_fun;
    /*
     * modules info
     */
    protected $_hasModules = false;
    //if true, has modules and need to check other steps
    protected $_extra_info;
    
    /**
     * Constructor: __construct.
     * Create objects and initialize.
     * @return  Void.
     */
    public function __construct() {
        global $hook;
        $this->_viewName = '';
        $this->_view = new View();
        $this->_hooks = $hook;
        $this->_err = new ErrorMessage();
        $this->_fun = new Functions();
        
        if (!is_null($this->_hooks) && $this->_hooks->hasConstructFunctions(get_class($this))) {
            $this->_hasModules = true;
            $this->handleInfoArrayModule($this->_hooks->constructFunctions(get_class($this)));
        }
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        if (!empty($this->_viewName)) {
            $this->_view->render($this->_viewName);
        }
        unset($this->_view);
        unset($this->_hooks);
        unset($this->_viewName);
        unset($this->_fun);
        unset($this->_err);
    }

    /**
     * Loads the index page based on the assign.
     * @return Void.
     */
    public function index() {
        $this->assign('content', 'This is index class index method, Method is not set yet.');
    }

    /**
     * Assigns a key value pair to be set in the view.
     * @param  String $variable Variable name
     * @param  Mixed  $value    Variable value
     * @return Void.
     */
    protected function assign($variable, $value) {
        $this->_view->assign($variable, $value);
    }

    /**
     * Loads model.
     * @param   String $name Model name
     * @return  Void.
     */
    public function loadModel($name) {
        $modelName = $name;
        $this->_model = new $modelName();
    }

    /**
     * Load basic view from the views folder linked to the respective controller. !!Always index.php
     * @param   String $name view name
     * @return  Void
     */
    public function loadView($name) {
        if (file_exists(ROOT . DS . "views" . DS . strtolower($name) . DS . "index.php")) {
            $this->_viewName = $name;
        }
    }
    
    /**
     * Function to insert a script tag to debug possible php variables to the console.
     * @param   Array | String $data An array of data or a string to be displayed.
     * @return  Void.
     */
    protected function debugToConsole($data) {
        if (is_array($data)) {
            $output = "<script>console.log( 'Debug Objects: " . implode(',', $data) . "' );</script>";
        } else {
            $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
        }
        echo $output;
    }

    /**
     * Search backwards starting from haystack length characters from the end.
     * @param   String $haystack String in which to search.
     * @param   String $needle   String to be searched.
     * @return  Boolean | Integer       True if needle is "" else index of first occurrence of needle
     */
    public function startsWith($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
    
    /**
     * A function made to go to specific functions in the controllers.
     * @param   string $class  Which class to instantiate.
     * @param   string $action Which method to call in class.
     * @return  Void            Result of method called
     */
    public function hook($class = null, $action = null) {
        if ($class && $action) {
            $class = ucfirst($class);
            if (class_exists($class) && (int)method_exists($class, $action)) {
                $reflection = new ReflectionMethod($class, $action);
                if (!$reflection->isPublic()) {
                    $action = DEFAULT_ACTION;
                }
                $controller = new $class();
                return $controller->$action();
            }
        }
    }
    
    /**
     * A function made to load specific css files.
     * @param   String $modulename Module name.
     * @param   String $filename   File name + extension.
     * @return  Void               Prints file.
     */
    public function handleModuleExtraFile($modulename, $filename) {
        if (file_exists(ROOT . DS . "modules" . DS . $modulename . DS . "public" . DS . "css" . DS . $filename) && strpos($filename, '.css') !== false) {
            header("Content-type: text/css");
            readfile(ROOT . DS . "modules" . DS . $modulename . DS . "public" . DS . "css" . DS . $filename);
            exit();
        }
        if (file_exists(ROOT . DS . "modules" . DS . $modulename . DS . "public" . DS . "js" . DS . $filename) && strpos($filename, '.js') !== false) {
            header('Content-Type: application/javascript');
            readfile(ROOT . DS . "modules" . DS . $modulename . DS . "public" . DS . "js" . DS . $filename);
            exit();
        }
        echo '/*does not exist */';
        exit();
    }
    
    /**
     * Handle the module info which shall extend the controller/view
     * @param   Array $info Structure
     * @return  Void.
     */
    protected function handleInfoArrayModule($info) {
        if (!is_array($info)) {
            return;
        }
        foreach ($info as $nameMod => $single) {
            if (!is_array($single)) {
                continue;
            }
            if (isset($single["pre_content"])) {
                    $this->_view->assign('precontent', array($single["pre_content"]['spot'] => ROOT . DS . "modules/" . DS . $nameMod . DS . "views" . DS . $single["pre_content"]['name'] . DS . ".php"));
            }
            if (isset($single["post_content"])) {
                $this->_view->assign('postcontent', array($single["post_content"]['spot'] => ROOT . DS . "modules" . DS . $nameMod . DS . "views" . DS . $single["post_content"]['name'] . DS . ".php"));
            }
            if (isset($single["extra_info"]) && is_array($single["extra_info"])) {
                foreach ($single["extra_info"] as $key => $extra) {
                    if (!$this->_view->isReserved($key)) {
                        $this->_view->assign($key, $extra);
                    }
                }
            }
            if (isset($single["extra_js"]) && is_array($single["extra_js"])) {
                foreach ($single["extra_js"] as $key => $extra) {
                    $this->_view->SetJSMODULE($key, $extra);
                }
            }
            if (isset($single["extra_css"]) && is_array($single["extra_css"])) {
                foreach ($single["extra_css"] as $key => $extra) {
                    $this->_view->SetCSSMODULE($key, $extra);
                }
            }
        }
    }
    
    /**
     * Load the view and render it.
     * @param   String  $which       The folder in which the file is.
     * @param   String  $target      The target files name.
     * @param   String  $title       The title of the page.
     * @param   Array   $css         The css which should be included.
     * @param   Array   $js          The js which should be included.
     * @param   Boolean $useSideBar  Whether or not the sidebar is shown.
     * @param   Integer $useSetup    The setup type to be used (standard only 1 is set);.
     * @param   Array   $externalCSS The external css that should be loaded, .css is added.
     * @param   Array   $externalJS  The external js that should be loaded, .js is added.
     * @return  Void.
     */
    protected function loadViewer($which, $target, $title, $css = array(), $js = array(), $useSideBar = false, $useSetup = 1, $externalCSS = array(), $externalJS = array()) {
        $this->_view->setSiteTitle($title);
        $this->_view->Assign("page", $title);
        $this->_view->SetCSS("bootstrap.min.css");
        $this->_view->SetCSS("theme.css");
        $this->_view->SetCSS("callout.css");
        $this->_view->SetCSS("metismenu.min.css");
        $this->_view->SetCSS("style.css");
        foreach ($css as $cssSingle) {
            $this->_view->SetCSS($cssSingle . ".css?v=" . CSS_UPDATED);
        }
        
        foreach ($externalCSS as $cssSingle => $website) {
            $this->_view->setCSSEXTERNAL($cssSingle . ".css", $website);
        }
        $this->_view->setJS("jQueryV3.3.1.js");
        $this->_view->setJSFooter("bootstrap.min.js");
        $this->_view->setJSFooter("metisMenu.min.js");
        $this->_view->setJSFooter("readmore.min.js");
        foreach ($js as $jsSingle) {
            $this->_view->setJSFooter($jsSingle . ".js");
        }
        foreach ($externalJS as $jsSingle => $website) {
            $this->_view->setJSExternal($jsSingle . ".js", $website);
        }
        $this->setViewSetup($which, $target, $useSetup, $useSideBar);
        $this->_view->outPutView();
    }

    /**
     * Set different view options.
     * @global \AccountController $user     The logged in user.
     * @param   String  $which      The folder in which we need to search.
     * @param   String  $target     The file to be loaded.
     * @param   Integer $useSetup   The number of the specific view setup.
     * @param   Boolean $useSideBar Whether or not the side bar should be loaded.
     * @return  Void.
     */
    protected function setViewSetup($which, $target, $useSetup, $useSideBar) {
        // Header
        $this->_view->assign('header', $this->_view->Render("header"));
        if ($useSetup === 2 || $useSetup === 3) {
            // Custom header
            $this->_view->assign('carousel', $this->_view->Render("custom_header"));
        }
        
        if ($useSetup === 1 || $useSetup === 3) {
            // Navbar
            $this->_view->assign('navbar', $this->_view->Render("navbar"));
            // Sidebar
            $this->_view->assign('sidebar_used', $useSideBar);
            if ($useSideBar) {
                global $user;
                $this->assign("gebruiker", $user->getUser());
                $this->_view->assign('sidebar', $this->_view->Render("sidebar"));
            }
        }
        
        // Content
        $this->_view->assign('content', $this->_view->Render($which . "/" . $target));
        // Footer
        $this->_view->assign('footer', $this->_view->Render("footer"));
    }
    
    /**
     * Most of the controllers have json functions in which they sanitize input and
     * do some queries. These functions are partly extracted to the parent class.
     * @param   Array          $allowed  Allowed values in the query
     * @param   DatabaseHelper $database Helper model for the database
     * @param   String         $table    Table in which to search
     * @param   Array          $stati    Possible statusses for projects //not used for other objects
     * @return  Array                         Array(count => value, data => value)
     */
    protected function parseJSONrequest($allowed, &$database, $table, $stati = null) {
        // Check if calling location is allowed to call
        if (!in_array($table, array("persons", "projects", "companies", "modules", "user_accounts"))) {
            return array();
        }
        // Sanitize the input
        $request = $this->handleRequestDataParent($allowed);
        $order = $request['order'];
        $from = $request['from'];
        $total = $request['total'];
        $search = $request['search'];
        $name = $request['name'];
        
        // Check if the requested status is possible
        if ($table == "projects" && in_array($search, $stati)) {
            $search = array_search($search, $stati);
        }
        
        // Create the search string
        $search = "%" . str_replace("+", " ", filter_var(strip_tags($search), FILTER_SANITIZE_STRING)) . "%";
        
        // Define custom AND conditions for de search term to be applied to
        if ($table == "persons") {
            $searchSql = $this->_fun->getSqlFromArray($allowed, $search);
        } elseif ($table == "user_accounts") {
            $searchSql = $this->_fun->getSqlFromArray($allowed, $search, 'ua.');
        } elseif ($table == "companies") {
            $searchSql['sql'] = "(`website` LIKE ? OR `name` LIKE ? OR `city` LIKE ? OR `country` LIKE ? OR `street` LIKE ?)";
            $searchSql['search'] = array($search, $search, $search, $search, $search);
        } elseif ($table == "projects") {
            $searchSql['sql'] = "(`name` LIKE ? OR `project_status` LIKE ? OR `deadline` LIKE ?)";
            $searchSql['search'] = array($search,$search,$search);
        }
        
        $count = 0;
        // Do the different queries for count and set the query for getting the records.
        if ($table == "modules") {
            $database->setSql("SELECT count(`id`) AS `count` FROM " . $table . " WHERE (`name` LIKE ? OR `description` LIKE ?)");
            $count = $database->getRow(array($search, $search));
            $searchSql['search'] = array($search, $search);
            $database->setSql("SELECT * FROM `modules` WHERE (`name` LIKE ? OR `description` LIKE ?) ORDER BY `" . $name . "` " . $order . " LIMIT " . $total . " OFFSET " . $from);
        } elseif ($table == "user_accounts") {
            $database->setSql("SELECT count(`id`) AS `count` FROM `user_accounts` AS `ua` WHERE " . $searchSql['sql']);
            $count = $database->getRow($searchSql['search']);
            $database->setSql("SELECT `ua`.`id`, `ua`.`username`, `ua`.`email`, `ua`.`fullname`, `ua`.`admin`, `ua`.`joined`, `ua`.`active` " .
                                "FROM `user_accounts` AS `ua` " .
                                "WHERE " .  $searchSql['sql'] . " ORDER BY `" . $name . "` " . $order . " LIMIT " . $total . " OFFSET " . $from);
        } else {
            $database->setSql("SELECT count(`id`) AS `count` FROM `" . $table . "` WHERE `deleted` = 0 AND " . $searchSql['sql']);
            $count = $database->getRow($searchSql['search']);
            $database->setSql("SELECT * FROM `" . $table . "` WHERE `deleted` = 0 AND " . $searchSql['sql'] . " ORDER BY `" . $name . "` " . $order . " LIMIT " . $total . " OFFSET " . $from);
        }
        // Return the amount of records and the array with the records
        return array("count" => $count, "data" => $database->getRows($searchSql['search']));
    }
    
    /**
     * Handle the request info to the format that is accepted.
     * @param array $allowed The allowed items to be sorted on.
     * @return Array
     */
    protected function handleRequestDataParent($allowed = array()) {
        $array = array();
        $array["order"] = isset($_REQUEST['order']) && strtolower($_REQUEST['order']) === "desc" ?  "DESC" : "ASC";
        $array["from"] = isset($_REQUEST['offset']) && is_numeric($_REQUEST['offset']) ?  filter_var($_REQUEST['offset'], FILTER_SANITIZE_NUMBER_INT) : "0";
        $array["total"] = isset($_REQUEST['limit']) && is_numeric($_REQUEST['limit']) ? filter_var($_REQUEST['limit'], FILTER_SANITIZE_NUMBER_INT) : "100";
        $array["search"] = isset($_REQUEST['search']) ? $_REQUEST['search'] : "";
        $array["name"] = isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $allowed) ?  $_REQUEST['sort'] : "id";
        return $array;
    }
    
    /**
     * Assign the messages to the view.
     * @return Void
     */
    protected function handleMessages() {
        if (isset($_GET['successmessage'])) {
            $this->Assign("successmessage", htmlspecialchars(urldecode($_GET['successmessage'])));
        }
        if (isset($_GET['errormessage'])) {
            $this->Assign("errormessage", htmlspecialchars(urldecode($_GET['errormessage'])));
        }
    }
}
