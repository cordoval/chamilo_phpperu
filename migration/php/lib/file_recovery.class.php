<?php
/**
 * $Id: $
 * @package migration.lib
 */
/**
 *	@author Sven Vanpoucke
 */

class FileRecovery extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_OLD_PATH = 'old_path';
    const PROPERTY_NEW_PATH = 'new_path';

    function get_old_path()
    {
        return $this->get_default_property(self :: PROPERTY_OLD_PATH);
    }

    function set_old_path($old_path)
    {
        $this->set_default_property(self :: PROPERTY_OLD_PATH, $old_path);
    }

    function get_new_path()
    {
        return $this->get_default_property(self :: PROPERTY_NEW_PATH);
    }

    function set_new_path($new_path)
    {
        $this->set_default_property(self :: PROPERTY_NEW_PATH, $new_path);
    }

    /**
     * Get the default properties of all migrations.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_OLD_PATH, self :: PROPERTY_NEW_PATH));
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