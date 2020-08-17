<?php

/**
 * Account Model
 * User model contains information about the user.
 * Used for logging-in and user-management.
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
 * @uses       \SG\Ram\Models\Address           Address model
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
use SG\Ram\Models\Address;

/**
 * Account
 * @category   Models
 * @package    Ram
 */
class Account extends Model
{
    private $_id;
    private $_username;
    private $_email;
    private $_admin;
    private $_active;
    private $_loggedin;
    private $_sessionid;
    private $_pass;
    private $_fullname;
    private $_adress;
    private $_personId;
    private $_logo = "no_avatar.jpg";
    private $_showStats = false;
    private $_playSounds = false;

    /**
     * Constructor.
     * @param array|null $data to fill the object.
     * @return void.
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
     * Builds object if data comes from database.
     * @param   Array $data Row of the database to fill the model.
     * @return  Void
     */
    private function buildObject($data) {
        $this->setId($data['id']);
        $this->setUsername($data['username']);
        $this->setEmail($data['email']);
        $this->setAdmin($data['admin']);
        $this->setPassword($data['ps']);
        $this->setActive($data['active']);
        $this->setFullname($data['fullname']);
        if (isset($data['person_id'])) {
            $this->setPersonId($data['person_id']);
        }
        $this->setAddress($data);
    }

    /**
     * Checks for content (email).
     * @return Boolean  True if $_email is not empty.
     */
    public function hasContent() {
        return $this->_email != "";
    }
    
    /**
     * Checks whether the id is set
     * @return  Integer  Id of the object
     */
    public function exists() {
        return $this->getId();
    }
    
    /**
     * SetVar function allows for setting a variable by
     * calling it by its name.
     * @param   String $which Variable name to set.
     * @param   Mixed  $value Value to set.
     * @return  Void
     */
    public function setVar($which, $value) {
        switch ($which) {
            case "id":
                $this->setId($value);
                break;
            case "email":
                $this->setEmail($value);
                break;
            case "username":
                $this->setUsername($value);
                break;
            case "admin":
                $this->setAdmin($value);
                break;
            case "active":
                $this->setActive($value);
                break;
            case "sessionid":
                $this->setSessionid($value);
                break;
            case "person_id":
                $this->setPersonId($value);
                break;
            default:
                break;
        }
    }
    
     /*
     * Getters;
     */
    /**
     * Get id.
     * @return integer  Id.
     */
    public function getId() {
        return $this->_id;
    }
    
    /**
     * Get logo.
     * @return string   Logo.
     */
    public function getLogo() {
        $personLogo = file_exists("./img/person/logos/" . $this->_logo);
        if (strlen($this->_logo) < 5 || !$personLogo) {
            $this->_logo = "no_avatar.jpg";
        }
        return $this->_logo;
    }
    
    /**
     * Get email.
     * @return String   Email.
     */
    public function getEmail() {
        return $this->_email;
    }
    
    /**
     * Get Username.
     * @return String   Username.
     */
    public function getUsername() {
        return $this->_username;
    }
    
    /**
     * Get Admin.
     * @return Integer  1 if user is admin, 0 for no admin)
     */
    public function getAdmin() {
        return $this->_admin;
    }
    
    /**
     * Get Active.
     * @return Integer  Is the user active? (1 = yes, 0 = no)
     */
    public function getActive() {
        return $this->_active;
    }
    
    /**
     * Get LoggedIn.
    * @return Boolean   True if logged in
     * </deprecated> duplicate of isLoggedIn
     */
    public function getLoggedIn() {
        return $this->_loggedin;
    }
    
    /**
     * Get Sessionid.
    * @return String    Session id
     */
    public function getSessionid() {
        return $this->_sessionid;
    }
    
    /**
     * Get Password.
     * @return string Password.
     */
    public function getPassword() {
        return $this->_pass;
    }
    
    /**
     * Get Fullname.
     * @return String Fullname.
     */
    public function getFullname() {
        return $this->_fullname;
    }
    
    /**
     * Get the person id of the coupled person, or -1 if nothing.
     * @return integer the id of the person.
     */
    public function getPersonId() {
        return $this->_personId;
    }
    
    /**
     * Get Address.
     * @return Address  The address object or null
     */
    public function getAddress() {
        return $this->_adress;
    }
    
    /**
     * Get ShowStats.
    * @return Integer   Setting to show statistics.
     */
    public function getShowStats() {
        return $this->_showStats;
    }
    
    /**
     * Get PlaySounds.
     * @return Integer  Setting to play sound on alerts.
     */
    public function getPlaySounds() {
        return $this->_playSounds;
    }
      
    /**
     * check if user is loggedin.
     * @return Boolean      True if user is logged in
     *
     * */
    public function isLoggedIn() {
        return $this->_loggedin;
    }
    
    /**
     * check if user is customer.
     * @return Boolean      True if value <= 0
     * */
    public function isCustomer() {
        return $this->_admin <= 0;
    }
    
    /**
     * check if user is admin.
     * @return Boolean      True if value >= 1
     * */
    public function isAdmin() {
        return $this->_admin >= 1;
    }
    
    /**
     * check if user is superadmin.
     * @return Boolean      True if value >= 2
     * */
    public function isSuperAdmin() {
        return $this->_admin >= 2;
    }
    
    /**
     * check if user is main admin.
     * @return Boolean      True if value == 3
     * */
    public function isMainAdmin() {
        return $this->_admin == 3;
    }
    
    /*
     * Setters
     */
    /**
     * Set the id.
     * @param Integer $value value to set.
     * @return  Void.
     */
    public function setId($value) {
        $this->_id = $value;
    }
    
    /**
     * Set the Logo.
     * @param String $logo Logo location.
     * @return  Void.
     */
    public function setLogo($logo) {
        $this->_logo = $logo;
    }
    
    /**
     * Set the Email.
     * @param String $value E-mail address.
     * @return  Void.
     */
    public function setEmail($value) {
        $this->_email = $value;
    }
    
    /**
     * Set the Username.
     * @param String $value Username.
     * @return  Void.
     */
    public function setUsername($value) {
        $this->_username = $value;
    }
    
    /**
     * Set the Admin.
     * @param Integer $value Value of adminlevel
     * @return  Void.
     */
    public function setAdmin($value) {
        $this->_admin = $value;
    }
    
    /**
     * Set the Active.
     * @param Integer $value 1 if active or 0 if not.
     * @return  Void.
     */
    public function setActive($value) {
        $this->_active = $value;
    }
    
    /**
     * Set the LoggedIn.
     * @param Boolean $value If user logged in set to true.
     * @return  Void.
     */
    public function setLoggedIn($value) {
        $this->_loggedin = $value;
    }
    
    /**
     * Set the Sessionid.
     * @param String $value Session id
     * @return  void.
     */
    public function setSessionid($value) {
        $this->_sessionid = $value;
    }
    
    /**
     * Set the Password.
     * @param String $value Password.
     * @return  Void.
     */
    public function setPassword($value) {
        $this->_pass = $value;
    }
    
    /**
     * Set the Fullname.
     * @param String $name Fullname
     * @return  Void.
     */
    public function setFullname($name) {
        $this->_fullname = $name;
    }
    
    /**
     * Set the person Id.
     * @param integer $id The id of the person coupled.
     * @return  Void
     */
    public function setPersonId($id) {
        $this->_personId = $id;
    }
    
    /**
     * Set the Address.
     * @param Address | Array | null $adress Address, database row or null
     * @return Void.
     */
    public function setAddress($adress) {
        if (is_object($adress) && is_a($adress, 'SG\Ram\Models\Address')) {
            $this->_adress = $adress;
        } elseif (is_array($adress) && isset($adress['id'])) {
            $this->_adress = new Address($adress);
        } else {
            $this->_adress = new Address();
        }
    }
    
    /**
     * Set the ShowStats.
     * @param Integer | Boolean $show Sets value to true if $show > 0 or $show == true.
     * @return Void.
     */
    public function setShowStats($show) {
        if (is_numeric($show)) {
            $this->_showStats = $show > 0;
        } else {
            $this->_showStats = $show;
        }
    }
    
    /**
     * Set the PlaySoundsPlaySounds.
     * @param Integer | Boolean $play Sets value to true if $play > 0 or $play == true.
     * @return  void.
     */
    public function setPlaySounds($play) {
        if (is_numeric($play)) {
            $this->_playSounds = $play > 0;
        } else {
            $this->_playSounds = $play;
        }
    }
}
