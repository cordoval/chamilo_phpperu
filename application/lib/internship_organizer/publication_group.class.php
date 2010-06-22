<?php

class InternshipOrganizerPublicationGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_group';
    
    /**
     * InternshipOrganizerPublicationGroup properties
     */
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_GROUP_ID);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the publication_id of this InternshipOrganizerPublicationGroup.
     * @return the publication_id.
     */
    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    /**
     * Sets the publication_id of this InternshipOrganizerPublicationGroup.
     * @param publication_id
     */
    function set_survey_publication($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Returns the group_id of this InternshipOrganizerPublicationGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this InternshipOrganizerPublicationGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function create()
    {
        return $this->get_data_manager()->create_survey_publication_group($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>