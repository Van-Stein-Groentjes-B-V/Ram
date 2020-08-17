<?php

/**
 * Module Model
 * Model contains all the information of a Module.
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
 * </deprecated> Check if needed
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
 * Module model
 * @category   Models
 * @package    Ram
 */
class Module extends Model
{
    private $_id;
    private $_name;
    private $_description;
    private $_version;
    private $_active;
    private $_menu_exists;
    private $_menu_title;
    private $_menu_icon;
    private $_created;
    private $_updated;
    
    /**
     * Constructor.
     * @param Array | null $data Data to fill the object with (database row).
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
     * Builds object if data comes from database.
     * @param Array $data Row of the database to fill the model.
     * @return  Void.
     */
    private function buildObject($data) {
        $this->setId($data['id']);
        $this->setName($data['name']);
        $this->setDescription($data['description']);
        $this->setVersion($data['version']);
        $this->setActive($data['active']);
        $this->setMenuExists($data['menu_exists']);
        $this->setMenuTitle($data['menu_title']);
        $this->setMenuIcon($data['menu_icon']);
        $this->setCreated($data['created_at']);
        $this->setUpdated($data['updated_at']);
    }
    
    /**
     * Getters.
     */
    /**
     * Get id.
     * @return Integer  Id of the module.
     */
    public function getId() {
        return $this->_id;
    }
    
    /**
     * Get name.
     * @return String   Module name.
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Get description.
     * @return String   Module Description.
     */
    public function getDescription() {
        return $this->_description;
    }
    
    /**
     * Get version.
     * @return String Version.
     */
    public function getVersion() {
        return $this->_version;
    }
    
    /**
     * Get Active.
     * @return Integer   1 = active, 0 = inactive.
     */
    public function getActive() {
        return $this->_active;
    }
    
    /**
     * Get MenuExists.
     * @return String MenuExists.
     */
    public function getMenuExists() {
        return $this->_menu_exists;
    }
    
    /**
     * Get MenuTitle.
     * @return string MenuTitle.
     */
    public function getMenuTitle() {
        return $this->_menu_title;
    }
    
    /**
     * Get MenuIcon.
     * @return string MenuIcon.
     */
    public function getMenuIcon() {
        return $this->_menu_icon;
    }
    
    /**
     * Get Created.
     * @return string Created.
     */
    public function getCreated() {
        return $this->_created;
    }
    
    /**
     * Get Updated.
     * @return string Updated.
     */
    public function getUpdated() {
        return $this->_updated;
    }
    
    /**
     * Setters.
     */
    /**
     * Set the id.
     * @param integer $id id.
     * @return  void.
     */
    public function setId($id) {
        $this->_id = $id;
    }
    
    /**
     * Set the name.
     * @param string $name name.
     * @return  void.
     */
    public function setName($name) {
        $this->_name = $name;
    }
    
    /**
     * Set the Description.
     * @param string $description Description.
     * @return  void.
     */
    public function setDescription($description) {
        $this->_description = $description;
    }
    
    /**
     * Set the Version.
     * @param string $version Version.
     * @return  void.
     */
    public function setVersion($version) {
        $this->_version = $version;
    }
    
    /**
     * Set the Active.
     * @param string $active Active.
     * @return  void.
     */
    public function setActive($active) {
        $this->_active = $active;
    }
    
    /**
     * Set the MenuExists.
     * @param string $exists MenuExists.
     * @return  void.
     */
    public function setMenuExists($exists) {
        $this->_menu_exists = $exists;
    }
    
    /**
     * Set the MenuTitle.
     * @param string $title MenuTitle.
     * @return  void.
     */
    public function setMenuTitle($title) {
        $this->_menu_title = $title;
    }
    
    /**
     * Set the MenuIcon.
     * @param string $icon MenuIcon.
     * @return  void.
     */
    public function setMenuIcon($icon) {
        $this->_menu_icon = $icon;
    }
    
    /**
     * Set the Created.
     * @param string $created Created.
     * @return  void.
     */
    public function setCreated($created) {
        $this->_created = $created;
    }
    
    /**
     * Set the Updated.
     * @param string $updated Updated.
     * @return  void.
     */
    public function setUpdated($updated) {
        $this->_updated = $updated;
    }
}
