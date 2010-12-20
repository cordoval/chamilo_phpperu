<?php
namespace group;

use common\libraries\NotCondition;
use common\libraries\Utilities;
use common\libraries\InequalityCondition;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\DataClass;
use common\libraries\InCondition;
use common\libraries\ObjectTableOrder;

require_once dirname(__FILE__) . "/group_rights.class.php";
/**
 * $Id: group.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib
 */

/**
 * @author Hans de Bisschop
 * @author Dieter De Neef
 * @author Sven Vanpoucke
 */
class Group extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_PARENT = 'parent_id';
    const PROPERTY_LEFT_VALUE = 'left_value';
    const PROPERTY_RIGHT_VALUE = 'right_value';
    const PROPERTY_CODE = 'code';

    /**
     * Array used to cache users in this group, depending on
     * request (include subgroups, recursive subgroups)
     * @var array
     */
    private $users;

    /**
     * Array used to cache user counts in this group, depending on
     * request (include subgroups, recursive subgroups)
     * @var array
     */
    private $user_count;

    /**
     * Array used to cache subgroups, depending on
     * request (recursive or not)
     * @var array
     */
    private $subgroups;

    /**
     * Array used to cache subgroup counts, depending on
     * request (recursive or not)
     * @var array
     */
    private $subgroup_count;

    /**
     * Cache of the siblings of a group, depending on
     * request (recursive or not)
     * @var array
     */
    private $siblings;

    /**
     * ObjectResultSet used to cache groups this group can use
     * @var ObjectResultSet
     */
    private $allowed_groups;

    /**
     * Get the default properties of all groups.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_NAME,
                self :: PROPERTY_DESCRIPTION,
                self :: PROPERTY_SORT,
                self :: PROPERTY_PARENT,
                self :: PROPERTY_LEFT_VALUE,
                self :: PROPERTY_RIGHT_VALUE,
                self :: PROPERTY_CODE));
    }

    /*
     * Gets the table name for this class
     */

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return GroupDataManager :: get_instance();
    }

    /**
     * Returns the name of this group.
     * @return String The name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the description of this group.
     * @return String The description
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Sets the name of this group.
     * @param String $name the name.
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function set_code($code)
    {
        $this->set_default_property(self :: PROPERTY_CODE, $code);
    }

    /**
     * Sets the description of this group.
     * @param String $description the description.
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    function set_sort($sort)
    {
        $this->set_default_property(self :: PROPERTY_SORT, $sort);
    }

    function get_parent()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT);
    }

    function set_parent($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT, $parent);
    }

    function get_left_value()
    {
        return $this->get_default_property(self :: PROPERTY_LEFT_VALUE);
    }

    function set_left_value($left_value)
    {
        $this->set_default_property(self :: PROPERTY_LEFT_VALUE, $left_value);
    }

    function get_right_value()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHT_VALUE);
    }

    function set_right_value($right_value)
    {
        $this->set_default_property(self :: PROPERTY_RIGHT_VALUE, $right_value);
    }

    /**
     * Get all of the group's parents
     */
    function get_parents($include_self = true)
    {
        $gdm = $this->get_data_manager();

        $parent_conditions = array();
        if ($include_self)
        {
            $parent_conditions[] = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $this->get_left_value());
            $parent_conditions[] = new InequalityCondition(Group :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $this->get_right_value());
        }
        else
        {
            $parent_conditions[] = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $this->get_left_value());
            $parent_conditions[] = new InequalityCondition(Group :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_right_value());
        }

        $parent_condition = new AndCondition($parent_conditions);
        $order = new ObjectTableOrder(Group :: PROPERTY_LEFT_VALUE, SORT_DESC);

        return $gdm->retrieve_groups($parent_condition, null, null, $order);
    }

    function is_child_of($parent)
    {
        if (! is_object($parent))
        {
            $gdm = $this->get_data_manager();
            $parent = $gdm->retrieve_group($parent);
        }

        // TODO: What if $parent is invalid ? Return error
        // Check if the left and right value of the child are within the
        // left and right value of the parent, if so it is a child
        if ($this->get_left_value() > $parent->get_left_value() && $parent->get_right_value() > $this->get_right_value())
        {
            return true;
        }

        return false;
    }

    function is_parent_of($child)
    {
        if (! is_object($child))
        {
            $gdm = $this->get_data_manager();
            $child = $gdm->retrieve_group($child);
        }

        if ($this->get_left_value() < $child->get_left_value() && $child->get_right_value() < $this->get_right_value())
        {
            return true;
        }

        return false;
    }

    /**
     * Get the groups on the same level with the same parent
     */
    function get_siblings($include_self = true)
    {
        if (! isset($this->siblings[(int) $include_self]))
        {
            $gdm = $this->get_data_manager();

            $siblings_conditions = array();
            $siblings_conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_parent());

            if (! $include_self)
            {
                $siblings_conditions[] = new NotCondition(new EqualityCondition(Group :: PROPERTY_ID, $this->get_id()));
            }

            $siblings_condition = new AndCondition($siblings_conditions);

            $this->siblings[(int) $include_self] = $gdm->retrieve_groups($siblings_condition);
        }

        return $this->siblings[(int) $include_self];

    }

    function has_siblings()
    {
        $gdm = $this->get_data_manager();

        $siblings_conditions = array();
        $siblings_conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_parent());
        $siblings_conditions[] = new NotCondition(new EqualityCondition(Group :: PROPERTY_ID, $this->get_id()));

        $siblings_condition = new AndCondition($siblings_conditions);

        return ($gdm->count_groups($siblings_condition) > 0);
    }

    /**
     * Get the group's children
     */
    function get_children($recursive = true)
    {
        return $this->get_subgroups($recursive);
    }

    function has_children()
    {
        return ! ($this->get_left_value() == ($this->get_right_value() - 1));

     //		$gdm = $this->get_data_manager();
    //		$children_condition = new EqualityCondition(Location :: PROPERTY_PARENT, $this->get_id());
    //		return ($gdm->count_groups($children_condition) > 0);
    }

    function move($new_parent_id, $new_previous_id = 0)
    {
        //        $location = GroupRights :: get_location_by_identifier_from_groups_subtree($this->get_id());
        //        if ($location)
        //        {
        //            if (! $location->move(GroupRights :: get_location_id_by_identifier_from_groups_subtree($new_parent_id)))
        //            {
        //                return false;
        //            }
        //        }


        return $this->move_group($new_parent_id, $new_previous_id = 0);
    }

    function move_group($new_parent_id, $new_previous_id = 0)
    {
        $gdm = $this->get_data_manager();

        //        if (! GroupRights :: is_allowed_in_groups_subtree(GroupRights :: RIGHT_CREATE, $new_parent_id))
        //        {
        //            return false;
        //        }


        if (! $gdm->move_group($this, $new_parent_id, $new_previous_id))
        {
            return false;
        }

        return true;
    }

    /**
     * Instructs the Datamanager to delete this group.
     * @return boolean True if success, false otherwise.
     */
    function delete_group()
    {
        $gdm = $this->get_data_manager();

        // Delete the actual location
        if (! $gdm->delete_group($this))
        {
            return false;
        }

        // Update left and right values
        if (! $gdm->delete_nested_values($this))
        {
            // TODO: Some kind of general error handling framework would be nice: PEAR-ERROR maybe ?
            return false;
        }

        return true;
    }

    function delete()
    {
        $gdm = $this->get_data_manager();

        //        $location = GroupRights :: get_location_by_identifier_from_groups_subtree($this->get_id());
        //        if ($location)
        //        {
        //            if (! $location->remove())
        //            {
        //                return false;
        //            }
        //        }


        return $this->delete_group();
    }

    function truncate()
    {
        return $this->get_data_manager()->truncate_group($this);
    }

    function create_batch()
    {
        $gdm = $this->get_data_manager();
        return $gdm->create_group($this);
    }

    function create($previous_id = 0)
    {
        $gdm = $this->get_data_manager();

        $parent_id = $this->get_parent();

        $previous_visited = 0;

        if ($parent_id || $previous_id)
        {
            if ($previous_id)
            {
                $node = $gdm->retrieve_group($previous_id);
                $parent_id = $node->get_parent();

     // TODO: If $node is invalid, what then ?
            }
            else
            {
                $node = $gdm->retrieve_group($parent_id);
            }

            // Set the new location's parent id
            $this->set_parent($parent_id);

            // TODO: If $node is invalid, what then ?
            // get the "visited"-value where to add the new element behind
            // if $previous_id is given, we need to use the right-value
            // if only the $parent_id is given we need to use the left-value
            $previous_visited = $previous_id ? $node->get_right_value() : $node->get_left_value();

            // Correct the left and right values wherever necessary.
            if (! $gdm->add_nested_values($previous_visited, 1))
            {
                // TODO: Some kind of general error handling framework would be nice: PEAR-ERROR maybe ?
                return false;
            }
        }

        // Left and right values have been shifted so now we
        // want to really add the location itself, but first
        // we have to set it's left and right value.
        $this->set_left_value($previous_visited + 1);
        $this->set_right_value($previous_visited + 2);
        if (! $gdm->create_group($this))
        {
            return false;
        }

        //        $parent_location = GroupRights :: get_location_id_by_identifier_from_groups_subtree($this->get_parent());
        //        $parent_location = ($parent_location ? $parent_location : 0);
        //        if (! GroupRights :: create_location_in_groups_subtree($this->get_name(), $this->get_id(), $parent_location))
        //        {
        //            $this->delete_group();
        //            return false;
        //        }


        return true;
    }

    function get_rights_templates()
    {
        $gdm = $this->get_data_manager();
        $condition = new EqualityCondition(GroupRightsTemplate :: PROPERTY_GROUP_ID, $this->get_id());

        return $gdm->retrieve_group_rights_templates($condition);
    }

    function add_rights_template_link($rights_template_id)
    {
        $gdm = $this->get_data_manager();
        return $gdm->add_rights_template_link($this, $rights_template_id);
    }

    function delete_rights_template_link($rights_template_id)
    {
        $gdm = $this->get_data_manager();
        return $gdm->delete_rights_template_link($this, $rights_template_id);
    }

    function update_rights_template_links($rights_templates)
    {
        $gdm = $this->get_data_manager();
        return $gdm->update_rights_template_links($this, $rights_templates);
    }

    function get_users($include_subgroups = false, $recursive_subgroups = false)
    {
        if (! isset($this->users[(int) $include_subgroups][(int) $recursive_subgroups]))
        {
            $condition = $this->get_user_condition($include_subgroups, $recursive_subgroups);
            $group_rel_users = $this->get_data_manager()->retrieve_group_rel_users($condition);
            $users = array();

            while ($group_rel_user = $group_rel_users->next_result())
            {
                $users[$group_rel_user->get_user_id()] = $group_rel_user->get_user_id();
            }

            $this->users[(int) $include_subgroups][(int) $recursive_subgroups] = $users;
        }

        return $this->users[(int) $include_subgroups][(int) $recursive_subgroups];
    }

    private function get_user_condition($include_subgroups = false, $recursive_subgroups = false)
    {
        $groups = array();
        $groups[] = $this->get_id();

        if ($include_subgroups)
        {
            $subgroups = $this->get_subgroups($recursive_subgroups);

            foreach ($subgroups as $subgroup)
            {
                $groups[] = $subgroup->get_id();
            }
        }

        return new InCondition(GroupRelUser :: PROPERTY_GROUP_ID, $groups);
    }

    function count_users($include_subgroups = false, $recursive_subgroups = false)
    {
        if (! isset($this->user_count[(int) $include_subgroups][(int) $recursive_subgroups]))
        {
            $condition = $this->get_user_condition($include_subgroups, $recursive_subgroups);
            $this->user_count[(int) $include_subgroups][(int) $recursive_subgroups] = $this->get_data_manager()->count_group_rel_users($condition);
        }

        return $this->user_count[(int) $include_subgroups][(int) $recursive_subgroups];
    }

    function get_subgroups($recursive = false)
    {
        if (! isset($this->subgroups[(int) $recursive]))
        {
            $gdm = $this->get_data_manager();

            if ($recursive)
            {
                $children_conditions = array();
                $children_conditions[] = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_left_value());
                $children_conditions[] = new InequalityCondition(Group :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, $this->get_right_value());
                $children_condition = new AndCondition($children_conditions);
            }
            else
            {
                $children_condition = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_id());
            }

            $groups = $gdm->retrieve_groups($children_condition);

            $subgroups = array();

            while ($group = $groups->next_result())
            {
                $subgroups[$group->get_id()] = $group;
            }

            $this->subgroups[(int) $recursive] = $subgroups;
        }

        return $this->subgroups[(int) $recursive];
    }

    function count_subgroups($recursive = false)
    {
        if (! isset($this->subgroup_count[(int) $recursive]))
        {
            $gdm = $this->get_data_manager();

            if ($recursive)
            {
                $this->subgroup_count[(int) $recursive] = ($this->get_right_value() - $this->get_left_value() - 1) / 2;
            }
            else
            {
                $gdm = GroupDataManager :: get_instance();
                $children_condition = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_id());
                $this->subgroup_count[(int) $recursive] = $gdm->count_groups($children_condition);
            }
        }

        return $this->subgroup_count[(int) $recursive];
    }

    /**
     * @return array
     */
    function get_allowed_groups()
    {
        if (! isset($this->allowed_groups))
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(GroupUseGroup :: PROPERTY_REQUEST_GROUP_ID, $this->get_id(), GroupUseGroup :: get_table_name());
            $condition = new AndCondition($conditions);

            $groups_result = array();
            $usable_groups = $this->get_data_manager()->retrieve_usable_groups($condition);

            while ($usable_group = $usable_groups->next_result())
            {
                $groups_result[$usable_group->get_id()] = $usable_group->get_id();

                $subgroups = $usable_group->get_children();

                while ($subgroup = $subgroups->next_result())
                {
                    $groups_result[$subgroup->get_id()] = $subgroup->get_id();
                }
            }

            $this->allowed_groups = $groups_result;
        }

        return $this->allowed_groups;
    }

}
?>