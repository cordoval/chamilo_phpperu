<?php
/**
 * $Id: location.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib
 * @author Hans de Bisschop
 */

class PortfolioLocation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_LEFT_VALUE = 'left_value';
    const PROPERTY_RIGHT_VALUE = 'right_value';
    const PROPERTY_PARENT = 'parent_id';
    const PROPERTY_TREE_IDENTIFIER = 'owner_id';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_IDENTIFIER = 'item_id';
    const PROPERTY_INHERIT = 'inherit';
    const PROPERTY_LOCKED = 'locked';

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_LEFT_VALUE, self :: PROPERTY_RIGHT_VALUE, self :: PROPERTY_PARENT, 
     												  self :: PROPERTY_INHERIT, self :: PROPERTY_LOCKED));
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

    
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
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
    
	function get_tree_identifier()
    {
        return $this->get_default_property(self :: PROPERTY_TREE_IDENTIFIER);
    }

    function set_tree_identifier($tree_identifier)
    {
        $this->set_default_property(self :: PROPERTY_TREE_IDENTIFIER, $tree_identifier);
    }
    
	
    function inherits()
    {
        return $this->get_inherit();
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

    
    function is_root()
    {
        $parent = $this->get_parent();
        return ($parent == 0);
    }

    function is_child_of($parent)
    {
        if (! is_object($parent))
        {
           
            $parent = PortfolioRights::retrieve_group($parent);
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

  

    /**
     * Get the location's first level children
     */
    function get_children()
    {
        $children_conditions = array();
        $children_conditions[] = new EqualityCondition(self :: PROPERTY_PARENT, $this->get_id());
        $children_conditions[] = new EqualityCondition(self :: PROPERTY_APPLICATION, $this->get_application());
        $siblings_conditions[] = new EqualityCondition(self :: PROPERTY_TREE_TYPE, $this->get_tree_type());
        $siblings_conditions[] = new EqualityCondition(self :: PROPERTY_TREE_IDENTIFIER, $this->get_tree_identifier());

        $children_condition = new AndCondition($children_conditions);

        return PortfolioRights::retrieve_locations($children_condition);
    }

    function has_children()
    {
        $children_conditions = array();
        $children_conditions[] = new EqualityCondition(self :: PROPERTY_PARENT, $this->get_id());
        $children_conditions[] = new EqualityCondition(self :: PROPERTY_APPLICATION, $this->get_application());
        $siblings_conditions[] = new EqualityCondition(self :: PROPERTY_TREE_TYPE, $this->get_tree_type());
        $siblings_conditions[] = new EqualityCondition(self :: PROPERTY_TREE_IDENTIFIER, $this->get_tree_identifier());

        $children_condition = new AndCondition($children_conditions);

        return (PortfolioRights::count_locations($children_condition) > 0);
    }

    /**
     * Get all of the location's parents
     */
    function get_parents($include_self = true)
    {
        $parent_conditions = array();
        if ($include_self)
        {
            $parent_conditions[] = new InequalityCondition(self :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $this->get_left_value());
            $parent_conditions[] = new InequalityCondition(self :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $this->get_right_value());
        }
        else
        {
            $parent_conditions[] = new InequalityCondition(self :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $this->get_left_value());
            $parent_conditions[] = new InequalityCondition(self :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_right_value());
        }
        $parent_conditions[] = new EqualityCondition(self :: PROPERTY_APPLICATION, $this->get_application());
        $parent_conditions[] = new EqualityCondition(self :: PROPERTY_TREE_TYPE, $this->get_tree_type());
        $parent_conditions[] = new EqualityCondition(self :: PROPERTY_TREE_IDENTIFIER, $this->get_tree_identifier());

        $parent_condition = new AndCondition($parent_conditions);
        $order[] = new ObjectTableOrder(self :: PROPERTY_LEFT_VALUE, SORT_DESC);

        return PortfolioRights::retrieve_locations($parent_condition, null, null, $order);
    }

    function get_parent_location($include_self = true)
    {
        return PortfolioRights::retrieve_location($this->get_parent());
    }

    function get_locked_parent()
    {
        $locked_parent_conditions = array();
        $locked_parent_conditions[] = new InequalityCondition(self :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $this->get_left_value());
        $locked_parent_conditions[] = new InequalityCondition(self :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_right_value());
        $locked_parent_conditions[] = new EqualityCondition(self :: PROPERTY_APPLICATION, $this->get_application());
        $locked_parent_conditions[] = new EqualityCondition(self :: PROPERTY_TREE_TYPE, $this->get_tree_type());
        $locked_parent_conditions[] = new EqualityCondition(self :: PROPERTY_TREE_IDENTIFIER, $this->get_tree_identifier());
        $locked_parent_conditions[] = new EqualityCondition(self :: PROPERTY_LOCKED, true);

        $locked_parent_condition = new AndCondition($locked_parent_conditions);
        $order[] = new ObjectTableOrder(self :: PROPERTY_LEFT_VALUE);

        $locked_parents = PortfolioRights::retrieve_locations($locked_parent_condition, null, 1, $order);

        if ($locked_parents->size() > 0)
        {
            return $locked_parents->next_result();
        }
        else
        {
            return null;
        }
    }



    function remove()
    {
        // Delete the actual location
        if (! PortfolioRights::delete_location_nodes($this))
        {
            return false;
        }

        // Update left and right values
        if (! PortfolioRights::delete_nested_values($this))
        {
            // TODO: Some kind of general error handling framework would be nice: PEAR-ERROR maybe ?
            return false;
        }
        return true;
    }

    function create($previous_id = 0)
    {
        $parent_id = $this->get_parent();
        $previous_visited = 0;
        if ($parent_id || $previous_id)
        {
            if ($previous_id)
            {
                $node = PortfolioRights::retrieve_location($previous_id);
                $parent_id = $node->get_parent();

            // TODO: If $node is invalid, what then ?
            }
            else
            {
                $node = PortfolioRights::retrieve_location($parent_id);
            }

            // Set the new location's parent id
            $this->set_parent($parent_id);

            // TODO: If $node is invalid, what then ?


            // get the "visited"-value where to add the new element behind
            // if $previous_id is given, we need to use the right-value
            // if only the $parent_id is given we need to use the left-value
            $previous_visited = $previous_id ? $node->get_right_value() : $node->get_left_value();

            // Correct the left and right values wherever necessary.
            if (! PortfolioRights::add_nested_values($this, $previous_visited, 1))
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
        if (! PortfolioRights::location_create($this))
        {
            return false;
        }

        return true;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    public function get_data_manager() {
        return PortfolioDataManager::get_instance();
    }
}
?>