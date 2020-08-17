<?php

/**
 * General functions class. Contains some general utility functions
 * Like password validity checks, Date validity checks, Email functions and more
 *
 * PHP version 7+
 *
 * @category   Library
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Thomas Shamoian <thomas@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 * @uses       PHPMailer\PHPMailer\PHPMailer    PHPMailer library
 * @uses       DateTime                         PHP DateTime function
 * @uses       SG\Ram\Models\Account            Account model
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
 */

namespace SG\Ram;

use DateTime;
use PHPMailer\PHPMailer\PHPMailer;
use SG\Ram\Models\Account;

/**
 * functions
 * @category   Library
 * @package    Ram
 */
class Functions
{
    
    /**
     * Constructor.
     * @return Void.
     */
    public function __construct() {
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
    }
    
    /**
     * CreateRandomID: Returns a hex string length 20 to use as session id or activation key.
     * @return String           Hexadecimal string
     * */
    public function createRandomID() {
        $bytes = openssl_random_pseudo_bytes(20);
        return bin2hex($bytes);
    }

    /**
     * Send an email with correct headers.
     * @param   String $to      Target email string.
     * @param   String $subject Subject string.
     * @param   String $message Message string.
     * @return  Boolean         True on success
     */
    public function sendMail($to, $subject, $message) {
        $headers = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM_REAL . ">\r\nReply-To: " . EMAIL_REPLY_TO . "\r\nX-Mailer: PHP/" . phpversion() .
                    "\r\nContent-type: text/plain\r\n";
        return @mail($to, $subject, $message, $headers);
    }
    
    /**
     * Send an email with the correct headers with HTML in the message.
     * @param   String $to      Target email string.
     * @param   String $subject Subject string.
     * @param   String $message Message string.
     * @return  Boolean         True on success
     */
    public function sendMailHtml($to, $subject, $message) {
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        // Additional headers
        $headers .= "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM_REAL . ">" . "\r\n";
        $headers .= "Reply-To: " . EMAIL_REPLY_TO .  "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        return @mail($to, $subject, $message, $headers);
    }
    
    /**
     * Send an email with the correct headers with HTML as body and attachments.
     * @param   String         $to          Target email string.
     * @param   String         $subject     Subject string.
     * @param   String         $message     Message string.
     * @param   String | Array $attachments Filename(s) to be attached.
     * @return  Boolean                         True on success
     */
    public function sendMailHtmlWithAttachment($to, $subject, $message, $attachments = array()) {
        $mail = new PHPMailer();
        if (USE_SMPT_EMAIL) {
            $mail->isSMTP();
            //Set SMTP host name
            $mail->Host = SMTP_HOST;
            //Set this to true if SMTP host requires authentication to send email
            $mail->SMTPAuth = SMTP_AUTH;
            //Provide username and password
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPAutoTLS = false;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
        }
        // Debugging:
        if (DEBUG_EMAIL) {
            $mail->SMTPDebug  = 2;
        }

        // From email address and name
        $mail->From = EMAIL_REPLY_TO;
        $mail->FromName = EMAIL_FROM_NAME;
        $mail->addReplyTo(EMAIL_REPLY_TO, "Reply");
        // Send HTML or Plain Text email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->addAddress($to);
        
        // Add attachments
        if (!is_array($attachments)) {
            // for backwards compatibility
            $mail->addAttachment($attachment);
        } else {
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment);
            }
        }
        return $mail->send();
    }

    /**
     * Retrieves the IP address of the client.
     * @return  String  $ipaddress      String + the global $_SERVER.
     */
    public function getRealIpAddr() {
        $ipaddress = '';
        $ipdetails = array("HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR");
        foreach ($ipdetails as $ip) {
            if (isset($_SERVER[$ip])) {
                $ipaddress .= $_SERVER[$ip] . ' - ';
            }
        }
        if ($ipaddress == '') {
            $ipaddress = 'UNKNOWN';
        }
        
        return str_replace(' - ', '', $ipaddress);
    }

    /**
     * Check if email is valid.
     * @param   String $email Email address to check
     * @return  Boolean                 True if email is valid, otherwise false.
     */
    public function checkEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Preg-matches variable to check if it is a valid phone number. (dutch system)
     * Recognizes:
     * @example: +31612345678   ==> valid   @example: +316-12345678   ==> valid
     * @example: +316 12345678  ==> valid   @example: 00316-12345678  ==> valid
     * @example: 0031612345678  ==> valid   @example: +31-123456789   ==> valid
     * @example: 00316 12345678 ==> valid   @example: 0031-123456789  ==> valid
     * @example: +31 123456789  ==> valid
     * @example: +31123 456789  ==> valid
     * @example: +31123456789   ==> valid
     * @example: 0123 456789    ==> valid
     * @example: 06 12345678    ==> valid
     *
     * @param   String $number Phone number to check
     * @return  Boolean             True if preg_match is true.
     */
    public function checkPhone($number) {
        $regex_dutch = '/(^\+[0-9]{2}|^\+[0-9]{2}\(0\)|^\(\+[0-9]{2}\)\(0\)|^00[0-9]{2}|^0)([0-9]{9}$|[0-9\-\s]{10}$)/';
        return preg_match($regex_dutch, $number);
    }

   /**
    * When you need to hash a password, just feed it to the function.
    * and it will return the hash which you can store in your database.
    * The important thing here is that you don’t have to provide a salt.
    * value or a cost parameter. The new API will take care of all of.
    * that for you. And the salt is part of the hash, so you don’t.
    * have to store it separately.
    * Links:
    * Here is a implementation for PHP 5.5 and older:
    * @param    String  $strPassword Password to hash.
    * @param    Integer $numAlgo     Which algorithm should be used (default BCRYPT).
    * @param    Array   $arrOptions  An array of options.
    * @return   String               Hashed password.
    */
    public function createPasswordHash($strPassword, $numAlgo = PASSWORD_BCRYPT, $arrOptions = array()) {
        if (function_exists('password_hash')) {
            // php >= 5.5
            $hash = password_hash($strPassword, $numAlgo, $arrOptions);
        } else {
            $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
            $salt = base64_encode($salt);
            $salt = str_replace('+', '.', $salt);
            $hash = crypt($strPassword, '$2y$10$' . $salt . '$');
        }
        return $hash;
    }

    /**
     * Verify password.
     * @param   String $strPassword Password.
     * @param   String $strHash     Hashed string.
     * @return  Boolean             True if inputs match
     */
    public function verifyPasswordHash($strPassword, $strHash) {
        if (function_exists('password_verify')) {
            // php >= 5.5
            return password_verify($strPassword, $strHash);
        } else {
            $strHash2 = crypt($strPassword, $strHash);
            return $strHash == $strHash2;
        }
    }

    /**
     * Test input.
     * Trims and rewrites HTML characters
     * @param   String $data Test data.
     * @return  String       Test data.
     */
    public function testInput($data) {
        return htmlspecialchars(trim($data));
    }
    
    /**
     * Creates random name.
     * @param   Integer $length Length of the string to render
     * @return  String              Returns string with length $length
     */
    public function createRandomDirName($length = 32) {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    /**
     * Creates random directory name. Checks if the directory exists and than
     * renders a new directory name
     * @param   String  $parentdir Parent folder for the directory.
     * @param   Integer $times     Iteration of generation.
     * @param   Integer $length    Length of the directory name.
     * @return  String             Returns created string.
     */
    public function createRandomDirNamePlus($parentdir, $times = 0, $length = 32) {
        if ($times > 255) {
            return $length === 64 ? "" : $this->createRandomDirNamePlus($parentdir, $times, $length + 1);
        }
        $times++;
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if (file_exists($parentdir . $randomString)) {
            return $this->createRandomDirNamePlus($parentdir, $times, $length);
        }
        return $randomString;
    }
    
    /**
     * Creates a random password.
     * Mandates at least 1 capital, 1 number and 1 normal character. If special
     * characters need to be included than also at least 1 is required.
     * @param   Integer $length      Length of the password
     * @param   Boolean $useSpecials Use special characters in creation
     * @return  String               Returns a random password.
     */
    public function createRandomPass($length = 16, $useSpecials = false) {
        if ($length < 8) {
            $length = 8;
        }
        $needCapital = $needNumber = $needChar = $needSpecial = 1;
        $characters = "abcdefghijklmnopqrstuvwxyz";
        $numbers = "0123456789";
        $capitals = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $specials = $useSpecials ? "~ !#$|%^&*()+-./:;<>=,@[]_{}" : "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            switch (rand(0, 3)) {
                case 0:
                    $needNumber--;
                    $randomString .= $numbers[rand(0, strlen($numbers) - 1)];
                    break;
                case 1:
                    $needCapital--;
                    $randomString .= $capitals[rand(0, strlen($capitals) - 1)];
                    break;
                case 2:
                    $needSpecial--;
                    $randomString .= $specials[rand(0, strlen($specials) - 1)];
                    break;
                default:
                    $needChar--;
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                    break;
            }
        }
        return ($needCapital < 0 && $needNumber < 0 && $needChar < 0 && $needSpecial < 0) ? $randomString : createRandomPass($length, $useSpecials);
    }
    
    /**
     * Check if $data is a date format.
     * Accepts the following date formats:
     * - d-m-yyyy
     * - dd-m-yyyy
     * - d-mm-yyyy
     * - dd-mm-yyyy
     * - d-m-yy
     * - dd-m-yy
     * - d-mm-yy
     * - dd-mm-yy
     * Checks that the month is not bigger than 12 and the days of the month match.
     * Considers off-years as well.
     * @example checkDatumFormat("28-10-1983") returns true
     * @example checkDatumFormat("28/10/1983") returns false
     * @example checkDatumFormat("31-04-2010") returns false
     * @example checkDatumFormat("29-02-2000") returns true
     * @example checkDatumFormat("29-02-2001") returns false
     *
     * @param  String $data String to check.
     * @return Boolean      True if $data was date.
     */
    public function checkDatumFormat($data) {
        return preg_match(
            '/^(?:(?:31(-)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(-)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)'
            . '?\d{2})$|^(?:29(-)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$'
            . '|^(?:0?[1-9]|1\d|2[0-8])(-)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/',
            $data
        );
    }
    
    /**
     * Validates date follows a specific format.
     * @param   String $date   Date.
     * @param   String $format Date format.
     * @return  Boolean        True if date follows format.
     */
    public function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    /**
     * In multiple file upload, reorders an array with all the information
     * @param   Array $file_post Uploaded file-objects.
     * @return  Array            Collection of files
     */
    public function reArrayFiles(&$file_post) {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
    }
    
    /**
     * Loops through array and check the given key equals the $new.
     * @param   Array  $arrayExisting Array to check.
     * @param   String $new           Key to check
     * @param   String $nameVar       Key to compare to.
     * @param   String $returnKey     ReturnKey.
     * @return  String | Boolean      True on new = namevar
     */
    public function checkIfAlreadyExists($arrayExisting, $new, $nameVar, $returnKey = false) {
        if (is_array($arrayExisting)) {
            foreach ($arrayExisting as $key => $existing) {
                if (isset($existing[$nameVar]) && $existing[$nameVar] == $new) {
                    return $returnKey ? $key : $existing;
                }
            }
        }
        return false;
    }
    
    /**
     * Handle image upload, rename it, and move it to correct folder.
     * @param   Array  $file   The file (!!SINGLE!!) as delivered by the phphandler.
     * @param   String $naam   The name of the input in the form.
     * @param   String $subMap The extra maps lower than the specified folder.
     * @return  Array | String  New name and extension  ||  errorstring.
     *
     * ### DEPENDENCIES ###.
     * UPLOAD_FOLDER_IMAGES,  MUST BE SET AND BE WRITABLE.
     * MAX_SIZE_IMAGE,  max size the upload may be.
     */
    public function handleImage($file, $naam, $subMap = false) {
        $upload_folder =  UPLOAD_FOLDER_IMAGES;
        if ($subMap) {
            $upload_folder .= $subMap . DS;
        }
        $info = getimagesize($file[$naam]['tmp_name']);
        if ($file[$naam]['error'] !== UPLOAD_ERR_OK) {
            return _("Upload failed with error code ") . $file[$naam]['error'];
        } elseif ($info === false) {
            return _("This type of file is not supported. Use a file with a type of JPG, JPEG or PNG. ");
        } elseif (($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG) && ($info[2] !== IMAGETYPE_JPG)) {
            return _("This type of file is not supported. Use a file with a type of JPG, JPEG or PNG.");
        } elseif ($file[$naam]['size'] > MAX_SIZE_IMAGE) {
            return _("The size of the image is too large, select another image. ");
        } elseif (isset($file[$naam]) && !empty($file[$naam])) {
            // Finally we can do stuff
            $infoPicture = pathinfo($file[$naam]['name']);
            $extension = $infoPicture['extension'];
            $filetype = exif_imagetype($file[$naam]['tmp_name']);
            
            // Get the extension of the file
            $random_dir =  $this->createRandomDirName();
            $newname = $random_dir . "." . $extension;
            if (move_uploaded_file($file[$naam]['tmp_name'], $upload_folder . $newname)) {
                $this->remakeImage($upload_folder . $newname, $filetype);
                return array("newname" => $newname, "ext" => $extension);
            } else {
                return _('Sorry, something went wrong, please try again later.');
            }
        } else {
            return _('Log in. ');
        }
    }
    
    /**
     * Remake image.
     * @param   String $target_file Target string.
     * @param   String $filetype    File type.
     * @return  Boolean             True on success
     */
    public function remakeImage($target_file, $filetype) {
        // It gets the size of the image
        list( $width,$height ) = getimagesize($target_file);
        // Calculates to keep the aspect ratio
        $ratio = $width / $height;
        // It makes the new image height of 480
        $newheight = 480;
        // It makes the new image width based on the ratio
        $newwidth = 480 * $ratio;
        // It loads the images we use jpeg function you can use any function like imagecreatefromjpeg
        $thumb = imagecreatetruecolor($newwidth, $newheight);
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefill($thumb, 0, 0, $white);
        // Uses correct function based on image type
        switch ($filetype) {
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($target_file);
                break;
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($target_file);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($target_file);
                break;
            case IMAGETYPE_BMP:
                $source = imagecreatefrombmp($target_file);
                break;
            case IMAGETYPE_WBMP:
                $source = imagecreatefromwbmp($target_file);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($target_file);
                break;
            default:
                $source = imagecreatefromjpeg($target_file);
                break;
        }
        // Resize the $thumb image.
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        // It then save the new image to the location specified by $resize_image variable
        return imagejpeg($thumb, $target_file, 100);
    }
    
    /**
     * Move file.
     * @param   String $file          File.
     * @param   String $naam          Name.
     * @param   String $upload_folder Folder name
     * @return  Array | String        Array with filename and extension or error message
     */
    public function moveFile($file, $naam, $upload_folder = "") {
        $error = "";
        if ($file[$naam]['error'] !== UPLOAD_ERR_OK) {
            $error = _("Upload failed with error code ") . $file[$naam]['error'];
        } elseif (isset($file[$naam]) && !empty($file[$naam])) {
            $infoPicture = pathinfo($file[$naam]['name']);
            $extension = $infoPicture['extension'];
            // Get the extension of the file
            if ($this->getExtensionType($extension) !== false) {
                $random_dir =  $this->createRandomDirName();
                $newname = $random_dir . "." . $extension;
                if (move_uploaded_file($file[$naam]['tmp_name'], $upload_folder . $newname)) {
                    return array("newname" => $newname, "ext" => $extension);
                } else {
                    $error = _('Sorry, something went wrong. Try later again.');
                }
            } else {
                $error = _('extension not allowed.');
            }
        } else {
            $error = _('Please log in first. ');
        }
        return $error;
    }
    
    /**
     * All allowed file extensions.
     * @return Array    Array with file types
     */
    public function getAllowedExtensions() {
        return array("gif", "jpeg", "jpg", "png", "rar", "zip", "gz", "tar", "pdf", "txt", "doc", "docx",
                             "xls", "xlsx", "ppt", "pptx", "asp", "aspx", "xml", "php", "html", "csv");
    }
    
    /**
     * Allowed extensions array.
     * @param   String $ext Extension of uploaded file.
     * @return  Boolean         True if extension in array.
     */
    public function getExtensionType($ext) {
        return array_search($ext, $this->getAllowedExtensions());
    }
    
    /**
     * Get icon type.
     * @param   String $ext Uploaded ext.
     * @return  String          File type for icon display.
     */
    public function getIconFromType($ext) {
        $allowedExts = $this->getAllowedExtensions();
        $allowedExts2 = array("gif" => "images", "jpeg" => "images", "jpg" => "images", "png" => "images", "rar" => "file-archive",
            "zip" => "file-archive", "gz" => "file-archive", "tar" => "file-archive", "pdf" => "file-pdf", "txt" => "file", "doc" => "file-word", "docx" => "file-word",
            "xls" => "file-excel", "xlsx" => "file-excel", "ppt" => "file-powerpoint", "pptx" => "file-powerpoint", "asp" => "file-code", "aspx" => "file-code",
            "xml" => "file-code", "php" => "file-code", "html" => "file-code", "csv" => "file-code");
        return is_numeric($ext) ? $allowedExts2[$allowedExts[$ext]] : $allowedExts2[$ext];
    }
    
    /**
     * Loops through the $req and checks the posted data if correctly filled in (up to standard if you will).
     * @param   Array $req  Array to check [(key)(string)name of the key in $filledin => (value)(string) short hand for the check options].
     * @param   Array $data Array with values to check [(key)(string) name of the value, (value)(various)the value to be checked].
     *
     * @return  Array | Boolean     On error a message and error array and True on sucess
     */
    public function checkValuesValidity($req, $data) {
        $hasError = false;
        $errors = array();
        foreach ($req as $field => $field_type) {
            if (
                !isset($data[$field]) || !(($field_type == "str" && strlen($data[$field]) > 0)
                    || ($field_type == "int" && is_numeric($data[$field]) )
                    || ($field_type == "date" && $this->_fun->validateDate($data[$field]))
                    || ($field_type == "bool")
                    || ($field_type == "ps" && $this->checkPassword($data[$field]))
                    || ($field_type == "em" && filter_var($data[$field], FILTER_VALIDATE_EMAIL)))
            ) {
                $errors[$field] = true;
                $hasError = true;
            } else {
                $errors[$field] = false;
            }
        }
        return $hasError ? array("errormessage" => _("values were not correct"), "errors" => $errors) : true;
    }
    
    /**
     * Checks the given password.
     * Needs to be longer than 8, contain at least 1 number, and 1 character
     * @param   String $pwd Password to check.
     * @return  Boolean         True if it validates requirements
     */
    public function checkPassword($pwd) {
        return !(strlen($pwd) < 8 || !preg_match("#[0-9]+#", $pwd) || !preg_match("#[a-zA-Z]+#", $pwd));
    }
    
    /**
     * Check if target is in array.
     * @param   String $target          Variable to be checked
     * @param   String $serializedArray Array with serialized array containing strings => table names.
     * @return  String | Boolean        Value in array or false
     */
    public function getAllowed($target, $serializedArray) {
        $array = unserialize($serializedArray);
        return array_key_exists($target, $array) ? $array[$target] : false;
    }
    
    /**
     * Sluggifies text to be used in links and e-mails.
     * @param   String $text The string to sluggify.
     * @return  String              Slug.
     */
    public function slugifyText($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        return empty($text) ? 'n-a' : $text;
    }
    
    /**
     * Get SQL from the given array.
     * @param   Array  $array  SQL array.
     * @param   Array  $search Search array options.
     * @param   String $prefix Prefixes.
     * @return  Array          Resulting query with search values.
     */
    public function getSqlFromArray($array, $search, $prefix = '') {
        $sqlStatement = "( ";
        $searchArray = array();
        $first = true;
        foreach ($array as $val) {
            if (!$first) {
                $sqlStatement .= 'OR ';
            }
            $first = false;
            $sqlStatement .= $prefix . "`" . $val . "` LIKE ? ";
            $searchArray[] = $search;
        }
        $sqlStatement .= ") ";
        return array("sql" => $sqlStatement, "search" => $searchArray);
    }
    
    /**
     * Test the SMTP settings.
     * @param   Array   $array Array with options.
     * @param   Account $user  String user.
     * @return  Boolean | String    True on success else error message
     */
    public function testSMTPOptions($array, $user) {
        if (!isset($array['SMTP_HOST']) || !isset($array['SMTP_USERNAME']) || !isset($array['SMTP_PASSWORD'])) {
            return _('bare minimum of values neccesary not given. ');
        }
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        // Set SMTP host name
        $mail->Host = $array['SMTP_HOST'];
        // Set this to true if SMTP host requires authentication to send email
        $mail->SMTPAuth = isset($array['SMTP_AUTH']) ? $array['SMTP_AUTH'] : SMTP_AUTH;
        // Provide username and password
        $mail->Username = $array['SMTP_USERNAME'];
        $mail->Password = $array['SMTP_PASSWORD'];
        $mail->SMTPAutoTLS = false;
        $mail->SMTPSecure = isset($array['SMTP_SECURE']) ? $array['SMTP_SECURE'] : SMTP_SECURE;
        $mail->Port = isset($array['SMTP_PORT']) ? $array['SMTP_PORT'] : SMTP_PORT;
        // $mail->SMTPDebug = 3;
        $mail->From = EMAIL_REPLY_TO !== "website@hosting.domain" ? EMAIL_REPLY_TO : $user->getEmail();
        $mail->FromName = EMAIL_FROM_NAME !== "van Stein en Groentjes" ? EMAIL_FROM_NAME : $user->getFullname();
        $replyTo = EMAIL_REPLY_TO !== "website@hosting.domain" ? EMAIL_REPLY_TO : $user->getEmail();
        $mail->addReplyTo($replyTo, "Reply");
        // Send HTML or Plain Text email
        $mail->isHTML(true);
        $mail->Subject = _('testing SMTP');
        $mail->Body = _('testing SMTP');
        $mail->addAddress($user->getEmail());
        return $mail->send() ? true : _('failed to send the email through SMTP. ');
    }
    
    /**
     * Replacing slug.
     * @param   String $body  Body in which to replace slugs
     * @param   Array  $array All slugs available
     * @return  String          Body with replaced slugs
     */
    public function replaceSlugs($body, $array) {
        foreach ($array as $key => $val) {
            if (strpos($body, "[slug_" . $key . ']') !== false) {
                $body = str_replace("[slug_" . $key . "]", $val, $body);
            }
        }
        return $body;
    }
    
    /**
     * check for non allowed definitions.
     * @param   String $content Content | definitions.
     * @return  Boolean | String.
     */
    public function checkForInvalidness($content) {
        if (
            preg_match("~DB_HOST|DB_PORT|DB_SOCKET|DB_USER|DB_PASSWORD|DB_NAME|SALT_ADMIN|SMTP_PASSWORD|IMAP_PASSWORD|DB_LOG_PASSWORD|CAPTCHA_SECRET|"
                     . "ALLOWED_DELETE_TABLES|EXCEPTION_URL|\\db~i", $content) !== 0
        ) {
            return _("use of not allowed definition/model.");
        }
        $allMatches = array();
        if (preg_match_all("~\_setSql\(.+\)~", $content, $allMatches) > 0) {
            foreach ($allMatches[0] as $single) {
                if (preg_match("~CREATE|DROP|TRUNCATE~", $single) !== 0) {
                    return _("use of not allowed sql command.");
                }
            }
        }
        return false;
    }
    
    /**
     * Function MorphToIconLinks. Provides Font Awesome icon values for interface
     * @param   Array $row Array with links to morph
     * @return  Void.
     */
    public function morphToIconLinks(&$row) {
        $arraySocials = array("facebook" => "fab fa-facebook-square", "twitter" => "fab fa-twitter-square", "website" => "fas fa-globe",
        "youtube" => "fab fa-youtube-square", "linkedin" => "fab fa-linkedin", "gplus" => "fab fa-google-plus-square");
        $row['social'] = "";
        foreach ($arraySocials as $social => $classNames) {
            if (isset($row[$social]) && strlen($row[$social]) > 2) {
                $tSocial = strpos($row[$social], "http") !== false ? $row[$social] : "https://" . $row[$social];
                if ($social === "website") {
                    $row[$social] = "<a href=\"" . $tSocial . "\" class=\"social-button-table\"><i class=\"" . $classNames . "\"></i></a>";
                } else {
                    $row['social'] .= "<a href=\"" . $tSocial . "\" class=\"social-button-table\"><i class=\"" . $classNames . "\"></i></a>";
                }
            }
        }
    }
    
    /**
     * Cleans data.
     * @param   Array $data An array with information.
     * @return  Void.
     */
    public function filterDataToNormal(&$data) {
        foreach ($data as $key => $value) {
            $real = explode('-', $key);
            if (count($real)) {
                $data[$real[0]] = $value;
            }
        }
    }
    
    /**
     * ExtendArrayWithRandomNumber.
     * @param   Array $array Array to extend.
     * @return  Void.
     */
    public function extendArrayWithRandomNumber(&$array) {
        $toChange = array("company","company_name", "person_main_contact_name", "contractor_name", "contractor_main_contact_name", "intermediate_name", "responsible_name", "person_name");
        $random = rand();
        $array['randomString'] = $random;
        foreach ($toChange as $changeto) {
            if (isset($array[$changeto])) {
                $array[$changeto . '-' . $random] = $array[$changeto];
            }
        }
    }
    
    /**
     * Check if contains not allowed values.
     * @param   String $string String to check
     * @return  Boolean         True if contains invalid chars
     */
    public function containsNotAllowedValues($string) {
        return preg_match("/[^a-zA-Z0-9\!\?\|\+\-\_\%\$\# ]/", $string) !== 0;
    }
    
    /**
     * Filters multiple ways.
     * @param   Array $data Data to be filtered.
     * @param   Array $req  Required fields
     * @param   Array $opt  Optional fields
     * @return  Array       Filtered array.
     */
    public function filterVarData($data, $req, $opt) {
        $filtered = array();
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $filtered['id'] = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
        }
        $whichFilter = array("em" => FILTER_SANITIZE_EMAIL, "int" => FILTER_SANITIZE_NUMBER_INT, "str" => FILTER_SANITIZE_STRING, "date" => FILTER_SANITIZE_STRING, "bool" => FILTER_SANITIZE_NUMBER_INT);
        $all = array_merge($req, $opt);
        //$keyReq name of input and valReq type of
        foreach ($all as $keyReq => $valReq) {
            if (isset($data[$keyReq])) {
                if ($keyReq === 'description') {
                    $data['description'] = strip_tags($data['description'], "<h1><h2><h3><h4><h5><h6><p><a><li><small><ul><ol><q><s><em><strong><sub><span><code><section><u><sup>");
                    $filtered[$keyReq] = $data['description'];
                } else {
                    $filtered[$keyReq] = filter_var($data[$keyReq], $whichFilter[$valReq]);
                }
            }
        }
        return $filtered;
    }
    
    /**
     * Get the contents from a JSON file.
     * @param   String              $fileName   Name of the file to be read.
     * @param   String|Boolean|null $folder     Name of the folder of the file if falsey wont be added.
     * @param   String              $mainFolder Name of the parent folder it is in.
     * @return  Array                           JSON decoded array.
     */
    public function getJsonFromFile($fileName, $folder, $mainFolder = 'public') {
        $returnArray = array();
        $location = ROOT . DS . $mainFolder . DS;
        if ($folder) {
            $location .= $folder . DS;
        }
        $location .=  $fileName . '.json';
        if (file_exists($location)) {
            $contents = file_get_contents($location);
            $returnArray = json_decode($contents, true);
        }
        return $returnArray;
    }
    
    /**
     * Check if data is correct according to field type
     * @param String $field_type A String, allowed: "str", "int", "date", "dati", "bool", "em"
     * @param String $var        The variable that needs to be checked.
     * @return Boolean
     */
    public function checkIfDataIsCorrect($field_type, $var) {
        return (($field_type == "str" && strlen($var) > 0)
            || ($field_type == "int" && is_numeric($var) )
            || ($field_type == "date" && $var != "" && $this->validateDate($var, 'Y-m-d'))
            || ($field_type == "dati" && $var != "" && $this->validateDate($var))
            || ($field_type == "bool")
            || ($field_type == "em" && filter_var($var, FILTER_VALIDATE_EMAIL) ) );
    }
    
        
    /**
     * Backward compatibility function.
     * @param   Array $arr Rewrite array for php versie 5.3+
     * @return  Array           Rewritten array.
     **/
    public function refValues($arr) {
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            // Reference is required for PHP 5.3+
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }
}
