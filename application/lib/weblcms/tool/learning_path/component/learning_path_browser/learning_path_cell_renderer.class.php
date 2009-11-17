<?php
/**
 * $Id: learning_path_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_browser
 */
require_once dirname(__FILE__) . '/../../../../browser/object_publication_table/object_publication_table_cell_renderer.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_lp_attempt_tracker.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class LearningPathCellRenderer extends ObjectPublicationTableCellRenderer
{

    function LearningPathCellRenderer($browser)
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
            case 'progress' :
                return $this->get_progress($publication);
        }
        
        return parent :: render_cell($column, $publication);
    }

    function get_progress($publication)
    {
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->browser->get_course_id());
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $publication->get_id());
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, $this->browser->get_user_id());
        //$conditions[] = new NotCondition(new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS, 100));
        $condition = new AndCondition($conditions);
        
        $dummy = new WeblcmsLpAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $lp_tracker = $trackers[0];
        
        if ($lp_tracker)
            $progress = $lp_tracker->get_progress();
        else
            $progress = 0;
        
        $bar = $this->get_progress_bar($progress);
        $url = $this->browser->get_url(array('tool_action' => 'view', 'pid' => $publication->get_id(), 'lp_action' => 'view_progress'));
        return Text :: create_link($url, $bar);
    }

    private function get_progress_bar($progress)
    {
        $html[] = '<div style="position: relative; text-align: center; border: 1px solid black; height: 14px; width:100px;">';
        $html[] = '<div style="background-color: lightblue; height: 14px; width:' . $progress . 'px; text-align: center;">';
        $html[] = '</div>';
        $html[] = '<div style="width: 100px; text-align: center; position: absolute; top: 0px;">' . round($progress) . '%</div></div>';
        
        return implode("\n", $html);
    }

    function get_actions($publication)
    {
        $actions = parent :: get_actions($publication);
        
        unset($actions['move']);
        
        $actions['reporting'] = array('href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 'label' => Translation :: get('Statistics'), 'img' => Theme :: get_common_image_path() . 'action_reporting.png');
        
        return $actions;
    }

}
?>