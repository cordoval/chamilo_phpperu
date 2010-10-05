<?php
/**
 * $Id: alexia_installer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.install
 */

require_once dirname(__FILE__) . '/../lib/alexia_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * Alexia application.
 * @author Hans De Bisschop
 */
class AlexiaInstaller extends Installer
{

    /**
     * Constructor
     */
    function AlexiaInstaller($values)
    {
        parent :: __construct($values, AlexiaDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>