<?php
abstract class VideoConferencingParticipantsObject
{
    /**
     * @var array
     */
    private $default_properties;

    /**
     * @var ExternalRepositorySync
     */
//    private $synchronization_data;

    const PROPERTY_PARTICIPANT_ID = 'id';
    const PROPERTY_TYPE = 'type';

//	const RIGHT_EDIT = 1;
//    const RIGHT_DELETE = 2;
//    const RIGHT_USE = 3;
//    const RIGHT_DOWNLOAD = 4;
//	
    /**
     * @param array $default_properties
     */
    function VideoConferencingParticipantsObject($default_properties = array ())
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Get the default properties of all data classes.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_PARTICIPANT_ID;
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
        return (isset($this->default_properties) && array_key_exists($name, $this->default_properties)) ? $this->default_properties[$name] : null;
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
        $this->default_properties[$name] = $value;
    }

    function get_default_properties()
    {
        return $this->default_properties;
    }

    public function get_participant_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARTICIPANT_ID);
    }
    
    /**
     * @param string $title
     */
    public function set_participant_id($participant_id)
    {
        $this->set_default_property(self :: PROPERTY_PARTICIPANT_ID, $participant_id);
    }
    
	public function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }
    
    /**
     * @param string $title
     */
    public function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }
}
?>