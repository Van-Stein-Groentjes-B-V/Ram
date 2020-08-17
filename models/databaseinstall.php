<?php

/**
 * Database install model
 * the arrays are transformed into models
 * the rows will also get handled
 * <TODO>
 *     -add an SQL creator for other types than just mySQL
 * </TODO>
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
 * @uses       \SG\Ram\Models\Columnsql         Use column model.
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
use SG\Ram\Models\Columnsql;

/**
 * DatabaseInstall
 * @category   Models
 * @package    Ram
 */
class Databaseinstall extends Model
{
    private $_name;
    private $_primary;
    private $_charset;
    private $_full_text;
    private $_uniqueKeys;
    private $_foreignKeys;
    private $_indexes;
    private $_engine = "InnoDB";
    private $_columns = array();
    
    /**
     * Constructor.
     * @param string       $name Database name.
     * @param Array | null $data Data to fill the object.
     * @return Void.
     */
    public function __construct($name = null, $data = null) {
        parent::__construct();
        if ($name) {
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
     * @param String $name Database name.
     * @param Array  $data Row of the database to fill the model.
     * @return  Void.
     */
    private function buildObject($name, $data = false) {
        $this->setName($name);
        if ($data) {
            $this->setPrimary($data['primary_key']);
            $this->setCharset($data['charset']);
            $this->setFullText($data['full_text']);
            $this->setUniqueKeys($data['unique_keys']);
            $this->setForeignKeys($data['foreign_keys']);
            $this->setIndexes($data['indexes']);
            if (isset($data['engine'])) {
                $this->setEngine($data['engine']);
            }
            foreach ($data['columns'] as $nameRow => $dataRow) {
                $this->addColumn(new Columnsql($nameRow, $dataRow));
            }
        }
    }
    
    /**
     * Get Name.
     * @return String Name.
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Get Primary.
     * @return Boolean Primary.
     */
    public function getPrimary() {
        return $this->_primary;
    }
    
    /**
     * Get Charset.
     * @return String Charset.
     */
    public function getCharset() {
        return $this->_charset;
    }
    
    /**
     * Get FullText.
     * @return String FullText.
     */
    public function getFullText() {
        return $this->_full_text;
    }
    
    /**
     * Get UniqueKeys.
     * @return String UniqueKeys.
     */
    public function getUniqueKeys() {
        return $this->_uniqueKeys;
    }
    
    /**
     * Get ForeignKeys.
     * @return String ForeignKeys.
     */
    public function getForeignKeys() {
        return $this->_foreignKeys;
    }
    
    /**
     * Get Indexes.
     * @return String Indexes.
     */
    public function getIndexes() {
        return $this->_indexes;
    }
    
    /**
     * Get Engine.
     * @return String Engine.
     */
    public function getEngine() {
        return $this->_engine;
    }
    
    /**
     * Get Columns.
     * @return String Columns.
     */
    public function getColumns() {
        return $this->_columns;
    }
    
    /**
     * Setters.
     */
    /**
     * Set Name.
     * @param   String $name Database name.
     * @return  Void.
     */
    public function setName($name) {
        $this->_name = $name;
    }
    
    /**
     * Set Primary.
     * @param String $string Primary.
     * @return Void.
     */
    public function setPrimary($string) {
        $this->_primary = $string;
    }
    
    /**
     * Set Charset.
     * @param String $charset Charset.
     * @return  Void.
     */
    public function setCharset($charset) {
        $this->_charset = $charset;
    }
    
    /**
     * Set FullText.
     * @param String $text FullText.
     * @return Void.
     */
    public function setFullText($text) {
        $this->_full_text = $text;
    }
    
    /**
     * Set Engine.
     * @param String $engine Engine.
     * @return Void.
     */
    public function setEngine($engine) {
        $this->_engine = $engine;
    }
    
    /**
     * Set UniqueKeys.
     * @param String $keys UniqueKeys.
     * @return  Void.
     */
    public function setUniqueKeys($keys) {
        $this->_uniqueKeys = is_array($keys) ? $keys : array($keys);
    }
    
    /**
     * Set ForeignKeys.
     * @param String $keys ForeignKeys.
     * @return Void.
     */
    public function setForeignKeys($keys) {
        $this->_foreignKeys = is_array($keys) ? $keys : array($keys);
    }
    
    /**
     * Set Indexes.
     * @param String $index Indexes.
     * @return Void.
     */
    public function setIndexes($index) {
        $this->_indexes = is_array($index) ? $index : array($index);
    }
    
    /**
     * Set Columns.
     * @param String $columns Columns.
     * @return  Void.
     */
    public function setColumns($columns) {
        if (is_array($columns) && isset($columns[0]) && is_a($columns[0], 'SG\Ram\Models\Columnsql')) {
            $this->_columns = $columns;
        } elseif (is_a($columns, 'SG\Ram\Models\Columnsql')) {
            $this->_columns = array($columns);
        }
    }
    
    /**
     * Adds columns.
     * @param Columnsql_Model $object Columns model.
     * @return void.
     */
    public function addColumn($object) {
        $this->_columns[] = $object;
    }
    
    /**
     * Create the sql for the table (MySql).
     * @return Boolean | String    If count is smaller than 1 returns false.
     */
    public function creatSql() {
        if (count($this->_columns) < 1 || strlen($this->_primary) < 1) {
            return false;
        }
        $sql = "CREATE TABLE IF NOT EXISTS `" . $this->_name . "` ( ";
        foreach ($this->_columns as $column) {
            $sql .= $column->getSqlForRow() . ' , ';
        }
        $sql .= 'PRIMARY KEY (`' . $this->_primary . '`)';
        $sql .= $this->getUniqueSql(true);
        $sql .= $this->getIndexesSql(true);
        $sql .= $this->getFullTextSql(true);
        $sql .= $this->getForeignSql(true);
        $sql .= ") ENGINE=" . $this->_engine . " DEFAULT CHARSET=" . $this->_charset;
        return $sql;
    }
    
    /**
     * Update sql.
     * @return Boolean | String     If string length is smaller than 1 returns false.
     */
    public function updateSql() {
        if (strlen($this->_name) < 1) {
            return false;
        }
        
        $sql = "ALTER TABLE `" . $this->_name . "`";
        foreach ($this->_columns as $column) {
            $sql .= 'ADD ' . $column->getSqlForRow() . ' , ';
        }
        if ($this->getUniqueSql(true) !== "") {
            $sql .= 'ADD ' . $column->getUniqueSql(true) . ' , ';
        }
        if ($this->getIndexesSql(true) !== "") {
            $sql .= 'ADD ' . $column->getIndexesSql(true) . ' , ';
        }
        if ($this->getFullTextSql(true) !== "") {
            $sql .= 'ADD ' . $column->getFullTextSql(true) . ' , ';
        }
        if ($this->getForeignSql(true) !== "") {
            $sql .= 'ADD ' . $column->getForeignSql(true) . ' , ';
        }
        $sql = trim($sql, ' , ');
        return $sql;
    }
    
    /**
     * Get index sql.
     * @param Boolean $addCommas Do we need to add commas.
     * @return String $sql          SQL with commas.
     */
    public function getIndexesSql($addCommas = false) {
        $sql = "";
        if (count($this->_indexes) > 0) {
            foreach ($this->_indexes as $key) {
                if ($addCommas) {
                    $sql .= ", ";
                }
                $sql .= "INDEX (`" . $key . "`) ";
            }
        }
        return $sql;
    }
    
    /**
     * Get full text sql.
     * @param Boolean $addCommas Do we need to add commas.
     * @return String $sql          Full text with key.
     */
    public function getFullTextSql($addCommas = false) {
        $sql = "";
        if (count($this->_full_text) > 0) {
            foreach ($this->_full_text as $key) {
                if ($addCommas) {
                    $sql .= ", ";
                }
                $sql .= "FULLTEXT (`" . $key . "`) ";
            }
        }
        return $sql;
    }
    
    /**
     * Get the unique key.
     * @param Boolean $addCommas If we need to add commas
     * @return String               Unique SQL with key.
     */
    public function getUniqueSql($addCommas = false) {
        $sql = "";
        if (count($this->_uniqueKeys) > 0) {
            foreach ($this->_uniqueKeys as $key) {
                if ($addCommas) {
                    $sql .= ', ';
                }
                $sql .= 'UNIQUE (`' . $key . '`) ';
            }
        }
        return $sql;
    }
    
    /**
     * Get foreign key.
     * @param Boolean $addCommas If we need to add commas.
     * @return String $sql          FOREIGN KEY with key.
     */
    public function getForeignSql($addCommas = false) {
        $sql = "";
        if (count($this->_foreignKeys) > 0) {
            foreach ($this->_foreignKeys as $key => $ref) {
                if ($addCommas) {
                    $sql .= ', ';
                }
                $sql .= 'FOREIGN KEY (`' . $key . '`) REFERENCES ' . $ref . ' ';
            }
        }
        return $sql;
    }
    
    /**
     * Remove column by given name.
     * @param String $name Column name to remove.
     * @return Boolean      True on success.
     */
    public function removeColumnByName($name) {
        foreach ($this->_columns as $key => $column) {
            if ($column->getName() === $name) {
                unset($this->_columns[$key]);
                return true;
            }
        }
        return false;
    }
}
