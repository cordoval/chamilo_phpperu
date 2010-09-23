<?php

/**
 * This class describes a InternshipMoment data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipOrganizerMoment extends DataClass implements AttachmentSupport
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * Moment properties
     */
    const PROPERTY_ID = 'moment_id';
    const PROPERTY_NAME = 'moment_name';
    const PROPERTY_DESCRIPTION = 'moment_description';
    const PROPERTY_BEGIN = 'moment_begin';
    const PROPERTY_END = 'moment_end';
    const PROPERTY_AGREEMENT_ID = 'moment_agreement_id';
    const PROPERTY_OWNER = 'moment_owner_id';

    public function create()
    {
        $succes = parent :: create();
       
        if ($succes)
        {
            $parent_location = InternshipOrganizerRights :: get_internship_organizers_subtree_root_id();
            $location = InternshipOrganizerRights :: create_location_in_internship_organizers_subtree($this->get_name(), $this->get_id(), $parent_location, InternshipOrganizerRights :: TYPE_MOMENT, true);
            
            $rights = InternshipOrganizerRights :: get_available_rights_for_moments();
            foreach ($rights as $right)
            {
                RightsUtilities :: set_user_right_location_value($right, $this->get_owner(), $location->get_id(), 1);
            }
        }
        return $succes;
    }

    public function delete()
    {
        $location = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($this->get_id(), InternshipOrganizerRights :: TYPE_MOMENT);
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_BEGIN, self :: PROPERTY_END, self :: PROPERTY_AGREEMENT_ID, self :: PROPERTY_OWNER);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the id of this InternshipMoment.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this InternshipMoment.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the name of this InternshipMoment.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this InternshipMoment.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this InternshipMoment.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this InternshipMoment.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the begin of this InternshipMoment.
     * @return the begin.
     */
    function get_begin()
    {
        return $this->get_default_property(self :: PROPERTY_BEGIN);
    }

    /**
     * Sets the begin of this InternshipMoment.
     * @param begin
     */
    function set_begin($begin)
    {
        $this->set_default_property(self :: PROPERTY_BEGIN, $begin);
    }

    /**
     * Returns the end of this InternshipMoment.
     * @return the end.
     */
    function get_end()
    {
        return $this->get_default_property(self :: PROPERTY_END);
    }

    /**
     * Sets the end of this InternshipMoment.
     * @param end
     */
    function set_end($end)
    {
        $this->set_default_property(self :: PROPERTY_END, $end);
    }

    /**
     * Returns the agreement_id of this InternshipMoment.
     * @return the agreement_id.
     */
    function get_agreement_id()
    {
        return $this->get_default_property(self :: PROPERTY_AGREEMENT_ID);
    }

    /**
     * Sets the agreement_id of this InternshipMoment.
     * @param agreement_id
     */
    function set_agreement_id($agreement_id)
    {
        $this->set_default_property(self :: PROPERTY_AGREEMENT_ID, $agreement_id);
    }

    /**
     * Returns the owner of this InternshipMoment.
     * @return the owner.
     */
    function get_owner()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER);
    }

    /**
     * Sets the owner of this InternshipMoment.
     * @param owner
     */
    function set_owner($owner)
    {
        $this->set_default_property(self :: PROPERTY_OWNER, $owner);
    }

    function get_agreement()
    {
        return $this->get_data_manager()->retrieve_agreement($this->get_agreement_id());
    }

    static function get_table_name()
    {
        return 'moment';
        //		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    public function get_attached_content_objects()
    {
        
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication::PROPERTY_PUBLICATION_PLACE, InternshipOrganizerPublicationPlace::MOMENT);
        $conditions[] = new EqualityCondition(InternshipOrganizerPublication::PROPERTY_PLACE_ID, $this->get_id());
        $condition = new AndCondition($conditions);
        $publications =  $this->get_data_manager()->retrieve_publications($condition);
        $attachements = array();
        while($publication = $publications->next_result()){
        	$attachements[] = $publication->get_content_object();
        }
        return $attachements;
    }

}

?>