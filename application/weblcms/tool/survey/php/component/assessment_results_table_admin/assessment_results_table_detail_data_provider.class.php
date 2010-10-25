<?php
/**
 * $Id: assessment_results_table_detail_data_provider.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_results_table_admin
 */
/**
 * This class represents a data provider for a results candidate table
 */
class AssessmentResultsTableDetailDataProvider extends ObjectTableDataProvider
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
    function AssessmentResultsTableDetailDataProvider($parent, $owner, $pid = null, $types = array(), $query = null)
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
        $pub = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($this->pid);
        return $this->get_user_assessments($pub);
    }

    function get_user_assessments($pub)
    {
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $pub->get_id());
        
        if (! $this->parent->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->parent->get_user_id());
            $condition = new AndCondition($conditions);
        }
        
        $track = new WeblcmsAssessmentAttemptsTracker();
        $user_assessments = $track->retrieve_tracker_items($condition);
        foreach ($user_assessments as $user_assessment)
        {
            $all_assessments[] = $user_assessment;
        }
        return $all_assessments;
    }

    /*
	 * Inherited
	 */
    function get_object_count()
    {
        $pub = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($this->pid);
        return count($this->get_user_assessments($pub));
    }
}
?>