<?php

/**
 * Address Model
 * Contains information about the address
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
 * Address model
 * @category   Models
 * @package    Ram
 */
class Address extends Model
{
    private $_id;
    private $_street;
    private $_number;
    private $_addition = " ";
    private $_postalcode;
    private $_city;
    private $_country;
    
    /**
     * Constructor.
     * @param array|null $data to fill the object.
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
     * @param   Array $data Row of the database to fill the model.
     * @return  Void
     */
    private function buildObject($data) {
        $this->setId($data['id']);
        
        if (isset($data['street'])) {
            $this->setStreet($data['street']);
        }
        if (isset($data['number'])) {
            $this->setNumber($data['number']);
        }
        if (isset($data['city'])) {
            $this->setCity($data['city']);
        }
        if (isset($data['postalcode'])) {
            $this->setPostalcode($data['postalcode']);
        }
        
        if (isset($data['addition'])) {
            $this->setPostalcode($data['addition']);
        }
        $this->setCountry($data['country']);
    }
    
    /*
     *  Getters
     **/
    /**
     * Get id.
     * @return Integer id.
     */
    public function getId() {
        return $this->_id;
    }
    
    /**
     * Get street.
     * @return String   Streetname.
     */
    public function getStreet() {
        return $this->_street;
    }
    
    /**
     * Get addition.
     * @return String   addition.
     */
    public function getAddition() {
        return $this->_addition;
    }
    
    
    /**
     * Get number.
     * @return Integer Number
     */
    public function getNumber() {
        return $this->_number;
    }
    
    /**
     * Get postcode.
     * @return String Postcode.
     */
    public function getPostalcode() {
        return $this->_postalcode;
    }
    
    /**
     * Get city.
     * @return String city.
     */
    public function getCity() {
        return $this->_city;
    }
    
    /**
     * Get Country.
     * @return String    Dutch country names translated to English country names.
     */
    public function getCountry() {
        return $this->_country;
    }
    /*
     *  Setters
     **/
    /**
     * Set the id.
     * @param Integer $id Id.
     * @return  Void.
     */
    public function setId($id) {
        $this->_id = $id;
    }
    
    /**
     * Set the street.
     * @param String $street Street.
     * @return Void.
     */
    public function setStreet($street) {
        $this->_street = $street;
    }
    
    /**
     * Set the Number.
     * @param Integer $number Number.
     * @return  Void.
     */
    public function setNumber($number) {
        $this->_number = $number;
    }
    
    /**
     * Set the addition.
     * @param Integer $addition addition.
     * @return  Void.
     */
    public function setAddition($addition) {
        $this->_addition = $addition;
    }
    
    /**
     * Set the Postalcode.
     * @param String $postalcode Postalcode.
     * @return  void.
     */
    public function setPostalcode($postalcode) {
        $this->_postalcode = $postalcode;
    }
    
    /**
     * Set the City.
     * @param String $city City.
     * @return  Void.
     */
    public function setCity($city) {
        $this->_city = $city;
    }
    
    /**
     * Set the Country name.
     * @param Integer $country Country name.
     * @return  Void.
     */
    public function setCountry($country) {
        $this->_country = $country;
    }
    
    /**
     * Constructs the full street address with streetname number and addition
     * @return String       Full address;
     */
    public function getFullStreetAddress() {
        return $this->_street . " " . $this->_number . "" . $this->_addition;
    }
    
    /**
     * Constructs the postcode city combination
     * @return String       Postcode and city
     */
    public function getPostalcodeCity() {
        return $this->_postalcode . " " . $this->_city;
    }
    
    /**
     * Returns the full address (except the country)
     * @param Boolean $line Will the address be printed in-line if not the address spans two lines
     * @return String           With the full address
     */
    public function getFullAddress($line) {
        return $line ? $this->getFullStreetAddress() . " " .  $this->getPostalcodeCity() :
                       $this->getFullStreetAddress() . "<br/>" .  $this->getPostalcodeCity();
    }
}
