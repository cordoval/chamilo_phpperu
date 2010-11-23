<?php
use common\libraries\Path;
require_once Path :: get_plugin_path() . '/dropbox-api/Exception/Forbidden.php';
require_once Path :: get_plugin_path() . '/dropbox-api/Exception/NotFound.php';
require_once Path :: get_plugin_path() . '/dropbox-api/Exception/OverQuota.php';
require_once Path :: get_plugin_path() . '/dropbox-api/Exception/RequestToken.php';
/**
 * Dropbox base exception 
 * 
 * @package Dropbox 
 * @copyright Copyright (C) 2010 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/dropbox-php/wiki/License MIT
 */

/**
 * Base exception class
 */
class Dropbox_Exception extends Exception { }
