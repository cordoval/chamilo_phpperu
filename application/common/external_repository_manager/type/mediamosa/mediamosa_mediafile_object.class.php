<?php
/**
 * Description of mediamosa_transcoding_profile
 *
 * @author jevdheyd
 */
class MediamosaMediafileObject {

    private $default_properties;

    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_PARENT = 'parent';
    const PROPERTY_URL = 'url';
    //const PROPERTY_TAGS = 'tags';
    const PROPERTY_IS_DEFAULT = 'is_default';
    const PROPERTY_IS_DOWNLOADABLE = 'is_downloadable';

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
        $extended_property_names[] = self :: PROPERTY_PARENT;
        $extended_property_names[] = self :: PROPERTY_URL;
        $extended_property_names[] = self :: PROPERTY_WIDTH;
        $extended_property_names[] = self :: PROPERTY_TAGS;
        $extended_property_names[] = self :: PROPERTY_IS_DEFAULT;
        $extended_property_names[] = self :: PROPERTY_IS_DOWNLOADABLE;

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

    function set_parent($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT, $parent);
    }

    function get_parent()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT);
    }

    function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    /*function set_tags($tags)
    {
        $this->set_default_property(self :: PROPERTY_TAGS, $tags);
    }

    function get_tags()
    {
        return $this->get_default_property(self :: PROPERTY_TAGS);
    }*/

    

    function set_is_default($is_default = true)
    {
        $this->set_default_property(self :: PROPERTY_IS_DEFAULT, $is_default);
    }


    function get_is_default()
    {
        return $this->get_default_property(self :: PROPERTY_IS_DEFAULT);
    }

    function set_is_downloadable($is_downloadable = true)
    {
        $this->set_default_property(self ::PROPERTY_IS_DOWNLOADABLE, $is_downloadable);
    }

    function get_is_downloadable()
    {
        return $this->get_default_property(self :: PROPERTY_IS_DOWNLOADABLE);
    }

}
?>
