<?php
namespace application\weblcms\tool\learning_path;

use common\libraries\NotCondition;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\WebApplication;
use common\libraries\Text;
use application\weblcms\WeblcmsManager;
use application\weblcms\ObjectPublicationTableCellRenderer;
use application\weblcms\WeblcmsLpAttemptTracker;
use application\weblcms\Tool;

/**
 * $Id: learning_path_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_browser
 */
require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_lp_attempt_tracker.class.php';
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
        /*if ($column === ObjectPublicationTableColumnModel :: get_action_column())
        {
        	return $this->get_actions($publication)->as_html();
        }*/

        switch ($column->get_name())
        {
            case 'progress' :
                {
                    if (! $this->table_renderer->get_tool_browser()->get_parent()->is_empty_learning_path($publication))
                    {
                        return $this->get_progress($publication);
                    }
                    else
                    {
                        return Translation :: get('EmptyLearningPath');
                    }
                }
        }

        return parent :: render_cell($column, $publication);
    }

    function get_progress($publication)
    {
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->table_renderer->get_course_id());
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $publication->get_id());
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, $this->table_renderer->get_user_id());
        //$conditions[] = new NotCondition(new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS, 100));
        $condition = new AndCondition($conditions);

        $dummy = new WeblcmsLpAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $lp_tracker = $trackers[0];

        if ($lp_tracker)
        {
            $progress = $lp_tracker->get_progress();
        }
        else
        {
            $progress = 0;
        }

        $bar = $this->get_progress_bar($progress);
        $url = $this->table_renderer->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), 'lp_action' => 'view_progress'));
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

}
?>