<?php
namespace tracking;

use common\libraries\Installer;
/**
 * $Id: tracking_installer.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.install
 */
/**
 * This	 installer can be used to create the contentboxes structure
 */
class TrackingInstaller extends Installer
{

    /**
     * Constructor
     */
    function __construct($values)
    {
        parent :: __construct($values, TrackingDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>