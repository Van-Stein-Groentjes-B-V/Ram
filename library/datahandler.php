<?php

/**
 * Datahandler class contains general functions
 * to add update of remove information in the database
 *
 * PHP version 7+
 *
 * @category   Library
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Thomas Shamoian <thomas@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 * @uses       SG\Ram\functions         General functions class
 * @uses       SG\Ram\Models\Dbhelper   Class to do the actual database work
 * @uses       SG\Ram\Models\Person     Person class
 * @uses       DateTime                 PHP DateTime class
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

namespace SG\Ram;

use SG\Ram\functions;
use SG\Ram\Models\Dbhelper;
use SG\Ram\Models\Person;
use DateTime;

/**
 * DataHandler
 * @category   Library
 * @package    Ram
 */
class DataHandler
{
    private $_db;
    private $_fun;
    
    /**
     * Constructor with access management.
     * @return  Void.
     */
    public function __construct() {
        $this->_db = new Dbhelper();
        $this->_fun = new functions();
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        unset($this->_functions);
        unset($this->_db); // db helper
    }
    
    /**
     * <ToDo> Wtf doet deze!?
     * Validates data and checks if the data adheres to the necessary type. Also fills arrays with errors if
     * the data does not validate.
     * @param Array  $data           Data to check
     * @param Array  $arrayToCheck   Array or required fields or non required fields
     * @param Array  $upload_columns Array with columns to put the data in
     * @param Array  $values_data    Values to be put into the database
     * @param Array  $errors         Errors that arise in associated array with upload_columns
     * @param String $questionmarks  A questionmark for every value
     * @return String                 Returns the errorstring. De variables that are sent by reference will be updated
     */
    private function checkValidity($data, $arrayToCheck, &$upload_columns, &$values_data, &$errors, &$questionmarks) {
        $errormessage = "";
        foreach ($arrayToCheck as $field => $field_type) {
            if (
                isset($data[$field]) && $this->_fun->checkIfDataIsCorrect($field_type, $data[$field])
            ) {
                $upload_columns[] = "`" . $field . "`";
                if ($field_type == "bool") {
                    $values_data[] = $data[$field] ? 1 : 0;
                } else {
                    $values_data[] = $data[$field];
                }
                $questionmarks .= "?,";
            } else {
                $defaultValue = $field_type === "bool" ? 0 : $this->getDefaultValue($field_type);
                $upload_columns[] = "`" . $field . "`";
                $questionmarks .= "?,";
                $values_data[] = $defaultValue;
                $errormessage .= $field . _(" is not (correctly) filled in. ");
                $errors[$field] = true;
                $data[$field] = "";
            }
        }
        return $errormessage;
    }
    
    /**
     * Get a default nothing value for the possible versions.
     * @param String $field_type What type it is.
     * @return string|int
     */
    private function getDefaultValue($field_type) {
        if ($field_type === "date" || $field_type === "dati") {
            return null;
        } elseif ($field_type == "int") {
            return 0;
        }
        return '';
    }
    
    /**
     *  Handles the information to the db with an key value check.
     *  @param  String  $tablename     Name of table in which data should be saved.
     *  @param  Array   $reqarray      Array with required values, format "name" => "type";.
     *  @param  Array   $optionalarray Array with optional values, format "name" => "type".
     *  @param  String  $data          Information in correct format, "name" => "value".
     *  @param  String  $targetBy      Which should be updated? array or bool,
     *                                 Target which column, format "name" =>
     *                                 "value", False, insert into, instead of
     *                                 update.
     *  @param  Boolean $notdeleted    Whether deleted = 0 must be used (true) or not (false).
     *  @return Boolean|Array          True on completion, or array(errors => (array key=name => true | false), "errormessage" => string)) on failure.
     */
    public function handleInformationToDb($tablename, $reqarray, $optionalarray, $data, $targetBy, $notdeleted = false) {
        $questionmarks = "";
        $values_data = array();
        $errors = [];
        $upload_columns = [];
        $insert = false;
        if (!$targetBy) {
            $insert = true;
        }
        $errormessage = $this->checkValidity($data, $reqarray, $upload_columns, $values_data, $errors, $questionmarks);
        
        if ($errormessage !== "") {
            return array("errors" => $errors, "errormessage" => $errormessage);
        }
        $this->checkValidity($data, $optionalarray, $upload_columns, $values_data, $errors, $questionmarks);
             
        $valuesTrimmed = implode(',', $upload_columns);
  
        $questionmarksTrimmed = trim($questionmarks, ",");
        $resultid = -1;
        if (!$insert) {
            return $this->update($targetBy, $tablename, $notdeleted, $upload_columns, $values_data);
        } else {
            $resultid = $this->insert($tablename, $valuesTrimmed, $questionmarksTrimmed, $values_data);
        }
        return $insert === true && ($resultid != "" && $resultid != -1 && is_numeric($resultid)) ? $resultid : array("errors" => $errors, "errormessage" => _("Saving failed, try again later. errorcode: ") . $resultid);
    }
    
    /**
     *  Updates the information to the db with an key value check.
     *  @param  String  $targetBy       Which should be updated? array or bool,.
     *  @param  String  $tablename      Name of table in which data should be saved.
     *  @param  Boolean $notdeleted     Whether deleted = 0 must be used (true) or not (false).
     *  @param  Array   $upload_columns Target which column, format "name" => "value".
     *  @param  Boolean $values_data    False, insert into, instead of update.
     *  @return Boolean                 True on completion, or array(errors => (array key=name => true | false), "errormessage" => string)) on failure.
     */
    private function update($targetBy, $tablename, $notdeleted, $upload_columns, $values_data) {
        $updatestring = "";
        $first = false;
        foreach ($upload_columns as $value) {
            if ($first) {
                $updatestring .= ",";
            }
            $updatestring .= $value . "=?";
            $first = true;
        }
        $whereString = " WHERE ";
        $firstTarget = false;
        $targetValues = array();
        foreach ($targetBy as $column => $val) {
            if ($firstTarget) {
                $whereString .= " AND ";
            }
            $whereString .= $column . " = ?";
            $targetValues[] = $val;
            $firstTarget = true;
        }
        if (!$firstTarget) {
            return _("no target set");
        }
        if ($notdeleted) {
            $whereString .= " AND deleted = 0";
        }
        if ($updatestring != "") {
            $this->_db->setSql("UPDATE $tablename SET " . $updatestring . $whereString);
            $allVals = array_merge($values_data, $targetValues);
            $result = $this->_db->updateRecord($allVals);
            return !$result ? array("errors" => array(), "errormessage" => _("Update went wrong")) : true;
        }
        return array("errors" => array(), "errormessage" => _("Update string was empty"));
    }
    
    /**
     * Does an insert query.
     * @param   String $tablename     Tablename.
     * @param   String $values        Values.
     * @param   String $questionmarks Question marks.
     * @param   Array  $values_data   An array of data.
     * @return  Boolean | String      String with last inserted id or false.
     */
    private function insert($tablename, $values, $questionmarks, $values_data) {
        $this->_db->setSql("INSERT INTO $tablename (" . $values . ") VALUES (" . $questionmarks . ");");
        return $this->_db->insertRecord($values_data);
    }
    
    /**
     * Get address by id.
     * @param   Integer $address_id Id of address.
     * @return  Array   $result_adres   An array with address data or empty array.
     */
    public function getAdressById($address_id) {
        $this->_db->setSql('SELECT * FROM `address` WHERE `id` = ? AND `deleted` = 0');
        $result_address = $this->_db->getRow(array($address_id));
        return $result_address != null ? $result_address : array();
    }
    
    /**
     * Get address by user Id
     * @param   Integer $user_id user id.
     * @return  Array                   All addresses based on the id.
     */
    public function getAdressByUserId($user_id) {
        $this->_db->setSql("SELECT `address_id` FROM `user_address` WHERE `user_id` = ? AND `deleted` = 0");
        $address_id = $this->_db->getRow(array($user_id));
        if ($address_id) {
            return $this->getAdressById($address_id['address_id']);
        }
        return array();
    }
    
    /**
     * Set deleted on true for target.
     * @param   String $table    Table name.
     * @param   Array  $targetBy Array of target by And the value it must be(example:: array('id' => 1)).
     * @return  Boolean             True on success || (string) error message.
     */
    public function setDeleted($table, $targetBy) {
        $string = "";
        $values = [];
        $first = true;
        foreach ($targetBy as $key => $val) {
            if (!$first) {
                $string .= ' AND';
            }
            $string .= ' `' . $key . '` = ?';
            $values[] = $val;
            $first = false;
        }
        $this->_db->setSql("UPDATE " . $table . " SET `deleted` = 1 WHERE" . $string);
        return $this->_db->updateRecord($values);
    }
    
    /**
     * Function to return the addition to a query if record is deleted or not
     * @param   Boolean $deleted If addition needs to be made
     * @return  String              The addition or an empty string.
     */
    private function getQueryDeleted($deleted = false) {
        return $deleted ? " AND `deleted` = 0" : "";
    }
    
    /**
     * Get data from row.
     * @param   String        $table   Name of the table in which to search.
     * @param   Integer|Array $id      Id from row | (array) key =n ame, value =t he value it must be.
     * @param   Bool          $deleted Whether or not if deleted must be 0.
     * @param   Array         $select  Wanted values, anything else for all.
     * @param   String        $asModel Model name return as this model.
     * @return  Array                  Wanted data.
     */
    public function getDataFromRow($table, $id, $deleted = false, $select = false, $asModel = false) {
        $selectstring = '*';
        if (is_array($select)) {
            $selectstring = implode(',', $select);
        }
        $wherestring = " WHERE ";
        $values = array();
        if (is_array($id)) {
            $first = true;
            foreach ($id as $key => $val) {
                if (!$first) {
                    $wherestring .= " AND ";
                } else {
                    $first = false;
                }
                $wherestring .= $key . ' = ?';
                $values[] = $val;
            }
        } else {
            $wherestring .= "id = ?";
            $values[] = $id;
        }
        $this->_db->setSql("SELECT $selectstring FROM `" . $table . "` " . $wherestring . $this->getQueryDeleted($deleted));
        $row = $this->_db->getRow($values);
        return $asModel === false ? $row : new $asModel($row);
    }
    
    /**
     * Get data from rows.
     * @param   String  $table   Table name.
     * @param   Array   $where   Which columns are requested ('columnname' => value).
     * @param   Boolean $deleted Whether or not if deleted must be 0.
     * @param   Array   $select  Wanted values, anything else for all.
     * @param   String  $asModel Model name return as this model.
     * @return  Array            Wanted data.
     */
    public function getDataFromRows($table, $where, $deleted = false, $select = false, $asModel = false) {
        $selectstring = '*';
        $wherestring = " WHERE ";
        $valuesArray = array();
        if (is_array($select)) {
            $selectstring = implode(',', $select);
        }
        $first = true;
        foreach ($where as $key => $val) {
            if (!$first) {
                $wherestring .= " AND ";
            } else {
                $first = false;
            }
            $wherestring .= $key . ' = ?';
            $valuesArray[] = $val;
        }
        $this->_db->setSql("SELECT $selectstring FROM `" . $table . "` " . $wherestring . $this->getQueryDeleted($deleted));
        $rows = $this->_db->getRows($valuesArray);
        if ($asModel === false) {
            return $rows;
        } else {
            $array = array();
            foreach ($rows as $row) {
                $array[] = new $asModel($row);
            }
            return $array;
        }
    }
    
    /**
     * Get all data from table.
     * @param   String  $table   Table name.
     * @param   Boolean $deleted Whether or not if deleted must be 0.
     * @param   Array   $select  Wanted values, anything else for all.
     * @return  Array            Wanted data.
     */
    public function getAllDataFromTable($table, $deleted = false, $select = false) {
        $selectstring = '*';
        if (is_array($select)) {
            $selectstring = implode(',', $select);
        }
        $this->_db->setSql("SELECT $selectstring FROM `" . $table . "`" . $this->getQueryDeleted($deleted));
        return $this->_db->getRows();
    }
    
    /**
     * Get rows between two dates.
     * @example1 array{"time" => (string)"now", "column"=> (string)"name", "format"=>(string)"Y-m-d"}.
     *
     * @param   String  $table       Table name.
     * @param   Array   $higher      Array build like example 1.
     * @param   Array   $lower       Array build like example 1.
     * @param   Array   $extraTarget Array extra target.
     * @param   Boolean $deleted     Whether or not if deleted must be 0.
     * @param   Array   $select      Wanted values, anything else for all.
     * @return  Array                Wanted data.
     */
    public function getRowsBetween($table, $higher, $lower, $extraTarget = array(), $deleted = false, $select = false) {
        $selectstring = '*';
        if (is_array($select)) {
            $selectstring = implode(',', $select);
        }
        $extraTargetString = "";
        $extraVals = array();
        foreach ($extraTarget as $key => $val) {
            $extraTargetString .= " AND " . $key . " = ?";
            $extraVals[] = $val;
        }
        $higherTime = (new DateTime($higher['time']))->format($higher["format"]);
        $lowerTime = (new DateTime($lower['time']))->format($lower["format"]);
        $this->_db->setSql("SELECT $selectstring FROM `" . $table . "` WHERE `" . $higher['column'] . "` <= ? AND `" . $lower['column'] . "` >= ?" . $extraTargetString . $this->getQueryDeleted($deleted));
        array_unshift($extraVals, $higherTime, $lowerTime);
        return $this->_db->getRows($extraVals);
    }
    
    /**
     * Handler does build the actual query and return either an array of objects, an array of the record or the model
     * @param Boolean $multiple Do we want more lines than 1
     * @param String  $table    In which table do we search
     * @param String  $where    What is the where clause
     * @param Boolean $deleted  Include deleted records
     * @param Boolean $select   Columns on which to select
     * @param String  $asModel  Model name
     * @return \SG\Ram\asModel
     */
    private function getRowHandler($multiple, $table, $where, $deleted = false, $select = false, $asModel = false) {
         $selectstring = '*';
        $wherestring = " WHERE ";
        $valuesArray = array();
        if (is_array($select)) {
            $selectstring = implode(',', $select);
        }
        $first = true;
        foreach ($where as $key => $val) {
            if (!$first) {
                $wherestring .= " AND ";
            } else {
                $first = false;
            }
            $wherestring .= $key . ' LIKE ?';
            $valuesArray[] = $val;
        }
        $this->_db->setSql("SELECT $selectstring FROM `" . $table . "` " . $wherestring . $this->getQueryDeleted($deleted));
        if ($multiple) {
            $rows = $this->_db->getRows($valuesArray);
            if ($asModel === false) {
                return $rows;
            } else {
                $array = array();
                foreach ($rows as $row) {
                    $array[] = new $asModel($row);
                }
                return $array;
            }
        } else {
            $row = $this->_db->getRow($valuesArray);
            return $asModel === false ? $row : new $asModel($row);
        }
    }
    
    /**
     * Get data from row.
     * @param   String  $table   Table name.
     * @param   Array   $where   Which columns 'columnname' => value //DO NOT FORGET THE WILD CARDS.
     * @param   Boolean $deleted Whether or not if deleted must be 0.
     * @param   Array   $select  Wanted values, anything else for all.
     * @param   String  $asModel Model name.
     * @return  Array            Wanted data.
     */
    public function getRowWhereLike($table, $where, $deleted = false, $select = false, $asModel = false) {
        return $this->getRowHandler(false, $table, $where, $deleted = false, $select = false, $asModel = false);
    }
    
    /**
     * Get data from rows.
     * @param   String  $table   Table name.
     * @param   Array   $where   Requested columns 'columnname' => value //DO NOT FORGET THE WILD CARDS.
     * @param   Boolean $deleted Whether or not if deleted must be 0.
     * @param   Array   $select  Wanted values, anything else for all.
     * @param   String  $asModel Model name.
     * @return  Array            Wanted data.
     */
    public function getRowsWhereLike($table, $where, $deleted = false, $select = false, $asModel = false) {
        return $this->getRowHandler(true, $table, $where, $deleted = false, $select = false, $asModel = false);
    }
    
    /**
     * Delete a row from the database.
     * @param   String          $table  The table name.
     * @param   Array | Integer $target If array, key=columnname, val= value the column should be. If int the id of the item that should be deleted.
     * @return  Boolean | Integer               True on success, errorcode on failure.
     */
    public function destroyRow($table, $target) {
        $wherestring = " WHERE ";
        $values = array();
        if (is_array($target)) {
            $first = true;
            foreach ($target as $key => $val) {
                if (!$first) {
                    $wherestring .= " AND ";
                } else {
                    $first = false;
                }
                $wherestring .= $key . ' = ?';
                $values[] = $val;
            }
        } else {
            $wherestring .= "id = ?";
            $values[] = $target;
        }
        $this->_db->setSql("DELETE FROM `" . $table . "` " . $wherestring);
        return $this->_db->updateRecord($values);
    }
    
    /**
     * Get information from all people in the project.
     * @param   Integer $id Project id.
     * @return  Array           Array filled with Person_Model objects.
     */
    public function getPeopleProject($id) {
        $this->_db->setSql("SELECT `person_id` FROM `person_project` WHERE `project_id` = ? AND `deleted` = 0");
        $personids = $this->_db->getRows(array($id));
        $persons = array();
        $this->_db->setSql("SELECT * FROM `persons` WHERE `id` = ? AND `deleted` = 0");
        foreach ($personids as $personsid) {
            $result = $this->_db->getRow(array($personsid['person_id']));
            if ($result) {
                $persons[] = new Person($result);
            }
        }
        return $persons;
    }
    
    /**
     * Get the settings of the user.
     * @param   Integer $id Id off account.
     * @return  Array           Empty array or array with database records.
     */
    public function getSettingsUser($id) {
        $this->_db->setSql("SELECT * FROM `account_settings` WHERE `account_id` = ?");
        $result = $this->_db->getRow(array($id));
        return $result ? $result : array();
    }
    
    /**
     * Check, and if exists, remove the linking user account Person.
     * @param   Integer $id User_account id.
     * @return  Boolean | String    True or error message.
     */
    public function removeCouplingUserPerson($id) {
        $this->_db->setSql("SELECT `id` FROM `persons` WHERE `account_id` = ?");
        $person = $this->_db->getRow(array($id));
        if ($person) {
            $this->_db->setSql("UPDATE `persons` SET `account_id` = 0 WHERE `id` = ?");
            $result = $this->_db->updateRecord(array($person['id']));
            if (!$result) {
                return _('Something went wrong during updating the person.');
            }
        }
        return true;
    }
    
    /**
     * Get the worked time on a project.
     * @param   Integer $projectId The id of the project.
     * @return  Integer            Time worked in hours, rounded down.
     */
    public function getTimeWorkedProject($projectId) {
        $this->_db->setSql("SELECT SUM(`secondsonline`) AS `sum` FROM `user_stats` WHERE `project_id` = ? AND `endtime` <= NOW()");
        $result = $this->_db->getRow(array($projectId));
        return floor((intval($result['sum']) / 3600));
    }
    
    /**
     * Searching for the target if it exist in the db.
     * @param   String  $table  Database table.
     * @param   String  $target Target row.
     * @param   String  $like   Like target.
     * @param   Integer $id     Contains id from the person || project || company etc.
     * @return  Boolean         True if record exists else false
     */
    public function checkForDouble($table, $target, $like, $id = 0) {
        if ($id > 0) {
            $this->_db->setSql("SELECT `" . $target . "` FROM  `" . $table . "` WHERE `" . $target . "` = ? AND `deleted` = 0 AND `id` <> ?");
            $result = $this->_db->getRow(array($like, $id));
        } else {
            $this->_db->setSql("SELECT `" . $target . "` FROM  `" . $table . "` WHERE `" . $target . "` = ? AND `deleted` = 0");
            $result = $this->_db->getRow(array($like));
        }
        return $result != null;
    }
    
    /**
     * Get the userId from the personId
     * @param integer $personId The id of the person of which you want the userid.
     * @return integer The id of the user Or -42;
     */
    public function getUserIdByPersonId($personId) {
        $this->_db->setSql("SELECT `account_id` FROM `persons` WHERE `id` = ?");
        $row = $this->_db->getRow(array($personId));
        if ($row) {
            return $row['account_id'];
        }
        return -42;
    }
    
    /**
     * Check if project exists And if the person is coupled to it.
     * @param Integer            $projectId The project id.
     * @param \SG\Models\account $user      The user trying it.
     * @return boolean
     */
    public function checkIfProjectUserIsAllowed($projectId, $user) {
        if ($user->isSuperAdmin()) {
            return true;
        }
        $this->_db->setSql("SELECT `id` FROM `projects` AS `p` LEFT JOIN `person_project` AS `pp` ON `pp`.`project_id` = `p`.`id` AND `pp`.`deleted` = 0 "
                . "WHERE `p`.`id` = ? AND (`pp`.`person_id` = ? OR `p`.`responsible` = ?) AND `p`.`deleted` = 0");
        $result = $this->_db->getRow(array(filter_var($projectId, FILTER_SANITIZE_NUMBER_INT),$user->getPersonId(),$user->getPersonId()));
        if ($result) {
            return true;
        }
        return false;
    }
    
    /**
     * Save user preferences.
     * @param Array                  $request The request values
     * @param \SG\Ram\Models\account $user    The user model trying to update his preferences.
     * @param \SG\Ram\ErrorMessage   $err     The error message center.
     * @return boolean|String       True on success, errormessage on failure.
     */
    public function saveUserPreference($request, $user, $err) {
        $this->_db->setSql("SELECT `id` FROM `account_settings` WHERE `account_id` = ?");
        $result = $this->_db->getRow(array($user->getId()));
        if ($result) {
            // update
            $this->_db->setSql("UPDATE `account_settings` SET `show_stats` = ?, `play_sounds` = ? WHERE `id` = ?");
            $success = $this->_db->updateRecord(array($request['show_stats'] > 0 ? 1 : 0,$request['play_sound'] > 0 ? 1 : 0,$result['id']));
        } else {
            $this->_db->setSql("INSERT INTO `account_settings` (`show_stats`, `account_id`, `play_sounds`) VALUES (?, ?, ?)");
            $success = is_numeric($this->_db->insertRecord(array($request['show_stats'] > 0 ? 1 : 0,$this->_user->getId(),$request['play_sound'] > 0 ? 1 : 0)));
        }
        if (!$success) {
            return $err->get(\SG\Ram\ErrorMessage::$incorrectValues);
        }
        return true;
    }
}
