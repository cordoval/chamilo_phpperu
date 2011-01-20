<?php
namespace migration;


/**
 * $Id: dokeos185_resource.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */


/**
 * This class represents an old Dokeos 1.8.5 Resource
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Resource extends Dokeos185CourseDataMigrationDataClass
{

    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'resource';

    /**
     * Resource properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_SOURCE_TYPE = 'source_type';
    const PROPERTY_SOURCE_ID = 'source_id';
    const PROPERTY_RESOURCE_TYPE = 'resource_type';
    const PROPERTY_RESOURCE_ID = 'resource_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_SOURCE_TYPE, self :: PROPERTY_SOURCE_ID, self :: PROPERTY_RESOURCE_TYPE, self :: PROPERTY_RESOURCE_ID);
    }

    /**
     * Returns the id of this resource.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the source_type of this resource.
     * @return string the source_type.
     */
    function get_source_type()
    {
        return $this->get_default_property(self :: PROPERTY_SOURCE_TYPE);
    }

    /**
     * Returns the source_id of this resource.
     * @return int the source_id.
     */
    function get_source_id()
    {
        return $this->get_default_property(self :: PROPERTY_SOURCE_ID);
    }

    /**
     * Returns the res_type of this resource.
     * @return string the res_type.
     */
    function get_res_type()
    {
        return $this->get_default_property(self :: PROPERTY_RESOURCE_TYPE);
    }

    /**
     * Returns the resource_id of this resource.
     * @return int the resource_id.
     */
    function get_resource_id()
    {
        return $this->get_default_property(self :: PROPERTY_RESOURCE_ID);
    }

    /**
     * migrate resource, sets category
     * @param Array $array
     * @return
     */

    function convert_data()
    {
        
    }

    function is_valid()
    {

    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

}
?>