<?php
/**
 * $Id: webconference_option.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing
 */

/**
 * This class describes a WebconferenceOption data object
 *
 * @author Stefaan Vanbillemont
 */
class WebconferenceOption extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * WebconferenceOption properties
     */
    const PROPERTY_CONF_ID = 'conf_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONF_ID, self :: PROPERTY_NAME, self :: PROPERTY_VALUE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WebconferencingDataManager :: get_instance();
    }

    /**
     * Returns the conf_id of this WebconferenceOption.
     * @return the conf_id.
     */
    function get_conf_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONF_ID);
    }

    /**
     * Sets the conf_id of this WebconferenceOption.
     * @param conf_id
     */
    function set_conf_id($conf_id)
    {
        $this->set_default_property(self :: PROPERTY_CONF_ID, $conf_id);
    }

    /**
     * Returns the name of this WebconferenceOption.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this WebconferenceOption.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the value of this WebconferenceOption.
     * @return the value.
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value of this WebconferenceOption.
     * @param value
     */
    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>