<?php

 /**
 * Index controller view for user already loggedIn
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
?>
<div class="column col-sm-6 col-sm-offset-3 col-md-8 col-md-offset-2"> 
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title loginTitel"><?php echo _("Already logged in:"); ?></div>
        </div>
        <div class="panel-body">
            <div class="animated medium-text">
                 <p> 
                    <?php 
                        echo _("You are already logged-in. This website does not support logging in multiple times."); 
                    ?> 
                    <?php header("refresh:5; url=" . SITE_ROOT); ?> 
                </p>
                <div class="loading">Loading&#8230;</div>
            </div>
        </div>
    </div>
</div>

