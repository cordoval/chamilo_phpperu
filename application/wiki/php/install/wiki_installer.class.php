<?php
/**
 * $Id: wiki_installer.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.install
 */
require_once WebApplication :: get_application_class_lib_path('wiki') . 'wiki_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * wiki application.
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiInstaller extends Installer
{

    /**
     * Constructor
     */
    function WikiInstaller($values)
    {
        parent :: __construct($values, WikiDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>