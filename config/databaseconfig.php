<?php
/**
 * Database configuration file. This class defines the database with its 
 * tables, columns etc.
 *
 * PHP version 7+
 *
 * @category   Defines
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Thomas Shamoian <thomas@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
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

/**
 * Class to hold the database config, and base setup of the database.
 */
class DataBaseSqlArrays
{
    public function __construct() {
    }
    
    public function __destruct() {
    }
    
    /*
     * Example function of database setup. 
     * If new tables are needed and you want them to be created by the 
     * system make sure they follow this stucture 
     * @return Array.
     */
    public function standardFormat() {
        return array(
            "user_accounts" => [ //table name
                "primary_key"   =>  "id", //primary key
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(), //val => string of column
                "indexes"       =>  array(), //val => string of column
                "full_text"     =>  array(), //val => string of column
                "engine"        =>  "InnoDB", //standard === InnoDb, only fill in if other engine
                "columns"       =>  array(
                    "id"            =>  array(  //column name === key
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  false, //false on none, string "NULL" voor null, string "timestamp" voor CURRENTTIMESTAMP, array("default" = true, "value" => "value") voor default
                        "ALLOW_NULL"    =>  false,
                        "colation"      =>  false, //if false standard, else string with ALLOWED COLLATION
                        "increment"     =>  true,  //increment true or false
                        "attributes"    =>  false, //allows false or 4 strings:: "BIN", "UNS", "UNS_ZER" and "TIME"
                        "comments"      =>  ""
                        //"virtuality"    =>  false //false or 2 strings allowed :: "VIRTUAL" or "STORED"
                    )
                )
            ]
        );
    }
    
    /**
     * Default / initial database structure
     * @return  Array   Array with tablenames, columns, types, etc.
     */
    public function getStandardDb() {
        return array(
            "account_devices" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "device_id"     =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256
                    ),
                    "account_id"     =>  array(
                        "type"          =>  "int",
                        "length"        =>  11
                    ),
                    "device_name"   =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128
                    ),
                    "hash"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128
                    ),
                    "accepted"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),  //increment true or false
                    ),
                    "registered"    =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",  //increment true or false
                        "attributes"    =>  "TIME"
                    )
                )
            ],
            "account_settings"  =>  [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array("account_id"),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "account_id"     =>  array(
                        "type"          =>  "int",
                        "length"        =>  11
                    ),
                    "show_stats"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 1),
                        "comments"       =>  "Show online statistics in dashboard view (might need admin)"
                    ),
                    "play_sounds"    =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 1),
                        "comments"       =>  "Play sounds"
                    )
                )
            ],
            "attachments"  =>  [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "project_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "type"          =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "location"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "message"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                    ),
                    "filename"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "realname"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "deleted"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "person_id"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    )
                )
            ],
            "autoinvoice"  =>  [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "date"          =>  array(
                        "type"          =>  "date",
                    ),
                    "price"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                    ),
                    "person_id"   =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "owner_id"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "message"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "subject"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                    ),
                    "owner_company" =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "person_company" =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "frequency"     =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                        "comments"      =>  "freq: 0 is yearly, 1 is monthly"
                    ),
                )
            ],
            "companies"  =>  [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "name"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  64,
                    ),
                    "logo"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "city"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "country"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  64,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "street"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "number"       => array(
                        "type"          =>  "varchar",
                        "length"        =>  6,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "vat_nr"        =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "iban"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "main_contact"  =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "owned"         =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 0),
                        "comments"      =>  "1 = own company, 0 = not",
                    ),
                    "kvk"           =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  64,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "postalcode"    =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  10,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "tel"           =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "website"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "facebook"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "twitter"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "youtube"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "linkedin"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "deleted"       =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                )
            ],
            "persons"  =>  [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "name"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "company"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL"
                    ),
                    "street"        =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "number"        => array(
                        "type"          =>  "varchar",
                        "length"        =>  6,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "postalcode"    =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  64,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "email"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "tel"           =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "company_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "facebook"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "linkedin"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "twitter"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "logo"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "city"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "country"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "website"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "youtube"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "notes"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "account_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0)
                    ),
                    "person_pass" =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "title"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "deleted"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                )
            ],
            "person_project" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "person_id"     =>  array(
                        "type"          =>  "int",
                        "length"        =>  11
                    ),
                    "project_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11
                    ),
                    "deleted"       =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                )
            ],
            "knowledgebase" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "title"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "desc"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                    ),
                    "article"       =>  array(
                        "type"          =>  "longtext",
                    ),
                    "ordering"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                )
            ],
            "log" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "description"   =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  512,
                    ),
                    "username"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "ip"            =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "website"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                    ),
                    "level"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "logtime"       =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                        "attributes"    =>  "TIME"
                    ),
                )
            ],
            "modules" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "name"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  255,
                    ),
                    "description"   =>  array(
                        "type"          =>  "text",
                    ),
                    "version"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  127,
                    ),
                    "active"        =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 0)
                    ),
                    "person_id"     =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "comments"      =>  "Person who uploaded the module.",
                    ),
                    "created_at"    =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                    ),
                    "updated_at"    =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                        "attributes"    =>  "TIME",
                    ),
                )
            ],
            "modules_settings" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "module_id"     =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "title"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  255,
                    ),
                    "isInt"         =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL"
                    ),
                    "value"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  255,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL"
                    ),
                )
            ],
            "notification" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "user_id"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "message"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "project_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "date"          =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                        "attributes"    =>  "TIME",
                    ),
                    "status"        =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 0),
                        "comments"      =>  "o=unread, 1=read",
                    ),
                    "type"          =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 0),
                        "comments"      =>  "0=todos, 1=chat",
                    ),
                )
            ],
            "offer" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array('project_id'),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "name"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                    ),
                    "created"       =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                    ),
                    "accepted"      =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  2,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "project_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                )
            ],
            "offer_section" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "offer_id"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "title"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  64,
                    ),
                    "content"       =>  array(
                        "type"          =>  "mediumblob",
                    ),
                    "price"         =>  array(
                        "type"          =>  "double",
                    ),
                    "optional"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "position"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                )
            ],
            "offer_section_templates" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "title"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  64,
                    ),
                    "content"       =>  array(
                        "type"          =>  "mediumblob",
                    ),
                    "price"         =>  array(
                        "type"          =>  "double",
                    ),
                    "optional"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "position"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                )
            ],
            "own_companies" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "attributes"    =>  "UNS",
                        "increment"     =>  true  //increment true or false
                    ),
                    "name"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  50,
                    ),
                )
            ],
            "projects" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "name"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "dev_link"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "image"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL",
                    ),
                    "description"   =>  array(
                        "type"          =>  "mediumtext",
                        "ALLOW_NULL"    =>  true,
                    ),
                    "project_status"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 1),
                    ),
                    "last_changed"  =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                        "attributes"    =>  "TIME",
                    ),
                    "created"       =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp"
                    ),
                    "person_main_contact_id"  =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0)
                    ),
                    "android_link"  =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL",
                    ),
                    "ios_link"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL",
                    ),
                    "web_link"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL",
                    ),
                    "invoice_created"   =>  array(
                        "type"          =>  "date",
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL",
                    ),
                    "budget"        =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  12,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "hourly_wage"        =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  12,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "SO_hourly"        =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  12,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "SO_description"    =>  array(
                        "type"          =>  "mediumtext",
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL"
                    ),
                    "deadline"      =>  array(
                        "type"          =>  "date",
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL",
                    ),
                    "company_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "contractor_id" =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "intermediate_id"   =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "contractor_main_contact_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "repository"    =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "responsible"   =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "slug"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "deleted"       =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                )
            ],
            "project_status" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "attributes"    =>  "UNS",
                        "increment"     =>  true  //increment true or false
                    ),
                    "titel"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  50
                    ),
                    "type"         =>  array(
                        "type"          =>  "int",
                        "length"        =>  50
                    ),
                    "extensie"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  50,
                        "default"       =>  array("default" => true, "value" => "")
                    ),
                )
            ],
            "tickets" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "project_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "send"          =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                        "attributes"    =>  "TIME",
                    ),
                    "from_id"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "from_email"    =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "status"        =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 0),
                        "comments"      =>  "0=unread, 1=read",
                    ),
                    "message"       =>  array(
                        "type"          =>  "mediumblob",
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL"
                    ),
                    "deleted"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "subject"    =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                    ),
                )
            ],
            "todos" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "project_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "prio"          =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "deadline"      =>  array(
                        "type"          =>  "date",
                        "default"       =>  "NULL",
                        "ALLOW_NULL"    =>  true,
                    ),
                    "message"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  256,
                    ),
                    "done"          =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  2,
                        "default"       =>  array("default" => true, "value" => 1),
                    ),
                    "user_id"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "created"       =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp"
                    ),
                    "lastchange"    =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                        "attributes"    =>  "TIME",
                    ),
                    "set_progress"  =>  array(
                        "type"          =>  "datetime",
                        "default"       =>  "NULL",
                        "ALLOW_NULL"    =>  true,
                    ),
                    "hours"         =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "long_desc"     =>  array(
                        "type"          =>  "mediumtext",
                        "default"       =>  "NULL",
                        "ALLOW_NULL"    =>  true,
                    ),
                )
            ],
            "todo_column" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true  //increment true or false
                    ),
                    "titel"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  20,
                        "default"       =>  array("default" => true, "value" => ''),
                    ),
                    "kleur"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  16,
                        "default"       =>  array("default" => true, "value" => 'panel-info'),
                    ),
                    "type"          =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  'NULL',
                    ),
                )
            ],
            "user_accounts" => [
                "primary_key"   =>  "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array("email"),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       =>  array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true,  //increment true or false
                    ),
                    "username"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "email"         =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "fullname"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "street"        =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "number"  =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  6,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "postcode"      =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  8,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "country"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  64,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "company"       =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "sessionid"     =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  64,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "ps"            =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "rand"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    ),
                    "admin"         =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "active"        =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "joined"        =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                    ),
                    "person_id"      =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "default"       =>  array("default" => true, "value" => -1),
                    ),
                    "hash"          =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  30,
                        "default"       =>  array("default" => true, "value" => ""),
                    )
                )
            ],
            "user_session" => [
                "primary_key"   => "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       => array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true,  //increment true or false
                    ),
                    "user_id"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "sessioncode"   =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                    ),
                    "created"       =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                        "attributes"    =>  "TIME",
                    ),
                    "ip"            =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  128,
                        "default"       =>  array("default" => true, "value" => ""),
                    )
                )
            ],
            "user_settings" => [
                "primary_key"   => "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       => array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true,  //increment true or false
                    ),
                    "user_id"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL",
                    ),
                    "setting"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "ALLOW_NULL"    =>  true,
                        "default"       =>  "NULL",
                    ),
                )
            ],
            "user_stats" => [
                "primary_key"   => "id",
                "charset"       =>  "utf8",
                "foreign_keys"  =>  array(), //key == column, val==reference format of string "tablename(`columnname`)"
                "unique_keys"   =>  array(),
                "indexes"       =>  array(),
                "full_text"     =>  array(),
                "columns"       => array(
                    "id"            =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                        "increment"     =>  true,  //increment true or false
                    ),
                    "date"          =>  array(
                        "type"          =>  "date",
                    ),
                    "user_id"       =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "ip"            =>  array(
                        "type"          =>  "varchar",
                        "length"        =>  45,
                    ),
                    "secondsonline" =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                    "endtime"       =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                        "attributes"    =>  "TIME",
                    ),
                    "so"    =>  array(
                        "type"          =>  "tinyint",
                        "length"        =>  1,
                        "default"       =>  array("default" => true, "value" => 0),
                    ),
                    "starttime"     =>  array(
                        "type"          =>  "timestamp",
                        "default"       =>  "timestamp",
                    ),
                    "project_id"    =>  array(
                        "type"          =>  "int",
                        "length"        =>  11,
                    ),
                )
            ]
        );
    }
    
    public function dataProjectStati() {
        return array(
            "project_status" =>
                array(
                    array(
                        "titel" =>  "Not started",
                        "type"  =>  "0",
                        "extensie"  =>  "warning"
                    ),array(
                        "titel" =>  "In progress",
                        "type"  =>  "1",
                        "extensie"  =>  "warning"
                    ),array(
                        "titel" =>  "Feedback",
                        "type"  =>  "2",
                        "extensie"  =>  "success"
                    ),array(
                        "titel" =>  "Testing",
                        "type"  =>  "3",
                        "extensie"  =>  "success"
                    ),array(
                        "titel" =>  "Done",
                        "type"  =>  "4",
                        "extensie"  =>  "success"
                    ),array(
                        "titel" =>  "Idea",
                        "type"  =>  "5",
                        "extensie"  =>  "success"
                    ),array(
                        "titel" =>  "Continues",
                        "type"  =>  "6",
                        "extensie"  =>  "success"
                    ),array(
                        "titel" =>  "On hold",
                        "type"  =>  "7",
                        "extensie"  =>  "danger"
                    ),array(
                        "titel" =>  "Stopped",
                        "type"  =>  "8",
                        "extensie"  =>  "danger"
                    )
                )
        );
    }
}
