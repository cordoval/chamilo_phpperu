<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;

use common\libraries\Path;

require_once Path :: get_plugin_path(__NAMESPACE__) . 'dropbox-api/Exception.php';
/**
 * Dropbox Over Quota exception
 * 
 * @package Dropbox 
 * @copyright Copyright (C) 2010 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/dropbox-php/wiki/License MIT
 */

/**
 * This exception is thrown when the operation required more space than the available quota.
 * 
 * Basically, this exception is used when we get back a 507.
 */
class Dropbox_Exception_OverQuota extends Dropbox_Exception {


}
