<?php

namespace application\personal_messenger;

use common\libraries\Installer;
/**
 * $Id: personal_messenger_installer.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.install
 */
/**
 *	This installer can be used to create the storage structure for the
 * personal messenger application.
 */
class PersonalMessengerInstaller extends Installer
{

    /**
     * Constructor
     */
    function __construct($values)
    {
        parent :: __construct($values, PersonalMessengerDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>