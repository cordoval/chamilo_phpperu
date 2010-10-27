<?php

namespace application\distribute;

use common\libraries\WebApplication;
use common\libraries\Installer;
/**
 * $Id: distribute_installer.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.install
 */

require_once WebApplication :: get_application_class_lib_path('distribute') . 'distribute_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * distribute application.
 * @author Hans De Bisschop
 */
class DistributeInstaller extends Installer
{

    /**
     * Constructor
     */
    function DistributeInstaller($values)
    {
        parent :: __construct($values, DistributeDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>