<?php
/**
 * $Id: $
 * @package migration.lib
 */
/**
 *	@author Sven Vanpoucke
 */

class FailedElement extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_FAILED_TABLE_NAME = 'failed_table_name';
    const PROPERTY_FAILED_ID = 'failed_id';

    function get_failed_table_name()
    {
        return $this->get_default_property(self :: PROPERTY_FAILED_TABLE_NAME);
    }

    function set_failed_table_name($failed_table_name)
    {
        $this->set_default_property(self :: PROPERTY_FAILED_TABLE_NAME, $failed_table_name);
    }

    function get_failed_id()
    {
        return $this->get_default_property(self :: PROPERTY_FAILED_ID);
    }

    function set_failed_id($failed_id)
    {
        $this->set_default_property(self :: PROPERTY_FAILED_ID, $failed_id);
    }

    /**
     * Get the default properties of all migrations.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_FAILED_TABLE_NAME, self :: PROPERTY_FAILED_ID));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return MigrationDataManager :: get_instance();
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>