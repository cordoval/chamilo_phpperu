<?php
/**
 * @package common.webservice.test.provider
 */
class InputUser
{
    const PROPERTY_ID = 'id';
    
    private $defaultProperties;

    function InputUser($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID);
    }

    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }
}
?>