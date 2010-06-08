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

    function AssessmentCellRenderer($browser)
    {
        parent :: __construct($browser);
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        if ($column === ObjectPublicationTableColumnModel :: get_action_column())
        {
            return Utilities :: build_toolbar($this->get_actions($publication));
        }
        
        switch ($column->get_name())
        {
            case Assessment :: PROPERTY_ASSESSMENT_TYPE :
                $lo = $publication->get_content_object();
                $data = $lo->get_assessment_type();
                if ($publication->is_hidden())
                {
                    return '<span style="color: gray">' . $data . '</span>';
                }
                else
                {
                    return $data;
                }
        }
        
        return parent :: render_cell($column, $publication);
    }

    function get_actions($publication)
    {
        $actions = parent :: get_actions($publication);
        
        $assessment = $publication->get_content_object();
        $track = new WeblcmsAssessmentAttemptsTracker();
        $condition_t = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication->get_id());
        $condition_u = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->browser->get_user_id());
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
            $actions[] = array('href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 'label' => Translation :: get('TakeAssessment'), 'img' => Theme :: get_common_image_path() . 'action_right.png');
        }
        else
        {
            $actions[] = array('label' => Translation :: get('TakeAssessment'), 'img' => Theme :: get_common_image_path() . 'action_right_na.png');
        }
        
        $actions[] = array('href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())), 'label' => Translation :: get('ViewResults'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
        
        $actions[] = array('href' => $this->browser->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_QTI, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 'label' => Translation :: get('Export'), 'img' => Theme :: get_common_image_path() . 'action_export.png');
        
//        if ($assessment->get_assessment_type() == Assessment :: TYPE_SURVEY)
//        {
//            $actions[] = array('href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, AssessmentTool :: PARAM_PUBLICATION_ID => $publication->get_id())), 'label' => Translation :: get('InviteUsers'), 'img' => Theme :: get_common_image_path() . 'action_invite_users.png');
//        }
        
        return $actions;
    }

}
?>