<?php
/**
 * @author Nick Van Loocke
 */
abstract class CbaManagerComponent extends WebApplicationComponent
{
	/**
	 * Constructor
	 * @param Cba $cba The cba which
	 * provides this component
	 */
	function CbaManagerComponent($cba)
	{
		parent :: __construct($cba);
	}

	// Data Retrieval

	// Competency
	function count_competencys($condition)
	{
		return $this->get_parent()->count_competencys($condition);
	}

	function retrieve_competencys($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_competencys($condition, $offset, $count, $order_property);
	}

 	function retrieve_competency($id)
	{
		return $this->get_parent()->retrieve_competency($id);
	}
	
	// Indicator
	function count_indicators($condition)
	{
		return $this->get_parent()->count_indicators($condition);
	}

	function retrieve_indicators($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_indicators($condition, $offset, $count, $order_property);
	}

 	function retrieve_indicator($id)
	{
		return $this->get_parent()->retrieve_indicator($id);
	}
	
	// Criteria
	function count_criterias($condition)
	{
		return $this->get_parent()->count_criterias($condition);
	}

	function retrieve_criterias($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_criterias($condition, $offset, $count, $order_property);
	}

 	function retrieve_criteria($id)
	{
		return $this->get_parent()->retrieve_criteria($id);
	}
	
	// Criteria Score
	function count_criterias_score($condition)
	{
		return $this->get_parent()->count_criterias_score($condition);
	}

	function retrieve_criterias_score($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_criterias_score($condition, $offset, $count, $order_property);
	}

 	function retrieve_criteria_score($id)
	{
		return $this->get_parent()->retrieve_criteria_score($id);
	}
	
	function retrieve_criteria_score_new($criteria_id, $id)
	{
		return $this->get_parent()->retrieve_criteria_score_new($criteria_id, $id);
	}
	
	// Competency Indicator
 	function count_competencys_indicator($condition)
	{
		return $this->get_parent()->count_competencys_indicator($condition);
	}
	
 	function retrieve_competencys_indicator($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_competencys_indicator($condition, $offset, $count, $order_property);
	}
	
 	function retrieve_competency_indicator($id)
	{
		return $this->get_parent()->retrieve_competency_indicator($id);
	}
	
 	// Indicator Criteria
 	function count_indicators_criteria($condition)
	{
		return $this->get_parent()->count_indicators_criteria($condition);
	}
	
 	function retrieve_indicators_criteria($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_indicators_criteria($condition, $offset, $count, $order_property);
	}
	
 	function retrieve_indicator_criteria($id)
	{
		return $this->get_parent()->retrieve_indicator_criteria($id);
	}
	

	// Url Creation

	// Competency
	function get_create_competency_url()
	{
		return $this->get_parent()->get_create_competency_url();
	}

	function get_update_competency_url($competency)
	{
		return $this->get_parent()->get_update_competency_url($competency);
	}
	
	function get_delete_competency_url($competency)
	{
		return $this->get_parent()->get_delete_competency_url($competency);
	}

	function get_browse_competencys_url()
	{
		return $this->get_parent()->get_browse_competencys_url();
	}
	
	function get_browse_competency_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_COMPETENCY));
	}
	
	function get_competency_moving_url($competency)
    {
        return $this->get_parent()->get_competency_moving_url($competency);
    }
	
	// Indicator
	function get_create_indicator_url()
	{
		return $this->get_parent()->get_create_indicator_url();
	}

	function get_update_indicator_url($indicator)
	{
		return $this->get_parent()->get_update_indicator_url($indicator);
	}
	
	function get_delete_indicator_url($indicator)
	{
		return $this->get_parent()->get_delete_indicator_url($indicator);
	}

	function get_browse_indicators_url()
	{
		return $this->get_parent()->get_browse_indicators_url();
	}
	
	function get_browse_indicator_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_INDICATOR));
	}
	
	function get_indicator_moving_url($indicator)
    {
        return $this->get_parent()->get_indicator_moving_url($indicator);
    }

	// Criteria
	function get_create_criteria_url()
	{
		return $this->get_parent()->get_create_criteria_url();
	}

	function get_update_criteria_url($criteria)
	{
		return $this->get_parent()->get_update_criteria_url($criteria);
	}
	
	function get_delete_criteria_url($criteria)
	{
		return $this->get_parent()->get_delete_criteria_url($criteria);
	}

	function get_browse_criterias_url()
	{
		return $this->get_parent()->get_browse_criterias_url();
	}
	
	function get_browse_criteria_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CRITERIA));
	}
	
	function get_criteria_moving_url($criteria)
    {
        return $this->get_parent()->get_criteria_moving_url($criteria);
    }
	
	
	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}
	
	function display_header($breadcrumbtrail, $display_search = false, $display_menu = true, $helpitem)
    {
        $this->get_parent()->display_header($breadcrumbtrail, $display_search, $display_menu, $helpitem);
    }
    
	function get_content_object_types($only_master_types = false)
    {
        return $this->get_parent()->get_content_object_types($only_master_types);
    }
    
	function get_category_manager_url()
    {
        return $this->get_parent()->get_category_manager_url();
    }
    
    
	// Categories 
	
	function retrieve_competency_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(CompetencyCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }
    
	function count_competency_categories($condition = null)
    {
        return $this->database->count_objects(CompetencyCategory :: get_table_name(), $condition);
    }
    
	function retrieve_indicator_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(IndicatorCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }
    
	function count_indicator_categories($condition = null)
    {
        return $this->database->count_objects(IndicatorCategory :: get_table_name(), $condition);
    }
    
	function retrieve_criteria_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(CriteriaCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }
    
	function count_criteria_categories($condition = null)
    {
        return $this->database->count_objects(CriteriaCategory :: get_table_name(), $condition);
    }

}
?>