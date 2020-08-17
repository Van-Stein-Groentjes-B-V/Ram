<?php

/**
 * Todo Model
 * Contains information on the todos
 *
 * PHP version 7+
 *
 * @category   Models
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 * @uses       \SG\Ram\Model                    Extend the main Model.
 * @uses       DateTime                         PHP DateTime object
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

namespace SG\Ram\Models;

use SG\Ram\Model;

/**
 * Todo model
 * @category   Models
 * @package    SG
 */
class Todo extends model
{
    private $_id;
    private $_projectId;
    private $_project = null;
    private $_prio;
    private $_deadline;
    private $_deadlineText;
    private $_message;
    private $_done;
    private $_userId;
    private $_lastChange;
    private $_setProgress;
    private $_hours;
    private $_longDescription;
    
    /**
     * Constructor.
     * @param Array | null $data Data to fill the object.
     * @return Void.
     */
    public function __construct($data = null) {
        parent::__construct();
        if ($data) {
            $this->buildObject($data);
        }
        $this->_db = new Dbhelper();
    }

    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
    }
    
    /**
     * Builds object is data comes from database.
     * @param Array $data Row of the database to fill the model
     * @return  Void
     */
    private function buildObject($data) {
        $this->setId($data['id']);
        $this->setProjectId($data['project_id']);
        $this->setPrio($data['prio']);
        $this->setDeadline($data['deadline']);
        $this->setMessage($data['message']);
        $this->setDone($data['done']);
        $this->setUserid($data['user_id']);
        $this->setLastchange($data['lastchange']);
        $this->setSetProgress($data['set_progress']);
        $this->setHours($data['hours']);
        $this->setLongDesc($data['long_desc']);
        $ddateori = strtotime($data['deadline']);
        $ddate = date('d-m-Y', $ddateori);
        if ($ddate != "30-11--0001" && $ddate != "01-01-1970") {
            $ddate = date('d-m-Y', $ddateori);
            $ddate = '<span class="deadline">' . $ddate . '</span>';
        } else {
            $ddate = "";
        }
        $this->setDeadline_text($ddate);
    }

    /**
     * Get fields name.
     * @return Array     Fields of the todo object in an array
     */
    public function getFields() {
        $fields = array();
        $fields["project_id"] = $this->_projectId;
        $fields["prio"] = $this->_prio;
        $fields["deadline"] = $this->_deadline;
        $fields["message"] = $this->_message;
        $fields["done"] = $this->_done;
        $fields["user_id"] = $this->_userId;
        $fields["lastchange"] = $this->_lastChange;
        $fields["set_progress"] = $this->_setProgress;
        $fields["hours"] = $this->_hours;
        $fields["long_desc"] = $this->_longDescription;
        return $fields;
    }

    /**
     * Set var.
     * @param String $databasecolumn Variable to set
     * @param String $value          Value. to set
     * @return Void.
     */
    public function setVar($databasecolumn, $value) {
        switch ($databasecolumn) {
            case "project_id":
                $this->setProject_id($value);
                break;
            case "prio":
                $this->setPrio($value);
                break;
            case "deadline":
                $this->setDeadline($value);
                break;
            case "message":
                $this->setMessage($value);
                break;
            case "done":
                $this->setDone($value);
                break;
            case "user_id":
                $this->setUserid($value);
                break;
            case "lastchange":
                $this->setLastchange($value);
                break;
            case "set_progress":
                $this->setSet_progress($value);
                break;
            case "hours":
                $this->setHours($value);
                break;
            case "long_desc":
                $this->setLong_desc($value);
                break;
            default:
                break;
        }
    }

     /**
     * Getters.
     */
    /**
     * Get Project.
     * @return String Project.
     */
    public function getProject() {
        return $this->_project;
    }

    /**
     * Get DeadlineText.
     * @return String DeadlineText.
     */
    public function getDeadlineText() {
        return $this->_deadlineText;
    }

    /**
     * Get id.
     * @return Integer Id.
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Get ProjectId.
     * @return Integer ProjectId.
     */
    public function getProjectId() {
        return $this->_projectId;
    }

    /**
     * Get Prio.
     * @return String Prio.
     */
    public function getPrio() {
        return $this->_prio;
    }

    /**
     * Get Deadline.
     * @return String Deadline.
     */
    public function getDeadline() {
        return $this->_deadline;
    }

    /**
     * Get Message.
     * @return String Message.
     */
    public function getMessage() {
        return $this->_message;
    }

    /**
     * Get Done.
     * @return String Done.
     */
    public function getDone() {
        return $this->_done;
    }

    /**
     * Get Userid.
     * @return Integer Userid.
     */
    public function getUserId() {
        return $this->_userId;
    }

    /**
     * Get Lastchange.
     * @return String Lastchange.
     */
    public function getLastchange() {
        return $this->_lastChange;
    }

    /**
     * Get SetProgress.
     * @return String SetProgress.
     */
    public function getSetProgress() {
        return $this->_setProgress;
    }

    /**
     * Get Hours.
     * @return String Hours.
     */
    public function getHours() {
        return $this->_hours;
    }

    /**
     * Get LongDesc.
     * @return String LongDesc.
     */
    public function getLongDesc() {
        return $this->_longDescription;
    }

    /**
     * Setters.
     */
    /**
     * Set the id.
     * @param Integer $id id.
     * @return  Void.
     */
    public function setId($id) {
        $this->_id = $id;
    }
    
    /**
     * Set the Project.
     * @param String $project Project.
     * @return  Void.
     */
    public function setProject($project) {
        $this->_project = $project;
    }

    /**
     * Set the ProjectId.
     * @param Integer $project_id ProjectId.
     * @return  Void.
     */
    public function setProjectId($project_id) {
        $this->_projectId = $project_id;
    }

    /**
     * Set the Prio.
     * @param String $prio Prio.
     * @return  Void.
     */
    public function setPrio($prio) {
        $this->_prio = $prio;
    }

    /**
     * Set the Deadline.
     * @param String $deadline Deadline.
     * @return  Void.
     */
    public function setDeadline($deadline) {
        $this->_deadline = $deadline;
    }

    /**
     * Set the Message.
     * @param String $message Message.
     * @return  Void.
     */
    public function setMessage($message) {
        $this->_message = $message;
    }
    
    /**
     * Set the Message.
     * @param String $deadline_text deadline_text.
     * @return  Void.
     */
    public function setDeadlineText($deadline_text) {
        $this->_deadlineText = $deadline_text;
    }

    /**
     * Set the Done.
     * @param String $done Done.
     * @return  Void.
     */
    public function setDone($done) {
        $this->_done = $done;
    }

    /**
     * Set the Userid.
     * @param Integer $userid Userid.
     * @return  Void.
     */
    public function setUserId($userid) {
        $this->_userId = $userid;
    }

    /**
     * Set the Lastchange.
     * @param String $lastchange Lastchange.
     * @return  Void.
     */
    public function setLastchange($lastchange) {
        $this->_lastChange = $lastchange;
    }

    /**
     * Set the SetProgress.
     * @param String $set_progress SetProgress.
     * @return  Void.
     */
    public function setSetProgress($set_progress) {
        $this->_setProgress = $set_progress;
    }

    /**
     * Set the Hours.
     * @param Integer $hours Hours.
     * @return  Void.
     */
    public function setHours($hours) {
        $this->_hours = $hours;
    }

    /**
     * Set the LongDesc.
     * @param Integer $long_desc LongDesc.
     * @return  Void.
     */
    public function setLongDesc($long_desc) {
        $this->_longDescription = $long_desc;
    }
}
