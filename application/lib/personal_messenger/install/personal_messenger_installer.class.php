<?php
/**
 * $Id: personal_messenger_installer.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.install
 */
require_once dirname(__FILE__) . '/../personal_messenger_data_manager.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * personal messenger application.
 */
class PersonalMessengerInstaller extends Installer
{

    /**
     * Constructor
     */
    function PersonalMessengerInstaller($values)
    {
        parent :: __construct($values, PersonalMessengerDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>