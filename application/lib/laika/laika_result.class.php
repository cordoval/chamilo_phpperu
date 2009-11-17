<?php
/**
 * $Id: laika_result.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */

class LaikaResult
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'result';
    
    const PROPERTY_ID = 'id';
    const PROPERTY_SCALE_ID = 'scale_id';
    const PROPERTY_RESULT = 'result';
    const PROPERTY_PERCENTILE = 'percentile';
    const PROPERTY_PERCENTILE_CODE = 'percentile_code';
    
    private $defaultProperties;

    function __construct($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets the value of a given default property
     * @param String $name the name of the default property
     * @return Object $value the new value
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gives a value to a given default property
     * @param String $name the name of the default property
     * @param Object $value the new value
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Retrieves the default properties of this class
     * @return Array of Objects
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Sets the default properties of this class
     * @param Array Of Objects $defaultProperties
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Method to check whether a certain name is a default property name
     * @param String $name
     * @return 
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Get the default property names
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_SCALE_ID, self :: PROPERTY_RESULT, self :: PROPERTY_PERCENTILE, self :: PROPERTY_PERCENTILE_CODE);
    }

    function create()
    {
        $ldm = LaikaDataManager :: get_instance();
        $id = $ldm->get_next_laika_result_id();
        $this->set_id($id);
        return $ldm->create_laika_result($this);
    }

    function update()
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->update_laika_result($this);
    }

    function delete()
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->delete_laika_result($this);
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function get_scale_id()
    {
        return $this->get_default_property(self :: PROPERTY_SCALE_ID);
    }

    function set_scale_id($scale_id)
    {
        $this->set_default_property(self :: PROPERTY_SCALE_ID, $scale_id);
    }

    function get_result()
    {
        return $this->get_default_property(self :: PROPERTY_RESULT);
    }

    function set_result($result)
    {
        $this->set_default_property(self :: PROPERTY_RESULT, $result);
    }

    function get_percentile()
    {
        return $this->get_default_property(self :: PROPERTY_PERCENTILE);
    }

    function set_percentile($percentile)
    {
        $this->set_default_property(self :: PROPERTY_PERCENTILE, $percentile);
    }

    function get_percentile_code()
    {
        return $this->get_default_property(self :: PROPERTY_PERCENTILE_CODE);
    }

    function set_percentile_code($percentile_code)
    {
        $this->set_default_property(self :: PROPERTY_PERCENTILE_CODE, $percentile_code);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>