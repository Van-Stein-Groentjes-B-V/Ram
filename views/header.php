<?php
/**
 * Header
 * Will be loaded with every view. Holds the general information of the page
 * and sets possible meta tags.
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

global $user;
//Get the page variable we are loading
$page = $data['page'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Most of the icons are free downloaded from https://fontawesome.com/license we thank them for the possibility. -->
        <meta charset="utf-8">
        <title><?php echo $data["site_title"]; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, height=device-height, viewport-fit=cover" />
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <?php echo $data["meta_description"]; ?>
        <?php echo $data["no_index"]; ?>
        <meta name="author" content="<?php echo $data["author"]; ?>">
        <link rel="stylesheet" href="<?php echo SITE_ROOT ?>public/css/fontawesome-all.min.css">
        <?php echo $data['css']; ?>
        <?php echo $data['js']; ?>
        <script src="<?php SITE_ROOT . "/public/js/tinycmce.min.js"?>"></script>
        <?php
        $theme = "theme";
        $data['page'] = '';

        ?>
        <!--[if IE]>
          <script src="<?php echo SITE_ROOT . "/public/js/"; ?>htmlshiv.js"></script>
          <script src="<?php echo SITE_ROOT . "/public/js/"; ?>respond.js"></script>
        <![endif]-->

        <link rel="shortcut icon" href="public/img/favicon.ico" />

        <base href="<?php echo SITE_ROOT ?>">
        <script>
            var base_url = "<?php echo SITE_ROOT; ?>";
            var server_base_url = "<?php echo SITE_ROOT; ?>";
            var api_url = "<?php echo SITE_ROOT; ?>api/";
            var playSounds = <?php if ($user) { echo $user->getUser()->getPlaySounds() ? "true" : "false"; } else { echo "false";} ?>
        </script>
    </head>
    <body>
        
        <?php 
            //display feedback to the user in the form of a success or errormessage
            $addClass = "";
            if ($page !== "dashboard") {
                $addClass = "extraheight";
            }
        ?>
        
        <?php if ($page !== "Login" && $page !== "Create admin" && $page !== "RAM Management configuration"){?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main maincontainer <?php echo $addClass;?>">
            <?php include "templates/alertmessages.php";?>
            <!-- this div will be closed by the content-->
        <?php } ?>
        <img class="ramlogo" src="./img/ramlogo.png" alt="ramlogo" />