<?php

class InternshipOrganizerPublicationUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_user';
    
    /**
     * InternshipOrganizerPublicationUser properties
     */
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_USER_ID = 'user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_USER_ID);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the publication_id of this InternshipOrganizerPublicationUser.
     * @return the publication_id.
     */
    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    /**
     * Sets the publication_id of this InternshipOrganizerPublicationUser.
     * @param publication_id
     */
    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Returns the user_id of this InternshipOrganizerPublicationUser.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the user_id of this InternshipOrganizerPublicationUser.
     * @param user_id
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>