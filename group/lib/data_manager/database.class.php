<?php
/**
 * $Id: database.class.php 232 2009-11-16 10:11:48Z vanpouckesven $
 * @package group.lib.datamanager
 */
require_once 'MDB2.php';
require_once dirname(__FILE__) . '/../group_data_manager_interface.class.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
 *  @author Sven Vanpoucke
==============================================================================
 */

class DatabaseGroupDataManager extends Database implements GroupDataManagerInterface
{
    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('group_');
    }

    function update_group($group)
    {
        $condition = new EqualityCondition(Group :: PROPERTY_ID, $group->get_id());
        return $this->update($group, $condition);
    }

    function delete_group($group)
    {
        $condition = new EqualityCondition(Group :: PROPERTY_ID, $group->get_id());
        $bool = $this->delete($group->get_table_name(), $condition);

        $condition_subgroups = new EqualityCondition(Group :: PROPERTY_PARENT, $group->get_id());
        $groups = $this->retrieve_groups($condition_subgroups);
        while ($gr = $groups->next_result())
        {
            $bool = $bool & $this->delete_group($gr);
        }

        $this->truncate_group($group);

        return $bool;

    }

    function truncate_group($group)
    {
        $condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
        return $this->delete(GroupRelUser :: get_table_name(), $condition);
    }

    function delete_group_rel_user($groupreluser)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $groupreluser->get_group_id());
        $conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $groupreluser->get_user_id());
        $condition = new AndCondition($conditions);

        return $this->delete($groupreluser->get_table_name(), $condition);
    }

    function create_group($group)
    {
        return $this->create($group);
    }

    function create_group_rel_user($groupreluser)
    {
        return $this->create($groupreluser);
    }

    function count_groups($condition = null)
    {
        return $this->count_objects(Group :: get_table_name(), $condition);
    }

    function count_group_rel_users($condition = null)
    {
        return $this->count_objects(GroupRelUser :: get_table_name(), $condition);
    }

    function retrieve_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(Group :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_group_rel_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(GroupRelUser :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_group_rel_user($user_id, $group_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group_id);
        $condition = new AndCondition($conditions);

        return $this->retrieve_object(GroupRelUser :: get_table_name(), $condition);
    }

    function retrieve_user_groups($user_id)
    {
        $condition = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user_id);
        return $this->retrieve_objects(GroupRelUser :: get_table_name(), $condition);
    }

    function retrieve_group($id)
    {
        $condition = new EqualityCondition(Group :: PROPERTY_ID, $id);
        return $this->retrieve_object(Group :: get_table_name(), $condition);
    }

    function retrieve_group_by_name($name)
    {
        $condition = new EqualityCondition(Group :: PROPERTY_NAME, $name);
        return $this->retrieve_object(Group :: get_table_name(), $condition);
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->create_storage_unit($name, $properties, $indexes);
    }

    function retrieve_group_rights_templates($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(GroupRightsTemplate :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function delete_group_rights_templates($condition)
    {
        return $this->delete(GroupRightsTemplate :: get_table_name(), $condition);
    }

    function add_rights_template_link($group, $rights_template_id)
    {
        $props = array();
        $props[GroupRightsTemplate :: PROPERTY_GROUP_ID] = $group->get_id();
        $props[GroupRightsTemplate :: PROPERTY_RIGHTS_TEMPLATE_ID] = $rights_template_id;
        $this->get_connection()->loadModule('Extended');
        return $this->get_connection()->extended->autoExecute($this->get_table_name(GroupRightsTemplate :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT);
    }

    function delete_rights_template_link($group, $rights_template_id)
    {
        $conditions = array();
        $conditions = new EqualityCondition(GroupRightsTemplate :: PROPERTY_GROUP_ID, $group->get_id());
        $conditions = new EqualityCondition(GroupRightsTemplate :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_template_id);
        $condition = new AndCondition($conditions);

        return $this->delete(GroupRightsTemplate :: get_table_name(), $condition);
    }

    function update_rights_template_links($group, $rights_templates)
    {
        // Delete the no longer existing links
        $conditions = array();
        $conditions = new NotCondition(new InCondition(GroupRightsTemplate :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_templates));
        $conditions = new EqualityCondition(GroupRightsTemplate :: PROPERTY_GROUP_ID, $group->get_id());
        $condition = new AndCondition($conditions);

        $success = $this->delete(GroupRightsTemplate :: get_table_name(), $condition);
        if (! $success)
        {
            return false;
        }

        // Get the group's rights_templates
        $condition = new EqualityCondition(GroupRightsTemplate :: PROPERTY_GROUP_ID, $group->get_id());
        $group_rights_templates = $this->retrieve_group_rights_templates($condition);
        $existing_rights_templates = array();

        while ($group_rights_template = $group_rights_templates->next_result())
        {
            $existing_rights_templates[] = $group_rights_template->get_rights_template_id();
        }

        // Add the new links
        foreach ($rights_templates as $rights_template)
        {
            if (! in_array($rights_template, $existing_rights_templates))
            {
                if (! $this->add_rights_template_link($group, $rights_template))
                {
                    return false;
                }
            }
        }

        return true;
    }

    function is_groupname_available($groupname, $group_id = null)
    {
        $condition = new EqualityCondition(Group :: PROPERTY_NAME, $groupname);

        if ($group_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(Group :: PROPERTY_NAME, $groupname);
            $conditions = new EqualityCondition(Group :: PROPERTY_ID, $group_id);
            $condition = new AndCondition($conditions);
        }

        return ! ($this->count_objects(Group :: get_table_name(), $condition) == 1);
    }

    function add_nested_values($previous_visited, $number_of_elements = 1)
    {
        // Update all necessary left-values
        $condition = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);

        $query = 'UPDATE ' . $this->escape_table_name('group') . ' SET ' . $this->escape_column_name(Group :: PROPERTY_LEFT_VALUE) . '=' . $this->escape_column_name(Group :: PROPERTY_LEFT_VALUE) . ' + ' . $this->quote($number_of_elements * 2);
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }

		$res = $this->query($query);
		$res->free();

        // Update all necessary right-values
        $condition = new InequalityCondition(Group :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
        $query = 'UPDATE ' . $this->escape_table_name('group') . ' SET ' . $this->escape_column_name(Group :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Group :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->quote($number_of_elements * 2);

        $translator = new ConditionTranslator($this);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);
        $res->free();
        // TODO: For now we just return true ...
        return true;
    }

    function delete_nested_values($group)
    {
        $delta = $group->get_right_value() - $group->get_left_value() + 1;

        // Update all necessary nested-values
        $condition = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $group->get_left_value());

        $query = 'UPDATE ' . $this->escape_table_name('group');
        $query .= ' SET ' . $this->escape_column_name(Group ::$thisVALUE) . '=' . $this->escape_column_name(Group :: PROPERTY_LEFT_VALUE) . ' - ' . $this->quote($delta) . ', ';
        $query .= $this->escape_column_name(Group :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Group :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta);

        $translator = new ConditionTranslator($this);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);
        $res->free();

        // Update some more nested-values
        $conditions = array();
        $conditions[] = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $group->get_left_value());
        $conditions[] = new InequalityCondition(Group :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $group->get_right_value());
        $condition = new AndCondition($conditions);

        $query = 'UPDATE ' . $this->escape_table_name('group');
        $query .= ' SET ' . $this->escape_column_name(Group :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Group :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta);

        $translator = new ConditionTranslator($this);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);
        $res->free();
        return true;
    }

    function move_group($group, $new_parent_id, $new_previous_id = 0)
    {
        // Check some things first to avoid trouble
        if ($new_previous_id)
        {
            // Don't let people move an element behind itself
            // No need to spawn an error, since we're just not doing anything
            if ($new_previous_id == $group->get_id())
            {
                return true;
            }

            $new_previous = $this->retrieve_group($new_previous_id);
            // TODO: What if group $new_previous_id doesn't exist ? Return error.
            $new_parent_id = $new_previous->get_parent();
        }
        else
        {
            // No parent ID was set ... problem !
            if ($new_parent_id == 0)
            {
                return false;
            }
            // Move the group underneath one of it's children ?
            // I think not ... Return error
            if ($group->is_parent_of($new_parent_id))
            {
                return false;
            }
            // Move an element underneath itself ?
            // No can do ... just ignore and return true
            if ($new_parent_id == $group->get_id())
            {
                return true;
            }
            // Try to retrieve the data of the parent element
            $new_parent = $this->retrieve_group($new_parent_id);
            // TODO: What if this is an invalid group ? Return error.
        }

        $number_of_elements = ($group->get_right_value() - $group->get_left_value() + 1) / 2;
        $previous_visited = $new_previous_id ? $new_previous->get_right_value() : $new_parent->get_left_value();

        // Update the nested values so we can actually add the element
        // Return false if this failed
        if (! $this->add_nested_values($previous_visited, $number_of_elements))
        {
            return false;
        }

        // Now we can update the actual parent_id
        // Return false if this failed
        $group = $this->retrieve_group($group->get_id());
        $group->set_parent($new_parent_id);
        if (! $group->update())
        {
            return false;
        }

        // Update the left/right values of those elements that are being moved


        // First get the offset we need to add to the left/right values
        // if $newPrevId is given we need to get the right value,
        // otherwise the left since the left/right has changed
        // because we already updated it up there. We need to get them again.
        // We have to do that anyway, to have the proper new left/right values
        if ($new_previous_id)
        {
            $temp = $this->retrieve_group($new_previous_id);
            // TODO: What if $temp doesn't exist ? Return error.
            $calculate_width = $temp->get_right_value();
        }
        else
        {
            $temp = $this->retrieve_group($new_parent_id);
            // TODO: What if $temp doesn't exist ? Return error.
            $calculate_width = $temp->get_left_value();
        }

        // Get the element that is being moved again, since the left and
        // right might have changed by the add-call


        $group = $this->retrieve_group($group->get_id());
        // TODO: What if $group doesn't exist ? Return error.

        // Calculate the offset of the element to to the spot where it should go
        // correct the offset by one, since it needs to go inbetween!
        $offset = $calculate_width - $group->get_left_value() + 1;

        // Do the actual update
        $conditions = array();
        $conditions[] = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, ($group->get_left_value() - 1));
        $conditions[] = new InequalityCondition(Group :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, ($group->get_right_value() + 1));
        $condition = new AndCondition($conditions);

        $query = 'UPDATE ' . $this->escape_table_name('group');
        $query .= ' SET ' . $this->escape_column_name(Group :: PROPERTY_LEFT_VALUE) . '=' . $this->escape_column_name(Group :: PROPERTY_LEFT_VALUE) . ' + ' . $this->quote($offset) . ', ';
        $query .= $this->escape_column_name(Group :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Group :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->quote($offset);

        $translator = new ConditionTranslator($this);
        $query .= $translator->render_query($condition);

        $res = $this->query($query);
        $res->free();

        // Remove the subtree where the group was before
        if (! $this->delete_nested_values($group))
        {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the given column name is the name of a column that
     * contains a date value, and hence should be formatted as such.
     * @param string $name The column name.
     * @return boolean True if the column is a date column, false otherwise.
     */
    static function is_date_column($name)
    {
        return false;
    }
}
?>