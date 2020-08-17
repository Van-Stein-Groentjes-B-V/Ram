<?php

/**
 * View file. Holds all the values of the view that is loaded
 * Handles the view functionality of our MVC framework.
 *
 * PHP version 7+
 *
 * @category   Library
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
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
 */

namespace SG\Ram;

/**
 * View
 * @category   Library
 * @package    Ram
 */
class View
{
    private $_data = array();
    private $_render = false;
    
    /**
     * Constructing the receiving data.
     * @return  Void.
     */
    public function __construct() {
        $this->_data['site_title'] = '';
        $this->_data['site_icon'] = '';
        $this->_data['author'] = 'Van Stein & Groentjes B.V.';
        $this->_data['meta_description'] = '';
        $this->_data['meta_keywords'] = '';
        $this->_data['no_index'] = '';
        $this->_data['css'] = '';
        $this->_data['js'] = '';
        $this->_data['js_footer'] = '';
        $this->_data['header'] = '';
        $this->_data['custom_header'] = '';
        $this->_data['carousel'] = '';
        $this->_data['navbar'] = '';
        $this->_data['sidebar'] = '';
        $this->_data['breadcrums'] = '';
        $this->_data['precontent'] = array();
        //must be printed before content
        $this->_data['content'] = '';
        //must be printed after content
        $this->_data['postcontent'] = array();
        $this->_data['footer'] = '';
        $this->_data['page'] = 'Dashboard';
        $this->_data['settings'] = array();
    }
    
    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
    }
    
    /**
     * Assign function assigns a value to the data array that is accessible from the
     * view. This value is added associatively
     * @param   String $variable Variable to set.
     * @param   String $value    Value to link to variable.
     * @return  Void.
     */
    public function assign($variable, $value) {
        if ($variable == '') {
            $this->_data = $value;
        } else {
            if ($variable == 'precontent' || $variable == 'postcontent') {
                $this->_data[$variable] = array_merge($this->_data[$variable], $value);
            } else {
                $this->_data[$variable] = $value;
            }
        }
    }

    /**
     * Rendering the page.
     * @param   String  $view          Page to render.
     * @param   Boolean $direct_output Output the page directly or wait.
     * @return  Void.                       Rendered view or void.
     */
    public function render($view, $direct_output = false) {
        if (substr($view, -4) == ".php") {
            $file = $view;
        } else {
            $file = ROOT . DS . "views" . DS . strtolower($view) . ".php";
        }
        
        if (file_exists($file)) {
            /**
             * trigger render to include file when this model is destroyed
             * if we render it now, we wouldn't be able to assign variables
             * to the view!
             */
            $this->_render = $file;
        } else {
            $moduleName = explode("/", $view);
            $file = ROOT . DS . "modules" . DS . "module_" . $moduleName[0] . DS . "views" . DS . strtolower($view) . ".php";
            if (!file_exists($file)) {
                echo $file . "File doesn't exists";
            } else {
                /**
                * trigger render to include file when this model is destroyed
                * if we render it now, we wouldn't be able to assign variables
                * to the view!
                */
                $this->_render = $file;
            }
        }

        // Turn output buffering on, capturing all output
        if ($direct_output !== true) {
            ob_start();
        }

        // Parse data variables into local variables
        $data = $this->_data;
        // Get template
        include($this->_render);
        // Get the contents of the buffer and return it
        if ($direct_output !== true) {
            return ob_get_clean();
        }
    }
    
    /**
     * Get the output view.
     * Renders all elements from the view.
     * @return Void.
     */
    public function outPutView() {
        $arr = array('header','custom_header','carousel','navbar','sidebar','breadcrums','precontent','content','postcontent','footer');
        foreach ($arr as $torender) {
            if (is_array($this->_data[$torender])) {
                sort($this->_data[$torender]);
                foreach ($this->_data[$torender] as $toOuput) {
                    echo $this->_render($toOuput, true);
                }
            } else {
                echo $this->_data[$torender];
            }
        }
    }

    /**
     * Set site title.
     * @param   String $name The title to set.
     * @return  Void.
     */
    public function setSiteTitle($name) {
        $this->_data['site_title'] = '' . $name . '';
    }

    /**
     * Set site icon.
     * @param   String $filename Filename of icon to set.
     * @param   String $website  Path to image/icon.
     * @return  Void.
     */
    public function setSiteIcon($filename, $website = SITE_ROOT) {
        $this->_data['site_icon'] = "<link href='" . $website . "public/img/" . $filename . "' rel='shortcut icon' type='image/vnd.microsoft.icon' />";
    }

    /**
     * Set author.
     * @param   String $name Name of the author.
     * @return  Void.
     */
    public function setAuthor($name) {
        $this->_data['author'] = '' . $name . '';
    }

    /**
     * Set meta keywords
     * @param   String $words Meta keywords to set in the view for SEO.
     * @return  Void.
     */
    public function setMetaKeywords($words) {
        $this->_data['meta_keywords'] = "<meta name='keywords' content='" . $words  . "' />";
    }
    
    /**
     * Set meta description.
     * @param String $descr Meta description for SEO.
     * @return Void.
     */
    public function setMetaDescription($descr) {
        $this->_data['meta_description'] = '<meta name="description" content="' . $descr . '" />';
    }

    /**
     * Include CSS file.
     * @param String $filename Filename of CSS file to include.
     * @param String $website  Path to CSS file.
     * @return Void.
     */
    public function setCSS($filename, $website = SITE_ROOT) {
        $this->_data['css'] = $this->_data['css'] . '<link href="' . $website . 'public/css/' . str_replace('\\', '/', $filename) . '" rel="stylesheet" type="text/css" />';
    }
    
    /**
     * Include CSS module file.
     * @param String $filename Filename of CSS module file to include.
     * @param String $website  Path to CSS module file.
     * @return void.
     */
    public function setCSSMODULE($filename, $website = SITE_ROOT) {
        $this->_data['css'] = $this->_data['css'] . '<link href="' . $website . str_replace('\\', '/', $filename) . '" rel="stylesheet" type="text/css" />';
    }
    
    /**
     * Include CSS external file.
     * @param String $filename Filename of external CSS to include.
     * @param String $website  Path to CSS external file.
     * @return Void.
     */
    public function setCSSEXTERNAL($filename, $website = SITE_ROOT) {
        $this->setCSSMODULE($filename, $website);
    }
    
    /**
     * Include JS file.
     * @param String $filename Filename of JS file to include.
     * @param String $website  Path to JS file.
     * @return Void.
     */
    public function setJS($filename, $website = SITE_ROOT) {
        $this->_data['js'] = $this->_data['js'] . '<script type="text/javascript" src="' . $website . 'public/js/' . $filename . '"></script>' . PHP_EOL;
    }
    
    /**
     * Include JS module file.
     * @param String $filename Filename of JS file from module.
     * @param String $website  Path to JS module file.
     * @return Void.
     */
    public function setJSMODULE($filename, $website = SITE_ROOT) {
        $this->_data['js_footer'] = $this->_data['js_footer'] . '<script type="text/javascript" src="' . $website . $filename . '"></script>' . PHP_EOL;
    }
    
    /**
     * Include JS external file.
     * @param String $filename Filename of external JS file to include.
     * @param String $website  Path to External JS file.
     * @return Void.
     */
    public function setJSExternal($filename, $website) {
        $this->setJSMODULE($filename, $website);
    }
    
    /**
     * Include JS footer file. Special function to make sure the Javascript is added to the footer.
     * @param String $filename Filename to be included in the footer.
     * @param String $website  Path to JS footer file.
     * @return Void.
     */
    public function setJSFooter($filename, $website = SITE_ROOT) {
        $this->_data['js_footer'] = $this->_data['js_footer'] . '<script type="text/javascript" src="' . $website . 'public/js/' . $filename . '"></script>' . PHP_EOL;
    }
    
    /**
     * Include JS from tinyMCE.
     * @param String $filename Filename.
     * @param String $website  Path to JS from tinyMCE file.
     * @return Void.
     */
    public function setTINY($filename, $website = SITE_ROOT) {
        $this->_data['js'] = $this->_data['js'] . '<script type="text/javascript" src="' . $website . 'public/tinymce/' . $filename . '"></script>' . PHP_EOL;
    }

    /**
     * Include JS map file.
     * @param String $filepath File location of maps Javascript file
     * @return Void.
     */
    public function setMapsJS($filepath) {
        $this->_data['js'] = $this->_data['js'] . '<script type="text/javascript" src="' . $filepath . '"></script>' . PHP_EOL;
    }
    
    /**
     * Set no index.
     * @return Void.
     */
    public function setNoIndex() {
        $this->_data['no_index'] = '<meta name="robots" content="noindex">';
    }
    
    /**
     * Get Data.
     * @return data.
     */
    public function getData() {
        return $this->_data;
    }
    
    /**
     * Check if exists.
     * @param   Array $key Key to check.
     * @return  Boolean     True if exists
     */
    public function isReserved($key) {
        $arr = array('header','carousel','navbar','sidebar','breadcrums','precontent','content','postcontent','footer','site_title','site_icon','author','meta_description','meta_keywords','no_index', 'page');
        return in_array($key, $arr);
    }
    
    /**
     * showElement function returns the span and icon for the showVisibility files
     * @param   String $which    What element to show or hide
     * @param   Array  $settings array with settings of the inputs.
     * @return  String
     */
    public function showElement($which, $settings) {
        if (isset($settings[$which]) && !$settings[$which]) {
            $show = 'input_hide';
            $icon = 'glyphicon-eye-close';
        } else {
            $show = 'input_show';
            $icon = 'glyphicon-eye-open';
        }
        return '<span data-name="' . $which . '" class="input-group-addon ' . $show . '"><i class="glyphicon ' . $icon . '"></i></span>';
    }
}
