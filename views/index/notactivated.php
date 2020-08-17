<?php 
 /**
 * View to show to a user that has not activated their account yet when
 * account activation is turned on.
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
echo $showMessage;
?>
    <div class="column col-sm-6 col-sm-offset-3 col-md-8 col-md-offset-2"> 
        <div class="panel panel-default messagePanel">
            <div class="panel-body">
                <div class="animated">
                     <p> 
                        <?php echo _('If you continue to receive this notification, please contact.')?> 
                        <a href="mailto:<?php echo EMAIL_REPLY_TO; ?>"><?php echo EMAIL_REPLY_TO; ?></a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
