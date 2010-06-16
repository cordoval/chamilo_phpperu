<?php
/**
 * $Id: assessment_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_browser
 */
require_once dirname(__FILE__) . '/../../../../browser/object_publication_table/object_publication_table_cell_renderer.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class AssessmentCellRenderer extends ObjectPublicationTableCellRenderer
{
    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        if ($column === ObjectPublicationTableColumnModel :: get_action_column())
        {
            return $this->get_actions($publication)->as_html();
        }
        
        switch ($column->get_name())
        {
            case Assessment :: PROPERTY_ASSESSMENT_TYPE :
                $type =  $publication->get_content_object()->get_assessment_type_name();
                if ($publication->is_hidden())
                {
                    return '<span style="color: gray">' . $type . '</span>';
                }
                else
                {
                    return $type;
                }
        }
        
        return parent :: render_cell($column, $publication);
    }

    function get_actions($publication)
    {
    	$toolbar = parent :: get_actions($publication);
        
        $assessment = $publication->get_content_object();
        $track = new WeblcmsAssessmentAttemptsTracker();
        $condition_t = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication->get_id());
        $condition_u = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->table_renderer->get_user_id());
        $condition = new AndCondition(array($condition_t, $condition_u));
        $trackers = $track->retrieve_tracker_items($condition);
        
        $count = count($trackers);
        
        foreach ($trackers as $tracker)
        {
            if ($tracker->get_status() == 'not attempted')
            {
                $this->active_tracker = $tracker;
                $count --;
                break;
            }
        }
        
        if ($assessment->get_maximum_attempts() == 0 || $count < $assessment->get_maximum_attempts())
        {
        	$toolbar->add_item(new ToolbarItem(Translation :: get('TakeAssessment'), Theme :: get_common_image_path() . 'action_right.png', $this->table_renderer->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
        	$toolbar->add_item(new ToolbarItem(Translation :: get('TakeAssessment'), Theme :: get_common_image_path() . 'action_right_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('ViewResults'), Theme :: get_common_image_path() . 'action_view_results.png', $this->table_renderer->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Export'), Theme :: get_common_image_path() . 'action_export.png', $this->table_renderer->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_QTI, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar;
    }

}
?>