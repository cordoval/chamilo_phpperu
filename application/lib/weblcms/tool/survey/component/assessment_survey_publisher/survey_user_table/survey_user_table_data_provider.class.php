<?php
/**
 * $Id: survey_user_table_data_provider.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_survey_publisher.survey_user_table
 */
/**
 * This class represents a data provider for a publication candidate table
 */
class SurveyUserTableDataProvider extends ObjectTableDataProvider
{
    /**
     * The user id of the current active user.
     */
    private $owner;
    /**
     * The possible types of learning objects which can be selected.
     */
    private $pid;
    
    private $parent;

    /**
     * Constructor.
     * @param int $owner The user id of the current active user.
     * @param array $types The possible types of learning objects which can be
     * selected.
     * @param string $query The search query.
     */
    function SurveyUserTableDataProvider($parent, $owner, $pid)
    {
        $this->owner = $owner;
        $this->pid = $pid;
        $this->parent = $parent;
    }

    /*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        //$survey_id = $this->survey->get_id();
        $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_SURVEY_ID, $this->pid);
        $results = WeblcmsDataManager :: get_instance()->retrieve_survey_invitations($condition, $offset, $count, $order_property);
        while ($object = $results->next_result())
        {
            $objects[] = $object;
        }
        return $objects;
    }

    /*
	 * Inherited
	 */
    function get_object_count()
    {
        return count($this->get_objects());
    }
}
?>