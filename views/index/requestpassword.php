<?php
 /**
 * Page to request a new password
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

<div class="container">
    <div class="col-sm-6">
        <div class="panel panel-default loginPanel" >
            <div class="panel-heading">
                <div class="panel-title loginTitel text-center"><?php echo _('RESET YOUR PASSWORD.');?></div>
            </div>

            <div class="panel-body" >
                <form action="" method="post" enctype="multipart/form-data" class="form-signin"><br>
                    <div class="input-group row-margin">
                        <input id="emailRQPS" type="email" class="form-control loginInputFields" name="emailRQPS" required placeholder="<?php echo _('E-mail');?>">
                    </div>
                    <div class="button-row">
                        <button type="submit" name="submit" class="btn btn-primary pull-right loginBTN"><?php echo _("SEND REQUEST");?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>