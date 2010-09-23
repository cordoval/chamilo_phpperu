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
    

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_CREATED, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_MOMENT_ID, self :: PROPERTY_OWNER_ID, self :: PROPERTY_TITLE);
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
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner);
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
   
    function get_moment()
    {
        return $this->get_data_manager()->retrieve_moment($this->get_moment_id());
    }

    static function get_table_name()
    {
        return 'appointment';
        //		return Utilities::camelcase_to_underscores ( self::CLASS_NAME );
    

    }
}

?>