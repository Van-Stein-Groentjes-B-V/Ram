<?php

/**
 * Company Model
 * Contains all information known in the system om the company
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
 * Company
 * @category   Models
 * @package    Ram
 */
class Company extends Model
{
    public $name;
    public $vat_nr;
    public $iban;
    public $address;
    public $kvk;
    public $website;
    public $tel;
    public $main_contact;
    
    private $_id;
    private $_logo = "no_avatar.jpg";
    private $_owned;
    private $_socialmedia = array();
    private $_facebook;
    private $_youtube;
    private $_twitter;
    private $_linkedin;
    
    /**
     * Constructor.
     * @param Array | Null $data Data to fill the object.
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
        $this->setName($data['name']);
        $this->setLogo($data['logo']);
        $this->setVatNr($data['vat_nr']);
        $this->setIban($data['iban']);
        $this->setTel($data['tel']);
        $this->setKvk($data['kvk']);
        $this->setWebsite($data['website']);
        $this->setMainContact($data['main_contact']);
        $this->setOwned($data['owned']);
        $this->setFacebook($data['facebook']);
        $this->setTwitter($data['twitter']);
        $this->setYoutube($data['youtube']);
        $this->setLinkedin($data['linkedin']);
        $this->setAddress($data);
    }

    /**
     * Getters;
     */
    /**
     * Get id.
     * @return Integer      Id of the company.
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Get Name.
     * @return String       Name of the company.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get Logo.
     * @return String       Location of the company logo.
     */
    public function getLogo() {
        $companieLogo = file_exists("./img/company/logos/" . $this->_logo);
        if (strlen($this->_logo) < 5 || !$companieLogo) {
            $this->_logo = "no_avatar.jpg";
        }
        return $this->_logo;
    }

    /**
     * Get Address.
     * @return String      Address of the company.
     * </todo> Use Address object
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Get VatNr.
     * @return string       VatNr.
     */
    public function getVatNr() {

        return $this->vat_nr;
    }

    /**
     * Get Iban.
     * @return String       Iban.
     */
    public function getIban() {
        return $this->iban;
    }

    /**
     * Get Tel.
     * @return String Tel.
     */
    public function getTel() {
        return $this->tel;
    }

    /**
     * Get Kvk.
     * @return String       Kvk.
     */
    public function getKvk() {
        return $this->kvk;
    }

    /**
     * Get Website.
     * @return String       Website.
     */
    public function getWebsite() {
        return !empty($this->website) ? $this->createLink($this->website) : "";
    }

    /**
     * Get MainContact.
     * @return Integer      MainContact.
     */
    public function getMainContact() {
        return $this->main_contact;
    }

    /**
     * Get Owned.
     * @return Boolean      True if company is owned by user.
     */
    public function getOwned() {
        return $this->_owned;
    }

    /**
     * Get SocialMedia.
     * @return Boolean SocialMedia.     Use / display social media links
     */
    public function getSocialMedia() {
        return $this->_socialmedia;
    }

    /**
     * Get Facebook.
     * @return String       Facebook.
     */
    public function getFacebook() {
        return !empty($this->_facebook) ? $this->createLink($this->_facebook) : "";
    }

    /**
     * Get Youtube.
     * @return string       Youtube.
     */
    public function getYoutube() {
        return !empty($this->_youtube) ? $this->createLink($this->_youtube) : "";
    }

    /**
     * Get Twitter.
     * @return string       Twitter.
     */
    public function getTwitter() {
        return !empty($this->_twitter) ? $this->createLink($this->_twitter) : "";
    }

    /**
     * Get Linkedin.
     * @return string       Linkedin.
     */
    public function getLinkedin() {
        return !empty($this->_linkedin) ? $this->createLink($this->_linkedin) : "";
    }

    /*
     * Setters
     */
    /**
     * Set the id.
     * @param Integer $id Id.
     * @return Void.
     */
    public function setId($id) {
        $this->_id = $id;
    }

    /**
     * Set the company name.
     * @param String $name Name.
     * @return Void.
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Set the logo.
     * @param String $logo Logo location
     * @return Void.
     */
    public function setLogo($logo) {
        $this->_logo = $logo;
    }

    /**
     * Set the address.
     * @param String $address Address.
     * @return Void.
     */
    public function setAddress($address) {
        if (is_a($this->address, 'SG\Ram\Models\Address')) {
            $this->address = $address;
        } else {
            $this->address = new Address($address);
        }
    }

    /**
     * Set the VatNr.
     * @param String $vat_nr VatNr.
     * @return Void.
     */
    public function setVatNr($vat_nr) {
        $this->vat_nr = $vat_nr;
    }

    /**
     * Set the Iban.
     * @param String $iban Iban.
     * @return Void.
     */
    public function setIban($iban) {
        $this->iban = $iban;
    }

    /**
     * Set the Registration number Chamber of Commerce.
     * @param String $kvk Registration number Chamber of Commerce.
     * @return Void.
     */
    public function setKvk($kvk) {
        $this->kvk = $kvk;
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
     * Set the website.
     * @param String $website Website link.
     * @return Void.
     */
    public function setWebsite($website) {
        $this->website = $website;
    }

    /**
     * Set the MainContact.
     * @param Integer $main_contact Id of mainContact.
     * @return Void.
     */
    public function setMainContact($main_contact) {
        $this->main_contact = $main_contact;
    }

    /**
     * Set the Owned.
     * @param Boolean $owned Owned.
     * @return Void.
     */
    public function setOwned($owned) {
        $this->_owned = $owned;
    }

    /**
     * Set the Socialmedia.
     * @param Boolean $socialmedia Display social media.
     * @return  void.
     */
    public function setSocialMedia($socialmedia) {
        $this->_socialmedia = $socialmedia;
    }

    /**
     * Set Facebook.
     * @param String $facebook Facebook link.
     * @return  Void.
     */
    public function setFacebook($facebook) {
        $this->_facebook = $facebook;
    }
    
    /**
     * Set Youtube.
     * @param String $youtube Youtube link.
     * @return Void.
     */
    public function setYoutube($youtube) {
        $this->_youtube = $youtube;
    }

    /**
     * Set Twitter.
     * @param String $twitter Twitter link.
     * @return Void.
     */
    public function setTwitter($twitter) {
        $this->_twitter = $twitter;
    }

    /**
     * Set Linkedin.
     * @param String $linkedin Linkedin link.
     * @return  void.
     */
    public function setLinkedin($linkedin) {
        $this->_linkedin = $linkedin;
    }
}
