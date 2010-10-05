<?php
/**
 * $Id: linker_installer.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker.install
 */
require_once dirname(__FILE__) . '/../lib/linker_data_manager.class.php';
/**
 * This installer can be used to create the storage structure for the
 * linker application.
 */
class LinkerInstaller extends Installer
{

    /**
     * Constructor
     */
    function LinkerInstaller($values)
    {
        parent :: __construct($values, LinkerDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>