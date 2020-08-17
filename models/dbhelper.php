<?php

/**
 * Database helper Model
 * This model contains main functions for insert, update and select queries
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
 * @uses       DateTime                         PHP DateTime class
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

use DateTime;
use SG\Ram\Model;

/**
 * Dbhelper
 * @category   Models
 * @package    Ram
 */
class Dbhelper extends Model
{
    protected $_db;
    private $_sql;
    
    /**
     * Construct the db connection.
     * @param   String  $data Possible specific data to connect to a specific database.
     * @param   Boolean $log  Log database or other
     * @return  Void.
     */
    public function __construct($data = null, $log = false) {
        parent::__construct();
        $this->_db = Db::init($data, $log);
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
    }
    
    /**
     * Set the sql query.
     * @param String $sql query.
     * @return Void.
     */
    public function setSql($sql) {
        $this->_sql = $sql;
    }

    /**
     * Get row.
     * @param Array | null $data Data to fill in, in the query. The PDO will replace all the
     * @return Boolean | Array      The requested row or null
     * @throws Exception.           If no query is set.
     */
    public function getRow($data = null) {
        if (!$this->_sql) {
            throw new Exception("No SQL query!");
        }
        $sth = $this->_db->prepare($this->_sql);
        $sth->execute($data);
        $row = $sth->fetch();
        return $row != false ? $row : null;
    }

    /**
     * Get rows.
     * @param Array | null $data Data to fill in, in the query. The PDO will replace all the
     * @return Array                Rows of the result or an empty array.
     * @throws Exception.           If no Sql is set
     */
    public function getRows($data = null) {
        if (!$this->_sql) {
            throw new Exception("No SQL query!");
        }
        $sth = $this->_db->prepare($this->_sql);
        $sth->execute($data);
        $result = array();
        while ($regel = $sth->fetch()) {
            $result[] = $regel;
        }
        return $result;
    }

    /**
     * Update the record.
     * @param Array | null $data Data to fill in, in the query. The PDO will replace all the
     * @return Boolean              True on success else false
     * @throws Exception.           If no Sql is set
     */
    public function updateRecord($data = null) {
        if (!$this->_sql) {
            throw new Exception("No SQL query!");
        }
        $sth = $this->_db->prepare($this->_sql);
        return $sth->execute($data);
    }

    /**
     * Insert record.
     * @param Array | null $data Data to fill in, in the query. The PDO will replace all the
     * @return String | Boolean     Index of inserted row or false if insert fails
     * @throws Exception.           If no Sql is set
     */
    public function insertRecord($data = null) {
        if (!$this->_sql) {
            throw new Exception("No SQL query!");
        }
        $sth = $this->_db->prepare($this->_sql);
        return $sth->execute($data) ? $this->_db->lastInsertId() : false;
    }
}
