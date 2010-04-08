<?php
/**
 * $Id: webservice_installer.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.install
 */
/**
 * This installer can be used to create the storage structure for the
 * webservice application.
 */
class WebserviceInstaller extends Installer
{

    /**
     * Constructor
     */
    function WebserviceInstaller($values)
    {
        parent :: __construct($values, WebserviceDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
    
	function install_extra()
    {
        if (! $this->create_webservice_subtree())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('WebserviceSubTreeCreated'));
        }
        
        return true;
    }
    
    private function create_webservice_subtree()
    {
    	return RightsUtilities :: create_subtree_root_location(WebserviceManager :: APPLICATION_NAME, 0, 'webservices_tree');
    }
    
    
}
?>