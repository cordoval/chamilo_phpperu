<?php
/**
 * $Id: rights_installer.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.install
 */

/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class RightsInstaller extends Installer
{

    /**
     * Constructor
     */
    function RightsInstaller($values)
    {
        parent :: __construct($values, RightsDataManager :: get_instance());
    }

    function install_extra()
    {
        if (! $this->create_default_rights_templates_and_rights())
        {
            return false;
        }
        
        return true;
    }

    function create_default_rights_templates_and_rights()
    {
        $rights_template = new RightsTemplate();
        $rights_template->set_name('Anonymous');
        if (! $rights_template->create())
        {
            return false;
        }
        
        $rights_template = new RightsTemplate();
        $rights_template->set_name('Administrator');
        if (! $rights_template->create())
        {
            return false;
        }
        
        return true;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>