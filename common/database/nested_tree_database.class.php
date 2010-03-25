<?php

/**
 * Extension on the database to embedd basic functionality for nested trees
 * @author Sven Vanpoucke
 */

class NestedTreeDatabase extends Database
{
	/**
	 * Counts the children of a tree node
	 * @param NestedTreeNode $node - the node
	 * @param Condition $condition - additional conditions
	 */
	function count_children($node, $condition = null)
	{
		$condition = $this->build_children_condition($node, false, $condition);
		return $this->count_objects($node->get_table_name(), $condition);
	}

	/**
	 * Retrieves the children of a tree node
	 * @param NestedTreeNode $node - the node
	 * @param boolean $recursive - if put on true, every child will be retrieved, even those who are not directly connected with parent_id
	 * @param Condition $condition - additional conditions
	 */
	function get_children($node, $recursive = false, $condition = null)
	{
		$condition = $this->build_children_condition($node, $recursive, $condition);
		return $this->retrieve_objects($node->get_table_name(), $condition);
	}

	/**
	 * Build the conditions for the get / count children methods
	 * @param NestedTreeNode $node - the node
	 * @param boolean $recursive - use recursive checks with left / right value or not recursive checks with parent_id
	 * @param Condition $condition - additional conditions
	 */
	private function build_children_condition($node, $recursive = false, $condition = null)
	{
	 	$children_conditions = array();

		if ($recursive)
        {
            $children_conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $node->get_left_value());
            $children_conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, $node->get_right_value());
        }
        else
        {
            $children_conditions[] = new EqualityCondition(NestedTreeNode :: PROPERTY_PARENT, $node->get_id());
        }

        if($condition)
        {
        	$children_conditions[] = $condition;
        }

        return new AndCondition($children_conditions);
	}

	/**
	 * Counts the parents of a tree node
	 * @param NestedTreeNode $node - the node
	 * @param boolean $include_object - if set to true the current node will be added to the count
	 * @param Condition $condition - additional conditions
	 */
	function count_parents($node, $include_object = false, $condition = null)
	{
		$condition = $this->build_parents_condition($node, true, $include_object, $condition);
		return $this->count_objects($node->get_table_name(), $condition);
	}

	/**
	 * Get the parents of a tree node
	 * @param NestedTreeNode $node - the node
	 * @param boolean $recursive - if set to true every parent will be retrieved recursivly, even those not connected with parent_id directly
	 * @param boolean $include_object - if set to true the current node will be added to the parents list
	 * @param Condition $condition - additional conditions
	 */
	function get_parents($node, $recursive = false, $include_object = false, $condition = null)
	{
    	$condition = $this->build_parents_condition($node, $recursive, $include_object, $condition);
		$order = new ObjectTableOrder(NestedTreeNode :: PROPERTY_LEFT_VALUE, SORT_DESC);
        return $this->retrieve_objects($node->get_table_name(), $condition, null, null, $order);
	}

	/**
	 * Build the conditions for the get / count parents methods
	 * @param NestedTreeNode $node - the node
	 * @param boolean $recursive - use recursive checks with left / right value or not recursive checks with parent_id
	 * @param boolean $include_object - if set to true the current node will be added to the parents list
	 * @param Condition $condition - additional conditions
	 */
	private function build_parents_condition($node, $recursive = false, $include_object = false, $condition = null)
	{
		$parent_conditions = array();

		if($recursive)
		{
			if ($include_object)
	        {
	            $parent_conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $node->get_left_value());
	            $parent_conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $node->get_right_value());
	        }
	        else
	        {
	            $parent_conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $node->get_left_value());
	            $parent_conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $node->get_right_value());
	        }
		}
		else
		{
			$parent_conditions[] = new EqualityCondition(NestedTreeNode :: PROPERTY_ID, $node->get_parent_id());
		}

		if($condition)
        {
        	$parent_conditions[] = $condition;
        }

        return new AndCondition($parent_conditions);
	}

	/**
	 * Counts the sibblings of a tree node
	 * @param NestedTreeNode $node - the node
	 * @param boolean $include_object - if set to true the current node will be added to the count
	 * @param Condition $condition - additional conditions
	 */
	function count_sibblings($node, $include_object = false, $condition = null)
	{
		$condition = $this->build_sibblings_condition($node, $include_object, $condition);
		return $this->count_objects($node->get_table_name(), $condition);
	}

	/**
	 * Gets the sibblings of a tree node
	 * @param NestedTreeNode $node - the node
	 * @param boolean $include_object - if set to true the current node will be added to the sibblings list
	 * @param Condition $condition - additional conditions
	 */
	function get_sibblings($node, $include_object = false, $condition = null)
	{
		$condition = $this->build_sibblings_condition($node, $include_object, $condition);
        return $this->retrieve_objects($node->get_table_name(), $condition);
	}

	/**
	 * Build the conditions for the get / count sibblings methods
	 * @param NestedTreeNode $node - the node
	 * @param boolean $include_object - if set to true the current node will be added to the sibblings list
	 * @param Condition $condition - additional conditions
	 */
	private function build_sibblings_condition($node, $include_object = false, $condition = null)
	{
		$siblings_conditions = array();

        $siblings_conditions[] = new EqualityCondition(NestedTreeNode :: PROPERTY_PARENT, $node->get_parent());

        if (!$include_object)
        {
            $siblings_conditions[] = new NotCondition(new EqualityCondition(NestedTreeNode :: PROPERTY_ID, $node->get_id()));
        }

        if($condition)
        {
        	$siblings_conditions[] = $condition;
        }

        return new AndCondition($siblings_conditions);
	}

	/**
	 * Move a node to a new parent with the use of a new parent id or a previous node id
	 * @param NestedTreeNode $node - the node
	 * @param int $new_parent_id - the new parent id
	 * @param int $new_previous_id - the previous node id
	 * @param Condition $condition - additional conditions
	 */
	function move($node, $new_parent_id = 0, $new_previous_id = 0, $condition = null)
	{
		// Check some things first to avoid trouble
        if ($new_previous_id)
        {
            // Don't let people move an element behind itself
            // No need to spawn an error, since we're just not doing anything
            if ($new_previous_id == $node->get_id())
            {
                return true;
            }

            $new_previous = $this->retrieve_node($node->get_table_name(), $new_previous_id);

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
            if ($node->is_parent_of($new_parent_id))
            {
                return false;
            }
            // Move an element underneath itself ?
            // No can do ... just ignore and return true
            if ($new_parent_id == $node->get_id())
            {
                return true;
            }
            // Try to retrieve the data of the parent element
            $new_parent = $this->retrieve_node($node->get_table_name(), $new_parent_id);

        	if(!$new_parent)
			{
				throw new Exception(Translation :: get('NewParentNodeCanNotBeNull'));
			}
        }

        $number_of_elements = ($node->get_right_value() - $node->get_left_value() + 1) / 2;
        $previous_visited = $new_previous_id ? $new_previous->get_right_value() : $new_parent->get_left_value();

        // Update the nested values so we can actually add the element
        // Return false if this failed
        if (! $this->add_nested_values($node, $previous_visited, $number_of_elements, $condition))
        {
            return false;
        }

        // Now we can update the actual parent_id
        // Return false if this failed
        $node = $this->retrieve_node($node->get_table_name(), $node->get_id());
        $node->set_parent_id($new_parent_id);
        if (! $node->update())
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
            $temp = $this->retrieve_node($node->get_table_name(), $new_previous_id);

	        if(!$temp)
			{
				throw new Exception(Translation :: get('NewPreviousNodeCanNotBeNull'));
			}

            $calculate_width = $temp->get_right_value();
        }
        else
        {
            $temp = $this->retrieve_node($node->get_table_name(), $new_parent_id);

        	if(!$temp)
			{
				throw new Exception(Translation :: get('NewParentNodeCanNotBeNull'));
			}

            $calculate_width = $temp->get_left_value();
        }

        // Get the element that is being moved again, since the left and
        // right might have changed by the add-call

        $node = $this->retrieve_node($node->get_table_name(), $node->get_id());

		if(!$node)
		{
			throw new Exception(Translation :: get('NodeCanNotBeNull'));
		}

        // Calculate the offset of the element to to the spot where it should go
        // correct the offset by one, since it needs to go inbetween!
        $offset = $calculate_width - $node->get_left_value() + 1;

        // Do the actual update
        $conditions = array();
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, ($node->get_left_value() - 1));
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, ($node->get_right_value() + 1));

		if($condition)
        {
        	$conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $properties = array();
        $properties[NestedTreeNode :: PROPERTY_LEFT_VALUE] = $this->escape_column_name(NestedTreeNode :: PROPERTY_LEFT_VALUE) . ' + ' . $this->quote($offset);
        $properties[NestedTreeNode :: PROPERTY_RIGHT_VALUE] = $this->escape_column_name(NestedTreeNode :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->quote($offset);
        $res = $this->update_objects($node->get_table_name(), $properties, $update_condition);

        if (!$res)
        {
            return false;
        }

        // Remove the subtree where the location was before
        if (! $this->delete_nested_values($node, $condition))
        {
            return false;
        }

        return true;
	}

	/**
	 * Retrieve a node from the database
	 * @param String $table_name - the table name
	 * @param int $id - the id of the node
	 */
	private function retrieve_node($table_name, $id)
	{
		$condition = new EqualityCondition(NestedTreeNode :: PROPERTY_ID, $id);
        return $this->retrieve_object($table_name, $condition);
	}

	/**
	 * Change the left/right values in the tree of every node that comes after the given node
	 * @param NestedTreeNode $node - the node
	 * @param int $previous_visited - the previous node
	 * @param int $number_of_elements - the number of elements which have to be inserted
	 * @param Condition $condition - additional condition
	 */
	function add_nested_values($node, $previous_visited, $number_of_elements = 1, $condition = null)
	{
		// Update all necessary left-values
		$conditions = array();
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);

        if($condition)
        {
        	$conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $properties = array(NestedTreeNode :: PROPERTY_LEFT_VALUE => $this->escape_column_name(NestedTreeNode :: PROPERTY_LEFT_VALUE) . ' + ' . $this->quote($number_of_elements * 2));
        $res = $this->update_objects($node->get_table_name(), $properties, $update_condition);

        if(!$res)
        {
        	return false;
        }

        // Update all necessary right-values
        $conditions = array();
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);

		if($condition)
        {
        	$conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

		$properties = array(NestedTreeNode :: PROPERTY_RIGHT_VALUE => $this->escape_column_name(NestedTreeNode :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->quote($number_of_elements * 2));
        $res = $this->update_objects($node->get_table_name(), $properties, $update_condition);

        if (!$res)
        {
            return false;
        }

        return true;
	}

	/**
	 * Change the left/right values in the tree of every node that is infected due to a delete of the given node
	 * @param NestedTreeNode $node - the node
	 * @param Condition $condition - additional condition
	 */
	function delete_nested_values($node, $condition)
	{
		$delta = $node->get_right_value() - $node->get_left_value() + 1;

        // Update all necessary nested-values
        $conditions = array();
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $node->get_left_value());

		if($condition)
        {
        	$conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $properties = array();
        $properties[NestedTreeNode :: PROPERTY_LEFT_VALUE] = $this->escape_column_name(NestedTreeNode :: PROPERTY_LEFT_VALUE) . ' - ' . $this->quote($delta);
        $properties[NestedTreeNode :: PROPERTY_RIGHT_VALUE] = $this->escape_column_name(NestedTreeNode :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta);
        $res = $this->update_objects($node->get_table_name(), $properties, $update_condition);

        if (!$res)
        {
            return false;
        }

        // Update some more nested-values
        $conditions = array();
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $node->get_left_value());
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $node->get_right_value());

		if($condition)
        {
        	$conditions[] = $condition;
        }

        $update_condition = new AndCondition($conditions);

        $properties = array(NestedTreeNode :: PROPERTY_RIGHT_VALUE => $this->escape_column_name(NestedTreeNode :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta));
        $res = $this->update_objects($node->get_table_name(), $properties, $update_condition);

        if (!$res)
        {
            return false;
        }

        return true;
	}

}

?>