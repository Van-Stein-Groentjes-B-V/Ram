<?php

/**
 * CSS modifier class. Loads custom CSS files.
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
 * CssModifier
 * @category   Library
 * @package    Ram
 */
class CssModifier
{
    // Filename to modify, with .css
    private $_fileName = "";
    // The name of the block to rewrite
    private $_blockName = "";
    // The array with the class names and the values
    // Format: $_arrayToChange[className] => ["css_name" => "value"]
    private $_arrayToChange = array();
    // Start of codeblock
    private $_startCodeBlock = START_CODE_BLOCK;
    // End code block
    private $_endCodeBlock = END_CODE_BLOCK;
    
    /**
     * Constructor: __construct.
     * Assemble and pre-process the data.
     * @param   String         $filename  Name of the file to load or null
     * @param   String         $blockname Name of the block of CSS code or null
     * @param   String | Array $data      A string | array of data or null.
     * @return  Void.
     */
    public function __construct($filename = null, $blockname = null, $data = null) {
        if ($filename && file_exists(ROOT . DS . "public" . DS . "css" . DS . $filename) && strpos($filename, ".css") !== false) {
            //only set the fileName if it exists
            $this->_fileName = $filename;
        }
        if ($blockname) {
            $this->_blockName = filter_var($blockname, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        if ($data) {
            $this->validateData($data);
        }
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
    }
    
    /**
     * Check whether file exists and the extension is CSS. If so, set it to the class variable _fileName.
     * @param   String $filename Name of the file to target.
     * @return  Boolean                 True on success, false on error.
     */
    public function setFileName($filename) {
        if (file_exists(ROOT . DS . "public" . DS . "css" . DS . $filename) && strpos($filename, '.css') !== false) {
            $this->_fileName = $filename;
            return true;
        }
        return false;
    }
    
    /**
     * Set the name of the block (also filter it)
     * @param   String $blockname Name of the block in the CSS file.
     * @return  Void.
     */
    public function setBlockName($blockname) {
        $this->_blockName = filter_var($blockname, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    
    /**
     * Call the validate data function to set the data in _arrayToChange.
     * @param   Array $data Array of data to check formatted like: ((key)property name => (value)property value).
     * @return  Void.
     */
    public function setCssData($data) {
        $this->validateData($data);
    }
    
    /**
     * Validate the data and sanitize it.
     * @param   Array $data Array of data to be sanitize :((key)property name => (value)property value).
     * @return  Void.
     */
    private function validateData($data) {
        foreach ($data as $key => $array) {
            if (!is_array($array) || $key === "" || is_numeric($key)) {
                continue;
            }
            $cssNameValue = array();
            foreach ($array as $keyArray => $value) {
                $cssNameValue[filter_var($keyArray, FILTER_SANITIZE_SPECIAL_CHARS)] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            }
            $this->_arrayToChange[filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS)] = $cssNameValue;
        }
    }
    
    /**
     * Checks if everything is set and opens the target file.
     * Then starts to check where the code block should be/replace existing code block.
     * @return  Boolean         True on success, false on error.
     */
    public function addToCssFile() {
        if ($this->_fileName === "" || !file_exists(ROOT . DS . "public" . DS . "css" . DS . $this->_fileName) || $this->_blockName === "") {
            return false;
        }
        // Get the content of the file
        $contents = file_get_contents(ROOT . DS . "public" . DS . "css" . DS . $this->_fileName);
        $contentArray = explode($this->_startCodeBlock, $contents);
        // If the css file is empty
        if (count($contentArray) <= 0 || !isset($contentArray[1])) {
            $contentArray[0] = $this->createBlockCss();
        } else {
            // Loop through content in search of the blockname. if not found, add the new block add the end.
            $found = false;
            foreach ($contentArray as $key => $contentSingle) {
                if (preg_match('~#' . $this->_blockName . '#~', $contentSingle)) {
                    $found = true;
                    $contentArray[$key] = $this->createBlockCss();
                    break;
                }
            }
            if (!$found) {
                $contentArray[] = $this->createBlockCss();
            }
        }
        // Rebuild the whole
        $contentNew = $this->_startCodeBlock . trim(implode($this->_startCodeBlock, $contentArray), $this->_startCodeBlock);
        return $this->writeToFile($contentNew);
    }
    
    /**
     * Creates a new CSS block with the values given in data.
     * @return  String      new CSS code block.
     */
    private function createBlockCss() {
        $css = "\n" . " * #" . $this->_blockName . "# \n" . "*/ \n";
        foreach ($this->_arrayToChange as $idName => $valueArray) {
            $css .= "#" . $idName . "{\n";
            foreach ($valueArray as $propertyName => $propertyValue) {
                $css .= "  " . $propertyName . ": " . $propertyValue . "; \n";
            }
            $css .= "} \n";
        }
        return $css .= "\n /* \n * #END_" . $this->_blockName . "# \n " . $this->_endCodeBlock . "\n\n";
    }
    
    /**
     * Write the content edited/generated to the same file, or, if it doesn't exist, create it .
     * @param   String $content The entire CSS file + newly generated CSS code.
     * @return  Boolean             True on success, false on error.
     */
    private function writeToFile($content) {
        $cssFile = fopen(ROOT . DS . "public" . DS . "css" . DS . $this->_fileName, 'w');
        $result = fwrite($cssFile, $content, strlen($content));
        fclose($cssFile);
        return $result > 10;
    }
}
