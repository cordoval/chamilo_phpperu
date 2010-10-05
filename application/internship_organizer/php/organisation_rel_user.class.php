<?php
/**
 * This class describes a InternshipOrganisationRelUser data object
 * @author Sven Vanhoecke
 */
class InternshipOrganizerOrganisationRelUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * InternshipOrganisationRelUser properties
     */
    const PROPERTY_ORGANISATION_ID = 'organisation_id';
    const PROPERTY_USER_ID = 'user_id';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ORGANISATION_ID, self :: PROPERTY_USER_ID);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the organisation_id of this InternshipOrganisationRelUser.
     * @return the organisation_id.
     */
    function get_organisation_id()
    {
        return $this->get_default_property(self :: PROPERTY_ORGANISATION_ID);
    }

    /**
     * Sets the organisation_id of this InternshipOrganisationRelUser.
     * @param organisation_id
     */
    function set_organisation_id($organisation_id)
    {
        $this->set_default_property(self :: PROPERTY_ORGANISATION_ID, $organisation_id);
    }

    /**
     * Returns the user_id of this InternshipOrganisationRelUser.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the user_id of this InternshipOrganisationRelUser.
     * @param user_id
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    static function get_table_name()
    {
        return 'organisation_rel_user';
        //		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>