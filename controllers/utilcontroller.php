<?php

/**
 * Util Controller. Class that holds utilities that can be added to the application
 * - Prints breadcrumbs
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
 * @uses       \SG\Controller   Extend the main controller.
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

/**
 * UtilController
 * @category   Controllers
 * @package    Ram
 */
class UtilController extends Controller
{
    /**
     * Print bread crumbs.
     * @param   Array  $pages  An array with breadcrumb pages
     * @param   String $active Which is active
     * @return  void
     */
    public function printBreadCrumbs($pages, $active) {

        echo '<ol class="breadcrumb">';
        foreach ($pages as $k => $v) {
            if ($k == $active) {
                echo '<li class="active"><a href="' . $k . '">' . $v . '</a></li>';
            } else {
                echo '<li><a href="' . $k . '">' . $v . '</a></li>';
            }
        }
        echo '</ol>';
    }
}
