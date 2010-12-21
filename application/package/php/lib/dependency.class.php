<?php
namespace application\package;

use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\InCondition;

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
    
    const PROPERTY_ID_DEPENDENCY = 'id_dependency';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_SEVERITY = 'severity';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ID_DEPENDENCY, 
                self :: PROPERTY_VERSION, 
                self :: PROPERTY_SEVERITY));
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
    function get_id_dependency()
    {
        return $this->get_default_property(self :: PROPERTY_ID_DEPENDENCY);
    }

    /**
     * Sets the id of this Package.
     * @param id
     */
    function set_id_dependency($id)
    {
        $this->set_default_property(self :: PROPERTY_ID_DEPENDENCY, $id);
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

    /**
     * Returns the severity of this Package.
     * @return the severity.
     */
    function get_severity()
    {
        return $this->get_default_property(self :: PROPERTY_SEVERITY);
    }

    /**
     * Sets the severity of this Package.
     * @param severity
     */
    function set_severity($severity)
    {
        $this->set_default_property(self :: PROPERTY_SEVERITY, $severity);
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
            $package_dependencies_ids[] = $package_dependencies->get_package_id();
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