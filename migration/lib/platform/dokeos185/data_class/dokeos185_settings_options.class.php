<?php
/**
 * $Id: dokeos185_settings_options.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_settings_options.class.php';

/**
 * This class presents a Dokeos185 settings_options
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SettingsOptions extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185SettingsOptions properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_DISPLAY_TEXT = 'display_text';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185SettingsOptions object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185SettingsOptions($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE, self :: PROPERTY_DISPLAY_TEXT);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this Dokeos185SettingsOptions.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the variable of this Dokeos185SettingsOptions.
     * @return the variable.
     */
    function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    /**
     * Returns the value of this Dokeos185SettingsOptions.
     * @return the value.
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Returns the display_text of this Dokeos185SettingsOptions.
     * @return the display_text.
     */
    function get_display_text()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_TEXT);
    }

    /**
     * Checks if a settingsoptions is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
    
    }

    /**
     * migrate settingsoption, sets category
     * @param Array $array
     * @return
     */
    function convert_data
    {
    
    }

    /**
     * Gets all the setting options of a course
     * @param Array $array
     * @return Array of dokeos185settingoption
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'settings_options';
        $classname = 'Dokeos185SettingsOptions';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'settings_options';
        return $array;
    }
}

?>