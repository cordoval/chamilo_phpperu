<?php 

namespace application\reservations;

use common\libraries\WebApplication;
use rights\RightsUtilities;
use common\libraries\Installer;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: reservations_installer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.install
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_data_manager.class.php';

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
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('ReservationsTree')), Utilities :: COMMON_LIBRARIES));
        }
        
        return true;
    }
    
    private function create_reservations_subtree()
    {
    	return RightsUtilities :: create_subtree_root_location(ReservationsManager :: APPLICATION_NAME, 0, ReservationsRights :: TREE_TYPE_RESERVATIONS);
    }
}
?>