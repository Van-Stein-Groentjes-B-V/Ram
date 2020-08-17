<?php

/**
 * Ticket Model
 * Contains information on the tickets
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
use DateTime;

/**
 * Ticket model
 * @category   Models
 * @package    SG
 */
class Ticket extends Model
{
    private $_id;
    private $_projectId;
    private $_send;
    private $_fromId;
    private $_fromName;
    private $_fromEmail;
    private $_status;
    private $_message;
    private $_deleted;
    private $_subject;
    
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
        $this->_id = $data['id'];
        $this->_projectId = $data['project_id'];
        $this->_send = $data['send'];
        $this->_fromId = $data['from_id'];
        $this->_fromEmail = $data['from_email'];
        $this->_status = $data['status'];
        $this->_message = $data['message'];
        $this->_deleted = $data['deleted'];
        $this->_subject = $data['subject'];
    }

    /**
     * Getters.
     */
    /**
     * Get id.
     * @return Integer id.
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
     * Get Send.
     * @return String Send.
     */
    public function getSend() {
        return $this->_send;
    }

    /**
     * Get FromId.
     * @return Integer FromId.
     */
    public function getFromId() {
        return $this->_fromId;
    }
    
    /**
     * Get SendDmy.
     * @return String.
     */
    public function getSendDmy() {
        return $this->_send ? (new DateTime($this->_send))->format('d-m-Y') : "";
    }
    
    /**
     * Get FromName.
     * @return String FromName.
     */
    public function getFromName() {
        return $this->_fromName;
    }

    /**
     * Get FromEmail.
     * @return String FromEmail.
     */
    public function getFromEmail() {
        return $this->_fromEmail;
    }

    /**
     * Get Status.
     * @return String Status.
     */
    public function getStatus() {
        return $this->_status;
    }

    /**
     * Get Message.
     * @return String Message.
     */
    public function getMessage() {
        return $this->_message;
    }

    /**
     * Get Deleted.
     * @return String | int Deleted.
     */
    public function getDeleted() {
        return $this->_deleted;
    }

    /**
     * Get Subject.
     * @return String Subject.
     */
    public function getSubject() {
        return $this->_subject;
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
     * Set the ProjectId.
     * @param Integer $project_id ProjectId.
     * @return  Void.
     */
    public function setProjectId($project_id) {
        $this->_projectId = $project_id;
    }

    /**
     * Set the Send.
     * @param String $send Send.
     * @return  Void.
     */
    public function setSend($send) {
        $this->_send = $send;
    }

    /**
     * Set the FromId.
     * @param Integer $from_id FromId.
     * @return  Void.
     */
    public function setFromId($from_id) {
        $this->_fromId = $from_id;
    }
    
    /**
     * Set the FromName.
     * @param String $name FromName.
     * @return  Void.
     */
    public function setFromName($name) {
        $this->_fromName = $name;
    }

    /**
     * Set the FromEmail.
     * @param String $from_email FromEmail.
     * @return  Void.
     */
    public function setFromEmail($from_email) {
        $this->_fromEmail = $from_email;
    }

    /**
     * Set the Status.
     * @param String $status Status.
     * @return  Void.
     */
    public function setStatus($status) {
        $this->_status = $status;
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
     * Set the Deleted.
     * @param String $deleted Deleted.
     * @return  Void.
     */
    public function setDeleted($deleted) {
        $this->_deleted = $deleted;
    }

    /**
     * Set the Subject.
     * @param String $subject Subject.
     * @return  Void.
     */
    public function setSubject($subject) {
        $this->_subject = $subject;
    }
}
