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
 * count_object_sibblings
 * get_object_sibblings
 * move_object
 * add_object_nested_values
 * delete_object_nested_values
 * 
 * You can make use of nested tree database where most of these methods are predefined. You only need to define these methods as delegation methods then (unless you need some additional properties)
 * 
 * @author Sven Vanpoucke
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
            
            if(!method_exists($dm, $func))
            {
            	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
            }
            
        	return call_user_func(array($dm, $func), $this);
        }
    }
    
    /**
     * Retrieve the children of the object
     * @param boolean $recursive - if put on true, every child will be retrieved, even those who are not directly connected with parent_id
     */
    function get_children($recursive = true)
    {
    	$dm = $this->get_data_manager();
	    $func = 'get_' . $this->get_object_name() . '_children';
	    
    	if(!method_exists($dm, $func))
        {
         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
        }
	    
        return call_user_func(array($dm, $func), $this, $recursive);
    }
    
    /**
     * Count the parents of the object, recursivly, every parent will be counted, even those who are not directly connected with parent_id
     * @param boolean $include_self - if put on true, the current object will be included in the count
     */
	function count_parents($include_self = true)
    {
    	$dm = $this->get_data_manager();
        $func = 'count_' . $this->get_object_name() . '_parents';
        
    	if(!method_exists($dm, $func))
        {
         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
        }
        
        return call_user_func(array($dm, $func), $this, $include_self);
    }
    
    /**
     * Retrieve the parents of the object, recursivly, every parent will be counted, even those who are not directly connected with parent_id
     * @param boolean $include_self - if put on true, the current object will be included in the parents list
     */
    function get_parents($include_self = true)
    {
    	$dm = $this->get_data_manager();
        $func = 'get_' . $this->get_object_name() . '_parents';
        
    	if(!method_exists($dm, $func))
        {
         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
        }
        
        return call_user_func(array($dm, $func), $this, true, $include_self)->as_array();
    }
    
    /**
     * Retrieve the parent of the object that is directly connected with parent_id
     */
	function get_parent()
    {
    	$dm = $this->get_data_manager();
        $func = 'get_' . $this->get_object_name() . '_parents';
        
    	if(!method_exists($dm, $func))
        {
         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
        }
        
        return call_user_func(array($dm, $func), $this, false);
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
            $func = 'retrieve_' . $this->get_object_name();
            
	        if(!method_exists($dm, $func))
	        {
	         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
	        }
            
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
    
    /**
     * Check if the object is the parent of the given object
     * @param NestedTreeNode $node - the possible child node
     */
    function is_parent_of($node)
    {
    	if (!is_object($node))
        {
            $dm = $this->get_data_manager();
            $func = 'retrieve_' . $this->get_object_name();
            
	        if(!method_exists($dm, $func))
	        {
	         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
	        }
            
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
    
    /**
     * Check if the object has sibblings
     */
    function has_siblings()
    {
    	return ($this->count_sibblings(false) > 0);
    }
    
    /**
     * Count the sibblings of the object
     * @param boolean $include_self - if set to true, the object will be included in the count
     */
    function count_sibblings($include_self = true)
    {
    	$dm = $this->get_data_manager();
    	$func = 'count_' . $this->get_object_name() . '_sibblings';
    	
    	if(!method_exists($dm, $func))
        {
         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
        }
    	
    	return call_user_func(array($dm, $func), $this, $include_self);
    }
    
    /**
     * Retrieve the sibblings of the object
     * @param boolean $include_self - if set to true, the object will be included in the sibblings list
     */
    function get_siblings($include_self = true)
    {
    	$dm = $this->get_data_manager();
    	$func = 'get_' . $this->get_object_name() . '_sibblings';
    	
    	if(!method_exists($dm, $func))
        {
         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
        }
    	
    	return call_user_func(array($dm, $func), $this, $include_self);
    }
    
    /**
     * Move the object to another place in the tree (either with parent id or previous node id)
     * @param int $new_parent_id - the new parent_id
     * @param int $new_previous_id - the previous node id where you want to add the object
     */
    function move($new_parent_id = 0, $new_previous_id = 0)
    {
    	$dm = $this->get_data_manager();
    	$func = 'move_' . $this->get_object_name();
    	
    	if(!method_exists($dm, $func))
        {
         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
        }
    	
    	return call_user_func(array($dm, $func), $this, $new_parent_id, $new_previous_id);
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
            $func = 'retrieve_' . $this->get_object_name();
            
	        if(!method_exists($dm, $func))
	        {
	         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
	        }
            
        	if ($previous_id)
            {
            	$node = call_user_func(array($dm, $func), $previous_id);
               	$parent_id = $node->get_parent_id();
            }
            else
            {
               $node = call_user_func(array($dm, $func), $parent_id);
            	
            }
			           
            // Set the new parent_id
            $this->set_parent_id($parent_id);

            // get the "visited"-value where to add the new element behind
            // if $previous_id is given, we need to use the right-value
            // if only the $parent_id is given we need to use the left-value
            $previous_visited = $previous_id ? $node->get_right_value() : $node->get_left_value();

            // Correct the left and right values wherever necessary.
            $func = 'add_' . $this->get_object_name() . '_nested_values';
            
	        if(!method_exists($dm, $func))
	        {
	         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
	        }
            
            if (!call_user_func(array($dm, $func), $this, $previous_visited, 1))
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
        
        $func = 'delete_' . $this->get_object_name();
        
    	if(!method_exists($dm, $func))
        {
         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
        }
        
   		$children = $this->get_children(true);
                
        while($child = $children->next_result())
        {
        	if(!call_user_func(array($dm, $func), $child))
        		return false;
        }
        
        $func = 'delete_' . $this->get_object_name() . '_nested_values';
        
    	if(!method_exists($dm, $func))
        {
         	throw new Exception(Translation :: get('MethodDoesNotExist', array('function' => $func)));
        }
        
        // Update left and right values
        if (!call_user_func(array($dm, $func), $this))
        {
            return false;
        }
        
        // Delete all children
        
        return true;
    }
}
