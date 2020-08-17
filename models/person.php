<?php

/**
 * Person Model
 * Model contains all the information of a Person.
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
 * Person
 * @category   Models
 * @package    Ram
 */
class Person extends Model
{
    public $name;
    public $title;
    public $email;
    public $tel;
    public $company;
    public $companyId;
    
    private $_id;
    private $_logo;
    private $address;
    private $_website;
    private $_socialmedia = array();
    private $_facebook;
    private $_youtube;
    private $_twitter;
    private $_linkedin;
    private $_notes;
    private $_accountId;
    private $_customerPass;
    
    /**
     * constructor.
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
     * @param Array $data Row of the database to fill the model.
     * @return Void.
     */
    private function buildObject($data) {
        $this->setId($data['id']);
        $this->setName($data['name']);
        $this->setTitle($data['title']);
        $this->setLogo($data['logo']);
        $this->setAddress($data);
        $this->setEmail($data['email']);
        $this->setTel($data['tel']);
        $this->setWebsite($data['website']);
        $this->setAccountId($data['account_id']);
        $this->setFacebook($data['facebook']);
        $this->setTwitter($data['twitter']);
        $this->setLinkedin($data['linkedin']);
        $this->setCustomerPass($data['person_pass']);
        $this->setNotes($data['notes']);
        $this->setCompany($data['company']);
        $this->setCompanyId($data['company_id']);
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
     * Get name.
     * @return String name.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get Title.
     * @return String Title.
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Get Logo.
     * @return String Logo.
     */
    public function getLogo() {
        $personLogo = file_exists("img/person/logos/" . $this->_logo);
        if (strlen($this->_logo) < 5 || !$personLogo) {
            $this->_logo = "no_avatar.jpg";
        }
        return $this->_logo;
    }

    /**
     * Get Address.
     * @return String Address.
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Get Email.
     * @return String Email.
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Get Tel.
     * @return String Tel.
     */
    public function getTel() {
        return $this->tel;
    }

    /**
     * Get CompanyId.
     * @return Integer CompanyId.
     */
    public function getCompanyId() {
        return $this->companyId;
    }
    
    /**
     * Get Company.
     * @return String Company.
     */
    public function getCompany() {
        return $this->company;
    }

    /**
     * Get Website.
     * @return String Website.
     */
    public function getWebsite() {
        return !empty($this->_website) ? $this->createLink($this->_website) : "";
    }

    /**
     * Get Socialmedia.
     * @return String Socialmedia.
     */
    public function getSocialMedia() {
        return $this->_socialmedia;
    }

    /**
     * Get Facebook.
     * @return String Facebook.
     */
    public function getFacebook() {
        return !empty($this->_facebook) ? $this->createLink($this->_facebook) : "";
    }

    /**
     * Get Youtube.
     * @return String Youtube.
     */
    public function getYoutube() {
        return !empty($this->_youtube) ? $this->createLink($this->_youtube) : "";
    }

    /**
     * Get Twitter.
     * @return String Twitter.
     */
    public function getTwitter() {
        return !empty($this->_twitter) ? $this->createLink($this->_twitter) : "";
    }

    /**
     * Get Linkedin.
     * @return String Linkedin.
     */
    public function getLinkedin() {
        return !empty($this->_linkedin) ? $this->createLink($this->_linkedin) : "";
    }

    /**
     * Get Notes.
     * @return String Notes.
     */
    public function getNotes() {
        return $this->_notes;
    }

    /**
     * Get Notes.
     * @return String Notes.
     */
    public function getAccountId() {
        return $this->_accountId;
    }

    /**
     * Get CustomerPass.
     * @return String CustomerPass.
     */
    public function getCustomerPass() {
        return $this->_customerPass;
    }

    /**
     * Setters.
     */
    /**
     * Set the id.
     * @param Integer $id Id.
     * @return  Void.
     */
    public function setId($id) {
        $this->_id = $id;
    }

    /**
     * Set the name.
     * @param String $name Name.
     * @return  Void.
     */
    public function setName($name) {
        $this->name = $name;
    }
    
    /**
     * Set the title.
     * @param String $title Title.
     * @return  Void.
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Set the logo.
     * @param String $logo Logo.
     * @return  Void.
     */
    public function setLogo($logo) {
        $this->_logo = $logo;
    }

    /**
     * Set the address.
     * @param String $address Address.
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
     * Set the email.
     * @param String $email Email.
     * @return  Void.
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Set the tel.
     * @param String $tel Tel.
     * @return  Void.
     */
    public function setTel($tel) {
        $this->tel = $tel;
    }

    /**
     * Set the company_id.
     * @param Integer $company_id Company_id.
     * @return  Void.
     */
    public function setCompanyId($company_id) {
        $this->companyId = $company_id;
    }

    /**
     * Set the website.
     * @param String $website Website.
     * @return  Void.
     */
    public function setWebsite($website) {
        $this->_website = $website;
    }

    /**
     * Set the socialmedia.
     * @param String $socialmedia Socialmedia.
     * @return  Void.
     */
    public function setSocialmedia($socialmedia) {
        $this->_socialmedia = $socialmedia;
    }

    /**
     * Set the facebook.
     * @param String $facebook Facebook.
     * @return  Void.
     */
    public function setFacebook($facebook) {
        $this->_facebook = $facebook;
    }

    /**
     * Set the youtube.
     * @param String $youtube Youtube.
     * @return  Void.
     */
    public function setYoutube($youtube) {
        $this->_youtube = $youtube;
    }

    /**
     * Set the twitter.
     * @param String $twitter Twitter.
     * @return  Void.
     */
    public function setTwitter($twitter) {
        $this->_twitter = $twitter;
    }

    /**
     * Set the linkedin.
     * @param String $linkedin Linkedin.
     * @return  Void.
     */
    public function setLinkedin($linkedin) {
        $this->_linkedin = $linkedin;
    }

    /**
     * Set the notes.
     * @param String $notes Notes.
     * @return  Void.
     */
    public function setNotes($notes) {
        $this->_notes = $notes;
    }

    /**
     * Set the account_id.
     * @param Integer $account_id Account_id.
     * @return  Void.
     */
    public function setAccountId($account_id) {
        $this->_accountId = $account_id;
    }

    /**
     * Set the customer_pass.
     * @param String $customer_pass Customer_pass.
     * @return  Void.
     */
    public function setCustomerPass($customer_pass) {
        $this->_customerPass = $customer_pass;
    }
    
    /**
     * Set the company.
     * @param String $company Company.
     * @return  Void.
     */
    public function setCompany($company) {
        $this->company = $company;
    }
}
