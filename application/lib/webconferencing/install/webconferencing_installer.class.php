<?php
/**
 * $Id: webconferencing_installer.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.install
 */

require_once dirname(__FILE__) . '/../webconferencing_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * webconferencing application.
 * @author Stefaan Vanbillemont
 */
class WebconferencingInstaller extends Installer
{

    /**
     * Constructor
     */
    function WebconferencingInstaller($values)
    {
        parent :: __construct($values, WebconferencingDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>