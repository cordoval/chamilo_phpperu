<?php

/**
 * Extension on the database to embedd basic functionality for nested trees
 */

abstract class NestedTreeDataClass extends DataClass
{
	const PROPERTY_PARENT_ID = 'parent_id';
	const PROPERTY_LEFT_VALUE = 'left_value';
	const PROPERTY_RIGHT_VALUE = 'right_value';
	
	/**
     * Returns the parent_id of this data class
     * @return int The parent_id.
     */
    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     * Sets parent_id of the data class
     * @param int $parent_id
     */
    function set_parent_id($parent_id)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
    }
    
	/**
     * Returns the left_value of this data class
     * @return int The left_value.
     */
    function get_left_value()
    {
        return $this->get_default_property(self :: PROPERTY_LEFT_VALUE);
    }

    /**
     * Sets left_value of the data class
     * @param int $left_value
     */
    function set_left_value($left_value)
    {
        $this->set_default_property(self :: PROPERTY_LEFT_VALUE, $left_value);
    }
    
	/**
     * Returns the right_value of this data class
     * @return int The right_value.
     */
    function get_right_value()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHT_VALUE);
    }

    /**
     * Sets right_value of the data class
     * @param int $right_value
     */
    function set_right_value($right_value)
    {
        $this->set_default_property(self :: PROPERTY_RIGHT_VALUE, $right_value);
    }
    
    // Nested trees functionality
    
    function has_children()
    {
    	 return ! ($this->get_left_value() == ($this->get_right_value() - 1));
    }
    
    function count_children($recursive = true)
    {
    	$dm = $this->get_data_manager();
    	
        if ($recursive)
        {
            return ($this->get_right_value() - $this->get_left_value() - 1) / 2;
        }
        else
        {
            $func = 'count_' . $this->get_object_name() . '_children';
        	return call_user_func(array($dm, $func), $this);
        }
    }
    
    function get_children($recursive = true)
    {
    	$dm = $this->get_data_manager();
	    $func = 'get_' . $this->get_object_name() . '_children';
        $node = call_user_func(array($dm, $func), $this, $recursive);
    }
    
	function count_parents($include_self = true)
    {
    	$dm = $this->get_data_manager();
        $func = 'count_' . $this->get_object_name() . '_parents';
        $node = call_user_func(array($dm, $func), $this, $include_self);
    }
    
    function get_parents($include_self = true)
    {
    	$dm = $this->get_data_manager();
        $func = 'get_' . $this->get_object_name() . '_parents';
        $node = call_user_func(array($dm, $func), $this, true, $include_self);
    }
    
	function get_parent()
    {
    	$dm = $this->get_data_manager();
        $func = 'get_' . $this->get_object_name() . '_parents';
        $node = call_user_func(array($dm, $func), $this, false);
    }
    
    function is_child_of($node)
    {
    	if (!is_object($node))
        {
            $dm = $this->get_data_manager();
            $func = 'retrieve_' . $this->get_object_name();
            $node = call_user_func(array($dm, $func), $node);
        }

        if(!$node)
        {
        	return false;
        }
        
        if ($this->get_left_value() > $node->get_left_value() && $node->get_right_value() > $this->get_right_value())
        {
            return true;
        }

        return false;
    }
    
    function is_parent_of($node)
    {
    	if (!is_object($node))
        {
            $dm = $this->get_data_manager();
            $func = 'retrieve_' . $this->get_object_name();
            $node = call_user_func(array($dm, $func), $node);
        }

        if(!$node)
        {
        	return false;
        }

        if ($this->get_left_value() < $node->get_left_value() && $node->get_right_value() < $this->get_right_value())
        {
            return true;
        }

        return false;
    }
    
    function has_siblings()
    {
    	$dm = $this->get_data_manager();
		$func = $this->get_object_name() . '_has_sibblings';
        return call_user_func(array($dm, $func), $this);
    }
    
    function get_siblings($include_self = true)
    {
    	$dm = $this->get_data_manager();
    	$func = 'get_' . $this->get_object_name() . '_sibblings';
    	return call_user_func(array($dm, $func), $this, $include_self);
    }
    
    function move($new_parent_id, $new_previous_id = 0)
    {
    	$dm = $this->get_data_manager();
    	$func = 'move_' . $this->get_object_name();
    	return call_user_func(array($dm, $func), $this, $new_parent_id, $new_previous_id);
    }
    
    function create($previous_id = 0)
    {
    	$dm = $this->get_data_manager();
        $parent_id = $this->get_parent_id();

        $previous_visited = 0;

        if ($parent_id || $previous_id)
        {
            $func = 'retrieve_' . $this->get_object_name();
        	if ($previous_id)
            {
                $node = call_user_func(array($dm, $func), $previous_id);
                $parent_id = $node->get_parent();
            }
            else
            {
                $node = call_user_func(array($dm, $func), $parent_id);
            }

            // Set the new parent_id
            $this->set_parent($parent_id);

            // get the "visited"-value where to add the new element behind
            // if $previous_id is given, we need to use the right-value
            // if only the $parent_id is given we need to use the left-value
            $previous_visited = $previous_id ? $node->get_right_value() : $node->get_left_value();

            // Correct the left and right values wherever necessary.
            $func = 'add_' . $this->get_object_name() . '_nested_values';
            if (!call_user_func(array($dm, $func), $previous_visited, 1))
            {
                return false;
            }
        }

        // Left and right values have been shifted so now we
        // want to really add the location itself, but first
        // we have to set it's left and right value.
        $this->set_left_value($previous_visited + 1);
        $this->set_right_value($previous_visited + 2);
        
        if(!parent :: create())
        {
        	return false;
        }

        return true;
    }
    
    function delete()
    {
    	$dm = $this->get_data_manager();

        if(!parent :: delete())
        {
        	return false;
        }

        $func = 'delete_' . $this->get_object_name() . '_nested_values';
        
        // Update left and right values
        if (!call_user_func(array($dm, $func), $this))
        {
            return false;
        }
        
        return true;
    }
}
