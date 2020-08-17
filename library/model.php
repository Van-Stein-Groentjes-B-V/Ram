<?php

/**
 * Model class. All objects that are used in the system extend on this class.
 *
 * PHP version 7+
 *
 * @category   Library
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
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
 */

namespace SG\Ram;

use ReflectionMethod;

/**
 * Model
 * @category   Library
 * @package    Ram
 */
class Model
{
    /**
     * Constructor.
     * @return Void.
     */
    public function __construct() {
    }

    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
    }
    
    /**
     * If the method exists, return the HTML special chars version.
     * @param   String $action Name of the method you want modified by htmlspecialchars.
     * @return  String         The result | false on error.
     */
    public function getParsedString($action) {
        if ((int)method_exists($this, $action)) {
            $reflection = new ReflectionMethod($this, $action);
            if ($reflection->isPublic() && is_string($this->$action())) {
                return htmlspecialchars_decode($this->$action(), ENT_HTML5);
            }
        }
        return false;
    }
    
    /**
     * Checks if the link includes an HTTP
     * @param   String $link Contains a web link.
     * @return  String       Returns an new string with HTTPS.
     */
    protected function createLink($link) {
        if (strpos($link, "http") !== false) {
            return strpos($link, "https") !== false ? $link : str_replace("http", "https", $link);
        } else {
            return "https://" . $link;
        }
    }
}
