<?php
/**
 * $Id: location.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib
 * @author Hans de Bisschop
 */

class Location extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_LOCATION = 'location_id';
    const PROPERTY_LEFT_VALUE = 'left_value';
    const PROPERTY_RIGHT_VALUE = 'right_value';
    const PROPERTY_PARENT = 'parent_id';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_IDENTIFIER = 'identifier';
    const PROPERTY_INHERIT = 'inherit';
    const PROPERTY_LOCKED = 'locked';

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_LOCATION, self :: PROPERTY_LEFT_VALUE, self :: PROPERTY_RIGHT_VALUE, self :: PROPERTY_PARENT, self :: PROPERTY_APPLICATION, self :: PROPERTY_TYPE, self :: PROPERTY_IDENTIFIER, self :: PROPERTY_INHERIT, self :: PROPERTY_LOCKED));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RightsDataManager :: get_instance();
    }

    function get_location()
    {
        return $this->get_default_property(self :: PROPERTY_LOCATION);
    }

    function set_location($location)
    {
        $this->set_default_property(self :: PROPERTY_LOCATION, $location);
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

    function get_parent()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT);
    }

    function set_parent($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT, $parent);
    }

    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    function set_type_from_object($object)
    {
        $this->set_type(Utilities :: camelcase_to_underscores(get_class($object)));
    }

    function get_identifier()
    {
        return $this->get_default_property(self :: PROPERTY_IDENTIFIER);
    }

    function set_identifier($identifier)
    {
        $this->set_default_property(self :: PROPERTY_IDENTIFIER, $identifier);
    }

    function get_inherit()
    {
        return $this->get_default_property(self :: PROPERTY_INHERIT);
    }

    function set_inherit($inherit)
    {
        $this->set_default_property(self :: PROPERTY_INHERIT, $inherit);
    }

    function inherits()
    {
        return $this->get_inherit();
    }

    function switch_inherit()
    {
        if ($this->inherits())
        {
            $this->set_inherit(false);
        }
        else
        {
            $this->set_inherit(true);
        }
    }

    function inherit()
    {
        $this->set_inherit(true);
    }

    function disinherit()
    {
        $this->set_inherit(false);
    }

    function get_locked()
    {
        return $this->get_default_property(self :: PROPERTY_LOCKED);
    }

    function set_locked($locked)
    {
        $this->set_default_property(self :: PROPERTY_LOCKED, $locked);
    }

    function is_locked()
    {
        return $this->get_locked();
    }

    function lock()
    {
        $this->set_locked(true);
    }

    function unlock()
    {
        $this->set_locked(false);
    }

    function switch_lock()
    {
        if ($this->is_locked())
        {
            $this->unlock();
        }
        else
        {
            $this->lock();
        }
    }

    function is_root()
    {
        $parent = $this->get_parent();
        return ($parent == 0);
    }

    function is_child_of($parent)
    {
        if (! is_object($parent))
        {
            $rdm = RightsDataManager :: get_instance();
            $parent = $rdm->retrieve_group($parent);
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
            $rdm = RightsDataManager :: get_instance();
            $child = $rdm->retrieve_location($child);
        }

        if ($this->get_left_value() < $child->get_left_value() && $child->get_right_value() < $this->get_right_value())
        {
            return true;
        }

        return false;
    }

    /**
     * Get the locations on the same level with the same parent
     */
    function get_siblings($include_self = true)
    {
        $rdm = RightsDataManager :: get_instance();

        $siblings_conditions = array();
        $siblings_conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, $this->get_parent());
        $siblings_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());

        if (! $include_self)
        {
            $siblings_conditions[] = new NotCondition(new EqualityCondition(Location :: PROPERTY_ID, $this->get_id()));
        }

        $siblings_condition = new AndCondition($siblings_conditions);

        return $rdm->retrieve_locations($siblings_condition);
    }

    function has_siblings()
    {
        $rdm = RightsDataManager :: get_instance();

        $siblings_conditions = array();
        $siblings_conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, $this->get_parent());
        $siblings_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());
        $siblings_conditions[] = new NotCondition(new EqualityCondition(Location :: PROPERTY_ID, $this->get_id()));

        $siblings_condition = new AndCondition($siblings_conditions);

        return ($rdm->count_locations($siblings_condition) > 0);
    }

    /**
     * Get the location's first level children
     */
    function get_children()
    {
        $rdm = RightsDataManager :: get_instance();

        $children_conditions = array();
        $children_conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, $this->get_id());
        $children_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());

        $children_condition = new AndCondition($children_conditions);

        return $rdm->retrieve_locations($children_condition);
    }

    function has_children()
    {
        $rdm = RightsDataManager :: get_instance();

        $children_conditions = array();
        $children_conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, $this->get_id());
        $children_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());

        $children_condition = new AndCondition($children_conditions);

        return ($rdm->count_locations($children_condition) > 0);
    }

    /**
     * Get all of the location's parents
     */
    function get_parents($include_self = true)
    {
        $rdm = RightsDataManager :: get_instance();

        $parent_conditions = array();
        if ($include_self)
        {
            $parent_conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $this->get_left_value());
            $parent_conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $this->get_right_value());
        }
        else
        {
            $parent_conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $this->get_left_value());
            $parent_conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_right_value());
        }
        $parent_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());

        $parent_condition = new AndCondition($parent_conditions);
        $order[] = new ObjectTableOrder(Location :: PROPERTY_LEFT_VALUE, SORT_DESC);

        return $rdm->retrieve_locations($parent_condition, null, null, $order);
    }

    function get_parent_location($include_self = true)
    {
        $rdm = RightsDataManager :: get_instance();

        return $rdm->retrieve_location($this->get_parent());
    }

    function get_locked_parent()
    {
        $rdm = RightsDataManager :: get_instance();

        $locked_parent_conditions = array();
        $locked_parent_conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $this->get_left_value());
        $locked_parent_conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_right_value());
        $locked_parent_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());
        $locked_parent_conditions[] = new EqualityCondition(Location :: PROPERTY_LOCKED, true);

        $locked_parent_condition = new AndCondition($locked_parent_conditions);
        $order[] = new ObjectTableOrder(Location :: PROPERTY_LEFT_VALUE);

        $locked_parents = $rdm->retrieve_locations($locked_parent_condition, null, 1, $order);

        if ($locked_parents->size() > 0)
        {
            return $locked_parents->next_result();
        }
        else
        {
            return null;
        }
    }

    function move($new_parent_id, $new_previous_id = 0)
    {
        $rdm = RightsDataManager :: get_instance();

        if (! $rdm->move_location($this, $new_parent_id, $new_previous_id))
        {
            return false;
        }

        return true;
    }

    function remove()
    {
        $rdm = RightsDataManager :: get_instance();

        // Delete the actual location
        if (! $rdm->delete_location_nodes($this))
        {
            return false;
        }

        // Update left and right values
        if (! $rdm->delete_nested_values($this))
        {
            // TODO: Some kind of general error handling framework would be nice: PEAR-ERROR maybe ?
            return false;
        }

        return true;
    }

    function create($previous_id = 0)
    {
        $rdm = RightsDataManager :: get_instance();
        $parent_id = $this->get_parent();

        $previous_visited = 0;

        if ($parent_id || $previous_id)
        {
            if ($previous_id)
            {
                $node = $rdm->retrieve_location($previous_id);
                $parent_id = $node->get_parent();

            // TODO: If $node is invalid, what then ?
            }
            else
            {
                $node = $rdm->retrieve_location($parent_id);
            }

            // Set the new location's parent id
            $this->set_parent($parent_id);

            // TODO: If $node is invalid, what then ?


            // get the "visited"-value where to add the new element behind
            // if $previous_id is given, we need to use the right-value
            // if only the $parent_id is given we need to use the left-value
            $previous_visited = $previous_id ? $node->get_right_value() : $node->get_left_value();

            // Correct the left and right values wherever necessary.
            if (! $rdm->add_nested_values($this, $previous_visited, 1))
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
        $this->set_id($rdm->get_next_location_id());
        if (! $rdm->create_location($this))
        {
            return false;
        }

        return true;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>