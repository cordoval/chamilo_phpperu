<?php
abstract class ExternalRepositoryObject
{
    /**
     * @var array
     */
    private $default_properties;

    /**
     * @var array
     */
    private $additional_properties;

    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_TYPE = 'type';

    /**
     * @param array $defaultProperties
     */
    function ExternalRepositoryObject($defaultProperties = array ())
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
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_DESCRIPTION;
        $extended_property_names[] = self :: PROPERTY_OWNER_ID;
        $extended_property_names[] = self :: PROPERTY_CREATED;
        $extended_property_names[] = self :: PROPERTY_TYPE;
        return $extended_property_names;
    }

    /**
     * Gets a default property of this data class object by name.
     * @param string $name The name of the property.
     * @param mixed
     */
    function get_default_property($name)
    {
        return (isset($this->defaultProperties) && array_key_exists($name, $this->defaultProperties)) ? $this->defaultProperties[$name] : null;
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
     * @return string
     */
    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * @return string
     */
    public function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * @return string
     */
    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * @return string
     */
    public function get_owner_id()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    /**
     * @return int
     */
    public function get_created()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    /**
     * @return string
     */
    public function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * @param string $title
     */
    public function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * @param string $id
     */
    public function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * @param string $description
     */
    public function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * @param string $owner_id
     */
    public function set_owner_id($owner_id)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
    }

    /**
     * @param int $created
     */
    public function set_created($created)
    {
        $this->set_default_property(self :: PROPERTY_CREATED, $created);
    }

    /**
     * @param string $type
     */
    public function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * Gets the name of the icon corresponding to this external_repository object.
     */
    function get_icon_name()
    {
        return $this->get_type();
    }

    function get_icon_image()
    {
        $src = Theme :: get_common_image_path() . 'external_repository/' . $this->get_icon_name() . '.png';
        return '<img src="' . $src . '" alt="' . htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($this->get_type()))) . '" />';
    }
}
?>