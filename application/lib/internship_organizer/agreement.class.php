<?php

class InternshipOrganizerAgreement extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * InternshipAgreement properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_BEGIN = 'begin';
    const PROPERTY_END = 'end';
    const PROPERTY_PERIOD_ID = 'period_id';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_OWNER = 'owner';
    
    const STATUS_ADD_LOCATION = 1;
    const STATUS_TO_APPROVE = 2;
    const STATUS_APPROVED = 3;

    public function create()
    {
        $succes = parent :: create();
        if ($succes)
        {
            $parent_location = InternshipOrganizerRights :: get_internship_organizers_subtree_root_id();
            $location = InternshipOrganizerRights :: create_location_in_internship_organizers_subtree($this->get_name(), $this->get_id(), $parent_location, InternshipOrganizerRights :: TYPE_AGREEMENT, true);
            
            $rights = InternshipOrganizerRights :: get_available_rights_for_agreements();
            foreach ($rights as $right)
            {
                RightsUtilities :: set_user_right_location_value($right, $this->get_owner(), $location->get_id(), 1);
            }
        }
        return $succes;
    }

    public function delete()
    {
        $location = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($this->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT);
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_BEGIN, self :: PROPERTY_END, self :: PROPERTY_PERIOD_ID, self :: PROPERTY_STATUS);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the id of this InternshipAgreement.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this InternshipAgreement.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the name of this InternshipAgreement.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this InternshipAgreement.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this InternshipAgreement.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this InternshipAgreement.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the begin of this InternshipAgreement.
     * @return begin.
     */
    function get_begin()
    {
        return $this->get_default_property(self :: PROPERTY_BEGIN);
    }

    /**
     * Sets the begin of this InternshipAgreement.
     * @param begin
     */
    function set_begin($begin)
    {
        $this->set_default_property(self :: PROPERTY_BEGIN, $begin);
    }

    /**
     * Returns the end of this InternshipAgreement.
     * @return end.
     */
    function get_end()
    {
        return $this->get_default_property(self :: PROPERTY_END);
    }

    /**
     * Sets the end of this InternshipAgreement.
     * @param end
     */
    function set_end($end)
    {
        $this->set_default_property(self :: PROPERTY_END, $end);
    }

    /**
     * Returns the period_id of this InternshipAgreement.
     * @return period_id.
     */
    function get_period_id()
    {
        return $this->get_default_property(self :: PROPERTY_PERIOD_ID);
    }

    /**
     * Sets the period_id of this InternshipAgreement.
     * @param period_id
     */
    function set_period_id($period_id)
    {
        $this->set_default_property(self :: PROPERTY_PERIOD_ID, $period_id);
    }

    /**
     * Returns the status of this InternshipAgreement.
     * @return status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Sets the status of this InternshipAgreement.
     * @param status
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    /**
     * Returns the owner of this InternshipAgreement.
     * @return owner.
     */
    function get_owner()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER);
    }

    /**
     * Sets the owner of this InternshipAgreement.
     * @param owner
     */
    function set_owner($owner)
    {
        $this->set_default_property(self :: PROPERTY_OWNER, $owner);
    }

    static function get_status_name($index)
    {
        switch ($index)
        {
            case 1 :
                return Translation :: get('InternshipOrganizerAgreementAddLocation');
            //                break;
            case 2 :
                return Translation :: get('InternshipOrganizerAgreementToApprove');
            //                break;
            case 3 :
                return Translation :: get('InternshipOrganizerAgreementApproved');
            //                break;
            default :
                //no default
                break;
        }
    }

    function update_status()
    {
        
        $dm = $this->get_data_manager();
        $condition = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $this->get_id());
        $count = $dm->count_agreement_rel_locations($condition);
        if ($count == 0)
        {
            $this->set_status(self :: STATUS_ADD_LOCATION);
            return $this->update();
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_AGREEMENT_ID, $this->get_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelLocation :: PROPERTY_LOCATION_TYPE, InternshipOrganizerAgreementRelLocation :: APPROVED);
        $condition = new AndCondition($conditions);
        
        $count = $dm->count_agreement_rel_locations($condition);
        if ($count == 0)
        {
            $this->set_status(self :: STATUS_TO_APPROVE);
            return $this->update();
        }
        
        $this->set_status(self :: STATUS_APPROVED);
        return $this->update();
    
    }

    function get_user_ids($user_types)
    {
        
        if (! is_array($user_types))
        {
            $user_types = array($user_types);
        }
        
        $target_users = array();
        $type_index = $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_AGREEMENT_ID, $this->get_id());
        $conditions[] = new InCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_TYPE, $user_types);
        $condition = new AndCondition($conditions);
        
        $agreement_rel_users = $this->get_data_manager()->retrieve_agreement_rel_users($condition);
        
        while ($agreement_rel_user = $agreement_rel_users->next_result())
        {
            $target_users[] = $agreement_rel_user->get_user_id();
        }
        
        if (in_array(InternshipOrganizerUserType :: MENTOR, $user_types))
        {
        
        }
        
        return array_unique($target_users);
    }

    function is_user_type($use_type, $user_id)
    {
        
        return in_array($user_id, $this->get_user_ids($use_type));
    
    }

    static function get_table_name()
    {
        return 'agreement';
        //		return Utilities::camelcase_to_underscores ( self::CLASS_NAME );
    

    }
}
