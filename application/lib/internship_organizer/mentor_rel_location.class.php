<?php
/**
 * This class describes a InternshipMentorRelLocation data object
 * @author Sven Vanhoecke
 */
class InternshipOrganizerMentorRelLocation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * InternshipMentorRelLocation properties
     */
    const PROPERTY_MENTOR_ID = 'mentor_id';
    const PROPERTY_LOCATION_ID = 'location_id';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_MENTOR_ID, self :: PROPERTY_LOCATION_ID);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the mentor_id of this InternshipMentorRelLocation.
     * @return the mentor_id.
     */
    function get_mentor_id()
    {
        return $this->get_default_property(self :: PROPERTY_MENTOR_ID);
    }

    /**
     * Sets the mentor_id of this InternshipMentorRelLocation.
     * @param mentor_id
     */
    function set_mentor_id($mentor_id)
    {
        $this->set_default_property(self :: PROPERTY_MENTOR_ID, $mentor_id);
    }

    /**
     * Returns the location_id of this InternshipMentorRelLocation.
     * @return the location_id.
     */
    function get_location_id()
    {
        return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
    }

    /**
     * Sets the location_id of this InternshipMentorRelLocation.
     * @param location_id
     */
    function set_location_id($location_id)
    {
        $this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
    }

    static function get_table_name()
    {
        return 'mentor_rel_location';
        //		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>