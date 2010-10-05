<?php
/**
 * $Id: content_object_table_data_provider.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
/**
 * This class represents a data provider for a publication candidate table
 */
class ContentObjectTableDataProvider extends ObjectTableDataProvider
{
    /**
     * The user id of the current active user.
     */
    private $owner;
    /**
     * The possible types of learning objects which can be selected.
     */
    private $types;
    /**
     * The search query, or null if none.
     */
    private $query;

    private $parent;

    /**
     * Constructor.
     * @param int $owner The user id of the current active user.
     * @param array $types The possible types of learning objects which can be
     * selected.
     * @param string $query The search query.
     */
    function ContentObjectTableDataProvider($owner, $types, $query = null, $parent)
    {
        $this->set_types($types);
        $this->set_owner($owner);
        $this->set_query($query);
        $this->set_parent($parent);
    }

    /*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        $dm = RepositoryDataManager :: get_instance();

        if (!$this->get_parent()->is_shared_object_browser())
        {
        	return $dm->retrieve_content_objects($this->get_condition(), $order_property, $offset, $count);
        }
        else
        {
        	return $dm->retrieve_shared_content_objects($this->get_condition(), $offset, $count, $order_property);
        }
    }

    /*
	 * Inherited
	 */
    function get_object_count()
    {
        $dm = RepositoryDataManager :: get_instance();
        
    	if (!$this->get_parent()->is_shared_object_browser())
        {
        	return $dm->count_content_objects($this->get_condition());
        }
        else
        {
        	return $dm->count_shared_content_objects($this->get_condition());
        }
        
    }

    /**
     * Gets the condition by which the learning objects should be selected.
     * @return Condition The condition.
     */
    function get_condition()
    {
        $owner = $this->get_owner();

        $conditions = array();
        $type_conditions = array();

        $types = $this->get_types();

        foreach ($types as $type)
        {
            $type_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
        }

        $conditions[] = new OrCondition($type_conditions);
        
        $query = $this->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $conditions[] = new OrCondition($or_conditions);
        }
        
        if (!$this->get_parent()->is_shared_object_browser())
        {
            $category = Request :: get('category');
            $category = $category ? $category : 0;

            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $owner->get_id());
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category);
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, 0);

            foreach ($this->get_parent()->get_excluded_objects() as $excluded)
            {
                $conds[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_ID, $excluded, ContentObject :: get_table_name()));
            }
        }
        else
        {
            $subconditions = array();
            
	    	$subconditions[] = new AndCondition(array(
	    			new EqualityCondition(ContentObjectUserShare :: PROPERTY_USER_ID, $this->get_parent()->get_user_id(), ContentObjectUserShare :: get_table_name()),
	    			new InEqualityCondition(ContentObjectUserShare :: PROPERTY_RIGHT_ID, InequalityCondition :: GREATER_THAN_OR_EQUAL, ContentObjectShare :: USE_RIGHT, ContentObjectUserShare :: get_table_name())));
			
			$group_ids = array();
	    	$groups = $this->get_parent()->get_user()->get_groups();
	    	if($groups)
	    	{
	    		while($group = $groups->next_result())
	    		{
	    			$group_ids[] = $group->get_id();
	    		}
	    	
				$subconditions[] = new AndCondition(array(
	    			new InCondition(ContentObjectGroupShare :: PROPERTY_GROUP_ID, $group_ids, ContentObjectGroupShare :: get_table_name()),
	    			new InEqualityCondition(ContentObjectGroupShare :: PROPERTY_RIGHT_ID, InequalityCondition :: GREATER_THAN_OR_EQUAL, ContentObjectShare :: USE_RIGHT, ContentObjectGroupShare :: get_table_name())));
	    	}
			
	    	$conditions[] = new OrCondition($subconditions);
	    
        }
        
        
        return new AndCondition($conditions);
    }

    protected function set_types($types)
    {
        $this->types = $types;
    }

    protected function set_owner($owner)
    {
        $this->owner = $owner;
    }

    protected function set_query($query)
    {
        $this->query = $query;
    }

    protected function set_parent($parent)
    {
        $this->parent = $parent;
    }

    protected function get_types()
    {
        return $this->types;
    }

    protected function get_owner()
    {
        return $this->owner;
    }

    protected function get_query()
    {
        return $this->query;
    }

    protected function get_parent()
    {
        return $this->parent;
    }

    protected function get_type_conditions()
    {
        return null;
    }
}
?>