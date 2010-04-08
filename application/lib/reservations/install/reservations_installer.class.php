<?php
/**
 * $Id: reservations_installer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.install
 */
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class ReservationsInstaller extends Installer
{

    /**
     * Constructor
     */
    function ReservationsInstaller($values)
    {
        parent :: __construct($values, ReservationsDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
    
	function install_extra()
    {
        if (! $this->create_reservations_subtree())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ReservationsTreeCreated'));
        }
        
        return true;
    }
    
    private function create_reservations_subtree()
    {
    	return RightsUtilities :: create_subtree_root_location(ReservationsManager :: APPLICATION_NAME, 0, 'reservations_tree');
    }
}
?>