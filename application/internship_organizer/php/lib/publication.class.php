<?php

class InternshipOrganizerPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';
    
    /**
     * InternshipOrganizerPublication properties
     */
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_PUBLISHER_ID = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_PUBLICATION_TYPE = 'publication_type';
    const PROPERTY_PUBLICATION_PLACE = 'publication_place';
    const PROPERTY_PLACE_ID = 'place_id';

    public function create()
    {
        $succes = parent :: create();
        if ($succes)
        {
            $parent_location = InternshipOrganizerRights :: get_internship_organizers_subtree_root_id();
            $location = InternshipOrganizerRights :: create_location_in_internship_organizers_subtree($this->get_name(), $this->get_id(), $parent_location, InternshipOrganizerRights :: TYPE_PUBLICATION, true);
            
            $rights = InternshipOrganizerRights :: get_available_rights_for_publications();
            foreach ($rights as $right)
            {
                RightsUtilities :: set_user_right_location_value($right, $this->get_publisher_id(), $location->get_id(), 1);
            }
        }
        return $succes;
    }

    public function delete()
    {
        $location = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($this->get_id(), InternshipOrganizerRights :: TYPE_PUBLICATION);
        if ($location)
        {
            if (! $location->remove())
            {
                return false;
            }
        }
        $succes = parent :: delete();
        return $succes;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_PUBLISHER_ID, self :: PROPERTY_PUBLISHED, self :: PROPERTY_PUBLICATION_TYPE, self :: PROPERTY_PUBLICATION_PLACE, self :: PROPERTY_PLACE_ID, self :: PROPERTY_CONTENT_OBJECT_TYPE));
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the name of this InternshipOrganizerPublication.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this InternshipOrganizerPublication.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this InternshipOrganizerPublication.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this InternshipOrganizerPublication.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the content_object_id of this InternshipOrganizerPublication.
     * @return the content_object_id.
     */
    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the content_object_id of this InternshipOrganizerPublication.
     * @param content_object_id
     */
    function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     * Returns the content_object_type of this InternshipOrganizerPublication.
     * @return the content_object_type.
     */
    function get_content_object_type()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE);
    }

    /**
     * Sets the content_object_type of this InternshipOrganizerPublication.
     * @param content_object_type
     */
    function set_content_object_type($content_object_type)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE, $content_object_type);
    }

    /**
     * Returns the from_date of this InternshipOrganizerPublication.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this InternshipOrganizerPublication.
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this InternshipOrganizerPublication.
     * @return the to_date.
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this InternshipOrganizerPublication.
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Returns the publisher_id of this InternshipOrganizerPublication.
     * @return the publisher_id.
     */
    function get_publisher_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER_ID);
    }

    /**
     * Sets the publisher_id of this InternshipOrganizerPublication.
     * @param publisher_id
     */
    function set_publisher_id($publisher_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER_ID, $publisher_id);
    }

    /**
     * Returns the publication_type of this InternshipOrganizerPublication.
     * @return the publication_type.
     */
    function get_publication_type()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_TYPE);
    }

    /**
     * Sets the publication_type of this InternshipOrganizerPublication.
     * @param publication_type
     */
    function set_publication_type($publication_type)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_TYPE, $publication_type);
    }

    /**
     * Returns the publication_place of this InternshipOrganizerPublication.
     * @return the publication_place.
     */
    function get_publication_place()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_PLACE);
    }

    /**
     * Sets the publication_place of this InternshipOrganizerPublication.
     * @param publication_place
     */
    function set_publication_place($publication_place)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_PLACE, $publication_place);
    }

    /**
     * Returns the place_id of this InternshipOrganizerPublication.
     * @return the place_id.
     */
    function get_place_id()
    {
        return $this->get_default_property(self :: PROPERTY_PLACE_ID);
    }

    /**
     * Sets the place_id of this InternshipOrganizerPublication.
     * @param place_id
     */
    function set_place_id($place_id)
    {
        $this->set_default_property(self :: PROPERTY_PLACE_ID, $place_id);
    }

    /**
     * Returns the published of this InternshipOrganizerPublication.
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this InternshipOrganizerPublication.
     * @param published
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function is_publication_period()
    {
        
        $from_date = $this->get_from_date();
        $to_date = $this->get_to_date();
        if ($from_date == 0 && $to_date == 0)
        {
            return true;
        }
        
        $time = time();
        
        if ($time < $from_date || $time > $to_date)
        {
            return false;
        }
        else
        {
            return true;
        }
    
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function get_content_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_content_object_id());
    }

    function get_publication_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher_id());
    }

}

?>