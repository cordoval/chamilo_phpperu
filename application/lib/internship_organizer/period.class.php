<?php
/**
 * internship_organizer
 */
require_once dirname(__FILE__) . '/internship_organizer_data_manager.class.php';
/**
 * This class describes a Period data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipOrganizerPeriod extends NestedTreeNode
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * Period properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'period_name';
    const PROPERTY_DESCRIPTION = 'period_description';
    const PROPERTY_BEGIN = 'period_begin';
    const PROPERTY_END = 'period_end';
    const PROPERTY_OWNER = 'period_owner_id';

    public function create()
    {
        $succes = parent :: create();
        if ($succes)
        {
            $parent_location = InternshipOrganizerRights :: get_internship_organizers_subtree_root_id();
            $location = InternshipOrganizerRights :: create_location_in_internship_organizers_subtree($this->get_name(), $this->get_id(), $parent_location, InternshipOrganizerRights :: TYPE_PERIOD, true);
            
            $rights = InternshipOrganizerRights :: get_available_rights_for_periods();
            foreach ($rights as $right)
            {
                RightsUtilities :: set_user_right_location_value($right, $this->get_owner(), $location->get_id(), 1);
            }
        }
        return $succes;
    }

    public function delete()
    {
        $location = InternshipOrganizerRights :: get_location_by_identifier_from_internship_organizers_subtree($this->get_id(), InternshipOrganizerRights :: TYPE_PERIOD);
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
        return parent :: get_default_property_names(array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_BEGIN, self :: PROPERTY_END, self :: PROPERTY_OWNER));
    }

    /**
     * Returns the id of this Period.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this Period.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the name of this Period.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this Period.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this Period.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Period.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the beginning of this Period.
     * @return begin.
     */
    function get_begin()
    {
        return $this->get_default_property(self :: PROPERTY_BEGIN);
    }

    /**
     * Sets the beginning of this Period.
     * @param begin
     */
    function set_begin($begin)
    {
        $this->set_default_property(self :: PROPERTY_BEGIN, $begin);
    }

    /**
     * Returns the end of this Period.
     * @return end.
     */
    function get_end()
    {
        return $this->get_default_property(self :: PROPERTY_END);
    }

    /**
     * Sets the end of this Period.
     * @param end
     */
    function set_end($end)
    {
        $this->set_default_property(self :: PROPERTY_END, $end);
    }

    /**
     * Returns the owner of this Period.
     * @return owner.
     */
    function get_owner()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER);
    }

    /**
     * Sets the owner of this Period.
     * @param owner
     */
    function set_owner($owner)
    {
        $this->set_default_property(self :: PROPERTY_OWNER, $owner);
    }

    static function get_table_name()
    {
        //        	 return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
        return 'period';
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    function truncate()
    {
        return $this->get_data_manager()->truncate_period($this);
    }

    function get_user_ids($user_types)
    {
        
        if (! is_array($user_types))
        {
            $user_types = array($user_types);
        }
        
        $target_users = array();
        $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $this->get_id());
        $conditions[] = new InCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, $user_types);
        $condition = new AndCondition($conditions);
        
        $period_rel_users = $this->get_data_manager()->retrieve_period_rel_users($condition);
        
        while ($period_rel_user = $period_rel_users->next_result())
        {
            $target_users[] = $period_rel_user->get_user_id();
        }
        
        $target_groups = array();
        $period_rel_groups = $this->get_data_manager()->retrieve_period_rel_groups($condition);
        
        while ($period_rel_group = $period_rel_groups->next_result())
        {
            $target_groups[] = $period_rel_group->get_group_id();
        }
        
        if (count($target_groups) != 0)
        {
            $gdm = GroupDataManager :: get_instance();
            foreach ($target_groups as $group_id)
            {
                $group = $gdm->retrieve_group($group_id);
                $target_users = array_merge($target_users, $group->get_users(true, true));
            }
        }
        
        return array_unique($target_users);
    }

    function get_unique_group_ids($user_types)
    {
        
        $condition = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $this->get_id());
        
        $target_groups = array();
        $period_rel_groups = $this->get_data_manager()->retrieve_period_rel_groups($condition);
        
        while ($period_rel_group = $period_rel_groups->next_result())
        {
            $target_groups[] = $period_rel_group->get_group_id();
        }
        
        return array_unique($target_groups);
    
    }

    function get_unique_user_ids()
    {
        
        $target_users = array();
        $condition = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $this->get_id());
        
        $period_rel_users = $this->get_data_manager()->retrieve_period_rel_users($condition);
        
        while ($period_rel_user = $period_rel_users->next_result())
        {
            $target_users[] = $period_rel_user->get_user_id();
        }
        
        $target_groups = array();
        $period_rel_groups = $this->get_data_manager()->retrieve_period_rel_groups($condition);
        
        while ($period_rel_group = $period_rel_groups->next_result())
        {
            $target_groups[] = $period_rel_group->get_group_id();
        }
        
        if (count($target_groups) != 0)
        {
            $gdm = GroupDataManager :: get_instance();
            foreach ($target_groups as $group_id)
            {
                $group = $gdm->retrieve_group($group_id);
                $target_users = array_merge($target_users, $group->get_users(true, true));
            }
        }
        
        return array_unique($target_users);
    }

    function is_user_type($use_type, $user_id)
    {
        
        return in_array($user_id, $this->get_user_ids($use_type));
    
    }

}

?>