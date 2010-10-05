<?php
/**
 * $Id: gutenberg_installer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application
 * @subpackage gutenberg
 */

require_once dirname(__FILE__) . '/../lib/gutenberg_data_manager.class.php';

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
    function GutenbergInstaller($values)
    {
        parent :: __construct($values, GutenbergDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>