<?php

/**
 * Database install Controller.
 * Installs the database necessary for the system
 * <TODO>
 * Fix it so it can be used for multiple types of db
 * </TODO>
 *
 * PHP version 7+
 *
 * @category   Controllers
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 * @uses       \SG\Ram\Controller               Extend the main controller.
 * @uses       \SG\Ram\functions                General functions class.
 * @uses       \SG\Ram\Models\Dbhelper          Database helper class.
 * @uses       \SG\Ram\Models\Databaseinstall   Database install instructions
 * @uses       \SG\Ram\Models\Columnsql         Columns for the database.
 * @uses       \SG\Ram\DatabaseSqlArrays        Arrays that define database.
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

namespace SG\Ram\Controllers;

use SG\Ram\Controller;
use SG\Ram\Models\Dbhelper;
use SG\Ram\Models\Databaseinstall;
use SG\Ram\Models\Columnsql;
use SG\Ram\DataBaseSqlArrays;

/**
 * DatabaseinstallController
 * @category   Controllers
 * @package    Ram
 */
class DatabaseinstallController extends Controller
{
    private $_db;
    private $_tablename;
    
    /**
     * Installs the database.
     * @param Array $data could contain an array with data.
     * @return void.
     */
    public function __construct($data = null) {
        parent::__construct();
        if ($data) {
            $this->_db = new Dbhelper($data);
            $this->_tablename = $data['dbname'];
        } else {
            $this->_db = new Dbhelper();
            $this->_tablename = DB_NAME;
        }
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
        unset($this->_tablename);
    }
    
    /**
     * create the tables or if already exists update it.
     * @param Array  $tables an array of table sorts.
     * @param String $user   name of the user.
     * @param String $host   name of the host.
     * @return Array $errors returns an array.
     */
    public function createOrEditTables($tables, $user, $host) {
        $errors = array();
        foreach ($tables as $name => $data) {
            $tableSql = new Databaseinstall($name, $data);
            if ($this->tableDoesNotExist($name)) {
                $this->_db->setSql($tableSql->creatSql());
                $result = $this->_db->updateRecord();
                if ($result !== true) {
                    $errors[$name] = $result;
                }
            } else {
                $result = $this->checkDbPerColumn($name, $data, $user, $host);
                if (!empty($result)) {
                    $errors = array_merge($errors, $result);
                }
            }
        }
        return $errors;
    }
    /**
     * insert the bare data that should be filled in.
     * If `$result` has an value higher than 8 the function.
     * returns and nothing is inserted into the table.
     * @param Array $toBeFilledData The array with values to be added.
     * @return boolean true || false.
     */
    public function fillTablesWithBareInfo($toBeFilledData) {
        $this->_db->setSql("SELECT COUNT(`id`) as `row_count` from `project_status`;");
        $result = $this->_db->getRow();
        $amount = implode($result);
        if ($amount <= 0) {
            foreach ($toBeFilledData as $tablename => $values) {
                foreach ($values as $val) {
                    $this->_db->setSql($this->createInsertSql($tablename, $val));
                    $this->_db->insertRecord();
                }
            }
        }
        return true;
    }
    
    /**
     * function add or extend existing tables.
     * mainly meant for modules.
     * requires $array().
     * @param Array $tables an array of table sorts.
     * @return Array $errors returns an array.
     */
    public function extendOrCreateTables($tables) {
        $errors = array();
        foreach ($tables as $name => $data) {
            if (isset($data['primary_key'])) {
                $this->createDatabaseTable($name, $data, $errors);
            } else {
                $this->updateDatabaseTable($name, $data, $errors);
            }
        }
        return $errors;
    }
    
    /**
     * Walk through the columns and add it if not exist.
     * @param String $name   The name of the table.
     * @param Array  $data   The information according to specific format
     * @param Array  $errors The array with the errors.
     * @return void.
     */
    private function updateDatabaseTable($name, $data, $errors) {
        $databaseInstall = new Databaseinstall($name);
        foreach ($data['columns'] as $nameRow => $dataRow) {
            $databaseInstall->addColumn(new Columnsql($nameRow, $dataRow));
        }
        if (!$this->tableDoesNotExist($name)) {
            if (!$this->checkColumnsAlreadyExists($databaseInstall)) {
                $this->_db->setSql($databaseInstall->updateSql());
                $result = $this->_db->updateRecord();
                if ($result !== true) {
                    $errors[$name] = $result;
                }
            }
        } else {
            $errors[] = _("Couldn\'t find the table to update.");
        }
    }
    
    /**
     * Create the table if not exist.
     * @param String $name   The name of the table.
     * @param Array  $data   The information according to specific format
     * @param Array  $errors The array with the errors.
     * @return void.
     */
    private function createDatabaseTable($name, $data, &$errors) {
        $databaseInstall = new Databaseinstall($name, $data);
        if ($this->tableDoesNotExist($name)) {
            $this->_db->setSql($databaseInstall->creatSql());
            $result = $this->_db->updateRecord();
            if ($result !== true) {
                $errors[$name] = $result;
            }
        } else {
            $result = $this->checkDbPerColumn($name, $data);
            if (!empty($result)) {
                $errors = array_merge($errors, $result);
            }
        }
    }
    
    /**
     * This functions checks if admin already exists.
     * @return array $row, if fails it will return null.
     */
    public function checkIfAdminAlreadyExists() {
        $this->_db->setSql("SELECT `id` FROM `user_accounts` WHERE `admin` = 3");
        return $this->_db->getRow(null);
    }
    
    /**
     * This functions checks if tables already exists.
     * @param Array $dbInstallModel contains an array with columns.
     * @return Array $dbInstallModel returns an array with columns names.
     */
    private function checkColumnsAlreadyExists($dbInstallModel) {
        foreach ($dbInstallModel->getColumns() as $column) {
            $this->_db->setSql("SHOW COLUMNS FROM `" . $dbInstallModel->getName() . "` LIKE '" . $column->getName() . "'");
            $result = $this->_db->getRow();
            if ($result) {
                $dbInstallModel->removeColumnByName($column->getName());
            }
        }
        return $dbInstallModel;
    }
    
    /**
     * This functions creates an insert query.
     * @param String $tablename contains an tablename.
     * @param Array  $array     contains an array of values.
     * @return String $sql returns an string.
     */
    private function createInsertSql($tablename, $array) {
        $sql = "INSERT INTO " . $tablename . " ( ";
        $values = "(";
        $first = true;
        foreach ($array as $key => $val) {
            if (!$first) {
                $sql .= ',';
                $values .= ',';
            } else {
                $first = false;
            }
            $sql .= $key;
            $values .= '"' . $val . '"';
        }
        return $sql . ") VALUES " . $values . ")";
    }
    
    /**
     * Checks if a table doesn't exists.
     * @param String $name contains a name.
     * @return array $row returns an row if query is true.
     */
    private function tableDoesNotExist($name) {
        $this->_db->setSql("SELECT * FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1;");
        return is_null($this->_db->getRow(array($this->_tablename, $name)));
    }
    
    /**
     * check if each column is set, and correctly set.
     * @param String $table contains an tablename.
     * @param Array  $data  contains an array of data.
     * @param String $user  name of the user.
     * @param String $host  name of the host.
     * @return Array $errors returns an array of faults.
     */
    private function checkDbPerColumn($table, $data, $user = "", $host = "") {
        $this->_db->setSql("SHOW COLUMNS FROM `" . $this->_tablename . "`.`" . $table . "`");
        $columns = $this->_db->getRows();
        $errors = array();
        $result = true;
        //first check if all columns are created
        foreach ($data['columns'] as $name => $information) {
            $column = new Columnsql($name, $information);
            $existing = $this->_fun->checkIfAlreadyExists($columns, $name, 'Field');
            if ($existing !== false) {
                $resultarray = $this->checkAllVars($existing, $column);
                if (!empty($resultarray['needupdate'])) {
                    $result = $this->updateColumn($table, $column, $user, $host);
                }
            } else {
                $this->_db->setSql("ALTER TABLE `" . $table . "` ADD " . $column->getSqlForRow());
                $result = $this->_db->updateRecord();
            }
            if ($result !== true) {
                $errors[$name] = $result;
            }
        }
        $this->checkAllElseTableAndUpdate($data, $table);
        return $errors;
    }
    
    /**
     * check if the vars are the same as the sql.
     * @param Array $existing contains an array.
     * @param Array $column   contains an array.
     * @return Array Array() returns an  array with some information.
     */
    private function checkAllVars($existing, $column) {
        $what = array();
        $keys = array();
        if (isset($existing['Type'])) {
            $type = explode("(", str_replace(')', '', $existing['Type']));
            if ($type[0] != $column->getType()) {
                $what[] = 'type';
            }
            if ((isset($type[1]) && $type[1] != $column->getLength()) || !isset($type[1]) && $column->getLength() !== false) {
                $what[] = 'length';
            }
        }
        if (isset($existing['Null']) && (($existing['Null'] === 'NO' && $column->getAllowNull()) || ($existing['Null'] !== 'NO' && !$column->getAllowNull()))) {
            $what[] = 'AllowNull';
        }
        if (isset($existing['Key']) && $existing['Key'] !== "") {
            $keys[] = array("column_name" => $column->getName(), "key_type" => $existing['Key']);
        }
        $default = $column->getDefault();
        if (
            isset($existing['Default']) && ((is_array($default) && $existing['Default'] != $default['value']) ||
                    ($existing['Default'] === 'CURRENT_TIMESTAMP' && $default !== "timestamp") ||
                    ($existing['Default'] !== 'CURRENT_TIMESTAMP' && $default === "timestamp") ||
                    ($existing['Default'] === null && $default !== false)   ||
                    ($existing['Default'] !== null && $default === false))
        ) {
            $what[] = 'default';
        }
        return array('needupdate' => $what, 'keys' => $keys);
    }
    
    /**
     * Updates column by param.
     * @param String $table  contains tablename.
     * @param String $column contains column name.
     * @param String $user   name of the user.
     * @param String $host   name of the host.
     * @return boolean true || false.
     */
    private function updateColumn($table, $column, $user, $host) {
        //check if user has rights to perform this action.
        $this->_db->setSql("SELECT * FROM mysql.user WHERE `USER` = ? AND `HOST` = ? AND `Alter_priv` = 'Y'");
        $permission = $this->_db->getRow([$user, $host]);
        if ($permission["Alter_priv"] === "Y") {
            $this->_db->setSql("ALTER TABLE `" . $table . "` CHANGE `" . $column->getName() . "` " . $column->getSqlForRow());
            return $this->_db->updateRecord();
        } else {
            $this->assign("errormessage", $this->_err->get(\SG\Ram\ErrorMessage::$noPermissionToDelete));
        }
    }
    
    /**
     * Checks the myssql style then it will try it with different styles.
     * @param Array  $data  contains an array with data.
     * @param String $table contains tablename.
     * @return boolean true || false.
     */
    private function checkAllElseTableAndUpdate($data, $table) {
        $databaseinstall = new Databaseinstall($table, $data);
        $sql = $this->checkIfIssetElseAdd($databaseinstall, $table);
        if (strlen($sql) > 5) {
            $this->_db->setSql("ALTER TABLE `" . $table . "` ADD " . $sql);
            return $this->_db->updateRecord();
        }
        return true;
    }
    
    /**
     * check if keys are already set.
     * doesnt check for fulltext or foreignKey.
     * @param \Databaseinstall_Model $databaseinstall calls the array.
     * @param string                 $table           table name.
     * @return string $sql string part of sql statement.
     */
    private function checkIfIssetElseAdd($databaseinstall, $table) {
        $sql = "";
        //check if unique is set + indexes
        $this->_db->setSql("SHOW INDEXES FROM `" . $this->_tablename . "`.`" . $table . "`");
        $columns = $this->_db->getRows();
        if (!empty($databaseinstall->getUniqueKeys())) {
            foreach ($databaseinstall->getUniqueKeys() as $singleU) {
                $existing = $this->_fun->checkIfAlreadyExists($columns, $singleU, 'Column_name');
                if ($existing === false || ($existing['Non_unique'] !== "0" && strpos($existing['Key_name'], $singleU) !== false)) {
                    $sql .= $databaseinstall->getUniqueSql(true);
                }
            }
        }
        if (!empty($databaseinstall->getIndexes())) {
            foreach ($databaseinstall->getIndexes() as $singleI) {
                $existing = $this->_fun->checkIfAlreadyExists($columns, $singleI, 'Column_name');
                if ($existing === false || ($existing['Non_unique'] !== "1" && strpos($existing['Key_name'], $singleI) !== false)) {
                    $sql .= $databaseinstall->getIndexesSql(true);
                }
            }
        }
        $sql .= $databaseinstall->getFullTextSql(true) . $databaseinstall->getForeignSql(true);
        return ltrim($sql, ',');
    }
    
    /**
     * get the database array.
     * @return Array $databaseArray returns database Array.
     */
    public function fillWithData() {
        require_once(ROOT . DS . "config" . DS . "databaseconfig.php");
        $databaseArray = new DataBaseSqlArrays();
        return array("db" => $databaseArray->getStandardDb(),"data" => $databaseArray->dataProjectStati());
    }
}
