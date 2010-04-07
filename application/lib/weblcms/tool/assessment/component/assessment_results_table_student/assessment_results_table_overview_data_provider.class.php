<?php
/**
 * $Id: assessment_results_table_overview_data_provider.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_results_table_student
 */
/**
 * This class represents a data provider for a results candidate table
 */
class AssessmentResultsTableOverviewStudentDataProvider extends ObjectTableDataProvider
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
    
    private $pid;

    /**
     * Constructor.
     * @param int $owner The user id of the current active user.
     * @param array $types The possible types of learning objects which can be
     * selected.
     * @param string $query The search query.
     */
    function AssessmentResultsTableOverviewStudentDataProvider($parent, $owner, $pid = null, $types = array(), $query = null)
    {
        $this->types = $types;
        $this->owner = $owner;
        $this->query = $query;
        $this->parent = $parent;
        $this->pid = $pid;
    }

    /*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        if ($this->pid == null)
        {
            $publications = $this->get_publications($offset, $count, $order_property);
        }
        else
        {
            $publications = $this->get_publication($this->pid);
        }
        //return $this->get_user_assessments($publications);
        

        return $publications;
    }

    function get_publication($pid)
    {
        $datamanager = WeblcmsDataManager :: get_instance();
        $publication = $datamanager->retrieve_content_object_publication($pid);
        if (! $publication->is_visible_for_target_users() && ! ($this->parent->is_allowed(DELETE_RIGHT) || $this->parent->is_allowed(EDIT_RIGHT)))
        {
            return array();
        }
        return array($publication);
    }

    function get_publications($from, $count, $column, $direction)
    {
        $datamanager = WeblcmsDataManager :: get_instance();
        $tool_condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'assessment');
        $condition = $tool_condition;
        
        if ($this->parent->is_allowed(EDIT_RIGHT))
        {
            $user_id = null;
            $course_groups = null;
        }
        else
        {
            $user_id = $this->parent->get_user_id();
            $course_groups = $this->parent->get_course_groups();
        }
        $course = $this->parent->get_course_id();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course);
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'assessment');
        
        /*$access = array();
		if (!empty($user_id))
		{
			$access[] = new EqualityCondition('user_id', $user_id, $datamanager->get_database()->get_alias('content_object_publication_user'));
		}

		if(count($course_groups) > 0)
		{
			$access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_database()->get_alias('content_object_publication_course_group'));
		}

		$conditions[] = new OrCondition($access);*/
        
        $subselect_condition = $this->get_condition();
        
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        
        $parent = $this->parent;
        $category = $parent->get_parameter(WeblcmsManager :: PARAM_CATEGORY);
        $category = $category ? $category : 0;
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category, ContentObjectPublication :: get_table_name());
        
        $condition = new AndCondition($conditions);
        
        $publications = $datamanager->retrieve_content_object_publications_new($condition);
        
        while ($publication = $publications->next_result())
        {
            // If the results is hidden and the user is not allowed to DELETE or EDIT, don't show this results
            if (! $publication->is_visible_for_target_users())
            {
                continue;
            }
            $visible_publications[] = $publication;
        }
        $publications = $visible_publications;
        return $publications;
    }

    /*
	 * Inherited
	 */
    function get_object_count()
    {
        return count($this->get_publications());
    }

    /**
     * Gets the condition by which the learning objects should be selected.
     * @return Condition The condition.
     */
    function get_condition()
    {
        $conds = array();
        
        $type_cond = array();
        $types = array('assessment', 'survey');
        foreach ($types as $type)
        {
            $type_cond[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
        }
        $conds[] = new OrCondition($type_cond);
        $c = Utilities :: query_to_condition($this->query);
        if (! is_null($c))
        {
            $conds[] = $c;
        }
        return new AndCondition($conds);
    }
}
?>