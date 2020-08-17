<?php

/**
 * User Model
 * Contains information on the user
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
 * @uses       \SG\Ram\Models\Person            Person object
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
 * User model
 * @category   Models
 * @package    SG
 */

class User extends Model
{
    private $_id;
    private $_username;
    private $address;
    private $_email;
    private $_fullname;
    private $_company;
    private $_sessionid;
    private $_ps;
    private $_rand;
    private $_admin;
    private $_active;
    private $_joined;
    private $_loggedin = false;
    private $_user;
    private $_personId;
    private $_personName;
    private $_session;
    private $_log;
    
    /**
     * Constructor.
     * @param Array | null $data Data to fill the object.
     * @return Void.
     */
    public function __construct($data = null) {
        if (isset($data)) {
            $this->setData($data);
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
     * Set Minimum.
     * @param Array $data Row of the database to fill the model
     * @return  Void
     */
    public function setMinimum($data) {
        $this->setId($data['id']);
        $this->setUsername($data['username']);
        $this->setEmail($data['email']);
        $this->setFullname($data['fullname']);
        $this->setAdmin($data['admin']);
        $this->setActive($data['active']);
        $this->setJoined($data['joined']);
        if (isset($data['person_id'])) {
            $this->setPersonId($data['person_id']);
            $this->setPersonName($data['person_name']);
        }
    }

    /**
     * Builds object is data comes from database.
     * @param Array $data Row of the database to fill the model
     * @return  Boolean
     */
    private function setData($data) {
        $this->setId($data['id']);
        $this->setUsername($data['username']);
        $this->setEmail($data['email']);
        $this->setFullname($data['fullname']);
        $this->setCompany($data['company']);
        $this->setSessionid($data['sessionid']);
        $this->setPs($data['ps']);
        $this->setRand($data['rand']);
        $this->setAdmin($data['admin']);
        $this->setActive($data['active']);
        $this->setJoined($data['joined']);
        
        $this->setAddress($data);
        return true;
    }

    
    /**
     * Getters.
     */
    /**
     * Get User.
     * @return String User.
     */
    public function getUser() {
        return $this->_user;
    }

    /**
     * Get Log.
     * @return String Log.
     */
    public function getLog() {
        return $this->_log;
    }

    /**
     * Get Helper.
     * @return String Helper.
     */
    public function getHelper() {
        return $this->_helper;
    }

    /**
     * Get Mail.
     * @return String Mail.
     */
    public function getMail() {
        return $this->_mail;
    }

    /**
     * Get Session.
     * @return String Session.
     */
    public function getSession() {
        return $this->_session;
    }

    /**
     * Get Loggedin.
     * @return String Loggedin.
     */
    public function getLoggedin() {
        return $this->_loggedin;
    }

    /**
     * Get Username.
     * @return String Username.
     */
    public function getUsername() {
        return $this->_username;
    }

    /**
     * Get Id.
     * @return Integer Id.
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Get Email.
     * @return String Email.
     */
    public function getEmail() {
        return $this->_email;
    }

    /**
     * Get Fullname.
     * @return String Fullname.
     */
    public function getFullname() {
        return $this->_fullname;
    }

    /**
     * Get Address.
     * @return String Address.
     */
    public function getAddress() {
        return $this->_address;
    }

    /**
     * Get Company.
     * @return String Company.
     */
    public function getCompany() {
        return $this->_company;
    }

    /**
     * Get SessionId.
     * @return String SessionId.
     */
    public function getSessionId() {
        return $this->_sessionid;
    }

    /**
     * Get Ps.
     * @return String Ps.
     */
    public function getPs() {
        return $this->_ps;
    }

    /**
     * Get Rand.
     * @return String Rand.
     */
    public function getRand() {
        return $this->_rand;
    }

    /**
     * Get Admin.
     * @return String Admin.
     */
    public function getAdmin() {
        return $this->_admin;
    }

    /**
     * Get Active.
     * @return String Active.
     */
    public function getActive() {
        return $this->_active;
    }
    
    /**
     * Get ActiveIcon.
     * @return String ActiveIcon.
     */
    public function getActiveIcon() {
        return "<i class='fas fa-times-circle' style='color:" . $this->getActiveIconColor() . ";font-size:17px;'></i></button>";
    }

    /**
     * getActiveIconColor
     * Checks value for active and returns correct color
     * @return String   Color with in HEX
     */
    private function getActiveIconColor() {
        return $this->_active < 1 ? "#a94442" : "#009688";
    }

    /**
     * Get Joined.
     * @return date Joined.
     */
    public function getJoined() {
        return $this->_joined;
    }
    
    /**
     * Get PersonId.
     * @return String PersonId.
     */
    public function getPersonId() {
        return $this->_personId;
    }
    
    /**
     * Get PersonName.
     * @return String PersonName.
     */
    public function getPersonName() {
        return $this->_personName;
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
     * Set the User.
     * @param String $_user User.
     * @return  Void.
     */
    public function setUser($_user) {
        $this->_user = $_user;
    }

    /**
     * Set the Username.
     * @param String $username Username.
     * @return  Void.
     */
    public function setUserName($username) {
        $this->_username = $username;
    }

    /**
     * Set the Email.
     * @param String $email Email.
     * @return  Void.
     */
    public function setEmail($email) {
        $this->_email = $email;
    }
    
    /**
     * Set the Loggedin.
     * @param String $loggedin Loggedin.
     * @return  Void.
     */
    public function setLoggedin($loggedin) {
        $this->_loggedin = $loggedin;
    }

    /**
     * Set the FullName.
     * @param String $fullname FullName.
     * @return  Void.
     */
    public function setFullName($fullname) {
        $this->_fullname = $fullname;
    }

    /**
     * Set the Address.
     * @param String $address address.
     * @return  Void.
     */
    public function setAddress($address) {
        if (is_a($this->address, 'SG\Ram\Models\Address')) {
            $this->address = $address;
        } else {
            $this->address = new Address($address);
        }
    }

    /**
     * Set the company.
     * @param String $company company.
     * @return  Void.
     */
    public function setCompany($company) {
        $this->_company = $company;
    }

    /**
     * Set the SessionId.
     * @param Integer $sessionid SessionId.
     * @return  Void.
     */
    public function setSessionId($sessionid) {
        $this->_sessionid = $sessionid;
    }

    /**
     * Set the Ps.
     * @param String $ps Ps.
     * @return  Void.
     */
    public function setPs($ps) {
        $this->_ps = $ps;
    }

    /**
     * Set the Rand.
     * @param String $rand Rand.
     * @return  Void.
     */
    public function setRand($rand) {
        $this->_rand = $rand;
    }

    /**
     * Set the Admin.
     * @param String $admin Admin.
     * @return  Void.
     */
    public function setAdmin($admin) {
        $this->_admin = $admin;
    }

    /**
     * Set the Active.
     * @param String $active Active.
     * @return  Void.
     */
    public function setActive($active) {
        $this->_active = $active;
    }

    /**
     * Set the Joined.
     * @param date $joined Joined.
     * @return  Void.
     */
    public function setJoined($joined) {
        $this->_joined = $joined;
    }

    /**
     * Set the isAdmin.
     * @return  String admin.
     */
    public function isAdmin() {
        return $this->_admin;
    }
    
    /**
     * Set the PersonId.
     * @param Integer $id PersonId.
     * @return  Void.
     */
    public function setPersonId($id) {
        $this->_personId = $id;
    }
    
    /**
     * Set the PersonName.
     * @param String $name PersonName.
     * @return  Void.
     */
    public function setPersonName($name) {
        $this->_personName = $name;
    }
}
