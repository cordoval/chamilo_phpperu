<?php
namespace application\gutenberg;

use common\libraries\WebApplication;
use common\libraries\Installer;

/**
 * $Id: gutenberg_installer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application
 * @subpackage gutenberg
 */

require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'gutenberg_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * Gutenberg application.
 * @author Hans De Bisschop
 */
class GutenbergInstaller extends Installer
{

    /**
     * Constructor
     */
    function __construct($values)
    {
        parent :: __construct($values, GutenbergDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>