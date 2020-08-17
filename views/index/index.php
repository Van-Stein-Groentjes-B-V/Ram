<?php

/**
 * Index controller view that lets the user select if they are a customer or an
 * employee. After which they go to a login screen.
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
$company = "My Company B.V.";
if (isset($data['company'])){
    $company = $data['company'];
}
?>
<div id="content" class="main">
    <div id="actualcontent">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title loginTitel"><?php echo _("Customer project portal"); ?></div>
                </div>
                <div class="panel-body">
                    <div class="animated medium-text">
                         <p> 
                             <?php echo _("Welcome to the "), $company, _(" development and project management interface.") ,
                            _("If you are a customer and want to view your projects under development, please login using the form with your user name and password that is emailed to you."),
                            _("If you lost your password or did not receive an email, please create a ticket in our"); 
                             ?>
                        </p>
                        <a href="http://vansteinengroentjes/support/">
                            <?php echo _('support section'); ?>
                        </a>.
                        <p class="button-row">
                            <a href="index/customerlogin/" class="btn btn-default">
                                <i class="fa fa-sign-in"></i> 
                                <?php echo _("Login for customers"); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title loginTitel"><?php echo _("Proceed to secure backend."); ?></div>
                </div>
                <div class="panel-body">
                    <div class="animated medium-text">
                        <p> 
                            <?php 
                                echo _("If you are an employee of ") , $company , _(", you can continue to login to the backend."); 
                            ?>
                        </p>
                        <p class="button-row">
                            <a href="index/login/" class="btn btn-default"><i class="fa fa-sign-in"></i> <?php echo _("Login for employees"); ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


