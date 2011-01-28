<?php
namespace application\package;

use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\InCondition;
use common\libraries\Translation;

use admin;

/**
 * This class describes a Package data object
 *
 * $Id: remote_package.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib
 * @author Hans De Bisschop
 */
class Dependency extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_NAME = 'name';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_TYPE = 'type';
    
    const TYPE_EXTENSION = 1;
    const TYPE_SERVER = 2;
    const TYPE_SETTINGS = 3;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, 
                self :: PROPERTY_VERSION, 
                self :: PROPERTY_TYPE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PackageDataManager :: get_instance();
    }

    /**
     * Returns the id of this Package.
     * @return the id.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the id of this Package.
     * @param id
     */
    function set_name($id)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $id);
    }

    /**
     * Returns the version of this Package.
     * @return the version.
     */
    function get_version()
    {
        return $this->get_default_property(self :: PROPERTY_VERSION);
    }

    /**
     * Sets the version of this Package.
     * @param version
     */
    function set_version($version)
    {
        $this->set_default_property(self :: PROPERTY_VERSION, $version);
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    static function get_type_name($type)
    {
        switch ($type)
        {
            case self :: TYPE_EXTENSION :
                return Translation :: get('Extension');
                break;
            case self :: TYPE_SERVER :
                return Translation :: get('Server');
                break;
            case self :: TYPE_SETTINGS :
                return Translation :: get('Settings');
                break;
        }
    }
    
    static function get_types()
    {
        $types = array();
        $types[self :: TYPE_EXTENSION] = self :: get_type_name(self :: TYPE_EXTENSION);
        $types[self :: TYPE_SERVER] = self :: get_type_name(self :: TYPE_SERVER);
        $types[self :: TYPE_SETTINGS] = self :: get_type_name(self :: TYPE_SETTINGS);
        
        return $types;
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function get_packages($only_ids = true)
    {
        $condition = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_ID, $this->get_id());
        $package_dependencies = $this->get_data_manager()->retrieve_package_dependencies($condition);
        $package_dependencies_ids = array();
        while ($package_dependency = $package_dependencies->next_result())
        {
            $package_dependencies_ids[] = $package_dependency->get_package_id();
        }
        
        if ($only_ids)
        {
            return $package_dependencies_ids;
        }
        else
        {
            $package_dependencies_ids[] = - 1;
            $condition = new InCondition(Package :: PROPERTY_ID, $package_dependencies_ids);
            return $this->get_data_manager()->retrieve_packages($condition);
        }
    }
}
?>