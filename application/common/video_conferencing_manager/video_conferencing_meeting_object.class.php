<?php
abstract class VideoConferencingMeetingObject
{
    /**
     * @var array
     */
    private $default_properties;

    /**
     * @var ExternalRepositorySync
     */
//    private $synchronization_data;

    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_START_TIME = 'start_time';
	const PROPERTY_START_DATE = 'start_date';
	const PROPERTY_END_TIME = 'end_time';
	const PROPERTY_END_DATE = 'end_date';
	const PROPERTY_PARITCIPANTS = 'participants';

//	const RIGHT_EDIT = 1;
//    const RIGHT_DELETE = 2;
//    const RIGHT_USE = 3;
//    const RIGHT_DOWNLOAD = 4;
//	
    /**
     * @param array $default_properties
     */
    function VideoConferencingMeetingObject($default_properties = array ())
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Get the default properties of all data classes.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_DESCRIPTION;
        $extended_property_names[] = self :: PROPERTY_START_TIME;
        $extended_property_names[] = self :: PROPERTY_START_DATE;
        $extended_property_names[] = self :: PROPERTY_END_TIME;
        $extended_property_names[] = self :: PROPERTY_END_DATE;
        $extended_property_names[] = self :: PROPERTY_PARITCIPANTS;
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

    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    public function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    public function get_start_date()
    {
        return $this->get_default_property(self :: PROPERTY_START_DATE);
    }   
    
    public function get_end_time()
    {
        return $this->get_default_property(self :: PROPERTY_END_TIME);
    }
    
    public function get_end_date()
    {
        return $this->get_default_property(self :: PROPERTY_END_DATE);
    }
    
    public function get_participants()
    {
        return $this->get_default_property(self :: PROPERTY_PARTICIPANTS);
    }
    
    /**
     * @return array
     */
//    public function get_rights()
//    {
//        return $this->get_default_property(self :: PROPERTY_RIGHTS);
//    }

    /**
     * @param int $right
     * @return boolean
     */
//    private function get_right($right)
//    {
//        $rights = $this->get_rights();
//        if (! in_array($right, array_keys($rights)))
//        {
//            return false;
//        }
//        else
//        {
//            return $rights[$right];
//        }
//    }

//    public static function get_available_rights()
//    {
//        return array(self :: RIGHT_DELETE, self :: RIGHT_DOWNLOAD, self :: RIGHT_EDIT, self :: RIGHT_USE);
//    }

    /**
     * @param string $title
     */
    public function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * @param string $description
     */
    public function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

	
    
//    /**
//     * @param array $rights
//     */
//    public function set_rights($rights)
//    {
//        $this->set_default_property(self :: PROPERTY_RIGHTS, $rights);
//    }
//
//    /**
//     * @param int $right
//     * @param boolean $value
//     */
//    public function set_right($right, $value)
//    {
//        $rights = $this->get_rights();
//        $rights[$right] = $value;
//        $this->set_rights($rights);
//    }

    /**
     * @param string $start_time
     */
    public function set_start_time($start_time)
    {
        $this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
    }
    
	/**
     * @param string $start_date
     */
    public function set_start_date($start_date)
    {
        $this->set_default_property(self :: PROPERTY_START_DATE, $start_date);
    }
    
	/**
     * @param string $end_date
     */
    public function set_end_date($end_date)
    {
        $this->set_default_property(self :: PROPERTY_END_DATE, $end_date);
    }    
    
	/**
     * @param string $end_time
     */
    public function set_end_time($end_time)
    {
        $this->set_default_property(self :: PROPERTY_END_TIME, $end_time);
    }    
    
	/**
     * @param string $participants
     */
    public function set_participants($participants)
    {
        $this->set_default_property(self :: PROPERTY_PARTICIPANTS, $participants);
    }    
    
    /**
     * @return string
     */
//    abstract static function get_object_type();

//    /**
//     * @return boolean
//     */
//    function is_usable()
//    {
//        return $this->get_right(self :: RIGHT_USE);
//    }
//
//    /**
//     * @return boolean
//     */
//    function is_editable()
//    {
//        return $this->get_right(self :: RIGHT_EDIT);
//    }
//
//    /**
//     * @return boolean
//     */
//    function is_deletable()
//    {
//        return $this->get_right(self :: RIGHT_DELETE);
//    }
//
//    /**
//     * @return boolean
//     */
//    function is_downloadable()
//    {
//        return $this->get_right(self :: RIGHT_DOWNLOAD);
//    }

//    /**
//     * @return ExternalRepositorySync
//     */
//    function get_synchronization_data()
//    {
//        if (! isset($this->synchronization_data))
//        {
//            $sync_conditions = array();
//            $sync_conditions[] = new EqualityCondition(ExternalRepositorySync :: PROPERTY_EXTERNAL_REPOSITORY_OBJECT_ID, $this->get_id());
//            $sync_conditions[] = new EqualityCondition(ExternalRepositorySync :: PROPERTY_EXTERNAL_REPOSITORY_ID, $this->get_external_repository_id());
//            $sync_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id(), ContentObject :: get_table_name());
//            $sync_condition = new AndCondition($sync_conditions);
//
//            $this->synchronization_data = RepositoryDataManager :: get_instance()->retrieve_external_repository_sync($sync_condition);
//        }
//
//        return $this->synchronization_data;
//    }
//    
//    /**
//     * @return int
//     */
//    function get_synchronization_status()
//    {
//        return $this->get_synchronization_data()->get_synchronization_status(null, $this->get_modified());
//    }
//
//    /**
//     * @return boolean
//     */
//    function is_importable()
//    {
//        return !$this->get_synchronization_data() instanceof ExternalRepositorySync;
//    }
}
?>