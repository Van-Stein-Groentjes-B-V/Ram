<?php

/**
 * Custom Header
 * Will be shown on the login screen and all related screens. Shows a blank 
 * header with the logo of RAM management in the center.
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

if ($data !== "Login" && $data !== "Create admin" && $data !== "RAM Management configuration"){?>
    </div><!-- close the div that is opened in the header -->
</div><!-- close the div for the sidemenu -->
<?php }?>
<div class="main">
    <div class="darkmenu menuHeaderLogin" id="mainmenu">
         <a href="<?php echo SITE_ROOT; ?>">
             <div class="LogoPlaceholder">
                <img src="./img/ramlogo_small.png" alt="ramlogo" />
                <p class="companyName">RAM</p>
            </div>
         </a>
    </div>
    <div class="col-sm-6 col-sm-offset-3 col-md-8 col-md-offset-2"> 
        <?php include "templates/alertmessages.php";?>
    </div>
</div>
<div> <!-- open a div that can be closed again in the footer -->
