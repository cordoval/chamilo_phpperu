<?php

class InternshipOrganizerAppointment extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * InternshipOrganizerAppointment properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_MOMENT_ID = 'moment_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_TYPE = 'type';
    
    const STATUS_CONFIRMED = 1;
    const STATUS_CONFIRMED_NAME = 'confirmed';
    const STATUS_PENDING = 2;
    const STATUS_PENDING_NAME = 'pending';
    const STATUS_CANCELLED = 3;
    const STATUS_CANCELLED_NAME = 'cancelled';
    
    const TYPE_VISIT = 1;
    const TYPE_VISTIT_NAME = 'visit';
    const TYPE_MEETING = 2;
    const TYPE_MEETING_NAME = 'meeting';
    const TYPE_EVALUATION = 3;
    const TYPE_EVALUATION_NAME = 'evaluation';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_CREATED, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_MOMENT_ID, self :: PROPERTY_OWNER_ID, self :: PROPERTY_TITLE, self :: PROPERTY_TYPE, self :: PROPERTY_STATUS);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the id of this InternshipOrganizerAppointment.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this InternshipOrganizerAppointment.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the id of this InternshipOrganizerMoment.
     * @return the id.
     */
    function get_moment_id()
    {
        return $this->get_default_property(self :: PROPERTY_MOMENT_ID);
    }

    /**
     * Sets the id of this InternshipOrganizerMoment.
     * @param id
     */
    function set_moment_id($id)
    {
        $this->set_default_property(self :: PROPERTY_MOMENT_ID, $id);
    }

    /**
     * Returns the title of this InternshipOrganizerAppointment.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Sets the name of this InternshipOrganizerAppointment.
     * @param title
     */
    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * Returns the description of this InternshipOrganizerAppointment.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this InternshipOrganizerAppointment.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the id of this owner.
     * @return the id.
     */
    function get_owner_id()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    /**
     * Sets the id of this owner.
     * @param id
     */
    function set_owner_id($owner_id)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
    }

    /**
     * Returns the created of this InternshipOrganizerAppointment.
     * @return the created.
     */
    function get_created()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    /**
     * Sets the created of this InternshipOrganizerAppointment.
     * @param created
     */
    function set_created($created)
    {
        $this->set_default_property(self :: PROPERTY_CREATED, $created);
    }

    /**
     * Returns the type of this InternshipOrganizerAppointment.
     * @return the type.
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Sets the type of this InternshipOrganizerAppointment.
     * @param type
     */
    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * Returns the status of this InternshipOrganizerAppointment.
     * @return the status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Sets the status of this InternshipOrganizerAppointment.
     * @param status
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_moment()
    {
        return $this->get_data_manager()->retrieve_moment($this->get_moment_id());
    }

    static function get_types()
    {
        return array(self :: TYPE_EVALUATION => self :: TYPE_EVALUATION_NAME, self :: TYPE_MEETING => self :: TYPE_MEETING_NAME, self :: TYPE_VISIT => self :: TYPE_VISTIT_NAME);
    }

    static function get_type_name($index)
    {
        switch ($index)
        {
            case self :: TYPE_EVALUATION :
                return self :: TYPE_EVALUATION_NAME;
                break;
            case self :: TYPE_MEETING :
                return self :: TYPE_MEETING_NAME;
                break;
            case self :: TYPE_VISIT :
                return self :: TYPE_VISTIT_NAME;
                break;
        }
    }

    static function get_status_name($index)
    {
        switch ($index)
        {
            case self :: STATUS_CANCELLED :
                return self :: STATUS_CANCELLED_NAME;
                break;
            case self :: STATUS_CONFIRMED :
                return self :: STATUS_CONFIRMED_NAME;
                break;
            case self :: STATUS_PENDING :
                return self :: STATUS_PENDING_NAME;
                break;
        }
    }

    static function get_states()
    {
        return array(self :: STATUS_CANCELLED => self :: STATUS_CANCELLED_NAME, self :: STATUS_CONFIRMED => self :: STATUS_CONFIRMED_NAME, self :: STATUS_PENDING => self :: STATUS_PENDING_NAME);
    }

    static function get_table_name()
    {
        return 'appointment';
        //		return Utilities::camelcase_to_underscores ( self::CLASS_NAME );
    

    }
}

?>