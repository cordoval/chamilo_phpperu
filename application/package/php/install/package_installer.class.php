<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Installer;
use common\libraries\Translation;
use rights\RightsUtilities;
use common\libraries\Utilities;
/**
 * package.install
 */

require_once WebApplication :: get_application_class_lib_path('package') . 'package_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * package application.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function __construct($values)
    {
    	parent :: __construct($values, PackageDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
	
	function install_extra()
    {
        if (! $this->create_languages_subtree())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('LanguagesTree')), Utilities :: COMMON_LIBRARIES));
        }
        
        return true;
    }
    
    private function create_languages_subtree()
    {
    	return RightsUtilities :: create_subtree_root_location(PackageManager :: APPLICATION_NAME, 0, PackageRights :: TREE_TYPE_LANGUAGES);
    }
}
?>