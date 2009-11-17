<?php
/**
 * $Id: home_block_config.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib
 */

class HomeBlockConfig extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'block_config';
    
    const PROPERTY_BLOCK_ID = 'block_id';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_BLOCK_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return HomeDataManager :: get_instance();
    }

    function get_block_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOCK_ID);
    }

    function set_block_id($block_id)
    {
        $this->set_default_property(self :: PROPERTY_BLOCK_ID, $block_id);
    }

    function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    function set_variable($variable)
    {
        $this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
    }

    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    function create()
    {
        $wdm = HomeDataManager :: get_instance();
        return $wdm->create_home_block_config($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>