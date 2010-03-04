<?php
/**
 * $Id: object_publication_table_data_provider.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.object_publication_table
 */
/**
 * This class represents a data provider for a publication candidate table
 */
class ObjectPublicationTableDataProvider extends ObjectTableDataProvider
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
    private $condition;
    
    private $parent;

    /**
     * Constructor.
     * @param int $owner The user id of the current active user.
     * @param array $types The possible types of learning objects which can be
     * selected.
     * @param string $query The search query.
     */
    function ObjectPublicationTableDataProvider($parent, $owner, $types, $condition = null)
    {
        $this->types = $types;
        $this->owner = $owner;
        $this->condition = $condition;
        $this->parent = $parent;
    }

    /*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return $this->get_publications($offset, $count, $order_property);
    }

    function get_publications($from, $count, $column, $direction)
    {
        $datamanager = WeblcmsDataManager :: get_instance();
        $publications = $datamanager->retrieve_content_object_publications_new($this->get_conditions(), $column, $from, $count);
        return $publications;
    }

    /*
	 * Inherited
	 */
    function get_object_count()
    {
        $datamanager = WeblcmsDataManager :: get_instance();
        $publications = $datamanager->count_content_object_publications_new($this->get_conditions());
        return $publications;
    }

    function get_conditions()
    {
        $datamanager = WeblcmsDataManager :: get_instance();
        if ($this->parent->is_allowed(EDIT_RIGHT))
        {
            $user_id = array();
            $course_group_ids = array();
        }
        else
        {
       		$user_id = $this->get_user_id();
            $course_groups = $this->get_course_groups();
                
            $course_group_ids = array();
               
            foreach($course_groups as $course_group)
            {
             	$course_group_ids[] = $course_group->get_id();
            }
        }
        $course = $this->parent->get_course_id();
        
        if ($this->parent->get_parameter(WeblcmsManager :: PARAM_CATEGORY))
        {
            $category = $this->parent->get_parameter(WeblcmsManager :: PARAM_CATEGORY);
        }
        else
        {
            $category = 0;
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course);
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $this->parent->get_tool_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category);

        $access = array();
        if($user_id)
        {
    		$access[] = new InCondition(ContentObjectPublicationUser :: PROPERTY_USER, $user_id, $datamanager->get_database()->get_alias('content_object_publication_user'));
        }
    	
    	if(count($course_group_ids) > 0)
    	{
        	$access[] = new InCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, $course_group_ids, $datamanager->get_database()->get_alias('content_object_publication_course_group'));
    	}
        	
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(
            			new EqualityCondition(ContentObjectPublicationUser :: PROPERTY_USER, null, $datamanager->get_database()->get_alias('content_object_publication_user')), 
            			new EqualityCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, null, $datamanager->get_database()->get_alias('content_object_publication_course_group'))));
        }
        
        $conditions[] = new OrCondition($access);
        
        $subselect_conditions = array();
        $subselect_conditions[] = $this->get_subselect_condition();
        $subselect_condition = new AndCondition($subselect_conditions);
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition, ContentObjectPublication :: get_table_name());
        
        if ($this->condition)
            $conditions[] = $this->condition;
        
        $condition = new AndCondition($conditions);
        //dump($condition);
        return $condition;
    }

    /**
     * Gets the condition by which the learning objects should be selected.
     * @return Condition The condition.
     */
    function get_subselect_condition()
    {
        $type_cond = array();
        $types = $this->types;
        foreach ($types as $type)
        {
            $type_cond[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
        }
        $condition = new OrCondition($type_cond);
        
        return $condition;
    }
}
?>