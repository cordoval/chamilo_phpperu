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
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_BEGIN = 'begin';
    const PROPERTY_END = 'end';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_BEGIN, self :: PROPERTY_END));
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

    function get_user_ids($user_type)
    {
        
        $target_users = array();
        $type_index = $conditions = array();
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, $this->get_id());
        $conditions[] = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, $user_type);
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

}

?>