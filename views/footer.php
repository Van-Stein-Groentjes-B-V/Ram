 <?php 
 
 /**
 * Footer
 * Is loaded last, cleans up possible open tags, and loads heavy javascript
 * 
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
if(isset($data["sidebar_used"])){
    echo $data["sidebar_used"] ? "</div></div>" : "";
}
if ($page !== "Login"){
    echo "</div> <!-- Closing div that is opened in the header -->";
}
?>
        <footer>
            <script>
                var pageToUse = "<?php echo $page; ?>";
            </script>
            <?php echo $data["js_footer"]; ?>
        </footer>
    </body>
</html>
