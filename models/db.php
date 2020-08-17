<?php

/**
 * Database Model
 * Sets up the database connection from config.
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
 * @uses       PDO                              PHP PDO Library
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
use PDO;

/**
 * DB class
 * @category   Models
 * @package    Ram
 */
class Db extends Model
{
    private static $db;
    
    /**
     * Initialize the database connection
     * @param   Array   $data Connection parameters
     * @param   Boolean $log  Log database
     * @return PDO connection
     */
    public static function init($data = null, $log = false) {
        if ($data) {
            $host = $data['host'];
            $dbname = $data['dbname'];
            $username = $data['username'];
            $password = $data['password'];
            $port = $data['port'];
        } elseif (USE_EXTERNAL_LOG && $log) {
            $host = DB_LOG_HOST;
            $dbname = DB_LOG_NAME;
            $username = DB_LOG_USER;
            $password = DB_LOG_PASSWORD;
            $port = DB_LOG_PORT;
        } else {
            $host = DB_HOST;
            $dbname = DB_NAME;
            $username = DB_USER;
            $password = DB_PASSWORD;
            $port = DB_PORT;
        }
        if (!self::$db) {
            try {
                $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbname . ';charset=utf8';
                self::$db = new PDO($dsn, $username, $password);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                error_log($e->getMessage());
                die("A database error was encountered -> " . $e->getMessage());
            }
        }
        return self::$db;
    }
}
