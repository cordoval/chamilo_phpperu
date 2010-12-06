<?php
namespace repository;

use common\extensions\external_repository_manager;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Installer;
use common\libraries\Utilities;
use common\extensions\external_repository_manager\ExternalRepositoryManager;

require_once Path :: get_admin_path() . 'lib/package_installer/source/package_info/package_info.class.php';
require_once Path :: get_repository_path() . 'lib/content_object_installer.class.php';
/**
 * $Id: repository_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
/**
 * This	 installer can be used to create the storage structure for the
 * repository.
 */
class RepositoryInstaller extends Installer
{

    /**
     * Constructor
     */
    function __construct($values)
    {
        parent :: __construct($values, RepositoryDataManager :: get_instance());
    }

    /**
     * Runs the install-script. After creating the necessary tables to store the
     * common learning object information, this function will scan the
     * directories of all learning object types. When an XML-file describing a
     * storage unit is found, this function will parse the file and create the
     * storage unit.
     */
    function install_extra()
    {
    	$rdm = $this->get_data_manager();

        //    	$dir = dirname(__FILE__) . '/../lib/content_object';
        //        // Register the learning objects
        //        $folders = Filesystem :: get_directory_content($dir, Filesystem :: LIST_DIRECTORIES, false);
        //
        //        foreach ($folders as $folder)
        //        {
        //            $content_object = ContentObjectInstaller::factory($folder);
        //            if ($content_object)
        //            {
        //            	$content_object->install();
        //            	$this->add_message(Installer::TYPE_NORMAL, $content_object->retrieve_message());
        //            }
        //        }


        if (! RepositoryRights :: create_content_objects_subtree_root_location())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ContentObjectsSubtreeCreated'));
        }

        if (! RepositoryRights :: create_external_instances_subtree_root_location())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ExternalInstancesSubtreeCreated'));
        }

        return true;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>