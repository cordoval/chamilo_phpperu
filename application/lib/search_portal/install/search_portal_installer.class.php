<?php
/**
 * $Id: search_portal_installer.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.install
 */
/**
 *	This installer can be used to create the storage structure for the
 *      search portal application.
 */
class SearchPortalInstaller extends Installer
{

    /**
     * Constructor
     */
    function SearchPortalInstaller($values)
    {
        parent :: __construct($values);
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>