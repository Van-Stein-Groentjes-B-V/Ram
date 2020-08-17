<?php
/**
 * Navbar View
 * Displays the navigation bar on top of the screen. Includes the sound for alerts
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
$page = $data['page'];
?>

<audio id="notificationsound" allow="autoplay" preload="auto">
    <source src="<?php echo SITE_ROOT; ?>public/sounds/notify.ogg" />
    <source src="<?php echo SITE_ROOT; ?>public/sounds/notify.mp3" />
</audio> 
<nav class="navbar navbar-inverse navbar-fixed-top sg-menu ">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" onclick="toggleSideBar();" class="navbar-toggle collapsed pull-left">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <h1 class="titlePage"><?php echo strtoupper(_($page)); ?></h1>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a class="dropdown-toggle font-medium" data-toggle="dropdown">
                        <i class="far fa-comments"></i>
                    </a>
                    <span class="badge badge-notify" id="messageCount"></span>
                    <ul class="dropdown-menu" role="menu" id="messageMenu">
                    </ul>
                </li>
                <li>
                    <a class="dropdown-toggle font-medium" data-toggle="dropdown">
                        <i class="far fa-bell"></i>
                    </a>
                    <span class="badge badge-notify" id="notificationCount"></span>
                    <ul class="dropdown-menu" role="menu" id="notificationMenu">
                    </ul>
                </li>
                <li>
                    <a class="dropdown-toggle font-medium" data-toggle="dropdown">
                        <i class="far fa-envelope"></i>
                    </a>
                    <span class="badge badge-notify" id="ticketCount"></span>
                    <ul class="dropdown-menu" role="menu" id="ticketMenu">
                    </ul>
                </li>
                <li>
                    <a href="index/logout" class="font-medium">
                        <i class="fa fa-power-off"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>