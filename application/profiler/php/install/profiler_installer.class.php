<?php
/**
 * $Id: profiler_installer.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.install
 */
require_once dirname(__FILE__) . '/../lib/profiler_data_manager.class.php';
require_once dirname(__FILE__) . '/../lib/profiler_rights.class.php';
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
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ProfilerSubtreeCreated'));
        }

        return true;
    }
}
?>