<?php

/**
 * Sidebar View
 * Displays the sidebar in the application. The sidebar contains the menu
 * We use Bootstrap jQuery and CSS to layout the page.
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


$user = "";
if(isset($data["gebruiker"])){
    $user = $data["gebruiker"];
}
$page = $data['page'];
$ModulesMenuItems = unserialize(MODIFY_MENU_EXISTING);
ksort ($ModulesMenuItems);

if (!$user->isCustomer()) {
    $menuItems = array( "dashboard" => array("label" => _("Dashboard"), "icon" => "fa fa-home fa-2"),  
                        "person" =>    array("label" => _("People"),    "icon" => "fas fa-user-friends"), 
                        "projects" =>  array("label" => _("Projects"),  "icon" => "fas fa-clipboard-list"),
                        "companies" => array("label" => _("Companies"), "icon" => "fa fa-building fa-2"),
                        "timesheet" => array("label" => _("Timesheet"), "icon" => "fa fa-clock fa-2"));
    
} else {
    $menuItems = array( "dashboard" => array("label" => _("Dashboard"), "icon" => "fa fa-home fa-2"),  
                        "person" =>    array("label" => _("People"),    "icon" => "fas fa-user-friends"), 
                        "projects" =>  array("label" => _("Projects"),  "icon" => "fas fa-clipboard-list"));
}
//submenuitems of settingsmenu
$settingMenuItems = array();
$settingMenuItems[] = array( "settings" => array("link" => "settings", "label" => _("Personal settings"), "icon" => "fas fa-users-cog"));


if ($user->isMainAdmin() || $user->isAdmin()) {
    $settingMenuItems[] = array("admin settings" => array("link" => "settings/admin", "label" => _("Admin settings"), "icon" => "fas fa-cogs"));
    $settingMenuItems[] = array("Module" => array("link" => "module", "label" => _("Module overview"), "icon" => "fas fa-project-diagram"));
}

?>
<div class="container-fluid entireheight">
    <div class="row entireheight">
        <div class="col-sm-3 col-md-2 col-lg-2 sidebar" id="sidebar">
            <ul class="nav nav-sidebar">
                <div class="brand" align="center">
                    <a class="ramLogoLink" href="<?php echo SITE_ROOT . "dashboard"; ?>"><p class="SideBarBrand"><img src="public/img/ramlogo_small.png" class="img-circle sidebarLogo" alt="logo">&ensp; RAM</p></a>
                </div>
                <div class="clearfix"></div>
                <hr class="borderLogoAndProfile" />
                <div class="col-md-12 offset-md-2 col-lg-12 profilePictureDiv" align="center"> 
                    <img class="profilePicture img-responsive img-circle" alt="person logo" src="./img/person/logos/<?php echo $user->getLogo(); ?> "> 
                    <li class="col-md-12 welcomeUserName" align="center"><?php echo _("Welkom ") . $user->getUsername() ."!" ?></li>
                </div>
                <div class="clearfix"></div>
                <hr class="borderLogoAndProfile" />
                <div class="row sessionToolBar col-sm-6 col-md-7 col-lg-12">
                    <p class="toolText" align="center">Work hour timer log</p>
                    <div id="MyClockDisplay" align="center" class="clock" onload="showTime()"></div>
                    <div id='project-log-timer'><span class='default-text-log-timer'><?php echo _("On project:"); ?> </span><span class='projectname-log-timer'></span></div>
                    <div class="buttonsLogTimer" align="center">
                        <button id="play" class="logButton btn btn-primary" value="play"><i class="fas fa-play"></i> Play</button>
                        <button id="pause" class="logButton btn btn-primary" value="pause"><i class="fas fa-pause"></i> Pause</button>
                        <button id="stop" class="logButton btn btn-primary" value="stop"><i class="fas fa-stop"></i> Stop</button>
                        <br><br>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr class="borderLogoAndProfile" />
            </ul>
            <ul class="nav nav-sidebar metismenu" id="sideMenuMain">
            <?php 
                // Load base menu items
                foreach ($menuItems as $link => $data){
                    $active = "";
                    if ($page === $link){
                        $active = "class='active'";
                    }
                    echo "<li ", $active, ">", 
                             "<a href='" . SITE_ROOT . $link, "/'><i class='". $data["icon"]. "'></i>&ensp;". $data["label"]. "</a>" , 
                         "</li>";
                }

                // Load usermanagement module when user is super admin
                if($user->isMainAdmin()){ 
                    $active = $page == "usermanagement" ? "class='active'" : "";
                    echo "<li ",  $active, ">",
                            "<a href='", SITE_ROOT , "usermanagement/'><i class='fas fa-users-cog fa-2'></i>&ensp;", _("Usermanagement"), "</a>",
                         "</li>";
                } 
                
                // Load module menuitems
                foreach($ModulesMenuItems AS $key => $values){
                    echo "  ";
                    foreach($values AS $module){
                        foreach($module AS $value){
                            if($value["mustBeAdmin"] && !$user->isMainAdmin()){
                                continue;
                            }
                            $active = "";
                            $icon = "";
                            if ($page == $value["name"]) { 
                                $active = "class='active'"; 
                            }
                            if ($value["icon"] !== "") { 
                                $icon = "<i class='" . htmlentities($value["icon"]) . " fa-2'></i>"; 
                            }
                            echo  "<li " , $active ,  ">" ,
                                      "<a href='" , $value["url"] , "'>" , $icon , " " , _($value["name"]) , "</a>" ,
                                  "</li>";
                        }
                    }
                }
                
                // Load settings menu
                ?>    
                <li <?php if ($page == "settings" || $page == "admin settings" || $page == "Module") { echo "class='active'"; } ?>>
                    <a href="javascript:void(null);" aria-expanded="<?php if ($page == "settings" || $page == "admin settings" || $page == "Module") { echo "true"; }else{"false";} ?>"><i class="fas fa-cog"></i>&ensp; <?php echo _("Settings");?>&ensp;<i class="fas fa-angle-right"></i></a>
                    <ul id="menu2" aria-expanded="<?php if ($page == "settings" || $page == "admin settings" || $page == "Module") { echo "true"; }else{"false";} ?>" class="nav nav-second-level collapse <?php if ($page == "settings" || $page == "admin settings" || $page == "Module") { echo "in"; } ?>">
                        <?php
                        // Load settingsmenu
                        foreach ($settingMenuItems as $menuitem){
                            foreach ($menuitem as $link => $info) {
                                $active = "";
                                if ($page === $link) {
                                    $active = "class='active'";
                                }
                                echo "<li ", $active, ">",
                                         "<a href='" , SITE_ROOT , $info["link"], "/'><i class='", $info["icon"], "'></i>&ensp;", $info["label"], "</a>" ,
                                     "</li>";
                            }
                        }
                        ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

      