<?php

/**
 * Attachment Model
 * Contains information of the Attachment.
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
 * Attachment model
 * @category   Models
 * @package    Ram
 */
class Attachment extends Model
{
    private $_id;
    private $_projectId;
    private $_type;
    private $_typeClass;
    private $_location;
    private $_message;
    private $_filename;
    private $_deleted;
    private $_personId;
    private $_namePerson;
    private $_realName;
    
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
     * Destructor
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
    }

    /**
     * Builds object is data comes from database.
     * @param array $data row of the database to fill the model
     * @return  Void
     */
    public function buildObject($data) {
        $this->_id = $data['id'];
        $this->_projectId = $data['project_id'];
        $this->_type = $data['type'];
        $this->_location = $data['location'];
        $this->_message = $data['message'];
        $this->_filename = $data['filename'];
        $this->_realName = $data['realname'];
        $this->_deleted = $data['deleted'];
        $this->_personId = $data['person_id'];
    }
    
    /*
     * Getters;
     */
    /**
     * Get id.
     * @return integer id.
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Get projectId.
     * @return Integer ProjectId.
     */
    public function getProjectId() {
        return $this->_projectId;
    }

    /**
     * Get Type.
     * @return String Type.
     */
    public function getType() {
        return $this->_type;
    }
    
    /**
     * Get TypeClass.
     * @return String TypeClass.
     */
    public function getTypeClass() {
        return $this->_typeClass;
    }

    /**
     * Get Location.
     * @return String Location.
     */
    public function getLocation() {
        return $this->_location;
    }

    /**
     * Get Message.
     * @return String Message.
     */
    public function getMessage() {
        return $this->_message;
    }

    /**
     * Get Filename.
     * @return String Filename.
     */
    public function getFilename() {
        return $this->_filename;
    }
    
    /**
     * Get RealName.
     * @return String RealName.
     */
    public function getRealName() {
        return $this->_realName;
    }

    /**
     * Get Deleted.
     * @return 0 | 1 Deleted.
     */
    public function getDeleted() {
        return $this->_deleted;
    }

    /**
     * Get personId.
     * @return Integer personId.
     */
    public function getPersonId() {
        return $this->_personId;
    }
    
    /**
     * Get NamePerson.
     * @return String NamePerson.
     */
    public function getNamePerson() {
        return $this->_namePerson;
    }

    /*
     * Setters
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
     * @param Integer $projectId ProjectId.
     * @return  Void.
     */
    public function setProjectId($projectId) {
        $this->_projectId = $projectId;
    }

    /**
     * Set the type.
     * @param Integer $type type.
     * @return  Void.
     */
    public function setType($type) {
        $this->_type = $type;
    }
    
    /**
     * Set the class.
     * @param Integer $class class.
     * @return  Void.
     */
    public function setTypeClass($class) {
        $this->_typeClass = $class;
    }

    /**
     * Set the location.
     * @param Integer $location location.
     * @return  Void.
     */
    public function setLocation($location) {
        $this->_location = $location;
    }

    /**
     * Set the message.
     * @param Integer $message message.
     * @return  Void.
     */
    public function setMessage($message) {
        $this->_message = $message;
    }

    /**
     * Set the filename.
     * @param Integer $filename filename.
     * @return  Void.
     */
    public function setFilename($filename) {
        $this->_filename = $filename;
    }
    
    /**
     * Set the realname.
     * @param Integer $realname realname.
     * @return  Void.
     */
    public function setRealName($realname) {
        $this->_realName = $realname;
    }

    /**
     * Set the deleted.
     * @param Integer $deleted deleted.
     * @return  Void.
     */
    public function setDeleted($deleted) {
        $this->_deleted = $deleted;
    }

    /**
     * Set the PersonId.
     * @param Integer $personId PersonId.
     * @return  Void.
     */
    public function setPersonId($personId) {
        $this->_personId = $personId;
    }
    
    /**
     * Set the name.
     * @param Integer $name name.
     * @return  Void.
     */
    public function setNamePerson($name) {
        $this->_namePerson = $name;
    }
}
