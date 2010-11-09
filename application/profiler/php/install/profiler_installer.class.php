<?php

namespace application\profiler;

use common\libraries\WebApplication;
use common\libraries\Installer;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: profiler_installer.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.install
 */
require_once WebApplication :: get_application_class_lib_path('profiler') . 'profiler_data_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('profiler') . 'profiler_rights.class.php';
/**
 * This installer can be used to create the storage structure for the
 * profiler application.
 */
class ProfilerInstaller extends Installer
{

    /**
     * Constructor
     */
    function ProfilerInstaller($values)
    {
        parent :: __construct($values, ProfilerDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
    function install_extra()
    {
    	if (!ProfilerRights :: create_profiler_subtree_root_location())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('ProfilerSubtree')) , Utilities :: COMMON_LIBRARIES));
        }

        return true;
    }
}
?>