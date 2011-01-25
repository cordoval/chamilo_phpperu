<?php
namespace application\package;

use common\libraries\Utilities;
use common\libraries\DataClass;

/**
 * $Id: package_author.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib
 */
/**
 * @author Hans de Bisschop
 * @author Dieter De Neef
 */

class PackageDependency extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_PACKAGE_ID = 'package_id';
    const PROPERTY_DEPENDENCY_ID = 'dependency_id';
    const PROPERTY_COMPARE = 'compare';
    const PROPERTY_SEVERITY = 'severity';
    const PROPERTY_DEPENDENCY_TYPE = 'dependency_type';

    function get_package_id()
    {
        return $this->get_default_property(self :: PROPERTY_PACKAGE_ID);
    }

    function set_package_id($package_id)
    {
        $this->set_default_property(self :: PROPERTY_PACKAGE_ID, $package_id);
    }

    function get_dependency_id()
    {
        return $this->get_default_property(self :: PROPERTY_DEPENDENCY_ID);
    }

    function set_dependency_id($dependency_id)
    {
        $this->set_default_property(self :: PROPERTY_DEPENDENCY_ID, $dependency_id);
    }

    function get_compare()
    {
        return $this->get_default_property(self :: PROPERTY_COMPARE);
    }

    function set_compare($compare)
    {
        $this->set_default_property(self :: PROPERTY_COMPARE, $compare);
    }

    function get_severity()
    {
        return $this->get_default_property(self :: PROPERTY_SEVERITY);
    }

    function set_severity($severity)
    {
        $this->set_default_property(self :: PROPERTY_SEVERITY, $severity);
    }
    
    function get_dependency_type()
    {
        return $this->get_default_property(self :: PROPERTY_DEPENDENCY_TYPE);
    }

    function set_dependency_type($dependency_type)
    {
        $this->set_default_property(self :: PROPERTY_DEPENDENCY_TYPE, $dependency_type);
    }

    /**
     * Get the default properties of all groups.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_PACKAGE_ID, 
                self :: PROPERTY_DEPENDENCY_ID, 
                self :: PROPERTY_COMPARE, 
                self :: PROPERTY_SEVERITY,
                self :: PROPERTY_DEPENDENCY_TYPE));
    }

    function get_package()
    {
        return $this->get_data_manager()->retrieve_package($this->get_package_id());
    }

    function get_dependency()
    {
        return $this->get_data_manager()->retrieve_package($this->get_dependency_id());
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PackageDataManager :: get_instance();
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }  
}
?>