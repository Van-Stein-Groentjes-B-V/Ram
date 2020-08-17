<?php

/**
 * Column SQL Model
 * Contains information of the Columnsql.
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
 * Columnsql
 * @category   Models
 * @package    Ram
 */
class Columnsql extends Model
{
    private $_name;
    private $_type;
    private $_length = false;
    private $_default = false;
    private $_allowNull = false;
    private $_collation = false;
    private $_increment = false;
    private $_attributes = false;
    private $_unique = false;
    private $_comments = "";
    //values not set by buildobject
    private $_primary = false;
    
    /**
     * constructor.
     * @param String       $name name.
     * @param Array | Null $data to fill the object.
     * @return Void.
     */
    public function __construct($name = null, $data = null) {
        parent::__construct();
        if ($data && $name) {
            $this->buildObject($name, $data);
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
     * @param String $name Name to set.
     * @param Array  $data Row of the database to fill the model.
     * @return  Void.
     */
    private function buildObject($name, $data) {
        $this->setName($name);
        foreach ($data as $key => $val) {
            $this->setVar($key, $val);
        }
    }
    
    /**
     * SetVar function allows for setting a variable by
     * calling it by its name.
     * @param   String $key Variable to set.
     * @param   Mixed  $val Value to set.
     * @return  Void.
     */
    private function setVar($key, $val) {
        switch ($key) {
            case 'type':
                $this->setType($val);
                break;
            case 'length':
                $this->setLength($val);
                break;
            case 'default':
                $this->setDefault($val);
                break;
            case 'ALLOW_NULL':
                $this->setAllowNull($val);
                break;
            case 'colation':
                $this->setCollation($val);
                break;
            case 'increment':
                $this->setIncrement($val);
                break;
            case 'attributes':
                $this->setAttributes($val);
                break;
            case 'comments':
                $this->setComments($val);
                break;
            case 'unique':
                $this->setUnique($val);
                break;
            default:
                break;
        }
    }
    
    /**
     * Set the Name.
     * @param String $name Name.
     * @return  Void.
     */
    public function setName($name) {
        $this->_name = $name;
    }

    /**
     * there are no check, as we assume all values will uphold the mandated naming convention
     * </Todo Add checks!!!>
     */
    
    /**
     * Set the Type.
     * @param String $type Type.
     * @return  Void.
     * </Todo> Add legend for supported values
     */
    public function setType($type) {
        $this->_type = $type;
    }
    
    /**
     * Set the Length.
     * @param Integer $length Set max length of value in a column
     * @return Void.
     */
    public function setLength($length) {
        $this->_length = $length;
    }
    
    /**
     * Set the Default.
     * @param Mixed $default Set the default value of a column
     * @return Void.
     */
    public function setDefault($default) {
        $this->_default = $default;
    }
    
    /**
     * Set the AllowNull.
     * @param Boolean $allow Set value NULL is allowed
     * @return Void.
     */
    public function setAllowNull($allow) {
        $this->_allowNull = $allow;
    }
    
    /**
     * Set the Collation.
     * @param String $collation What collation should be used.
     * @return Void.
     */
    public function setCollation($collation) {
        $this->_collation = $collation;
    }
    
    /**
     * Set the increment.
     * @param Boolean $increment Set column to auto increment
     * @return Void.
     */
    public function setIncrement($increment) {
        $this->_increment = $increment;
    }
    
    /**
     * Set the Attributes.
     * @param String $attributes Set column attributes.
     * @return  Void.
     * </Todo> Provide a legend of supported attributes
     */
    public function setAttributes($attributes) {
        $this->_attributes = $attributes;
    }
    
    /**
     * Set the Comments.
     * @param String $comments Add comments to this column.
     * @return Void.
     */
    public function setComments($comments) {
        $this->_comments = $comments;
    }
    
    /**
     * Set the Unique.
     * @param Boolean $unique Is this column unique
     * @return Void.
     * </todo> Do we support unique on combination columns?
     */
    public function setUnique($unique) {
        $this->_unique = $unique;
    }
    
    /**
     * Set primary..
     * @param Boolean $bool Is this a primary column
     * @return Void.
     * </todo> Do we support primary on combination columns?
     */
    public function setPrimary($bool) {
        $this->_primary = $bool;
    }
    
    /*
     * getters
     */
    /**
     * Get Name.
     * @return String Name.
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Get Type.
     * @return String Type.
     */
    public function getType() {
        return $this->_type;
    }
    
    /**
     * Get Length.
     * @return Integer Length.
     */
    public function getLength() {
        return $this->_length;
    }
    
    /**
     * Get Default.
     * @return Mixed    Default value for the column
     */
    public function getDefault() {
        return $this->_default;
    }
    
    /**
     * Get AllowNull.
     * @return Boolean AllowNull.
     */
    public function getAllowNull() {
        return $this->_allowNull;
    }
    
    /**
     * Get Collation.
     * @return String Collation.
     */
    public function getCollation() {
        return $this->_collation;
    }
    
    /**
     * Get Increment.
     * @return Boolean Increment.
     */
    public function getIncrement() {
        return $this->_increment;
    }
    
    /**
     * Get Attributes.
     * @return String Attributes.
     */
    public function getAttributes() {
        return $this->_attributes;
    }
    
    /**
     * Get Unique.
     * @return Boolean Unique.
     */
    public function getUnique() {
        return $this->_unique;
    }
    
    /**
     * Get Comments.
     * @return String Comments.
     */
    public function getComments() {
        return $this->_comments;
    }
    
    /**
     * Get Primary.
     * @return Boolean Primary.
     */
    public function getPrimary() {
        return $this->_primary;
    }
    
    /**
     * Get AttributesSqlName.
     * @return String AttributesSqlName.
     */
    public function getAttributesSqlName() {
        switch ($this->_attributes) {
            case "BIN":
                return "BINARY";
            case "UNS":
                return "UNSIGNED";
            case "UNS_ZER":
                return "UNSIGNED ZEROFILL";
            case "TIME":
                return "on update CURRENT_TIMESTAMP";
            default:
                return "";
        }
    }
    
    /**
     * Get CollationSql.
     * @return String CollationSql.
     */
    public function getCollationSql() {
        return "SET " . explode("_", $this->_collation)[0] . ' COLLATE ' . $this->_collation;
    }
    
    /**
     * Get DefaultSql.
     * @return String DefaultSql.
     */
    public function getDefaultSql() {
        if (is_array($this->_default)) {
            return "DEFAULT '" . $this->_default['value'] . "'";
        }
        if ($this->_default === "timestamp") {
            return "DEFAULT CURRENT_TIMESTAMP";
        }
        if ($this->_default === "NULL" && $this->_allowNull) {
            return "DEFAULT NULL";
        }
        return "";
    }

    /**
     * Get SqlForRow.
     * @return String SqlForRow.
     */
    public function getSqlForRow() {
        $sql = "`" . $this->_name . "` " . $this->_type;
        if ($this->_length !== false) {
            $sql .= "(" . $this->_length . ")";
        }
        if ($this->_attributes && ($this->_attributes === "UNS" || $this->_attributes === "UNS_ZER")) {
            $sql .= " " . $this->getAttributesSqlName();
        }
        if (!$this->_allowNull) {
            $sql .= " NOT NULL";
        }
        if ($this->_increment) {
            $sql .= " AUTO_INCREMENT";
        }
        if ($this->_attributes && $this->_attributes !== "UNS" && $this->_attributes !== "UNS_ZER") {
            $sql .= " " . $this->getAttributesSqlName();
        }
        if ($this->_collation) {
            $sql .= " " . $this->getCollationSql();
        }
        if ($this->_default) {
            $sql .= " " . $this->getDefaultSql();
        }
        if (strlen($this->_comments) > 0) {
            $sql .= " COMMENT '" . $this->_comments . "'";
        }
        return $sql;
    }
}
