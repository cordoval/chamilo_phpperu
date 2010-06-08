<?php

/**
 * Extension on the database to embedd basic functionality for nested trees
 *
 * Implement the following functions in you datamanager order to make your dataclass work (change object to your class name)
 *
 * count_object_children
 * get_object_children
 * count_object_parents
 * get_object_parents
 * count_object_siblings
 * get_object_siblings
 * move_object
 * add_object_nested_values
 * delete_object_nested_values
 *
 * You can make use of nested tree database where most of these methods are predefined. You only need to define these methods as delegation methods then (unless you need some additional properties)
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

abstract class NestedTreeNode extends DataClass
{
	const PROPERTY_PARENT_ID = 'parent_id';
	const PROPERTY_LEFT_VALUE = 'left_value';
	const PROPERTY_RIGHT_VALUE = 'right_value';

	static function get_default_property_names($extended_property_names)
    {
        $extended_property_names[] = self :: PROPERTY_PARENT_ID;
        $extended_property_names[] = self :: PROPERTY_LEFT_VALUE;
        $extended_property_names[] = self :: PROPERTY_RIGHT_VALUE;
    	return parent :: get_default_property_names($extended_property_names);
    }

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

    /**
     * Check if the object has children
     */
    function has_children()
    {
    	 return ! ($this->get_left_value() == ($this->get_right_value() - 1));
    }

    /**
     * Count the number of children of the object
     * @param boolean $recursive - if put on true, every child will be counted, even those who are not directly connected with parent_id
     */
    function count_children($recursive = true, $condition)
    {
    	$dm = $this->get_data_manager();

    	return $dm->nested_tree_count_children($this, $recursive, $condition);

//        if ($recursive)
//        {
//            return ($this->get_right_value() - $this->get_left_value() - 1) / 2;
//        }
//        else
//        {
//            $func = 'count_' . $this->get_object_name() . '_children';
//
//            if(!method_exists($dm, $func))
//            {
//            	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//            }
//
//        	//return call_user_func(array($dm, $func), $this);
//            //return $dm->count_children($this, $this->get_nested_tree_node_condition());
//            return $dm->nested_tree_count_children($this, $this->get_nested_tree_node_condition());
//
//        }
    }

    /**
     * Retrieve the children of the object
     * @param boolean $recursive - if put on true, every child will be retrieved, even those who are not directly connected with parent_id
     */
    function get_children($recursive = true, $condition)
    {
    	$dm = $this->get_data_manager();
    	return $dm->nested_tree_get_children($this, $recursive, $condition);

//    	$func = 'get_' . $this->get_object_name() . '_children';
//
//    	if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }
//
//        return call_user_func(array($dm, $func), $this, $recursive);
    }

    /**
     * Count the parents of the object, recursivly, every parent will be counted, even those who are not directly connected with parent_id
     * @param boolean $include_self - if put on true, the current object will be included in the count
     */
	function count_parents($include_self = true, $condition)
    {
    	$dm = $this->get_data_manager();
    	return $dm->nested_tree_count_parents($this, $include_self, $condition);
//        $func = 'count_' . $this->get_object_name() . '_parents';
//
//    	if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }
//
//        return call_user_func(array($dm, $func), $this, $include_self);
    }

    /**
     * Retrieve the parents of the object, recursivly, every parent will be counted, even those who are not directly connected with parent_id
     * @param boolean $include_self - if put on true, the current object will be included in the parents list
     */
    function get_parents($include_self = true, $condition)
    {
    	$dm = $this->get_data_manager();
    	return $dm->nested_tree_get_parents($this, true, $include_self, $condition);
//        $func = 'get_' . $this->get_object_name() . '_parents';
//
//    	if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }
//
//        return call_user_func(array($dm, $func), $this, true, $include_self)->as_array();
    }

    /**
     * Retrieve the parent of the object that is directly connected with parent_id
     */
	function get_parent()
    {
    	$dm = $this->get_data_manager();
    	return $dm->nested_tree_retrieve_parent_from_node($this);
//        $func = 'get_' . $this->get_object_name() . '_parents';
//
//    	if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }
//
//        return call_user_func(array($dm, $func), $this, false);
    }

    /**
     * Check if the current object is a child of the given object
     * @param NestedTreeNode $node - the possible parent node
     */
    function is_child_of($node)
    {
    	if (!is_object($node))
        {
            $dm = $this->get_data_manager();
            $node = $dm->nested_tree_retrieve_node($this, $node);
//            $func = 'retrieve_' . $this->get_object_name();
//
//	        if(!method_exists($dm, $func))
//	        {
//	         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//	        }
//
//            $node = call_user_func(array($dm, $func), $node);
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

    /**
     * Check if the object is the parent of the given object
     * @param NestedTreeNode $node - the possible child node
     */
    function is_parent_of($node)
    {
    	if (!is_object($node))
        {
            $dm = $this->get_data_manager();
            $dm = $this->get_data_manager();
            $node = $dm->nested_tree_retrieve_node($this, $node);

//            $func = 'retrieve_' . $this->get_object_name();
//
//	        if(!method_exists($dm, $func))
//	        {
//	         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//	        }
//
//            $node = call_user_func(array($dm, $func), $node);
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

    /**
     * Check if the object has siblings
     */
    function has_siblings()
    {
    	return ($this->count_sibblings($this, false) > 0);
    }

    /**
     * Count the siblings of the object
     * @param boolean $include_self - if set to true, the object will be included in the count
     */
    function count_siblings($include_self = true, $condition)
    {
    	$dm = $this->get_data_manager();
    	return $dm->nested_tree_count_sibblings($this, $include_self, $condition);
//    	$func = 'count_' . $this->get_object_name() . '_sibblings';
//
//    	if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }
//
//    	return call_user_func(array($dm, $func), $this, $include_self);
    }

    /**
     * Retrieve the siblings of the object
     * @param boolean $include_self - if set to true, the object will be included in the siblings list
     */
    function get_siblings($include_self = true, $condition)
    {
    	$dm = $this->get_data_manager();
    	return $dm->nested_tree_get_sibblings($this, $include_self, $condition);
//    	$func = 'get_' . $this->get_object_name() . '_siblings';
//
//    	if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }
//
//    	return call_user_func(array($dm, $func), $this, $include_self);
    }

    /**
     * Move the object to another place in the tree (either with parent id or previous node id)
     * @param int $new_parent_id - the new parent_id
     * @param int $new_previous_id - the previous node id where you want to add the object
     */
    function move($new_parent_id = 0, $new_previous_id = 0)
    {
    	$dm = $this->get_data_manager();

		// Check some things first to avoid trouble
        if ($new_previous_id)
        {
            // Don't let people move an element behind itself
            // No need to spawn an error, since we're just not doing anything
            if ($new_previous_id == $this->get_id())
            {
                return true;
            }

            $new_previous = $dm->nested_tree_retrieve_node($this, $new_previous_id);

            if(!$new_previous)
			{
				throw new Exception(Translation :: get('NewPreviousNodeCanNotBeNull'));
			}

            $new_parent_id = $new_previous->get_parent();
        }
        else
        {
            // No parent ID was set ... problem !
            if ($new_parent_id == 0)
            {
                return false;
            }
            // Move the location underneath one of it's children ?
            // I think not ... Return error
            if ($this->is_parent_of($new_parent_id))
            {
                return false;
            }
            // Move an element underneath itself ?
            // No can do ... just ignore and return true
            if ($new_parent_id == $this->get_id())
            {
                return true;
            }
            // Try to retrieve the data of the parent element
            $new_parent = $dm->nested_tree_retrieve_node($this, $new_parent_id);

        	if(!$new_parent)
			{
				throw new Exception(Translation :: get('NewParentNodeCanNotBeNull'));
			}
        }

        $number_of_elements = ($this->get_right_value() - $this->get_left_value() + 1) / 2;
        $previous_visited = $new_previous_id ? $new_previous->get_right_value() : $new_parent->get_left_value();

        // Update the nested values so we can actually add the element
        // Return false if this failed
        //if (! $dm->add_nested_values($this, $previous_visited, $number_of_elements, $this->get_nested_tree_node_condition()))


        if (!$dm->nested_tree_add_nested_values($this, $previous_visited, $number_of_elements, $this->get_nested_tree_node_condition()))
        {
            return false;
        }

//        $func = 'add_' . $this->get_object_name() . '_nested_values';
//
//        if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }
//
//        if (!call_user_func(array($dm, $func), $this, $previous_visited, $number_of_elements))
//        {
//            return false;
//        }

        // Now we can update the actual parent_id
        // Return false if this failed
        $current_node = $dm->nested_tree_retrieve_node($this, $this->get_id());

        $this->set_left_value($current_node->get_left_value());
        $this->set_right_value($current_node->get_right_value());
        $this->set_parent_id($new_parent_id);
        if (! $this->update())
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
            $temp = $dm->nested_tree_retrieve_node($this, $new_previous_id);

	        if(!$temp)
			{
				throw new Exception(Translation :: get('NewPreviousNodeCanNotBeNull'));
			}

            $calculate_width = $temp->get_right_value();
        }
        else
        {
            $temp = $dm->nested_tree_retrieve_node($this, $new_parent_id);

        	if(!$temp)
			{
				throw new Exception(Translation :: get('NewParentNodeCanNotBeNull'));
			}

            $calculate_width = $temp->get_left_value();
        }

        // Get the element that is being moved again, since the left and
        // right might have changed by the add-call

        $current_node = $dm->nested_tree_retrieve_node($this, $this->get_id());
		if(!$current_node)
		{
			throw new Exception(Translation :: get('NodeCanNotBeNull'));
		}

        $this->set_left_value($current_node->get_left_value());
        $this->set_right_value($current_node->get_right_value());

        // Calculate the offset of the element to to the spot where it should go
        // correct the offset by one, since it needs to go inbetween!
        $offset = $calculate_width - $this->get_left_value() + 1;

        // Do the actual update
        $conditions = array();
        $conditions[] = new InequalityCondition(self :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, ($this->get_left_value() - 1));
        $conditions[] = new InequalityCondition(self :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, ($this->get_right_value() + 1));

		if($condition)
        {
        	$conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $properties = array();
        $properties[self :: PROPERTY_LEFT_VALUE] = $dm->escape_column_name(self :: PROPERTY_LEFT_VALUE) . ' + ' . $dm->quote($offset);
        $properties[self :: PROPERTY_RIGHT_VALUE] = $dm->escape_column_name(self :: PROPERTY_RIGHT_VALUE) . ' + ' . $dm->quote($offset);
        $res = $dm->update_objects($this->get_table_name(), $properties, $update_condition);

        if (!$res)
        {
            return false;
        }

        // Remove the subtree where the location was before
        //if (! $dm->delete_nested_values($this, $this->get_nested_tree_node_condition()))

        return $dm->nested_tree_delete_nested_values($this);

//        $func = 'delete_' . $this->get_object_name() . '_nested_values';
//
//    	if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }
//
//        // Update left and right values
//        if (!call_user_func(array($dm, $func), $this))
//        {
//            return false;
//        }
//
//        return true;
    }

    /**
     * Create the object in the database (either with parent id or previous node id)
     * @param int $previous_id - the previous node id where you want to add the object
     */
    function create($previous_id = 0)
    {
    	$dm = $this->get_data_manager();
        $parent_id = $this->get_parent_id();

        $previous_visited = 0;

        if ($parent_id || $previous_id)
        {
//            $func = 'retrieve_' . $this->get_object_name();
//
//	        if(!method_exists($dm, $func))
//	        {
//	         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//	        }

        	if ($previous_id)
            {
//            	$node = call_user_func(array($dm, $func), $previous_id);
               	dump('hi pr');
            	$node = $dm->nested_tree_retrieve_node($this, $previous_id);
            	$parent_id = $node->get_parent_id();
            }
            else
            {
//               $node = call_user_func(array($dm, $func), $parent_id);
				$node = $dm->nested_tree_retrieve_parent_from_node($this);

            }

            // Set the new parent_id
            $this->set_parent_id($parent_id);

            // get the "visited"-value where to add the new element behind
            // if $previous_id is given, we need to use the right-value
            // if only the $parent_id is given we need to use the left-value
            $previous_visited = $previous_id ? $node->get_right_value() : $node->get_left_value();

            // Correct the left and right values wherever necessary.
//            $dm = $this->get_data_manager();



            if (!$dm->nested_tree_add_nested_values($this, $previous_visited, 1))
//            $func = 'add_' . $this->get_object_name() . '_nested_values';
//
//	        if(!method_exists($dm, $func))
//	        {
//	         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//	        }
//
//            if (!call_user_func(array($dm, $func), $this, $previous_visited, 1))
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

    /**
     * Delete the object from the database
     * Delete the nested values
     */
    function delete()
    {
    	$dm = $this->get_data_manager();

        if(!parent :: delete())
        {
        	return false;
        }

        // Delete Children

//        $func = 'delete_' . $this->get_object_name();
//
//    	if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }

   		$children = $this->get_children(true);

        while($child = $children->next_result())
        {
//        	if(!call_user_func(array($dm, $func), $child))
//        		return false;
        	$condition = new EqualityCondition(NestedTreeNode::PROPERTY_ID, $child->get_id());
        	if(!$dm->delete($this->get_table_name(), $condition))
        		return false;
        }

	    if (!$dm->nested_tree_delete_nested_values($this))
//        $func = 'delete_' . $this->get_object_name() . '_nested_values';
//
//    	if(!method_exists($dm, $func))
//        {
//         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
//        }
//
//        // Update left and right values
//        if (!call_user_func(array($dm, $func), $this))
	    {
	        return false;
	    }

        // Delete all children

        return true;
    }

    //abstract function get_nested_tree_node_condition();
}
