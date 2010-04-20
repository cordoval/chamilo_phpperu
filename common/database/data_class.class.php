<?php
/**
 * $Id: data_class.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common
 */

abstract class DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_ID = 'id';
    const NO_UID = - 1;

    /**
     * Default properties of the data class object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Optional properties of the data class object, stored in an associative
     * array. This is used when retrieving data from joins so we don't need to execute other query's for retrieving optional data which we already retrieved with joins.
     * @var array[String] = String
     */
    private $optionalProperties;

    private $errors;

    /**
     * Creates a new data class object.
     * @param array $defaultProperties The default properties of the data class
     *                                 object. Associative array.
     */
    function DataClass($defaultProperties = array (), $optionalProperties = array())
    {
        $this->defaultProperties = $defaultProperties;
        $this->optionalProperties = $optionalProperties;
    }

    /**
     * Gets a default property of this data class object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return (isset($this->defaultProperties) && array_key_exists($name, $this->defaultProperties))
        	? $this->defaultProperties[$name]
        	: null;
    }

    /**
     * Gets the default properties of this data class.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Get the default properties of all data classes.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_ID;
        return $extended_property_names;
    }

    /**
     * Sets a default property of this data class by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Checks if the given identifier is the name of a default data class
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

	/**
     * Gets the optional properties of this data class.
     * @return array An associative array containing the properties.
     */
    function get_optional_properties()
    {
        return $this->optionalProperties;
    }

    function set_optional_properties($optionalProperties)
    {
        $this->optionalProperties = $optionalProperties;
    }

	/**
     * Gets a optional property of this data class object by name.
     * @param string $name The name of the property.
     */
    function get_optional_property($name)
    {
        return (isset($this->optionalProperties) && array_key_exists($name, $this->optionalProperties))
        	? $this->optionalProperties[$name]
        	: null;
    }

	/**
     * Sets a optional property of this data class by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_optional_property($name, $value)
    {
        $this->optionalProperties[$name] = $value;
    }

    /**
     * Returns the id of this data class
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets id of the data class
     * @param int $id
     */
    function set_id($id)
    {
        if (isset($id) && strlen($id) > 0)
        {
            $this->set_default_property(self :: PROPERTY_ID, $id);
        }
    }

    function is_identified()
    {
        $id = $this->get_id();
        return isset($id) && strlen($id) > 0 && $id != self :: NO_UID;
    }

    function save()
    {
        if ($this->is_identified())
        {
            return $this->update();
        }
        else
        {
            return $this->create();
        }
    }

    function get_object_name()
    {
        return Utilities :: camelcase_to_underscores(get_class($this));
    }

    function create()
    {
        if ($this->check_before_save())
        {
            $dm = $this->get_data_manager();
            $class_name = $this->get_object_name();

//          $func = 'get_next_' . $class_name . '_id';
//          $id = call_user_func(array($dm, $func));
//          $this->set_id($id);

            $func = 'create_' . $class_name;
            return call_user_func(array($dm, $func), $this);
        }
        return false;
    }

    function update()
    {
        if ($this->check_before_save())
        {
            $dm = $this->get_data_manager();
            $class_name = $this->get_object_name();
	
            $func = 'update_' . $class_name;
            return call_user_func(array($dm, $func), $this);
        }
        return false;
    }

    function delete()
    {
        $dm = $this->get_data_manager();
        $class_name = $this->get_object_name();

        $func = 'delete_' . $class_name;
        return call_user_func(array($dm, $func), $this);
    }

	/**
     * Check wether the object contains all mandatory properties to be saved in datasource
     * This method should be overriden in classes inheriting from DataClass
     *
     * @return boolean Return true if the object can be saved, false otherwise
     */
    protected function check_before_save()
    {
        /*
         * Example: object with mandatory title
         *
         * if(StringUtilities :: is_null_or_empty($this->get_title()))
         * {
         *    $this->add_error(Translation :: get('TitleIsRequired'));
         * }
         *
         */

        return !$this->has_errors();
    }

    public function add_error($error_msg)
    {
        if (!isset($this->errors))
        {
            $this->errors = array();
        }

        $this->errors[] = $error_msg;
    }

    public function has_errors()
    {
        return isset($this->errors) && count($this->errors) > 0;
    }

    public function get_errors()
    {
        return isset($this->errors) ? $this->errors : array();
    }

    public function clear_errors()
    {
        unset($this->errors);
    }

    abstract function get_data_manager();

    static function is_extended_type()
    {
        return false;
    }
}
?>