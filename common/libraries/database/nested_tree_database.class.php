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
		return $this->retrieve_objects($node->get_table_name(), $condition, null, null, array(), get_class($node))
        ;
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
        
        if ($condition)
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
        return $this->retrieve_objects($node->get_table_name(), $condition, null, null, $order, get_class($node));
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
        
        if ($recursive)
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
        
        if ($condition)
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
        return $this->retrieve_objects($node->get_table_name(), $condition, null, null, array(), get_class($node));
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
        
        if (! $include_object)
        {
            $siblings_conditions[] = new NotCondition(new EqualityCondition(NestedTreeNode :: PROPERTY_ID, $node->get_id()));
        }
        
        if ($condition)
        {
            $siblings_conditions[] = $condition;
        }
        
        return new AndCondition($siblings_conditions);
    }

    /**
     * Retrieve a node from the database
     * @param String $table_name - the table name
     * @param int $id - the id of the node
     */
    private function retrieve_node($node, $id)
    {
        $condition = new EqualityCondition(NestedTreeNode :: PROPERTY_ID, $id);
        return $this->retrieve_object($node->get_table_name(), $condition, array(), get_class($node));
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
        
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $update_condition = new AndCondition($conditions);
        
        $properties = array(NestedTreeNode :: PROPERTY_LEFT_VALUE => $this->escape_column_name(NestedTreeNode :: PROPERTY_LEFT_VALUE) . ' + ' . $this->quote($number_of_elements * 2));
        $res = $this->update_objects($node->get_table_name(), $properties, $update_condition);
        
        if (! $res)
        {
            return false;
        }
        
        // Update all necessary right-values
        $conditions = array();
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
        
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $update_condition = new AndCondition($conditions);
        
        $properties = array(NestedTreeNode :: PROPERTY_RIGHT_VALUE => $this->escape_column_name(NestedTreeNode :: PROPERTY_RIGHT_VALUE) . ' + ' . $this->quote($number_of_elements * 2));
        $res = $this->update_objects($node->get_table_name(), $properties, $update_condition);
        
        if (! $res)
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
        
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $update_condition = new AndCondition($conditions);
        
        $properties = array();
        $properties[NestedTreeNode :: PROPERTY_LEFT_VALUE] = $this->escape_column_name(NestedTreeNode :: PROPERTY_LEFT_VALUE) . ' - ' . $this->quote($delta);
        $properties[NestedTreeNode :: PROPERTY_RIGHT_VALUE] = $this->escape_column_name(NestedTreeNode :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta);
        $res = $this->update_objects($node->get_table_name(), $properties, $update_condition);
        
        if (! $res)
        {
            return false;
        }
        
        // Update some more nested-values
        $conditions = array();
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $node->get_left_value());
        $conditions[] = new InequalityCondition(NestedTreeNode :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $node->get_right_value());
        
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $update_condition = new AndCondition($conditions);
        
        $properties = array(NestedTreeNode :: PROPERTY_RIGHT_VALUE => $this->escape_column_name(NestedTreeNode :: PROPERTY_RIGHT_VALUE) . ' - ' . $this->quote($delta));
        $res = $this->update_objects($node->get_table_name(), $properties, $update_condition);
        
        if (! $res)
        {
            return false;
        }
        
        return true;
    }

}

?>