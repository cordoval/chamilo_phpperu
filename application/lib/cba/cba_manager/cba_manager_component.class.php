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
	
	
	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}	
	
	function display_header($breadcrumbtrail, $display_search = false, $display_menu = true, $helpitem, $newbreadcrumb)
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
    
    
	function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }
   
}
?>