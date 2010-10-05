<?php
/**
 * $Id: $
 * @package migration.lib
 */
/**
 *	@author Sven Vanpoucke
 */

class IdReference extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_REFERENCE_TABLE_NAME = 'reference_table_name';
    const PROPERTY_OLD_ID = 'old_id';
    const PROPERTY_NEW_ID = 'new_id';

    function get_reference_table_name()
    {
        return $this->get_default_property(self :: PROPERTY_REFERENCE_TABLE_NAME);
    }

    function set_reference_table_name($reference_table_name)
    {
        $this->set_default_property(self :: PROPERTY_REFERENCE_TABLE_NAME, $reference_table_name);
    }

    function get_old_id()
    {
        return $this->get_default_property(self :: PROPERTY_OLD_ID);
    }

    function set_old_id($old_id)
    {
        $this->set_default_property(self :: PROPERTY_OLD_ID, $old_id);
    }
    
	function get_new_id()
    {
        return $this->get_default_property(self :: PROPERTY_NEW_ID);
    }

    function set_new_id($new_id)
    {
        $this->set_default_property(self :: PROPERTY_NEW_ID, $new_id);
    }

    /**
     * Get the default properties of all migrations.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
         return parent :: get_default_property_names(array(self :: PROPERTY_REFERENCE_TABLE_NAME, self :: PROPERTY_OLD_ID, self :: PROPERTY_NEW_ID));
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