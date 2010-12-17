<?php
namespace application\package;

use common\libraries\DataClass;

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
    
    const PROPERTY_ID = 'id';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_SEVERITY = 'severity';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ID, self :: PROPERTY_VERSION, self :: PROPERTY_SEVERITY));
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
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this Package.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
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
    function get_company()
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
}

?>