<?php
/**
 * $Id: phrases_installer.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.install
 */
require_once dirname(__FILE__) . '/../lib/phrases_data_manager.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal calendar application.
 */
class PhrasesInstaller extends Installer
{

    /**
     * Constructor
     */
    function PhrasesInstaller($values)
    {
        parent :: __construct($values, PhrasesDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>