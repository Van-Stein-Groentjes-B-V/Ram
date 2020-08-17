<?php

/**
 * Status types that are defined
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
 *
 */

namespace SG\Ram;

/**
 * StatusTypes
 * @category   Library
 * @package    Ram
 */
class StatusTypes
{
    public static $notStarted = 0;
    public static $testing = 1;
    public static $feedback = 2;
    public static $inProgress = 3;
    public static $done = 4;
    public static $idea = 5;
    public static $continues = 6;
    public static $onHold = 7;
    public static $stopped = 9;
    public static $statusTypesArray = array(1,3,6);
}
