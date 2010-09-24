<?php

/** @author Steven Willaert */

class InternshipOrganizerOrganisation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * Organisation properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'organisation_name';
    const PROPERTY_DESCRIPTION = 'organisation_description';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the id of this Organisation.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this Organisation.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the name of this Organisation.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this Organisation.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this Organisation.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Organisation.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    static function get_table_name()
    {
        return 'organisation';
    }

    function count_locations()
    {
        $dm = $this->get_data_manager();
        $organisation_id = $this->get_id();
        $condition = new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $organisation_id);
        return $dm->count_locations($condition);
    }

    function get_locations()
    {
        $dm = $this->get_data_manager();
        $organisation_id = $this->get_id();
        $condition = new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $organisation_id);
        return $dm->retrieve_locations($condition);
    }

    function get_location_ids()
    {
        $locations = $this->get_locations();
        $location_ids = array();
        
        while ($location = $locations->next_result())
        {
            $location_ids[] = $location->get_id();
        
        }
        
        return $location_ids;
    }

    function get_user_ids()
    {
        
        $condition = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_ORGANISATION_ID, $this->get_id());
        $organisation_rel_locations = $this->get_data_manager()->retrieve_organisation_rel_users($condition);
        $user_ids = array();
        
        while ($organisation_rel_location = $organisation_rel_locations->next_result())
        {
            $user_ids[] = $organisation_rel_location->get_user_id();
        
        }
        return $user_ids;
    }
}

?>