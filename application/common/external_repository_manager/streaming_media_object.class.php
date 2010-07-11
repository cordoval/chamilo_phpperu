<?php
abstract class StreamingMediaObject
{
	private $default_properties;
	private $additional_properties;
	
	const PROPERTY_TITLE = 'title'; 
	const PROPERTY_ID = 'id';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_URL = 'url';
	const PROPERTY_DURATION = 'duration';
	const PROPERTY_THUMBNAIL = 'thumbnail';
	const PROPERTY_STATUS = 'status';
	const STATUS_AVAILABLE = 1;
	const STATUS_UNAVAILABLE = 2;


	function StreamingMediaObject($id, $title,$description, $url, $duration, $thumbnail, $status)
	{
		$this->set_id($id);
		$this->set_title($title);
		$this->set_description($description);
		$this->set_url($url);
		$this->set_duration($duration);
		$this->set_thumbnail($thumbnail);
		$this->set_status($status);
	}
	
	/**
     * Returns a string representation of the type of this learning object.
     * @return string The type.
     */
    abstract function get_type();

    public function get_status_text()
    {
    	switch ($this->get_status())
    	{
    		case self :: STATUS_AVAILABLE :
    			return Translation :: get('Available');
    			break;
    		case self :: STATUS_UNAVAILABLE :
    			return Translation :: get('Unavailable');
    			break;
    		default : return Translation :: get('Unknown');
    	}
    }
    
    function is_usable()
    {
    	if ($this->get_status() == self :: STATUS_AVAILABLE)
    	{
    		return true;
    	}
    	else 
    	{
    		return false;
    	}
    }
    
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
        $extended_property_names[] = self :: PROPERTY_DESCRIPTION;
        $extended_property_names[] = self :: PROPERTY_DURATION;
        $extended_property_names[] = self :: PROPERTY_THUMBNAIL;
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_URL;
        $extended_porperty_names[] = self :: PROPERTY_STATUS;
        
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
    
	/**
     * @return the $title
     */
    /**
     * @return the $thumbnail
     */
    public function get_thumbnail()
    {
        return $this->get_default_property(self :: PROPERTY_THUMBNAIL);
    }

	/**
     * @param $thumbnail the $thumbnail to set
     */
    public function set_thumbnail($thumbnail)
    {
        $this->set_default_property(self :: PROPERTY_THUMBNAIL, $thumbnail);
    }

	public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

	/**
     * @return the $id
     */
    public function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

	/**
     * @return the $description
     */
    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

	/**
     * @return the $url
     */
    public function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

	/**
     * @return the $duration
     */
    public function get_duration()
    {
        return $this->get_default_property(self :: PROPERTY_DURATION);
    }

    public function get_status()
    {
    	return $this->get_default_property(self :: PROPERTY_STATUS);	
    }
    
	/**
     * @param $title the $title to set
     */
    public function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

	/**
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

	/**
     * @param $description the $description to set
     */
    public function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

	/**
     * @param $url the $url to set
     */
    public function set_url($url)
    {
		$this->set_default_property(self :: PROPERTY_URL, $url);    
    }

	public function set_status($status)
	{
		$this->set_default_property(self :: PROPERTY_STATUS, $status);
	}	
	/**
     * @param $duration the $duration to set
     */
    public function set_duration($duration)
    {
        $this->set_default_property(self :: PROPERTY_DURATION, $duration);
    }
    
//    static function get_sort_properties()
//    {
//    	return array(self :: PROPERTY_DESCRIPTION, self :: PROPERTY_DURATION, self :: PROPERTY_TITLE);
//    }
}
?>