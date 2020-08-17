<?php

/**
 * Timesheet Controller.
 * To Add, edit, delete Timesheet or to add new functions for the Timesheet page
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
 * @uses       \SG\Ram\Controller   Extend the main controller.
 * @uses       \SG\Ram\dataHandler   Extend the main controller.
 * @uses       \SG\Ram\Models\Dbhelper   Extend the main controller.
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
use SG\Ram\Models\Dbhelper;

/**
 * TimesheetController
 * @category   Controllers
 * @package    Ram
 */
class TimesheetController extends Controller
{
    private $_user;
    private $_db;
    private $_dataHandler;
/**
     * Constructor: __construct
     * Initialize the objects
     * @return  Void
     */
    public function __construct() {
        parent::__construct();
        global $user;
        $this->_user = $user->getUser();
        $this->_dataHandler = new dataHandler();
        $this->_db = new Dbhelper();
    }

    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_dataHandler);
        unset($this->_db);
        unset($this->_user);
    }
    
    /**
     * Loads the index page
     * @return Void
     */
    public function index() {
        $this->Assign("gebruiker", $this->_user);
        $this->LoadViewer(
            "timesheet",
            "index",
            "timesheet",
            array("dashboard", "timeline-own",  "bootstrap-select.min", "timepicker", "date-picker"),
            array("backend", "timelinesg", "timeline-own", "bootstrap-select.min", "bootstrap-timepicker", "bootstrap-datepicker", "datePickerCustom"),
            true
        );
    }
    
    /**
     * Loads the specific page
     * @return  Void
     */
    public function specific() {
        $this->Assign("gebruiker", $this->_user);
        $this->LoadViewer('timesheet', 'specific', 'timesheet', array("dashboard", "timeline-own", "timepicker"), array("backend", "timelinesg", "timeline-own", "bootstrap-timepicker", "bootstrap-datepicker"), true);
    }
    
    /**
     * Loads the admin timesheet page
     * @return  Void
     */
    public function admin() {
        $this->Assign("gebruiker", $this->_user);
        if ($this->_user->isSuperAdmin()) {
            return $this->LoadViewer(
                "timesheet",
                "admin",
                "timesheet",
                array("dashboard", "timeline-own", "timepicker"),
                array("backend", "timelinesg", "timeline-own", "bootstrap-timepicker", "bootstrap-datepicker"),
                true
            );
        } else {
            return $this->index();
        }
    }
    
    /**
     * Fill function for dropdowns for projects.
     * @return Void.
     */
    public function getProjects() {
        $this->_db->setSql('SELECT * FROM `projects` WHERE `deleted` = 0');
        return $this->_db->getRows();
    }
}
