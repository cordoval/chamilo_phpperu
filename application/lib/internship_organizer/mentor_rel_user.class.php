<?php
/**
 * This class describes a InternshipMentorRelUser data object
 * @author Sven Vanhoecke
 */
class InternshipOrganizerMentorRelUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * InternshipMentorRelUser properties
     */
    const PROPERTY_MENTOR_ID = 'mentor_id';
    const PROPERTY_USER_ID = 'user_id';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_MENTOR_ID, self :: PROPERTY_USER_ID);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the mentor_id of this InternshipMentorRelUser.
     * @return the mentor_id.
     */
    function get_mentor_id()
    {
        return $this->get_default_property(self :: PROPERTY_MENTOR_ID);
    }

    /**
     * Sets the mentor_id of this InternshipMentorRelUser.
     * @param mentor_id
     */
    function set_mentor_id($mentor_id)
    {
        $this->set_default_property(self :: PROPERTY_MENTOR_ID, $mentor_id);
    }

    /**
     * Returns the user_id of this InternshipMentorRelUser.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the user_id of this InternshipMentorRelUser.
     * @param user_id
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    static function get_table_name()
    {
        return 'mentor_rel_user';
        //		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>