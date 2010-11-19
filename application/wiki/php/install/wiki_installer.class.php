<?php
namespace application\wiki;

use common\libraries\Installer;


/**
 * $Id: wiki_installer.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.install
 */

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
    function __construct($values)
    {
        parent :: __construct($values, WikiDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>