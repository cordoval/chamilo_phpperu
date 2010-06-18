<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mediamosa_streaming_media_server_objectclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaServerObject{

    //id, url, name, login, password, is_upload_possible
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_LOGIN = 'login';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_IS_UPLOAD_POSSIBLE = 'is_upload_possible';

    /**
     * @return the $default_properties
     */
    public function get_default_properties()
    {
        return $this->default_properties;
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
     * @param $default_properties the $default_properties to set
     */
    public function set_default_properties($default_properties)
    {
        $this->default_properties = $default_properties;
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
     * Get the default properties of all data classes.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_ID;
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_LOGIN;
        $extended_property_names[] = self :: PROPERTY_PASSWORD;
        $extended_property_names[] = self :: PROPERTY_IS_UPLOAD_POSSIBLE;

        return $extended_property_names;
    }

	public function get_additional_properties()
    {
        return $this->additional_properties;
    }

	/**
     * @param $additional_properties the $additional_properties to set
     */
    public function set_additional_properties($additional_properties)
    {
        $this->additional_properties = $additional_properties;
    }

    /**
     * Sets an additional (type-specific) property of this learning object by
     * name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_additional_property($name, $value)
    {
        //$this->check_for_additional_properties();
        $this->additional_properties[$name] = $value;
    }

    /**
     * Gets an additional (type-specific) property of this learning object by
     * name.
     * @param string $name The name of the property.
     */
    function get_additional_property($name)
    {
        return $this->additional_properties[$name];
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_login($lgon)
    {
        $this->set_default_property(self :: PROPERTY_LOGIN, $login)
    }

    function get_login()
    {
        return $this->get_default_property(self :: PROPERTY_LOGIN);
    }

    function set_password($password)
    {
        $this->set_default_property(self :: PROPERTY_PASSWORD, $password);
    }

    function get_password()
    {
        return $this->get_default_property(self :: PROPERTY_PASSWORD);
    }
    
    function set_is_upload_possible($is_upload_possible)
    {
        $this->set_default_property(self :: PROPERTY_IS_UPLOAD_POSSIBLE, $is_upload_possible);
    }
    
    function get_is_upload_possible()
    {
        return $this->get_default_property(self :: PROPERTY_IS_UPLOAD_POSSIBLE);
    }
}
?>
