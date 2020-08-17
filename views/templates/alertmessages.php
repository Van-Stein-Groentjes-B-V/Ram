<?php

 /**
 * Template for view files. 
 * Shows an error or success message when the system provides one.
 * 
 * PHP version 7+
 *
 * @category   Views
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @editor     Thomas Shamoian <thomas@vansteinengroentjes.nl>
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
 **/

//types of messages to check
$messages = array("successmessage", "errormessage");
$showMessage = "";
foreach ($messages as $message){
    if (isset($data[$message]) && $data[$message] != ""){
        if($showMessage === ""){
            $showMessage = "active";
        }else{
            $showMessage = "active2";
        }
    }
}
?>
<div id="alertcontainer" class="<?php echo $showMessage; ?>">
    <?php
        foreach ($messages as $message){
            if (isset($data[$message]) && $data[$message] != ""){
                echo "<div class='col-md-12 column ", $message, "'>",
                        $data[$message],
                     "</div>";
            }
        }
    ?>
</div>