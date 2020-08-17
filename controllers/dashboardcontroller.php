<?php

/**
 * Dashboard Controller.
 * Shows statistics and projects that are connected to the user.
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
 * @uses       \SG\Ram\Controller       Extend the main controller.
 * @uses       \SG\Ram\dataHandler      Data handler.
 * @uses       \SG\Ram\Models\Project   Project Object.
 * @uses       \SG\Ram\Models\Dbhelper  Database helper object.
 * @uses       \SG\Ram\StatusTypes      Different statustypes.
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
use SG\Ram\Models\Project;
use SG\Ram\Models\Dbhelper;
use SG\Ram\StatusTypes;

/**
 * DashboardController
 * @category   Controllers
 * @package    Ram
 */
class DashboardController extends Controller
{
    private $_user;
    private $_dataHandler;
    private $_statusTypesArray;
    
    /**
     * Constructor: __construct.
     * Assemble and pre-process the data.
     * @return  void.
     */
    public function __construct() {
        parent::__construct();
        global $user;
        $this->_user = $user->getUser();
        $this->_db = new Dbhelper();
        $this->_dataHandler = new dataHandler();
        $this->_statusTypesArray = StatusTypes::$statusTypesArray;
    }

    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_dataHandler);
        unset($this->_statusTypesArray);
        unset($this->_db); //dbhelper
        unset($this->_user);
    }
    
    /**
     * Shows the statistics.
     * Shows also project where user is connected to.
     * @return Void
     */
    public function index() {
        $show = false;
        $this->handleMessages();
        if ($this->_user->isSuperAdmin() && $this->_user->getShowStats()) {
            $show = true;
        }
        $this->Assign("show_stats", $show);
        $this->Assign("projects", $this->getProjects());
        $this->Assign("show_stats", $show);
        $this->Assign("personId", $this->_dataHandler->getDataFromRow("persons", array("account_id" => $this->_user->getId()), true, array("id")));
        $this->LoadViewer('dashboard', 'index', 'dashboard', array("dashboard"), array("backend"), true);
    }
    
    /**
     * Gets projects based on the user id and sorts it by assigned first by you, and it sorts also by project_status.
     * Then it shows the rest of the project where the user is assigned also sorted by project_status.
     * @return Array $projects returns an array with projects.
     */
    private function getProjects() {
        $projects = array();
        $projectsPerson = array();
        //Get all project id and sort by project_status
        $personID = $this->_dataHandler->getDataFromRow('persons', array('account_id' => $this->_user->getId()), true, array('id'));
        $this->_db->setSql("SELECT `p`.* " .
                                "FROM `projects` AS `p` " .
                                    "LEFT JOIN `person_project` AS `pp` ON `pp`.`project_id` = `p`.`id` AND `pp`.`deleted` = 0 " .
                                "WHERE `pp`.`person_id` = ? AND `p`.`deleted` = 0 " .
                                "ORDER BY `p`.`project_status` DESC");
        
        $projectArray = $this->_db->getRows(array($personID['id']));
        
        //show first projects where this->_user is responsible
        $this->_db->setSql("SELECT * FROM `projects` WHERE `responsible` = ? AND `deleted` = 0 ORDER BY `project_status` DESC");
        $responsible = $this->_db->getRows(array($personID['id']));
        
        foreach ($responsible as $responsiblePerson) {
            if ($responsiblePerson['responsible'] == 1 && in_array($responsiblePerson['project_status'], $this->_statusTypesArray)) {
                $responsiblePerson = new Project($responsiblePerson);
                $responsiblePerson->setResponsibleAll($this->_dataHandler->getDataFromRow('persons', $responsiblePerson->getResponsible(), true));
                $projectsPerson[] = $responsiblePerson;
            }
        }
        
        foreach ($projectArray as $result) {
            if ($result && $result['responsible'] != 1 && in_array($result['project_status'], $this->_statusTypesArray)) {
                $personProject = new Project($result);
                $personProject->setResponsibleAll($this->_dataHandler->getDataFromRow('persons', $personProject->getResponsible(), true));
                $projects[] = $personProject;
            }
        }
        return array_merge($projectsPerson, $projects);
    }
}
