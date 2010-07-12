<?php
/**
 * $Id: $
 * @package migration.lib
 */
/**
 *	@author Sven Vanpoucke
 */

class MigrationBlockRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_NAME = 'name';
    const PROPERTY_IS_MIGRATED = 'is_migrated';

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function get_want_to_migrate()
    {
        return $this->get_default_property(self :: PROPERTY_WANT_TO_MIGRATE);
    }

    function set_want_to_migrate($want_to_migrate)
    {
        $this->set_default_property(self :: PROPERTY_WANT_TO_MIGRATE, $want_to_migrate);
    }
    
	function get_is_migrated()
    {
        return $this->get_default_property(self :: PROPERTY_IS_MIGRATED);
    }

    function set_is_migrated($is_migrated)
    {
        $this->set_default_property(self :: PROPERTY_IS_MIGRATED, $is_migrated);
    }

    /**
     * Get the default properties of all migrations.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
         return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_IS_MIGRATED));
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